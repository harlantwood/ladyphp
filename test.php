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
  require_once $php;
  $info[] = "Compiling <b>$lady</b> to <b>$newPhp</b> with <b>$php</b>";
  file_put_contents($newPhp, Lady::parseFile($lady, null, $style));
  $ok[] = 'Compiled';
}
elseif ($action == 'use'){
  if (!is_file($newPhp))
    $error[] = "<b>$newPhp</b> not found";
  elseif (file_get_contents($newPhp) == file_get_contents($php))
    $error[] = "<b>$newPhp</b> is same as <b>$php</b>";
  else {
    rename($php, 'lady-' . time() . '.php');
    rename($newPhp, $php);
    $ok[] = "<b>$newPhp</b> moved to <b>$php<b>";
  }
}
elseif ($action == 'example'){
  if (!is_file($example))
    $error[] = "File <b>$example</b> not found";
  else {
    if (is_file($newPhp) && file_get_contents($newPhp) != file_get_contents($php))
      $compiler = $newPhp;
    else
      $compiler = $php;
    $info[] = "Compiled with <b>$compiler</b>";
    require_once $compiler;
    echo '<h3>LadyPHP (hover code to show PHP)</h3><div class="switch"><pre>' . htmlspecialchars(file_get_contents($example)) . '</pre>';
    echo '<pre>' . htmlspecialchars(Lady::parseFile($example)) . '</pre></div>';
    ob_start();
    Lady::includeFile($example);
    echo '<h3>Output</h3><pre>' . htmlspecialchars(ob_get_clean()) . '</pre>';
  }
}
elseif ($action == 'tokens'){
  if (!is_file($example))
    $error[] = "File <b>$example</b> not found";
  else {
    if (is_file($newPhp) && file_get_contents($newPhp) != file_get_contents($php))
      $compiler = $newPhp;
    else
      $compiler = $php;
    $info[] = "Compiled with <b>$compiler</b>";
    require_once $compiler;
    $tokens = Lady::tokenize(file_get_contents($example));
    echo '<h3>LadyPHP (hover tokens to show info)</h3><pre class="tokenBox">';
    foreach($tokens as $n => $token){
      $token['name'] = token_name($token['type']);
      ksort($token);
      echo htmlspecialchars($token['blank']) . '<span class="token"><span class="tooltip">';
      foreach($token as $key => $value){
        echo htmlspecialchars($key) . ': ' . htmlspecialchars(var_export($value, true)) . '<br>';
      }
      echo '</span>' . htmlspecialchars($token['str']) . '</span>';
    }
  }
}
elseif ($action == 'test'){
  if (!is_file($newPhp))
    $error[] = "File <b>$newPhp</b> not found";
  else {
    require_once $newPhp;
    if (Lady::parseFile($lady, null, $style) == file_get_contents($newPhp))
      $ok[] = "Testing <b>$newPhp</b>: output is same as source code";
    else
      $error[] = "Testing <b>$newPhp</b>: output is not same as source code";
    $ladyContent = file_get_contents($lady);
    $ladyPreserve = Lady::parseFile($lady);
    $ladyCompress = Lady::parseFile($lady, null, Lady::COMPRESS);
    echo '<h3>LadyPHP (' . round(strlen($ladyContent) / 1024, 2) . ' kB)</h3>' .
         '<pre class="small">' . htmlspecialchars($ladyContent) . '</pre>' .
         '<h3>PHP (' . round(strlen($ladyPreserve) / 1024, 2) . ' kB)</h3>' .
         '<pre class="small">' . htmlspecialchars($ladyPreserve) . '</pre>' .
         '<h3>Compressed PHP (' . round(strlen($ladyCompress) / 1024, 2) . ' kB)</h3>' .
         '<pre class="small">' . htmlspecialchars($ladyCompress) . '</pre>';
  }
}
elseif ($action == 'format'){
  require_once 'lady.php';
  $max = [0, 0];
  $sources[] = explode("\n", trim(file_get_contents($example)));
  $sources[] = explode("\n", trim(Lady::parseFile($example)));
  $out = null;
  foreach ($sources as $i => $source){
    foreach ($source as $n => $line){
      if ($i == 1 && $n == 0)
        $line = $sources[1][0] = str_replace(Lady::HEAD, '', $line);
      $max[$i] = max($max[$i], mb_strlen($line));
    }
  }
  foreach ($sources[0] as $n => $line){
    $line = sprintf('    %-' . $max[0] . 's | %-' . $max[1] . "s", $line, $sources[1][$n]);
    $out .= rtrim($line) . "\n";
  }
  ob_start();
  Lady::includeFile($example);
  $result = ob_get_clean();
  $text = "## Example\n\n$out\n#### Output\n\n    $result";
  echo '<pre>' . htmlspecialchars($text) . '</pre>';
}

$content = ob_get_clean();
$menu = explode(' ', 'example tokens compile test use format');

?><!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>LadyPHP - <?php echo $action?></title>
    <style>
      html {font-size: 14px; background-color:#f5f5f5; font-family: 'Droid Sans', 'Tahoma', 'sans'}
      body {max-width: 50em; margin: 0 auto}
      .box {background-color: white; position: relative; border: 1px solid #ccc}
      .page {padding: 3em; margin-top: 1em; background-color: white}
      .menu {position: fixed; width: 48em; background-color: #f5f5f5; padding: .7em 1em; z-index: 1; margin: -1px; border-bottom: 1px solid #ccc}
      pre {background-color: #fbfbfb; border: 1px solid #aaa; padding: .5em 1em; overflow: auto}
      .small {max-height: 20em; overflow: hidden}
      .small:hover {overflow: auto}
      .info, .error, .ok {background-color: #bef; padding: .4em 1em; margin: .3em}
      .error {background-color: #fbb}
      .ok {background-color: #df6}
      *:focus {outline: none}
      a {color: #09f; font-weight:bold; text-decoration: none; margin: .2em; padding: .3em}
      a:hover {background-color: #48f; color: white}
      .selected {color: black}
      hr {display:none}
      .clear {clear: both}
      .switch {position: relative}
      .switch pre:last-child {position: absolute; left: 1px; top: 1px; margin: 0; border: none; display: none}
      .switch:hover pre:last-child {display: block}
      .tokenBox {overflow: visible}
      .token {background-color: #df6; position: relative}
      .token:nth-child(even) {background-color: #bef}
      .tooltip {display: none; position: absolute; z-index: 10; background-color: #ffd; top: 1.5em; left: -1em; font-weight: normal; padding: .2em; border: 1px solid #cc0; font-size: 95%}
      .token:hover {background-color: #ff5}
      .token:hover .tooltip {display: block}
      .token .tooltip:hover {display: none}
    </style>
  </head>
  <body>
    <div class="box">
      <div class="menu">
        <?php foreach($menu as $item){ ?>
          <a href="?<?php echo $item ?>" class="<?php echo ($item == $action) ? 'selected' : null ?>"><?php echo $item ?></a>
        <?php } ?>
      </div>
      <div class="page">
        <?php foreach(['info', 'error', 'ok'] as $type){ ?>
          <?php foreach($$type as $item){ ?>
            <div class="<?php echo $type ?>"><?php echo $item ?></div>
          <?php } ?>
        <?php } ?>
        <?php echo $content ?>
      </div>
    </div>
  </body>
</html>
