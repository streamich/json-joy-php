<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class OpRemoveTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        $op = new JsonJoy\Patch\OpRemove("/foo/bar");
        $this->assertEquals(['foo', 'bar'], $op->pathTokens);
        $this->assertEquals('/foo/bar', $op->path);
    }

    public function testRemoveRootElement(): void
    {
        $op = new JsonJoy\Patch\OpRemove("");
        $doc = [1, 2];
        $this->assertEquals(null, $op->apply($doc));
    }

    public function testCanModifyObjectKey(): void
    {
        $op = new JsonJoy\Patch\OpRemove("/foo");
        $doc = (object) [
            'foo' => 'bar',
        ];
        $result = $op->apply($doc);
        $this->assertEquals(true, JsonJoy\Json::equal((object) [], $result));
    }

    public function testThrowsWhenRemovingElementFromArrayOutOfBounds(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('INVALID_INDEX');
        $op = new JsonJoy\Patch\OpRemove("/3");
        $doc = [1, 2];
        $op->apply($doc);
    }

    public function testCanRemoveArrayElementAtFirstLevel(): void
    {
        $op = new JsonJoy\Patch\OpRemove("/1");
        $doc = [1, 2, 3];
        $result = $op->apply($doc);
        $this->assertEquals([1, 3], $result);
    }

    public function testCanRemoveArrayElementAtSecondLevel(): void
    {
        $op = new JsonJoy\Patch\OpRemove("/foo/2", true);
        $doc = (object) [
            'foo' => [1, 2, 3],
        ];
        $result = $op->apply($doc);
        $expected = (object) [
            'foo' => [1, 2],
        ];
        $isEqual = JsonJoy\Json::equal($expected, $result);
        $this->assertEquals(true, $isEqual);
    }

    public function testCanRemoveArrayElementAtThirdLevel(): void
    {
        $op = new JsonJoy\Patch\OpRemove("/foo/bar/0");
        $doc = (object) [
            'foo' => (object) [
                'bar' => [1, 2, 3],
            ],
        ];
        $result = $op->apply($doc);
        $expected = (object) [
            'foo' => (object) [
                'bar' => [2, 3],
            ],
        ];
        $isEqual = JsonJoy\Json::equal($expected, $result);
        $this->assertEquals(true, $isEqual);
    }
}
