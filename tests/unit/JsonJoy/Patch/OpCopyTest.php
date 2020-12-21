<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class OpCopyTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        $op = new JsonJoy\Patch\OpCopy("/foo/bar", '/gg');
        $this->assertEquals(['foo', 'bar'], $op->pathTokens);
        $this->assertEquals(['gg'], $op->fromTokens);
        $this->assertEquals('/foo/bar', $op->path);
        $this->assertEquals('/gg', $op->from);
    }

    public function testCanMoveFromOneKeyToAnother(): void
    {
        $op = new JsonJoy\Patch\OpCopy("/foo", '/bar');
        $doc = (object) [
            'bar' => 123,
        ];
        $res = $op->apply($doc);
        $this->assertEquals(true, JsonJoy\Json::equal($res, (object) ['bar' => 123, 'foo' => 123]));
    }
}
