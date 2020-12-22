<?php
namespace JsonJoy\Patch;

use JsonJoy;

class OpReplace
{
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
        $tokenCount = count($this->pathTokens);
        if (!$tokenCount) {
            return JsonJoy\Json::copy($this->value);
        }
        $parentTokens = array_slice($this->pathTokens, 0, $tokenCount - 1);
        $lastToken = $this->pathTokens[$tokenCount - 1];
        $obj = &JsonJoy\Pointer::get($parentTokens, $doc);
        if (is_object($obj)) {
            if (!property_exists($obj, $lastToken)) {
                throw new \Exception('NOT_FOUND');
            }
            $valueCopy = JsonJoy\Json::copy($this->value);
            $obj->$lastToken = $valueCopy;
            return $doc;
        }
        if (is_array($obj)) {
            $index = JsonJoy\Pointer::convertArrayIndex($lastToken, -1);
            if ($index >= count($obj)) {
                throw new \Exception('INVALID_INDEX');
            }
            $valueCopy = JsonJoy\Json::copy($this->value);
            $obj[$index] = $valueCopy;
            return $doc;
        }
        throw new \Exception('NOT_FOUND');
    }
}
