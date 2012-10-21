<?php
# format example for readme.md
# load it in vim with:
#  :r !php format.php

require_once __DIR__ . '/../lady.php';
Lady::register();

echo "## Example\n\n";
$max = [0, 0];
$sources[] = explode("\n", trim(file_get_contents(__DIR__ . '/example.lady')));
$sources[] = explode("\n", trim(Lady::parseFile(__DIR__ . '/example.lady')));
foreach ($sources as $i => $source)
  foreach ($source as $n => $line)
    $max[$i] = max($max[$i], mb_strlen($line));
foreach ($sources[0] as $n => $line)
  echo rtrim(sprintf('    %-' . $max[0] . 's         | %-' . $max[1] . "s", $line, $sources[1][$n])) . "\n";

echo "\n#### Output\n\n    ";
require 'lady://' . __DIR__ . '/example.lady';
