<?php

namespace JsonJoy\Patch;

use JsonJoy;

class OpAdd
{
    public static function &applyAdd(&$doc, array $pathTokens, $value)
    {
        if (JsonJoy\Pointer::isRoot($pathTokens)) {
            $doc = JsonJoy\Json::copy($value);
            return $doc;
        }
        $tokenCount = count($pathTokens);
        $parentTokens = array_slice($pathTokens, 0, $tokenCount - 1);
        $lastToken = $pathTokens[$tokenCount - 1];
        $obj = &JsonJoy\Pointer::get($parentTokens, $doc);
        if (is_object($obj)) {
            $obj->$lastToken = JsonJoy\Json::copy($value);
            return $doc;
        }
        if (is_array($obj)) {
            $index = 0;
            if ($lastToken === '-') {
                $index = count($obj);
            } else {
                $index = JsonJoy\Pointer::convertArrayIndex($lastToken, count($obj));
            }
            $valueCopy = JsonJoy\Json::copy($value);
            array_splice($obj, $index, 0, [$valueCopy]);
            return $doc;
        }
        throw new \Exception('NOT_FOUND');
    }

    public string $path;
    public $value;

    public array $pathTokens;

    public function __construct(string $path, $value)
    {
        $this->path = $path;
        $this->value = $value;
        $this->pathTokens = JsonJoy\Pointer::parse($path);
    }

    public function &apply(&$doc)
    {
        return OpAdd::applyAdd($doc, $this->pathTokens, $this->value);
    }
}
