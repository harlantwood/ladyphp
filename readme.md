# LadyPHP - type PHP with elegance

- semicolons are optional `;`
- variables are not prefixed with `$`
- indent style - no need for `{` and `}`

## Example

#### LadyPHP

    <?
    # variable
    apples = 3

    # indent style
    if (apples > 1)
      print 'Take one!'
      apples--
    else
      print 'Don\'t touch my apple!'
      print count(apples)

#### PHP

    <?
    $apples = 3;

    if ($apples > 1) {
      print 'Take one!';
      $apples--;
    } else {
      print 'Don\'t touch my apple!';
      print $count($apples);
    } 

## Usage

    <?php
    require_once('lady.php');
    $source = file_get_contents('test.lady');
    $php = lady($source);

## Planned

- Lists: `[]` and `{}` 

#### LadyPHP

    # array
    list = ['one', 'two']
    assoc = {'key': 'two'}

#### PHP

    # array
    $list = array('one', 'two');
    $assoc = array('key' => 'two');

