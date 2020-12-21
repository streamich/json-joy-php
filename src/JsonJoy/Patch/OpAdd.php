<?php
namespace JsonJoy\Patch;

use JsonJoy;

class OpAdd
{
    public static function applyAdd($doc, array $pathTokens, $value)
    {
        if (JsonJoy\Pointer::isRoot($pathTokens)) {
            return JsonJoy\Json::copy($value);
        }
        $tokenCount = count($pathTokens);
        $parentTokens = array_slice($pathTokens, 0, $tokenCount - 1);
        $lastToken = $pathTokens[$tokenCount - 1];
        $obj = JsonJoy\Pointer::get($parentTokens, $doc);
        if (is_object($obj)) {
            $obj->$lastToken = JsonJoy\Json::copy($value);
            return $doc;
        }
        if (is_array($obj)) {
            $index = JsonJoy\Pointer::convertArrayIndex($lastToken, count($obj));
            $valueCopy = JsonJoy\Json::copy($value);
            array_splice($obj, $index, 0, [$valueCopy]);
            if ($tokenCount === 1) {
                return $obj;
            }
            $parentParentTokens = array_slice($pathTokens, 0, $tokenCount - 2);
            $parentLastToken = $pathTokens[$tokenCount - 2];
            $parentObj = JsonJoy\Pointer::get($parentParentTokens, $doc);
            if (is_object($parentObj)) {
                $parentObj->$parentLastToken = $obj;
            } elseif (is_array($parentObj)) {
                $parentObj[$parentLastToken] = $obj;
            }
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

    public function apply($doc)
    {
        return OpAdd::applyAdd($doc, $this->pathTokens, $this->value);
    }
}
