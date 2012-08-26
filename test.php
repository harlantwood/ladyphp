<?php

require_once('ndebugger.php');
require_once('lady.php');
//Lady::parseFile('lady.lady', 'lady.php');

function h($s){
  return htmlspecialchars($s);
}

# debugger
NDebugger::enable();

# parse example
$example = Lady::testFile('example.lady');
ob_start();
Lady::includeFile('example.lady');
$run = ob_get_clean();

# test tokenizer
$out = null;
$tokens = Lady::tokenize(file_get_contents('example.lady'));
foreach($tokens as $n => $token){
  $out .= h($token['blank']) . '<span><b>';
  foreach($token as $key => $value){
    $out .= h($key) . ': ' . h(var_export($value, true)) . '<br>';
  }
  $out .= '</b>' . h($token['str']) . '</span>';
}

?>
<html>
  <head>
    <title>LadyPHP</title>
  </head>
  <body>
    <h1>LadyPHP</h1>
    <div><?= $example ?></div>
    <h2>Output</h2>
    <div><?= $run ?></div>
    <h2>Tokenizer</h2>
    <pre><?= $out ?></pre>
    <style>
      html{
        background-color: #fafafa;
        font-size: 16px;
        cursor: default;
      }
      body{
        margin: 3em auto;
        max-width: 50em;
        margin-bottom: 20em;
      }
      pre{
        padding: .5em;
        border: 1px solid gray;
        background-color: white;
      }
      span{
        background-color: #fdd;
        position: relative;
      }
      span:nth-child(even){
        background-color: #dfd;
      }
      b{
        display: none;
        position: absolute;
        z-index: 10;
        background-color: #cdf;
        top: 1.5em;
        left: -1em;
        font-weight: normal;
        padding: .2em;
        border: 1px solid #55d;
        font-size: 95%;
      }
      span:hover{
        background-color: #ffa;
      }
      span:hover b{
        display: block;
      }
      span b:hover{
        display: none;
      }
    </style>
  </body>
</html>
