<?php

declare(strict_types=1);

namespace PhpWndb\Dataset\Storage;

use InvalidArgumentException;

class StreamBinarySearcher implements StreamSearcher
{
    public function __construct(
        protected readonly int $blockSize,
    ) {
    }

    public function seekToLineStart(Stream $stream, string $lineStart): bool
    {
        if ($lineStart === '') {
            throw new InvalidArgumentException('`lineStart` is empty.');
        }

        $length = $stream->getLength();

        return $this->searchIn($stream, $lineStart, 0, $length);
    }

    protected function searchIn(Stream $stream, string $lineStart, int $start, int $end): bool
    {
        $needle = \PHP_EOL . $lineStart;
        $needleLength = \strlen($needle);

        $distance = $end - $start;
        $seekPosition = $distance <= $this->blockSize
            ? $start
            : $start + (int) \floor($distance / 2 - $this->blockSize / 2);

        $stream->seek($seekPosition);
        $data = $stream->read($this->blockSize);

        $lineStartPosition = \strpos($data, $needle);
        if ($lineStartPosition !== false) {
            $stream->seek($seekPosition + $lineStartPosition + $needleLength);
            return true;
        }

        if ($distance <= $this->blockSize) {
            return false;
        }

        $firstLineData = \strstr($data, \PHP_EOL);
        if ($firstLineData === false) {
            throw new \RuntimeException('There is data block without new line. Probably too small block size.');
        }

        $startPosition = $seekPosition + \strlen($data) - \strlen($firstLineData);
        $endPosition = $seekPosition + \strrpos($data, \PHP_EOL);

        $cmp = \strncmp($firstLineData, $needle, $needleLength);
        if ($cmp > 0) {
            return $startPosition >= $start
                && $this->searchIn($stream, $lineStart, $start, $startPosition);
        } else {
            return $endPosition < $end
                && $this->searchIn($stream, $lineStart, $endPosition, $end);
        }
    }
}
