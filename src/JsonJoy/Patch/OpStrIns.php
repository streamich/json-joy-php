<?php

namespace JsonJoy\Patch;

use JsonJoy;

function str_ins($str, int $pos, string $ins): string {
    if (!is_string($str)) {
        throw new \Exception('NOT_A_STRING');
    }
    $len = strlen($str);
    if ($pos > $len) $pos = $len;
    return substr($str, 0, $pos) . $ins . substr($str, $pos);
}

class OpStrIns
{
    public string $path;
    public int $pos;
    public string $str;

    public array $pathTokens;

    public function __construct(string $path, $pos, $str)
    {
        $this->path = $path;
        $this->pos = $pos;
        $this->str = $str;
        $this->pathTokens = JsonJoy\Pointer::parse($path);
    }

    private function str($str) {
        return str_ins($str, $this->pos, $this->str);
    }

    public function &apply(&$doc)
    {
        $tokenCount = count($this->pathTokens);
        if (!$tokenCount) {
            return $this->str($doc);
        }
        $parentTokens = array_slice($this->pathTokens, 0, $tokenCount - 1);
        $lastToken = $this->pathTokens[$tokenCount - 1];
        $obj = &JsonJoy\Pointer::get($parentTokens, $doc);
        if (is_object($obj)) {
            $obj->$lastToken = $this->str($obj->$lastToken);
            return $doc;
        }
        if (is_array($obj)) {
            $index = JsonJoy\Pointer::convertArrayIndex($lastToken, count($obj) - 1);
            $obj[$index] = $this->str($obj[$index]);
            return $doc;
        }
        throw new \Exception('NOT_FOUND');
    }
}
