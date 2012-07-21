# LadyPHP - type PHP with elegance

Simple (and stupid) preprocessor for PHP. Main purpose of this is making a source code a little more beautiful.

- optional `;` at end of line
- variables are not prefixed with `$`
- indent style (2 spaces), no need for `{` and `}`
- original line numbers are preserved (handy for debugging)
- Lady herself is written in Lady, use the source for reference

## Example

#### LadyPHP

    <?

    apples = 3

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

## Bugs

- multiline comments doesn't works
- semicolon is added at the end of line even if there is a comment
