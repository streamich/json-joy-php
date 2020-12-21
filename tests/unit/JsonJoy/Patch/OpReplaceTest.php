<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class OpReplaceTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        $op = new JsonJoy\Patch\OpReplace("/foo/bar", (object) ['a' => null]);
        $this->assertEquals(['foo', 'bar'], $op->pathTokens);
        $this->assertEquals('/foo/bar', $op->path);
        $this->assertEquals((object) ['a' => null], $op->value);
    }

    public function testCanSetRootValue(): void
    {
        $op = new JsonJoy\Patch\OpReplace("", (object) ['a' => null]);
        $doc = null;
        $this->assertEquals((object) ['a' => null], $op->apply($doc));
    }

    public function testCreatesDeepCloneOfArray(): void
    {
        $op = new JsonJoy\Patch\OpReplace("", [123]);
        $doc = null;
        $result = $op->apply($doc);
        $this->assertEquals($op->value, $result);
        $result[] = 'asdf';
        $this->assertNotEquals($op->value, $result);
    }

    public function testCanModifyObjectKey(): void
    {
        $op = new JsonJoy\Patch\OpReplace("/foo", [123]);
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
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('INVALID_INDEX');
        $op = new JsonJoy\Patch\OpReplace("/0", 3);
        $doc = [];
        $op->apply($doc);
    }

    public function testCanReplaceArrayElementAtFirstLevel(): void
    {
        $op = new JsonJoy\Patch\OpReplace("/1", "gg");
        $doc = [1, 2, 3];
        $result = $op->apply($doc);
        $this->assertEquals([1, "gg", 3], $result);
    }

    public function testCanReplaceArrayElementAtSecondLevel(): void
    {
        $op = new JsonJoy\Patch\OpReplace("/foo/0", true);
        $doc = (object) [
            'foo' => [1, 2, 3],
        ];
        $result = $op->apply($doc);
        $expected = (object) [
            'foo' => [true, 2, 3],
        ];
        $isEqual = JsonJoy\Json::equal($expected, $result);
        $this->assertEquals(true, $isEqual);
    }

    public function testCanReplaceArrayElementAtThirdLevel(): void
    {
        $op = new JsonJoy\Patch\OpReplace("/foo/bar/2", [3]);
        $doc = (object) [
            'foo' => (object) [
                'bar' => [1, 2, 3],
            ],
        ];
        $result = $op->apply($doc);
        $expected = (object) [
            'foo' => (object) [
                'bar' => [1, 2, [3]],
            ],
        ];
        $isEqual = JsonJoy\Json::equal($expected, $result);
        $this->assertEquals(true, $isEqual);
    }
}
