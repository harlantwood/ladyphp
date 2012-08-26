<?php

include_once('./ndebugger.php');
NDebugger::enable();

$lady = 'lady.lady';
$php = 'lady.php';
$newPhp = 'lady_new.php';
$example = 'example.lady';
$style = 0;
$info = array();
$error = array();
$ok = array();

$action = 'example';
foreach ($_GET as $k => $v){
  $action = $k;
  break;
}

ob_start();

if ($action == 'compile'){
  require($php);
  $info[] = "Compiling <b>$lady</b> to <b>$newPhp</b> with <b>$php</b>";
  file_put_contents($newPhp, Lady::parseFile($lady, null, $style));
  $ok[] = 'Compiled';
  //print '<meta http-equiv="refresh" content="1;url=?test">';
}
elseif ($action == 'use'){
  if (!is_file($newPhp))
    $error[] =  "<b>$newPhp</b> not found";
  elseif (file_get_contents($newPhp) == file_get_contents($php))
    $error[] =  "<b>$newPhp</b> is same as <b>$php</b>";
  else {
    rename($php, 'lady-' . time() . '.php');
    rename($newPhp, $php);
    $ok[] =  "<b>$newPhp</b> moved to <b>$php<b>";
  }
}
elseif ($action == 'example'){
  if (!is_file($example))
    $error[] = "File <b>$example</b> not found";
  else {
    require($php);
    # tokenizer
    $tokens = Lady::tokenize(file_get_contents($example));
    print '<h3>LadyPHP</h3><pre>';
    foreach($tokens as $n => $token){
      print htmlspecialchars($token['blank']) . '<span class="token"><div class="tooltip">';
      foreach($token as $key => $value){
        print htmlspecialchars($key) . ': ' . htmlspecialchars(var_export($value, true)) . '<br>';
      }
      print '</div>' . htmlspecialchars($token['str']) . '</span>';
    }
    # php
    print '</pre><h3>PHP</h3><pre class="small">' . htmlspecialchars(Lady::parseFile($example)) . '</pre>';
    ob_start();
    Lady::includeFile($example);
    print '<h3>Output</h3><pre>' . htmlspecialchars(ob_get_clean()) . '</pre>';
  }
}
elseif ($action == 'test'){
  if (!is_file($newPhp))
    $error[] = "File <b>$newPhp</b> not found";
  else {
    require($newPhp);
    if (Lady::parseFile($lady, null, $style) == file_get_contents($newPhp))
      $ok[] = "Testing <b>$newPhp</b>: output is same as source code";
    else
      $error[] = "Testing <b>$newPhp</b>: output is not same as source code";
    $ladyContent = file_get_contents($lady);
    $ladyPreserve = Lady::parseFile($lady);
    $ladyCompress = Lady::parseFile($lady, null, Lady::COMPRESS);
    print '<h3>LadyPHP (' . round(strlen($ladyContent) / 1024, 2) . ' kB)</h3>' .
          '<pre class="small">' . htmlspecialchars($ladyContent) . '</pre>' .
          '<h3>PHP (' . round(strlen($ladyPreserve) / 1024, 2) . ' kB)</h3>' .
          '<pre class="small">' . htmlspecialchars($ladyPreserve) . '</pre>' .
          '<h3>Compressed PHP (' . round(strlen($ladyCompress) / 1024, 2) . ' kB)</h3>' .
          '<pre class="small">' . htmlspecialchars($ladyCompress) . '</pre>';

  }
}

$content = ob_get_clean();
$menu = explode(' ', 'example compile test use');

?>
<head><title>LadyPHP: <?=$action?></title></head>
<body>


<div class="bar"></div>
<div class="box">
  <div class="menu">
    <?foreach($menu as $item):?>
      <a href="?<?=$item?>" class="<?= ($item == $action) ? 'selected' : null ?>"><?=$item?></a>
    <?endforeach?>
  </div>

  <div class="page">
  <?foreach($info as $item):?>
    <div class="info"><?=$item?></div>
  <?endforeach?>

  <?foreach($error as $item):?>
    <div class="error"><?=$item?></div>
  <?endforeach?>

  <?foreach($ok as $item):?>
    <div class="ok"><?=$item?></div>
  <?endforeach?>

  <?=$content?>
  </div>
</div>

<style>
  body {max-width: 48em; margin: 0 auto; font-size: 14px; background-color:#fafafa}
  .box {background-color: white; position: relative; border: 1px solid #ccc}
  .page {min-height: 5em; padding: 3em; margin-top: 1em; background-color: white}
  .bar {position: fixed; background-color: #222; width: 200%; height: 2.6em; margin-left: -50%}
  .menu {position: fixed; width: 46em; background-color: #222; padding: .7em 1em; z-index: 1; margin: -1px}
  .selected {color: white}
  pre {background-color: #fbfbfb; border: 1px solid #aaa; padding: .5em 1em}
  .small {max-height: 20em; overflow: hidden}
  .small:hover {background-color: #f8f8f8; overflow: auto}
  .info, .error, .ok {background-color: #def; padding: .4em 1em; margin: .3em}
  .error {background-color: #fdd}
  .ok {background-color: #dfd}
  a {color: #cdf; font-weight:bold; text-decoration: none; margin: .5em}
  a:hover {color: #fff; text-decoration: underline}
  hr {display:none}
  .clear {clear: both}
  pre:hover .token {background-color: #fdd; position: relative}
  pre:hover .token:nth-child(even) {background-color: #dfd}
  .tooltip {display: none; position: absolute; z-index: 10; background-color: #cdf; top: 1.5em; left: -1em; font-weight: normal; padding: .2em; border: 1px solid #55d; font-size: 95%}
  .token:hover {background-color: #ffa}
  .token:hover .tooltip {display: block}
  .token .tooltip:hover {display: none}
  .token {-webkit-transition: background .5s; -moz-transition: background .5s; transition: background .5s}
</style>
