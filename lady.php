<?php

class Lady {
  protected $blocks, $keywords;

  public function __construct() {
    $paramBlocks = 'catch elseif for foreach if switch try while';
    $blocks = 'class do else function';
    $keywords = "end abstract and as break callable case clone
      const continue declare default echo enddeclare endfor endforeach
      endif endswitch endwhile extends final global goto implements
      include include_once instanceof insteadof interface namespace
      new or print private protected public require require_once return
      static throw trait use var xor yield $blocks $paramBlocks";
    $this->keywords = preg_replace('~\s+~', '|', trim($keywords));
    $this->blocks = preg_replace('~\s+~', '|', trim($blocks));
    $this->paramBlocks = preg_replace('~\s+~', '|', trim($paramBlocks));
  }

  function toPhp($input){
    $pattern = '~^ ([^"\']*)? ("[^"\\\\]*(\\\\.[^"\\\\]*)*"'
      . '|\'[^\'\\\\]*(\\\\.[^\'\\\\]*)*\')? ~x';
    $rules = array(
      '~ \s+$ ~' => '',
      '~ \.([\w_]) ~x' => '->\1',
      '~ \b def \b ~x' => 'function',
      '~ ([^>\$]|^)\b ([a-z\_][\w\d\_]* \b (?!\()) ~x' => '\1\$\2',
      '~ ([^\s]|^) \: (\s) ~mx' => '\1 =>\2',
      "~ \\$({$this->keywords}) \b ~x" => '\1',
      "~ (\s* function \s*) (\(.* ) $~xm" => '\1\2 {',
      "~^ (\s* function \s*) \\$ ~xm" => '\1',
      "~^ (\s* function \s+ [^(\\v]+) $ ~xm" => '\1()',
      "~^ (\s* ({$this->blocks})) \s* ([^\s\(].*$) ~xm" => '\1 \3 {',
      "~^ (\s* ({$this->paramBlocks})) \s* ([^\(].*$) ~xm" => '\1 (\3) {',
      "~^ (\s*) end \b ~xm" => '\1}',
      '~^ \n ~x' => ";\n",
      '~^ (.* [^{}[(;:,\s]) $~xm' => '\1;',
      '~ ; (\n \s* [\]\+\-\/\*\.\)\?\:] ) ~x' => '\1',
      '~ <\?(php)?;? ~x' => '<?php',
    );
    $output = '';
    while (mb_strlen($input) > 0) {
      if (!preg_match($pattern, $input, $match)) {
        continue;
      }
      $match += array_fill(0, 3, '');
      list($code, $string) = array_slice($match, 1, 2);
      $output .= preg_replace(array_keys($rules),
        array_values($rules), $code);
      $output .= $string;
      $input = mb_substr($input, mb_strlen($match[0]));
    }
    return $output;
  }
}

/**
 * Process parameters from command line
 */
if (realpath($argv[0]) == realpath(__FILE__)){
  $input = file_get_contents('php://stdin');
  $lady = new Lady();
  echo $lady->toPhp($input);
}
