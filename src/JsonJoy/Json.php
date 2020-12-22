<?php

namespace JsonJoy;

/**
 * Implements utility methods on JSON for json-joy. json-joy uses associative
 * arrays to represent JSON arrays, and std objects to represent JSON objects.
 * Provide `true` as second argument of `json_decode`.
 *
 * ```php
 * $doc = json_decode($json, false);
 * ```
 */
class Json
{
    public static function copy($doc)
    {
        if (is_array($doc)) {
            $arr = [];
            foreach ($doc as $value) {
                $arr[] = Json::copy($value);
            }
            return $arr;
        }
        if (is_object($doc)) {
            $obj = (object) [];
            foreach ($doc as $key => $value) {
                $obj->$key = Json::copy($value);
            }
            return $obj;
        }
        return $doc;
    }

    public static function equal($a, $b): bool
    {
        if (is_array($a)) {
            if (!is_array($b)) {
                return false;
            }
            if (count($a) !== count($b)) {
                return false;
            }
            foreach ($a as $i => $aa) {
                if (!Json::equal($aa, $b[$i])) {
                    return false;
                }
            }
            return true;
        }
        if (is_object($a)) {
            if (!is_object($b)) {
                return false;
            }
            foreach ($a as $key => $value) {
                if (!Json::equal($value, $b->$key)) {
                    return false;
                }
            }
            return true;
        }
        return $a === $b;
    }
}
