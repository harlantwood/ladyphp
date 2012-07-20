<?php

include_once('ndebug.php');

$lady = 'lady.lady';
$php = 'lady.php';
$newPhp = 'lady_new.php';
$info = array();
$error = array();
$ok = array();

$action = null;
foreach ($_GET as $k => $v){
  $action = $k;
  break;
}

ob_start();

if ($action == 'compile'){
  require($php);
  $info[] = "Compiling <b>$lady</b> to <b>$newPhp</b>";
  file_put_contents($newPhp, Lady::parseFile($lady));
  $ok[] = 'Compiled :)';
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
else {
  if (is_file($newPhp)){
    require($newPhp);
    if (Lady::parseFile($lady) == file_get_contents($newPhp))
      $ok[] = "Testing <b>$newPhp</b>: output is same as source code";
    else
      $error[] = "Testing <b>$newPhp</b>: output is $not same as source code";
    print Lady::test($lady, Lady::PRESERVE);
  } else {
    $error[] = "File <b>$newPhp</b> not found";
  }
}

$content = ob_get_clean();
$menu = explode(' ', 'test compile use');
?>

<div class="menu">
  <?foreach($menu as $item):?>
    <a href="?<?=$item?>"><?=$item?></a>
  <?endforeach?>
</div>

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

<style>
  pre {height: 88%; overflow: auto; width: 50%; float: left}
  .info, .error, .ok {background-color: #def; padding: .4em 1em; margin: .3em}
  .error {background-color: #fdd}
  .ok {background-color: #dfd}
  .menu {background-color: #222;padding: .7em 1em; text-align: right; position: absolute; right: 0; top: 0}
  a {color: #cdf; font-weight:bold; text-decoration: none; margin: .5em}
  a:hover {color: #fff; text-decoration: underline}
  hr {display:none}
</style>
