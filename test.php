<?php

require_once('ndebugger.php');
require_once('lady.php');

function h($s){
  return htmlspecialchars($s, ENT_QUOTES);
}

NDebugger::enable();
Lady::includeFile('tokenizer.lady');
$source = file_get_contents('example.lady');
$out = null;

$tokens = LadyTokenizer::tokenize($source);
foreach($tokens as $n => $token){
  $out .= '<span><b>';
  foreach($token as $key => $value){
    $out .= h($key) . ': ' . h(var_export($value, true)) . '<br>';
  }
  $out .= '</b>' . h($token['blank']) . h($token['str']) . '</span>';
}

?>
<title>LadyPHP Tokenizer</title>
<h1>LadyPHP Tokenizer</h1>
<pre><?= $out ?></pre>
<style>
body{
  margin: 1em auto;
  max-width: 50em;
}
pre{
  padding: .5em;
  border: 1px solid gray;
}
span{
  background-color: #fdf;
  position: relative;
}
b{
  display: none;
  position: absolute;
  z-index: 10;
  background: #aaf;
  top: 1em;
  left: 1em;
}
span:hover b{
  display: block;
}
span b:hover{
  display: none;
}
span:nth-child(odd){
  background-color: #dfd;
}
</style>
