<?php
namespace JsonJoy\Patch;

use JsonJoy;

class OpTest {
    public string $path;
    public $value;

    public array $pathTokens;

    public function __construct(string $path, $value) {
        $this->path = $path;
        $this->value = $value;
        $this->pathTokens = JsonJoy\Pointer::parse($path);
    }

    public function apply($doc) {
        $value = JsonJoy\Pointer::get($this->pathTokens, $doc);
        $isEqual = JsonJoy\Json::equal($value, $this->value);
        if (!$isEqual) throw new \Exception('TEST');
        return $doc;
    }
}
