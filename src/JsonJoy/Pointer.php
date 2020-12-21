<?php
namespace JsonJoy;

use InvalidArgumentException;

class Pointer {
  static public function create(string $pointer): Pointer {
    if (strlen($pointer) == 0) return new Pointer([]);
    if ($pointer[0] != '/') throw new InvalidArgumentException('POINTER_INVALID');
    $referenceTokens = explode('/', substr($pointer, 1));
    return new Pointer($referenceTokens);
  }

  public array $referenceTokens;

  public function __construct(array $referenceTokens) {
    $this->referenceTokens = $referenceTokens;
  }
}
