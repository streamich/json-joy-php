<?php
namespace JsonJoy\Patch;

use JsonJoy;

class OpRemove
{
    public static function applyRemove($doc, array $pathTokens)
    {
        $tokenCount = count($pathTokens);
        if (!$tokenCount) {
            return [null, $doc];
        }
        $parentTokens = array_slice($pathTokens, 0, $tokenCount - 1);
        $lastToken = $pathTokens[$tokenCount - 1];
        $obj = &JsonJoy\Pointer::get($parentTokens, $doc);
        if (is_object($obj)) {
            if (!property_exists($obj, $lastToken)) {
                throw new \Exception('NOT_FOUND');
            }
            $value = $obj->$lastToken;
            unset($obj->$lastToken);
            return [$doc, $value];
        }
        if (is_array($obj)) {
            $index = JsonJoy\Pointer::convertArrayIndex($lastToken, -1);
            if (count($obj) <= $index) {
                throw new \Exception('NOT_FOUND');
            }
            $value = $obj[$index];
            array_splice($obj, $index, 1);
            return [$doc, $value];
        }
        throw new \Exception('NOT_FOUND');
    }

    public string $path;
    public $value;

    public array $pathTokens;

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->pathTokens = JsonJoy\Pointer::parse($path);
    }

    public function apply($doc)
    {
        $tuple = OpRemove::applyRemove($doc, $this->pathTokens);
        return $tuple[0];
    }
}
