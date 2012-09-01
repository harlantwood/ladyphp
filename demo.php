<?php
require_once 'ndebugger.php';
NDebugger::enable();

# define
define('LADY',    __DIR__ . '/lady.lady');
define('PHP',     __DIR__ . '/lady.php');
define('EXAMPLE', __DIR__ . '/example.lady');
$tpl = new StdClass();

# load lady.php with prefix Old
$oldCode = $isClass = null;
foreach (token_get_all(file_get_contents(PHP)) as $token){
  $token = is_array($token) ? $token : [null, $token];
  $oldCode .=  $isClass ? $token[1] . 'Old' : $token[1];
  $isClass = ($token[0] == T_CLASS);
}
eval('?>' . $oldCode);

# load lady.lady
$newCode = OldLady::parseFile(LADY);
eval('?>' . $newCode);

# save
if (isset($_GET['save'])){
  if ($newCode != file_get_contents(PHP)){
    rename(PHP, 'lady-' . time() . '.php');
    file_put_contents(PHP, $newCode);
  }
  header('Location: ' . basename(__FILE__));
  die();
}

# example
$tpl->example = Lady::testFile(EXAMPLE);
Lady::register();

# test
if ($newCode == file_get_contents(PHP))
  $tpl->msg = 'lady.php is up to date <a href="'. basename(__FILE__) . '">reload</a>';
elseif (Lady::parseFile(LADY) == OldLady::parseFile(LADY))
  $tpl->msg = 'lady.lady creates <b>same</b> output as lady.php <a href="?save">save it</a>';
else
  $tpl->msg = 'lady.lady creates <b>different</b> output than lady.php <a href="?save">save it</a>';

?>
<!DOCTYPE html>
<html>
  <head>
    <title>LadyPHP demo</title>
    <style>
      body{
        width: 30em;
        margin: 2em auto;
        background: #fafafa;
      }
      .block{
        margin: 1em;
        padding: 1em .8em;
        background: white;
        border: 1px solid #aaa;
      }
      a{
        float: right;
        padding: 0 .2em;
        color: #e22;
        text-decoration: none;
      }
      a:hover{
        color: white;
        background: #e22;
      }
    </style>
  </head>
  <body>
    <div class="block"><?php echo $tpl->msg ?></div>
    <div class="block"><?php echo $tpl->example ?></div>
  </body>
</html>
