<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PatchTest extends TestCase
{
    public function testCanCreateJsonPatchOfOps(): void
    {
        $str = '[
            {"op": "add", "path": "/a", "value": 1},
            {"op": "replace", "path": "/b", "value": 2},
            {"op": "remove", "path": "/c"},
            {"op": "move", "path": "/d", "from": "/e"},
            {"op": "copy", "path": "/f", "from": "/g"},
            {"op": "test", "path": "/h", "value": 3}
        ]';
        $doc = json_decode($str, false);
        $ops = JsonJoy\Patch::createOps($doc);
        $this->assertEquals([
            new JsonJoy\Patch\OpAdd('/a', 1),
            new JsonJoy\Patch\OpReplace('/b', 2),
            new JsonJoy\Patch\OpRemove('/c'),
            new JsonJoy\Patch\OpMove('/d', '/e'),
            new JsonJoy\Patch\OpCopy('/f', '/g'),
            new JsonJoy\Patch\OpTest('/h', 3),
        ], $ops);
    }
}
