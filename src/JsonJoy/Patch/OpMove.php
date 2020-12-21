<?php
namespace JsonJoy\Patch;

use JsonJoy;

class OpMove {
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
        $remove = JsonJoy\Patch\OpRemove::applyRemove($doc, $this->fromTokens);
        return JsonJoy\Patch\OpAdd::applyAdd($doc, $this->pathTokens, $remove[1]);
    }
}
