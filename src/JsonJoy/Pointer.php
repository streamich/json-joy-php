<?php
namespace JsonJoy;

use InvalidArgumentException;

class Pointer
{
    public static function create(string $pointer): Pointer
    {
        if (strlen($pointer) == 0) {
            return new Pointer([]);
        }
        if ($pointer[0] != '/') {
            throw new InvalidArgumentException('POINTER_INVALID');
        }
        $referenceTokens = explode('/', substr($pointer, 1));
        return new Pointer($referenceTokens);
    }

    public static function escapeReferenceToken(string $token): string {
      return str_replace(['~', '/'], ['~0', '~1'], $token);
    }

    public static function unescapeReferenceToken(string $token): string {
      return str_replace(['~0', '~1'], ['~', '/'], $token);
    }

    public array $referenceTokens;

    public function __construct(array $referenceTokens)
    {
        $this->referenceTokens = $referenceTokens;
    }
}
