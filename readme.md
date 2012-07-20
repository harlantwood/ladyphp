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
    if (apples > 5)
      print 'Take one apple.'
      apples--
    else
      print "Don't touch my apples! I have only "
      print count(apples)

#### PHP

    <?php
    $apples = 3;
    if ($apples > 5){
      print 'Take one apple.';
      $apples--;}
    else{
      print "Don't touch my apples! I have only ";
      print count($apples);}


## Usage

    <?php
    require_once('lady.php');
    $php = Lady::parseFile('test.lady');

