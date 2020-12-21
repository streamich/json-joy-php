<?php
namespace JsonJoy\Patch;

use JsonJoy;

class OpRemove {
    public static function applyRemove($doc, array $pathTokens) {
        $tokenCount = count($pathTokens);
        if (!$tokenCount) {
            return [null, $doc];
        }
        $parentTokens = array_slice($pathTokens, 0, $tokenCount - 1);
        $lastToken = $pathTokens[$tokenCount - 1];
        $obj = JsonJoy\Pointer::get($parentTokens, $doc);
        if (is_object($obj)) {
            if (!property_exists($obj, $lastToken)) {
                throw new \Exception('NOT_FOUND');
            }
            $value = $obj->$lastToken;
            unset($obj->$lastToken);
            return [$doc, $value];
        }
        if (is_array($obj)) {
            $index = JsonJoy\Pointer::convertArrayIndex($lastToken, count($obj) - 1);
            $value = $obj[$index];
            array_splice($obj, $index, 1);
            if ($tokenCount === 1) return [$obj, $value];
            $parentParentTokens = array_slice($pathTokens, 0, $tokenCount - 2);
            $parentLastToken = $pathTokens[$tokenCount - 2];
            $parentObj = JsonJoy\Pointer::get($parentParentTokens, $doc);
            if (is_object($parentObj)) {
                $parentObj->$parentLastToken = $obj;
            } else if (is_array($parentObj)) {
                $parentObj[$parentLastToken] = $obj;
            }
            return [$doc, $value];
        }
        throw new \Exception('NOT_FOUND');
    }

    public string $path;
    public $value;

    public array $pathTokens;

    public function __construct(string $path) {
        $this->path = $path;
        $this->pathTokens = JsonJoy\Pointer::parse($path);
    }

    public function apply($doc) {
        $tuple = OpRemove::applyRemove($doc, $this->pathTokens);
        return $tuple[0];
    }
}
