<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class OpTestTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        $op = new JsonJoy\Patch\OpTest("/foo/bar", 888);
        $this->assertEquals(['foo', 'bar'], $op->pathTokens);
        $this->assertEquals('/foo/bar', $op->path);
        $this->assertEquals(888, $op->value);
    }

    public function testCanMoveFromOneKeyToAnother(): void
    {
        $op = new JsonJoy\Patch\OpTest("/foo", 123);
        $doc = (object) [
            'foo' => 123,
        ];
        $res = $op->apply($doc);
        $this->assertEquals($res, $doc);
    }

    public function testThrowsWhenTargetValueDoesNotPassTest(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('TEST');
        $op = new JsonJoy\Patch\OpTest("/foo", '123');
        $doc = (object) [
            'foo' => 123,
        ];
        $res = $op->apply($doc);
        $this->assertEquals($res, $doc);
    }
}
