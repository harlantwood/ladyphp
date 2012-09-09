<?php
require_once 'sdebug.php';
sdebug();

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
  $tpl->msg = 'lady.lady creates <b>same</b> output as lady.php <a href="?save">save</a>';
else
  $tpl->msg = 'lady.lady creates <b>different</b> output than lady.php <a href="?save">save anyway</a>';

# tokens
$tpl->tokens = null;
foreach(Lady::tokenize(file_get_contents(EXAMPLE)) as $n => $token){
  $tpl->tokens .= htmlspecialchars($token['blank']) . '<span class="token"><span class="tooltip">';
  foreach(array_merge(['name' => token_name($token['type'])], $token) as $key => $value)
    $tpl->tokens .= htmlspecialchars($key) . ': ' . htmlspecialchars(var_export($value, true)) . '<br>';
  $tpl->tokens .= '</span>' . htmlspecialchars($token['str']) . '</span>';
}

?>
<!DOCTYPE html>
<html>
  <head>
    <title>LadyPHP demo</title>
    <style>
      body{
        width: 35em;
        margin: 2em auto;
        margin-bottom: 10em;
        background: #fafafa;
        font-family: 'Droid Sans', 'Tahoma', 'Arial', sans;
        font-size: 16px;
      }
      .block{
        margin: 1em 0;
        padding: 1em .8em;
        background: white;
        border: 1px solid #aaa;
        border-radius: 4px;
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
      h1{
        color: #444;
      }
      h4{
        margin: 0;
      }
      .tokenBox {
        background-color #ddd;
        border: 1px solid #aaa;
        font-size: 14px;
        overflow: visible;
        padding: .5em;
      }
      .token {
        background-color: #df6;
        position: relative;
      }
      .token:nth-child(even) {
        background-color: #bef;
      }
      .tooltip {
        display: none;
        position: absolute;
        z-index: 10;
        background-color: #ffd;
        top: 1.5em;
        left: -1em;
        font-weight: normal;
        padding: .2em;
        border: 1px solid #cc0;
        font-size: 95%;
      }
      .token:hover {
        background-color: #ff5;
      }
      .token:hover .tooltip {
        display: block;
      }
      .token .tooltip:hover {
        display: none;
      }
    </style>
  </head>
  <body>
    <h1>LadyPHP demo</h1>
    <div class="block"><?php echo $tpl->msg ?></div>
    <div class="block"><?php echo $tpl->example ?></div>
    <div class="block"><p><b>tokens</b></p><pre class="tokenBox"><?php echo $tpl->tokens ?></pre></div>
  </body>
</html>
