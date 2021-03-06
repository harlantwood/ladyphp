# LadyPHP - type PHP with elegance

Simple (and stupid) preprocessor for PHP.

- adds semicolons at end of lines
- adds curly brackets according to indentation
- adds some syntactic sugar:

  ```
  Lady    │ PHP           Lady    │ PHP
  ────────┼─────────      ────────┼─────────
  var     │ $var          a b     │ $a . $b
  obj.var │ $obj->var     a .. b  │ $a . $b
  obj.f() │ $obj->f()     [1: 2]  │ array(1 => 2)
  Cls.var │ Cls::$var     fn      │ function
  Cls.f() │ Cls::f()      case 1  │ case 1:
  Cls.CON │ Cls::CON      <?      │ <?php
  Cls()   │ new Cls()     <?=     │ <?php echo
  ```

- original line numbers are preserved, it's handy for debugging
- lady herself is written in lady, USE THE SOURCE for reference


## Usage from PHP

```php
require(__DIR__ . '/lady.php');
lady(__DIR__ . '/example.lady');
```


## Usage from command line

```bash
php lady.php -i example.lady -o example.php
php lady.php -c  # converts all .lady files in dir
php lady.php -w  # watches and converts on the fly

options:
  -r  search files recursively
  -e  expanded (human-like) style
```


## Similar projects

- [snowscript](http://github.com/runekaagaard/snowscript)
- [pythophant](http://github.com/bonndan/pythophant)
- [flatwhite](http://github.com/knnktr-labs/flatwhite)
