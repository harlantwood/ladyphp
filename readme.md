
LadyPHP - type PHP with elegance
================================

Simple (and stupid) preprocessor for PHP. Main purpose of this is making source code a little more beautiful.

- optional `;` at end of line
- variables doesn't have to be prefixed with `$`, but it must starts with a lowercase letter
- indent style, no need for `{` and `}`
- `.` is converted to `->` or `::`, but not if it's surrounded by spaces
- `:` is converted to `=>`, but only if there isn't space before it
- `fn foo()` is converted to `function foo()`
- `Foo\Bar()` is converted to `new Foo\Bar()`
- optional `:` after `case ...` and `default`
- `<?` and `<?=` are converted to `<?php` and `<?php echo`
- original line numbers are preserved (handy for debugging)
- Lady herself is written in Lady, use the source for reference

## Usage

    <?php
    require_once('lady.php');
    Lady::register('./tmp');
    include 'lady://example.lady';

## Example

    <?                                         | <?php
                                               |
    class Fruit                                | class Fruit{
      var apples = 0                           |   var $apples = 0;
      var numbers = [                          |   var $numbers = [
        1: 'one',                              |     1 => 'one',
        2: 'two',                              |     2 => 'two',
        3: 'three'                             |     3 => 'three'
      ]                                        |   ];
                                               |
      fn addApples(n = 1)                      |   function addApples($n = 1){
        if (n >= 0)                            |     if ($n >= 0){
          this.apples += n                     |       $this->apples += $n;}
        return this                            |     return $this;}
                                               |
      fn countApples()                         |   function countApples(){
        apples = this.apples                   |     $apples = $this->apples;
        out = 'You have '                      |     $out = 'You have ';
        out .= isset(this.numbers[apples])     |     $out .= isset($this->numbers[$apples])
               ? this.numbers[apples] : apples |            ? $this->numbers[$apples] : $apples;
        switch (apples)                        |     switch ($apples){
          case 1                               |       case 1:
            return out . ' apple.'             |         return $out . ' apple.';
          default                              |       default:
            return "$out apples."              |         return "$out apples.";}}}
                                               |
    fruit = Fruit()                            | $fruit = new Fruit();
    fruit.addApples(1)                         | $fruit->addApples(1)
         .addApples(2)                         |      ->addApples(2);
    ?>                                         | ?>
    <p><?=fruit.countApples()?></p>            | <p><?php echo $fruit->countApples()?></p>

#### Output

    <p>You have three apples.</p>

## API

### Flags

- `Lady::COMPRESS` - compress php code
- `Lady::NOCACHE` - always overwrite cache file

### Lady::register()

    Lady::register(string $cacheDir = null)

Register `lady://` stream wrapper.

If `$cacheDir` is set, dir is used as storage for cache files.

### Lady::parse()

    Lady::parse(string $source, int $flags = 0)

Convert LadyPHP from string and return PHP code.

### Lady::parseFile()

    Lady::parseFile(string $file, string $cacheFile = null, int $flags = 0)

If `cacheFile` is null, convert LadyPHP from file and return PHP code.

If `cacheFile` is set, then check if `cacheFile` if newer then `file`.
If it's older, parse `file` and save PHP code to `cacheFile`. Then return content of `cacheFile`.

### Lady::includeFile()

    Lady::includeFile(string $file, string $cacheFile = null, int $flags = 0)

Same as `Lady::parseFile()`, but output will be execute or included.

### Lady::testFile()

    Lady::testFile(string $file, int $flags = 0)

Parse file and show input and output as html.

### Lady::compress()

    Lady::compress(string $php)

Helper function to remove comments and whitespaces from PHP code.

[example]: http://github.com/unu/ladyphp/blob/master/example.md
