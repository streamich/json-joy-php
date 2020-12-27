<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$f = fopen('php://stdin', 'r');
$input = '';
while ($line = fgets($f)) {
    $input = $input . $line;
}
fclose($f);

$doc = json_decode($input, false);

if (count($argv) < 2) {
    echo "JSON Patch not specified as first CLI parameter.\n";
    exit(1);
}
$patch = $argv[1];

try {
    $operations = json_decode($patch, false);
    $ops = JsonJoy\Patch::createOps($operations);
    $result = JsonJoy\Patch::apply($doc, $ops);
    $output = json_encode($result, JSON_PRETTY_PRINT);
    fwrite(STDOUT, $output . "\n");
} catch (\Exception $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}
