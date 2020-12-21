<?php
namespace JsonJoy\Patch;

use JsonJoy;

class OpRemove {
    public string $path;
    public $value;

    public array $pathTokens;

    public function __construct(string $path) {
        $this->path = $path;
        $this->pathTokens = JsonJoy\Pointer::parse($path);
    }

    public function apply($doc) {
        $tokenCount = count($this->pathTokens);
        if (!$tokenCount) {
            return null;
        }
        $parentTokens = array_slice($this->pathTokens, 0, $tokenCount - 1);
        $lastToken = $this->pathTokens[$tokenCount - 1];
        $obj = JsonJoy\Pointer::get($parentTokens, $doc);
        if (is_object($obj)) {
            if (!property_exists($obj, $lastToken)) {
                throw new \Exception('NOT_FOUND');
            }
            unset($obj->$lastToken);
            return $doc;
        }
        if (is_array($obj)) {
            $index = JsonJoy\Pointer::convertArrayIndex($lastToken, count($obj) - 1);
            array_splice($obj, $index, 1);
            if ($tokenCount === 1) return $obj;
            $parentParentTokens = array_slice($this->pathTokens, 0, $tokenCount - 2);
            $parentLastToken = $this->pathTokens[$tokenCount - 2];
            $parentObj = JsonJoy\Pointer::get($parentParentTokens, $doc);
            if (is_object($parentObj)) {
                $parentObj->$parentLastToken = $obj;
            } else if (is_array($parentObj)) {
                $parentObj[$parentLastToken] = $obj;
            }
            return $doc;
        }
        throw new \Exception('NOT_FOUND');
    }
}
