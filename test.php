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
NDebugger::barDump($action, 'action');

ob_start();

if ($action == 'compile'){
  require($php);
  $info[] = "Compiling <b>$lady</b> to <b>$newPhp</b> with <b>$php</b>";
  file_put_contents($newPhp, Lady::parseFile($lady, $style));
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
    print '<h3>LadyPHP</h3><pre>' . htmlspecialchars(file_get_contents($example)) . '</pre>';
    print '<h3>PHP</h3><pre>' . htmlspecialchars(Lady::parseFile($example, Lady::PRESERVE, true)) . '</pre>';
    print '<h3>Output</h3><pre>' . htmlspecialchars(Lady::includeFile($example)) . '</pre>';
  }
}
elseif ($action == 'test'){
  if (!is_file($newPhp))
    $error[] = "File <b>$newPhp</b> not found";
  else {
    require($newPhp);
    if (Lady::parseFile($lady, $style) == file_get_contents($newPhp))
      $ok[] = "Testing <b>$newPhp</b>: output is same as source code";
    else
      $error[] = "Testing <b>$newPhp</b>: output is not same as source code";
    print '<h3>LadyPHP</h3><pre>' . htmlspecialchars(file_get_contents($lady)) . '</pre>';
    print '<h3>Preserve</h3><pre>' . htmlspecialchars(Lady::parseFile($lady, Lady::PRESERVE)) . '</pre>';
    print '<h3>Strip</h3><pre>' . htmlspecialchars(Lady::parseFile($lady, Lady::STRIP)) . '</pre>';
    print '<h3>Compress</h3><pre>' . htmlspecialchars(Lady::parseFile($lady, Lady::COMPRESS)) . '</pre>';

  }
}

$content = ob_get_clean();
$menu = explode(' ', 'example test compile use');

?>
<head><title>LadyPHP: <?=$action?></title></head>
<body>


<div class="box">
  <div class="menu">
    <?foreach($menu as $item):?>
      <a href="?<?=$item?>"><?=$item?></a>
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
  body {max-width: 48em; margin: 0 auto; font-size: 14px; background-color:#eee}
  .box { background-color: white; position: relative; border: 1px solid #ccc}
  .page { padding: 3em; background-color: white}
  .menu {position: fixed; width: 46em; background-color: #222;padding: .7em 1em; z-index: 1; margin: -1px}
  pre {max-height: 25em; background-color: #fbfbfb; border: 1px solid #aaa; overflow: hidden; padding: .5em 1em }
  pre:hover {background-color: #f8f8f8; overflow: auto}
  .info, .error, .ok {background-color: #def; padding: .4em 1em; margin: .3em}
  .error {background-color: #fdd}
  .ok {background-color: #dfd}
  a {color: #cdf; font-weight:bold; text-decoration: none; margin: .5em}
  a:hover {color: #fff; text-decoration: underline}
  hr {display:none}
  .clear {clear: both}
</style>
