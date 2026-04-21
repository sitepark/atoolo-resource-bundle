<?php

declare(strict_types=1);

namespace Atoolo\Resource\Service;

use InvalidArgumentException;

/**
 * Fixed-depth decimal grouping strategy: - groups of N decimal digits (base 10^N) - exactly
 * (levels) directories + 1 filename group - left padded with zeros - optional extension for
 * filename
 *
 * Example levels=2, digitsPerLevel=3:
 * 0 -> 000/000/000
 * 1000 -> 000/001/000
 */
class FixedDecimalGroupingIdPathMapper implements IdPathMapper
{
    private int $levels;
    private int $digitsPerLevel;
    private int $base;

    public function __construct(int $levels, int $digitsPerLevel)
    {
        if ($levels < 0) {
            throw new InvalidArgumentException("levels must be >= 0");
        }
        if ($digitsPerLevel <= 0) {
            throw new InvalidArgumentException("digitsPerLevel must be > 0");
        }

        $this->levels = $levels;
        $this->digitsPerLevel = $digitsPerLevel;
        $this->base = $this->pow10Long($digitsPerLevel);
    }

    public function enabled(): bool
    {
        return true;
    }

    public function mapToIdPath(string $url): ?string
    {

        // already mapped media-url (/000/015/112.media/32813.php)
        if (preg_match(
            '#((/\d{' . $this->digitsPerLevel . '}){' . ($this->levels + 1) . '}\.\w+/\d+)\.\w+#',
            $url,
            $matches,
        )) {
            return substr($matches[1], 1);
        }

        // already mapped url (/000/001/000.suffix)
        if (preg_match(
            '#((/\d{' . $this->digitsPerLevel . '}){' . ($this->levels + 1) . '}).\w+#',
            $url,
            $matches,
        )) {
            return substr($matches[1], 1);
        }

        // resource url (/dir/filename-1234)
        if (preg_match('#-(\d+)$#', $url, $matches)) {
            $id = $matches[1];
            return $this->pathFor((int) $id);
        }

        // media-url (/dir/1234/filename.pdf)
        if (preg_match('#/(\d+)/[^/]+$#', $url, $matches)) {
            $id = $matches[1];
            return $this->pathFor((int) $id);
        }

        // embedded-media-url (/dir/1234-567/filename.pdf)
        if (preg_match('#/(\d+)-(\d+)/[^/]+$#', $url, $matches)) {
            $mediaContainerId = $matches[1];
            $mediaId = $matches[2];
            return $this->embeddedMediaPathFor((int) $mediaContainerId, (int) $mediaId);
        }

        return null;
    }

    public function embeddedMediaPathFor(int $mediaContainerId, int $mediaId): string
    {
        return $this->pathFor($mediaContainerId) . '.media/' . $mediaId;
    }

    /**
     * Converts an ID to a path string
     * @return string Path with directory separators
     */
    public function pathFor(int $id): string
    {
        $this->validate($id);

        $parts = $this->levels + 1;
        $groups = array_fill(0, $parts, 0);
        $value = $id;

        for ($i = $parts - 1; $i >= 0; $i--) {
            $groups[$i] = (int) fmod($value, $this->base);
            $value = (int) floor($value / $this->base);
        }

        $pathParts = [];
        $pathParts[] = $this->leftPad($groups[0], $this->digitsPerLevel);

        for ($i = 1; $i < $this->levels; $i++) {
            $pathParts[] = $this->leftPad($groups[$i], $this->digitsPerLevel);
        }

        $file = $this->leftPad($groups[$parts - 1], $this->digitsPerLevel);
        $pathParts[] = $file;

        return implode(DIRECTORY_SEPARATOR, $pathParts);
    }

    /**
     * Validates if the ID fits within the capacity
     */
    private function validate(int|float $id): void
    {
        if ($id < 0) {
            throw new InvalidArgumentException("id must be >= 0");
        }

        $cap = $this->safePowLong($this->base, $this->levels + 1);
        $max = $cap - 1;

        if ($max >= 0 && $id > $max) {
            throw new InvalidArgumentException(
                "id must be <= $max for fixed layout (levels=$this->levels, "
                . "digits=$this->digitsPerLevel)",
            );
        }
    }

    /**
     * Left pads a number with zeros
     */
    private function leftPad(int|float $n, int $width): string
    {
        $s = (string) (int) $n;
        $length = strlen($s);

        if ($length >= $width) {
            return $s;
        }

        return str_repeat('0', $width - $length) . $s;
    }

    /**
     * Calculates 10^exp
     */
    private function pow10Long(int $exp): int
    {
        $r = 1;
        for ($i = 0; $i < $exp; $i++) {
            $r *= 10;
        }
        return $r;
    }

    /**
     * Safely calculates base^exp
     */
    private function safePowLong(int|float $base, int $exp): int|float
    {
        $r = 1;
        for ($i = 0; $i < $exp; $i++) {
            $r *= $base;
        }
        return $r;
    }
}
