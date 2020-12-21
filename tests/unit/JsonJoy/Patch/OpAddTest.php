<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class OpAddTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        $op = new JsonJoy\Patch\OpAdd("/foo/bar", [123]);
        $this->assertEquals(['foo', 'bar'], $op->pathTokens);
        $this->assertEquals('/foo/bar', $op->path);
        $this->assertEquals([123], $op->value);
    }

    public function testCanSetRootValue(): void
    {
        $op = new JsonJoy\Patch\OpAdd("", [123]);
        $doc = null;
        $this->assertEquals([123], $op->apply($doc));
    }

    public function testCreatesDeepCloneOfArray(): void
    {
        $op = new JsonJoy\Patch\OpAdd("", [123]);
        $doc = null;
        $result = $op->apply($doc);
        $this->assertEquals($op->value, $result);
        $result[] = 'asdf';
        $this->assertNotEquals($op->value, $result);
    }

    public function testCanModifyObjectKey(): void
    {
        $op = new JsonJoy\Patch\OpAdd("/foo", [123]);
        $doc = (object) [
            'foo' => 'bar',
        ];
        $result = $op->apply($doc);
        $this->assertEquals((object) [
            'foo' => [123],
        ], $result);
    }

    public function testCanInsertElementIntoEmptyArray(): void
    {
        $op = new JsonJoy\Patch\OpAdd("/0", 3);
        $doc = [];
        $result = $op->apply($doc);
        $this->assertEquals([3], $result);
    }

    public function testCanInsertElementIntoEmptyArrayAtSecondLevel(): void
    {
        $op = new JsonJoy\Patch\OpAdd("/foo/0", 3);
        $doc = (object) [
            'foo' => [],
        ];
        $result = $op->apply($doc);
        $this->assertEquals((object) [
            'foo' => [3],
        ], $result);
    }

    public function testCanInsertElementIntoEmptyArrayAtThirdLevel(): void
    {
        $op = new JsonJoy\Patch\OpAdd("/foo/bar/0", 3);
        $doc = (object) [
            'foo' => (object) [
                'bar' => [],
            ],
        ];
        $result = $op->apply($doc);
        $this->assertEquals((object) [
            'foo' => (object) [
                'bar' => [3],
            ],
        ], $result);
    }
}
