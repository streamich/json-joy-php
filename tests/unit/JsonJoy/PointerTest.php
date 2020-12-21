<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PointerTest extends TestCase {
  public function testCanParseARootPointer(): void {
    $result = JsonJoy\Pointer::create('');
    $expected = new JsonJoy\Pointer([]);
    $this->assertEquals($expected, $result);
  }

  public function testCanParseASimpleJsonPointer(): void {
    $result = JsonJoy\Pointer::create('/foo/bar');
    $expected = new JsonJoy\Pointer(['foo', 'bar']);
    $this->assertEquals($expected, $result);
  }

  public function testThrowsOnInvalidPointer(): void {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('POINTER_INVALID');
    $result = JsonJoy\Pointer::create('foo/bar');
  }
}
