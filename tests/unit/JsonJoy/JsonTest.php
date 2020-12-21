<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class JsonTest extends TestCase
{
    public function testCopyPrimitives(): void
    {
        $this->assertEquals("", JsonJoy\Json::copy(""));
        $this->assertEquals("foo", JsonJoy\Json::copy("foo"));
        $this->assertEquals(0, JsonJoy\Json::copy(0));
        $this->assertEquals(123, JsonJoy\Json::copy(123));
        $this->assertEquals(true, JsonJoy\Json::copy(true));
        $this->assertEquals(false, JsonJoy\Json::copy(false));
        $this->assertEquals(null, JsonJoy\Json::copy(null));
    }

    public function testCopyArrays(): void
    {
        $this->assertEquals([], JsonJoy\Json::copy([]));
        $this->assertEquals([1], JsonJoy\Json::copy([1]));
        $this->assertEquals(["asdf", 1, true, null], JsonJoy\Json::copy(["asdf", 1, true, null]));
        $this->assertEquals([["asdf"], 1, [true, "foo", false, []], null], JsonJoy\Json::copy([["asdf"], 1, [true, "foo", false, []], null]));
    }

    public function testCopyClonesArray(): void
    {
        $original = [];
        $copy = JsonJoy\Json::copy($original);
        $original[] = 123;
        $this->assertNotEquals($original, $copy);
    }

    public function testCopyObjects(): void
    {
        $this->assertEquals((object) [
            'foo' => 'bar',
        ], JsonJoy\Json::copy((object) [
            'foo' => 'bar',
        ]));
        $this->assertEquals((object) [
            'foo' => 'bar',
            'baz' => (object) [
                'a' => 'b',
                'c' => [1,2,3],
            ],
        ], JsonJoy\Json::copy((object) [
            'foo' => 'bar',
            'baz' => (object) [
                'a' => 'b',
                'c' => [1,2,3],
            ],
        ]));
    }

    public function testCopyClonesObject(): void
    {
        $original = (object) [];
        $copy = JsonJoy\Json::copy($original);
        $original->foo = 'bar';
        $this->assertNotEquals($original, $copy);
    }

    public function testEqualStrings(): void
    {
        $this->assertEquals(true, JsonJoy\Json::equal("", ""));
        $this->assertEquals(true, JsonJoy\Json::equal("foo", "foo"));
        $this->assertEquals(false, JsonJoy\Json::equal("foo", "bar"));
    }

    public function testEqualNumbers(): void
    {
        $this->assertEquals(true, JsonJoy\Json::equal(0, 0));
        $this->assertEquals(true, JsonJoy\Json::equal(-2, -2));
        $this->assertEquals(true, JsonJoy\Json::equal(-2.5, -2.5));
    }

    public function testEqualNumbersVsStrings(): void
    {
        $this->assertEquals(false, JsonJoy\Json::equal(-2.5, '-2.5'));
        $this->assertEquals(false, JsonJoy\Json::equal(1, '1'));
        $this->assertEquals(false, JsonJoy\Json::equal(0, '0'));
        $this->assertEquals(false, JsonJoy\Json::equal(0, ''));
    }

    public function testEqualArrays(): void
    {
        $this->assertEquals(true, JsonJoy\Json::equal([], []));
        $this->assertEquals(true, JsonJoy\Json::equal([1], [1]));
        $this->assertEquals(true, JsonJoy\Json::equal(["asdf"], ["asdf"]));
        $this->assertEquals(true, JsonJoy\Json::equal([false, true, "a", 123], [false, true, "a", 123]));
        $this->assertEquals(true, JsonJoy\Json::equal([[1, [2]]], [[1, [2]]]));
        
        $this->assertEquals(false, JsonJoy\Json::equal([1], [1, 2]));
        $this->assertEquals(false, JsonJoy\Json::equal(["1"], [1]));
        $this->assertEquals(false, JsonJoy\Json::equal([true], [false]));
        $this->assertEquals(false, JsonJoy\Json::equal([null], [false]));
        $this->assertEquals(false, JsonJoy\Json::equal([[1, [2]]], [[1, ['2']]]));
    }

    public function testEqualObjectIsNotAnArray(): void
    {
        $this->assertEquals(false, JsonJoy\Json::equal([], (object) []));
    }

    public function testEqualObjects(): void
    {
        $this->assertEquals(true, JsonJoy\Json::equal((object) [], (object) []));

        $doc1 = (object) [
            'foo' => 'bar'
        ];
        $doc2 = (object) [
            'foo' => 'bar'
        ];
        $this->assertEquals(true, JsonJoy\Json::equal($doc1, $doc2));

        $doc1 = (object) [
            'foo' => 'bar',
            'bar' => 123,
            'baz' => false,
            'aha' => [1, 2, null, true, false, 'str'],
        ];
        $doc2 = (object) [
            'foo' => 'bar',
            'bar' => 123,
            'baz' => false,
            'aha' => [1, 2, null, true, false, 'str'],
        ];
        $this->assertEquals(true, JsonJoy\Json::equal($doc1, $doc2));

        $doc1 = (object) [
            'foo' => 'bar',
            'bar' => 123,
            'baz' => false,
            'aha' => [1, 2, null, true, false, 'str'],
        ];
        $doc2 = (object) [
            'foo' => 'bar',
            'bar' => 123,
            'baz' => false,
            'aha' => [1, 2, null, true, 'DIFFERENCE_HERE', 'str'],
        ];
        $this->assertEquals(false, JsonJoy\Json::equal($doc1, $doc2));
    }
}
