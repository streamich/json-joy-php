<?php

namespace JsonJoy;

/**
 * Implementation of JSON Pointer RFC6901 (https://tools.ietf.org/html/rfc6901).
 */
class Pointer
{
    public static function parse(string $pointer): array
    {
        if (strlen($pointer) == 0) {
            return [];
        }
        if ($pointer[0] != '/') {
            throw new \InvalidArgumentException('POINTER_INVALID');
        }
        $referenceTokens = explode('/', substr($pointer, 1));
        $referenceTokens = array_map('JsonJoy\Pointer::unescapeReferenceToken', $referenceTokens);
        return $referenceTokens;
    }

    public static function create(string $pointer): Pointer
    {
        $referenceTokens = Pointer::parse($pointer);
        return new Pointer($referenceTokens);
    }

    public static function escapeReferenceToken(string $token): string
    {
        return str_replace(['~', '/'], ['~0', '~1'], $token);
    }

    public static function unescapeReferenceToken(string $token): string
    {
        return str_replace(['~1', '~0'], ['/', '~'], $token);
    }

    public static function isRoot(array $tokens): bool
    {
        return count($tokens) === 0;
    }

    public static function format(array $referenceTokens): string
    {
        if (!count($referenceTokens)) {
            return '';
        }
        $referenceTokens = array_map('JsonJoy\Pointer::escapeReferenceToken', $referenceTokens);
        return '/' . implode('/', $referenceTokens);
    }

    public static function convertArrayIndex(string $str, int $maxValue): int
    {
        $index = (int)$str;
        if (((string)$index) !== $str) {
            throw new \Exception('INVALID_INDEX');
        }
        if ($index < 0) {
            throw new \Exception('INVALID_INDEX');
        }
        if ($maxValue > -1) {
            if ($index > $maxValue) {
                throw new \Exception('INVALID_INDEX');
            }
        }
        return $index;
    }

    public static function &get(array &$referenceTokens, &$doc)
    {
        foreach ($referenceTokens as $token) {
            if (is_object($doc)) {
                if (!property_exists($doc, $token)) {
                    throw new \Exception('NOT_FOUND');
                }
                $doc = &$doc->$token;
                continue;
            }
            if (is_array($doc)) {
                $index = Pointer::convertArrayIndex($token, count($doc) - 1);
                if (!array_key_exists($index, $doc)) {
                    throw new \Exception('NOT_FOUND');
                }
                $doc = &$doc[$token];
                continue;
            }
            throw new \Exception('NOT_FOUND');
        }
        return $doc;
    }
}
