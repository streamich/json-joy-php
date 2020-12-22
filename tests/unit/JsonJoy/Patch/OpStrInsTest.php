<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class OpStrInsTest extends TestCase
{
    public function testCanInstantiate(): void
    {
        $op = new JsonJoy\Patch\OpStrIns("/foo/bar", 3, "asdf");
        $this->assertEquals(['foo', 'bar'], $op->pathTokens);
        $this->assertEquals('/foo/bar', $op->path);
        $this->assertEquals(3, $op->pos);
        $this->assertEquals('asdf', $op->str);
    }

    public function testCanInsertIntoString(): void
    {
        $op = new JsonJoy\Patch\OpStrIns("/foo", 2, 'gg');
        $doc = (object) [
            'foo' => 'asdf',
        ];
        $result = $op->apply($doc);
        $this->assertEquals((object) [
            'foo' => 'asggdf',
        ], $result);
    }
}
