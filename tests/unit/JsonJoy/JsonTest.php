<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class JsonTest extends TestCase
{
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
