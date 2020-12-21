<?php
namespace JsonJoy\Patch;

use JsonJoy;

class OpAdd {
    public string $path;
    public $value;

    public array $pathTokens;

    public function __construct(string $path, $value) {
        $this->path = $path;
        $this->value = $value;
        $this->pathTokens = JsonJoy\Pointer::parse($path);
    }

    public function apply($doc) {
        if (JsonJoy\Pointer::isRoot($this->pathTokens)) {
            return JsonJoy\Json::copy($this->value);
        }
        $tokenCount = count($this->pathTokens);
        $parentTokens = array_slice($this->pathTokens, 0, $tokenCount - 1);
        $lastToken = $this->pathTokens[$tokenCount - 1];
        $obj = JsonJoy\Pointer::get($parentTokens, $doc);
        if (is_object($obj)) {
            $obj->$lastToken = JsonJoy\Json::copy($this->value);
            return $doc;
        }
        if (is_array($obj)) {
            $index = JsonJoy\Pointer::convertArrayIndex($lastToken, count($obj));
            $valueCopy = JsonJoy\Json::copy($this->value);
            array_splice($obj, $index, 0, [$valueCopy]);
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
