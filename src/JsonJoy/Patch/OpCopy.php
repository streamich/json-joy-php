<?php
namespace JsonJoy\Patch;

use JsonJoy;

class OpCopy {
    public string $path;
    public string $from;

    public array $pathTokens;
    public array $fromTokens;

    public function __construct(string $path, string $from) {
        $this->path = $path;
        $this->from = $from;
        $this->pathTokens = JsonJoy\Pointer::parse($path);
        $this->fromTokens = JsonJoy\Pointer::parse($from);
    }

    public function apply($doc) {
        $value = JsonJoy\Pointer::get($this->fromTokens, $doc);
        $valueCopy = JsonJoy\Json::copy($value);
        return JsonJoy\Patch\OpAdd::applyAdd($doc, $this->pathTokens, $valueCopy);
    }
}
