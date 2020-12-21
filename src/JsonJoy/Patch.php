<?php
namespace JsonJoy;

class Patch
{
    public static function createOps(array $operations): array
    {
        if (!is_array($operations)) {
            throw new \Exception('PATCH_INVALID');
        }
        $ops = [];
        foreach ($operations as $operation) {
            $ops[] = Patch::createOp($operation);
        }
        return $ops;
    }

    public static function createOp(object $operation): object
    {
        if (!is_object($operation)) {
            throw new \Exception('OP_INVALID');
        }
        if (!property_exists($operation, 'op')) {
            throw new \Exception('OP_INVALID');
        }
        $mnemonic = $operation->op;
        if (!is_string($mnemonic)) {
            throw new \Exception('OP_INVALID');
        }
        switch ($mnemonic) {
            case 'add':
                return Patch::createAddOp($operation);
            case 'replace':
                return Patch::createReplaceOp($operation);
            case 'remove':
                return Patch::createRemoveOp($operation);
            case 'move':
                return Patch::createMoveOp($operation);
            case 'copy':
                return Patch::createCopyOp($operation);
            case 'test':
                return Patch::createTestOp($operation);
        }
        throw new \Exception('OP_UNKNOWN');
    }

    private static function createAddOp($operation): Patch\OpAdd
    {
        if (!property_exists($operation, 'path')) {
            throw new \Exception('OP_PATH_INVALID');
        }
        $path = $operation->path;
        if (!is_string($path)) {
            throw new \Exception('OP_PATH_INVALID');
        }
        if (!property_exists($operation, 'value')) {
            throw new \Exception('OP_VALUE_INVALID');
        }
        $value = $operation->value;
        return new Patch\OpAdd($path, $value);
    }

    private static function createReplaceOp($operation): Patch\OpReplace
    {
        if (!property_exists($operation, 'path')) {
            throw new \Exception('OP_PATH_INVALID');
        }
        $path = $operation->path;
        if (!is_string($path)) {
            throw new \Exception('OP_PATH_INVALID');
        }
        if (!property_exists($operation, 'value')) {
            throw new \Exception('OP_VALUE_INVALID');
        }
        $value = $operation->value;
        return new Patch\OpReplace($path, $value);
    }

    private static function createRemoveOp($operation): Patch\OpRemove
    {
        if (!property_exists($operation, 'path')) {
            throw new \Exception('OP_PATH_INVALID');
        }
        $path = $operation->path;
        if (!is_string($path)) {
            throw new \Exception('OP_PATH_INVALID');
        }
        return new Patch\OpRemove($path);
    }

    private static function createTestOp($operation): Patch\OpTest
    {
        if (!property_exists($operation, 'path')) {
            throw new \Exception('OP_PATH_INVALID');
        }
        $path = $operation->path;
        if (!is_string($path)) {
            throw new \Exception('OP_PATH_INVALID');
        }
        if (!property_exists($operation, 'value')) {
            throw new \Exception('OP_VALUE_INVALID');
        }
        $value = $operation->value;
        return new Patch\OpTest($path, $value);
    }

    private static function createMoveOp($operation): Patch\OpMove
    {
        if (!property_exists($operation, 'path')) {
            throw new \Exception('OP_PATH_INVALID');
        }
        $path = $operation->path;
        if (!is_string($path)) {
            throw new \Exception('OP_PATH_INVALID');
        }
        if (!property_exists($operation, 'from')) {
            throw new \Exception('OP_FROM_INVALID');
        }
        $from = $operation->from;
        if (!is_string($from)) {
            throw new \Exception('OP_FROM_INVALID');
        }
        return new Patch\OpMove($path, $from);
    }

    private static function createCopyOp($operation): Patch\OpCopy
    {
        if (!property_exists($operation, 'path')) {
            throw new \Exception('OP_PATH_INVALID');
        }
        $path = $operation->path;
        if (!is_string($path)) {
            throw new \Exception('OP_PATH_INVALID');
        }
        if (!property_exists($operation, 'from')) {
            throw new \Exception('OP_FROM_INVALID');
        }
        $from = $operation->from;
        if (!is_string($from)) {
            throw new \Exception('OP_FROM_INVALID');
        }
        return new Patch\OpCopy($path, $from);
    }
}
