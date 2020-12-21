<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PointerTest extends TestCase
{
    public function testCanEscapeAReferenceToken(): void
    {
        $result = JsonJoy\Pointer::escapeReferenceToken('asdf');
        $this->assertEquals('asdf', $result);
        $result = JsonJoy\Pointer::escapeReferenceToken('');
        $this->assertEquals('', $result);
        $result = JsonJoy\Pointer::escapeReferenceToken('/');
        $this->assertEquals('~1', $result);
        $result = JsonJoy\Pointer::escapeReferenceToken('~');
        $this->assertEquals('~0', $result);
        $result = JsonJoy\Pointer::escapeReferenceToken('~/~//');
        $this->assertEquals('~0~1~0~1~1', $result);
        $result = JsonJoy\Pointer::escapeReferenceToken('/a~b~');
        $this->assertEquals('~1a~0b~0', $result);
    }

    public function testCanUnescapeAReferenceToken(): void
    {
        $result = JsonJoy\Pointer::unescapeReferenceToken('asdf');
        $this->assertEquals('asdf', $result);
        $result = JsonJoy\Pointer::unescapeReferenceToken('');
        $this->assertEquals('', $result);
        $result = JsonJoy\Pointer::unescapeReferenceToken('~1');
        $this->assertEquals('/', $result);
        $result = JsonJoy\Pointer::unescapeReferenceToken('~0');
        $this->assertEquals('~', $result);
        $result = JsonJoy\Pointer::unescapeReferenceToken('~0~1~0~1~1');
        $this->assertEquals('~/~//', $result);
        $result = JsonJoy\Pointer::unescapeReferenceToken('~1a~0b~0');
        $this->assertEquals('/a~b~', $result);
    }

    public function testCanParseARootPointer(): void
    {
        $result = JsonJoy\Pointer::create('');
        $expected = new JsonJoy\Pointer([]);
        $this->assertEquals($expected, $result);
    }

    public function testCanParseASimpleJsonPointer(): void
    {
        $result = JsonJoy\Pointer::create('/foo/bar');
        $expected = new JsonJoy\Pointer(['foo', 'bar']);
        $this->assertEquals($expected, $result);
    }

    public function testThrowsOnInvalidPointer(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('POINTER_INVALID');
        $result = JsonJoy\Pointer::create('foo/bar');
    }
}
