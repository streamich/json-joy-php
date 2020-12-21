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

    public function testDecodesReferenceTokens(): void
    {
        $result = JsonJoy\Pointer::create('/fo~1o/bar~0/~0');
        $expected = new JsonJoy\Pointer(['fo/o', 'bar~', '~']);
        $this->assertEquals($expected, $result);
    }


    public function testCanEncodeJsonPointerIntoStringForm(): void
    {
        $pointer = new JsonJoy\Pointer(['a', 'b', '/', 'aga~aga']);
        $str = $pointer->toString();
        $this->assertEquals('/a/b/~1/aga~0aga', $str);
    }

    public function testEncodesReferenceTokensWhenConvertingToString(): void
    {
        $pointer = JsonJoy\Pointer::create('/fo~1o/bar~0/~0');
        $str = $pointer->__toString();
        $this->assertEquals('/fo~1o/bar~0/~0', $str);
    }

    public function testGetValueInObject(): void
    {
        $str = '{"1": "foo", "2": [1, 2, 3]}';
        $doc = json_decode($str, false);
        $ptr = JsonJoy\Pointer::create('/1');
        $val = $ptr->get($doc);
        $this->assertEquals("foo", $val);
        $ptr = JsonJoy\Pointer::create('/2');
        $val = $ptr->get($doc);
        $this->assertEquals([1, 2, 3], $val);
    }

    public function testGetValueInArray(): void
    {
        $str = '{"1": "foo", "2": [1, 2, 3]}';
        $doc = json_decode($str, false);
        $ptr = JsonJoy\Pointer::create('/2/0');
        $val = $ptr->get($doc);
        $this->assertEquals(1, $val);
        $ptr = JsonJoy\Pointer::create('/2/1');
        $val = $ptr->get($doc);
        $this->assertEquals(2, $val);
        $ptr = JsonJoy\Pointer::create('/2/2');
        $val = $ptr->get($doc);
        $this->assertEquals(3, $val);
    }

    public function testThrowsWhenGettingAtNegativeArrayIndex(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('INVALID_INDEX');
        $str = '{"1": "foo", "2": [1, 2, 3]}';
        $doc = json_decode($str, false);
        $ptr = JsonJoy\Pointer::create('/2/-1');
        $val = $ptr->get($doc);
    }

    public function testThrowsWhenGettingElementOutOfArrayBounds(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('INVALID_INDEX');
        $str = '{"1": "foo", "2": [1, 2, 3]}';
        $doc = json_decode($str, false);
        $ptr = JsonJoy\Pointer::create('/2/3');
        $val = $ptr->get($doc);
    }

    public function testCanGetDocumentRoot(): void
    {
        $str = '{"1": "foo", "2": [1, 2, 3]}';
        $doc = json_decode($str, false);
        $ptr = JsonJoy\Pointer::create('');
        $val = $ptr->get($doc);
        $this->assertEquals($doc, $val);
    }
}
