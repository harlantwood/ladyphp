<?

class Lady{

  static public function parse($source, $debug = false){
    $code = $dump = '';

    // TOKENS
    $tokens = token_get_all($source);
    foreach ($tokens as $n => $token){
      if (!is_array($token)){
        $name = $string = $token;
      }
      else {
        $name = token_name($token[0]);
        $string = $token[1];
      }
      if ($name == 'T_STRING')
        $code .= '$' . $string;
      elseif ($name != 'T_COMMENT')
        $code .= $string;
      $dump .= "$n. $name: $string\n";
    }

    // LINES
    $lines = explode("\n", $code);
    $indent = 0;
    foreach ($lines as $n => $line){
      $line = rtrim($line);
      $indent_before = $indent;
      $indent = (strlen($line) - strlen(ltrim($line))) / 2;
      $jump = $indent - $indent_before;
      if ($jump > 0 && $n > 0)
        $lines[$n - 1] .= ' {';
      if ($jump < 0)
        $line = str_repeat('} ', -$jump) . $line;
      if ($jump <= 0 
          && $n > 0 
          &&  preg_match('/.*[^<\? {}].*/', $lines[$n - 1])
          && !preg_match('/^ *(#|\/\/)/', $lines[$n - 1])
          && !preg_match('/; *$/', $lines[$n - 1]))
        $lines[$n - 1] .= ';';
      $lines[$n] = $line;
    }
    $code = implode("\n", $lines);

    // OUTPUT
    if ($debug)
      return $dump;
    else
      return $code;
  }

  static public function parseFile($file){
    return self::parse(file_get_contents($file));
  }

  static public function includeFile($file){
    ob_start();
    eval('?>' . self::parseFile($file));
    return ob_get_clean();
  }

  static public function test($file){
    $source = file_get_contents($file);
    $output = self::parseFile($file);
    $source = htmlspecialchars($source);
    $output = htmlspecialchars($output);
    return "<pre>$source<hr>$output";
  }

}
