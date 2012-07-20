<?php
class Lady{
  const REGEX_CODE = '/.*[^(<\?|<\?php)\{\} ].*/';
  const REGEX_COMMENT = '/^ *(#|\/\/)/';
  const REGEX_EMPTY = '/^ *$/';
  const REGEX_END = '/(;|&|\|)$/';
  const REGEX_CONTINUE = '/^[&\|]/';
  const REGEX_END_OPENING = '/(^[^(]*|.*\))$/';
  const REGEX_SMALL = '/^[a-z].*/';
  const REGEX_NOVAR = '/^(false|true|self|null)$/';
  static public function parse($source, $debug = false){
    $code = $dump = $noVar = null;
    $tokens = token_get_all($source);
    foreach ($tokens as $n => $token){
      if (!is_array($token)){
        $tokens[$n][0] = $token;
        $tokens[$n][1] = $token;}
      else{
        $tokens[$n][0] = token_name($token[0]);
        $tokens[$n][1] = $token[1];}}
    foreach ($tokens as $n => $token){
      list($name, $string) = $token;
      if ($name == 'T_STRING'
      && $tokens[$n + 1][1] != '('
      && preg_match(self::REGEX_SMALL, $string)
      && !preg_match(self::REGEX_NOVAR, $string)){
        $code .= '$' . $string;}
      elseif ($name == 'T_COMMENT'){
        $code .= "\n";}
      else{
        $code .= $string;}
      $dump .= $n . '. ' . $name . ': ' . $string . "\n";}
    $lines = explode("\n", $code);
    $indent = 0;
    foreach ($lines as $n => $line){
      if (preg_match(self::REGEX_CODE, $line)){
        $shrinkedLines[] = str_repeat('    ', $indent) . $line;}}
    $shrinkedLines[] = 'true;';
    $lines = $shrinkedLines;
    foreach ($lines as $n => $line){
      if (preg_match(self::REGEX_CODE, $line)
      && !preg_match(self::REGEX_CONTINUE, trim($line))){
        $indent_before = $indent;
        $indent = (strlen($line) - strlen(ltrim($line))) / 2;
        $jump = $indent - $indent_before;}
      else{
        $jump = 0;}
      $line = trim($line);
      if ($jump <= 0
      && $n > 0
      && !preg_match(self::REGEX_CONTINUE, $line)
      && preg_match(self::REGEX_CODE, $lines[$n - 1])
      && !preg_match(self::REGEX_COMMENT, $lines[$n - 1])
      && !preg_match(self::REGEX_END, $lines[$n - 1])
      && !preg_match(self::REGEX_EMPTY, $lines[$n - 1])){
        $lines[$n - 1] .= ';';}
      if ($jump > 0 && $n > 0
      && preg_match(self::REGEX_END_OPENING, $lines[$n - 1])){
        $lines[$n - 1] .= '{';}
      if ($jump < 0){
        $lines[$n - 1] .= str_repeat('}', -$jump);}
      $lines[$n] = str_repeat('  ', $indent) . $line;}
    $code = "<?php\n" . implode("\n", $lines);
    return $debug ? $dump : $code;}
  static public function parseFile($file, $debug = false){
    return self::parse(file_get_contents($file), $debug);}
  static public function includeFile($file){
    ob_start();
    eval('?>' . self::parseFile($file));
    return ob_get_clean();}
  static public function test($file, $debug = false){
    $source = file_get_contents($file);
    $output = self::parseFile($file, $debug);
    $source = htmlspecialchars($source);
    $output = htmlspecialchars($output);
    return '<pre>' . $source . '</pre><hr><pre>' . $output . '</pre>';}}
true;