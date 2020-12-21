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
        $referenceTokens = array_map('JsonJoy\Pointer::unescapeReferenceToken', $referenceTokens);
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

    public function toString(): string
    {
      if (!count($this->referenceTokens)) return '';
      $referenceTokens = array_map('JsonJoy\Pointer::escapeReferenceToken', $this->referenceTokens);
      return '/' . implode('/', $referenceTokens);
    }

    public function __toString(): string
    {
      return $this->toString();
    }
}
