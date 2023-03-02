<?php

declare(strict_types=1);

namespace PhpWndb\Dataset\Tests\Storage;

use Iterator;
use PHPUnit\Framework\TestCase;
use PhpWndb\Dataset\Storage\StreamBinarySearcher;
use PhpWndb\Dataset\Storage\StringStream;

class StreamBinarySearcherTest extends TestCase
{
    /**
     * @dataProvider dpTestSeekToLineStart
     */
    public function testSeekToLineStart(string $lineStart, bool $found, ?int $position): void
    {
        $searcher = new StreamBinarySearcher(8);
        $stream = new StringStream(
            '
a
bb
ccc
dddd
eeeee
z end'
        );

        self::assertSame($found, $searcher->seekToLineStart($stream, $lineStart));
        if ($position !== null) {
            self::assertSame($position, $stream->tell());
        }
    }

    /**
     * @return Iterator<mixed>
     */
    public static function dpTestSeekToLineStart(): Iterator
    {
        yield ['a', true, 2];
        yield ['cc', true, 8];
        yield ['z', true, 22];
        yield ['ab ', false, null];
        yield ['de ', false, null];
        yield ['end', false, null];
        yield ['wrong', false, null];
    }
}
