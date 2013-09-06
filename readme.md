# LadyPHP plugin (prototype)

This will be plugin for text editors (at least Vim), that lets you use nicer syntax for writing PHP scripts.

It should work like this:

- when you open PHP file, it's converted to LadyPHP with nice syntax
- when you save this file, it's converted back to pure PHP

## LadyPHP Syntax

  ```
  Lady      │ PHP
  ──────────┼─────────
  var       │ $var
  obj.var   │ $obj->var
  obj.f()   │ $obj->f()
  def       │ function
  if $cond  │ if ($cond){
  end       │ }
  <?        │ <?php
  [1: 2]    │ [1 => 2]
  ```

- semicolons at end of lines are optional
- you can't use inline html

## API

```bash
cat example.lady | php lady.php > example.php
```
