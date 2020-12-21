<?php

require_once __DIR__ . '/../vendor/autoload.php';

$f = fopen('php://stdin', 'r');
$input = '';
while ($line = fgets($f)) {
    $input = $input . $line;
}
fclose($f);

$doc = json_decode($input, false);

if (count($argv) < 2) {
    echo "JSON Pointer not specified as first CLI parameter.\n";
    exit(1);
}
$pointer = $argv[1];

try {
    $tokens = JsonJoy\Pointer::parse($pointer);
    $value = JsonJoy\Pointer::get($tokens, $doc);
    $output = json_encode($value, JSON_PRETTY_PRINT);
    fwrite(STDOUT, $output . "\n");
} catch (\Exception $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}
