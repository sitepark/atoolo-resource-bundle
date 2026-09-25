<?php

declare(strict_types=1);

namespace Atoolo\Resource\Change;

use InvalidArgumentException;

/**
 * The resources the CMS has created, changed or removed.
 *
 * A removal applies to every language of the resource, because the CMS
 * removes a resource as a whole.
 */
final class ResourceChanges
{
    /**
     * @param list<ResourceChange> $changed
     * @param list<string> $removedIds
     */
    public function __construct(
        public readonly array $changed = [],
        public readonly array $removedIds = [],
    ) {}

    public function isEmpty(): bool
    {
        return empty($this->changed) && empty($this->removedIds);
    }

    public function count(): int
    {
        return count($this->changed) + count($this->removedIds);
    }

    /**
     * The paths of all changed resources, without duplicates.
     *
     * @return list<string>
     */
    public function changedPaths(): array
    {
        return array_values(array_unique(array_map(
            static fn(ResourceChange $change) => $change->path,
            $this->changed,
        )));
    }

    /**
     * Merges the changes of a later notification into these. The last
     * action on a resource wins: a later removal drops earlier changes of
     * the resource, a later change revokes an earlier removal.
     */
    public function merge(self $later): self
    {
        $laterRemoved = array_flip($later->removedIds);
        $laterChangedIds = [];
        foreach ($later->changed as $change) {
            $laterChangedIds[$change->id] = true;
        }

        $changed = [];
        foreach ([...$this->changed, ...$later->changed] as $change) {
            if (isset($laterRemoved[$change->id])) {
                continue;
            }
            $changed[$change->path] = $change;
        }

        $removedIds = [];
        foreach ([...$this->removedIds, ...$later->removedIds] as $id) {
            if (isset($laterChangedIds[$id]) && !isset($laterRemoved[$id])) {
                continue;
            }
            $removedIds[$id] = $id;
        }

        return new self(
            array_values($changed),
            array_values($removedIds),
        );
    }

    /**
     * @return array{
     *     changed: list<array{id: string, path: string}>,
     *     removed: list<array{id: string}>
     * }
     */
    public function toArray(): array
    {
        return [
            'changed' => array_map(
                static fn(ResourceChange $change) => [
                    'id' => $change->id,
                    'path' => $change->path,
                ],
                $this->changed,
            ),
            'removed' => array_map(
                static fn(string $id) => ['id' => $id],
                $this->removedIds,
            ),
        ];
    }

    /**
     * @throws InvalidArgumentException if the data does not follow the
     *   structure of {@see toArray()}
     */
    public static function fromArray(mixed $data): self
    {
        if (!is_array($data)) {
            throw new InvalidArgumentException('object expected');
        }

        $changed = [];
        foreach (self::list($data, 'changed') as $i => $entry) {
            $changed[] = new ResourceChange(
                self::id($entry, "changed[$i]"),
                self::path($entry, "changed[$i]"),
            );
        }

        $removedIds = [];
        foreach (self::list($data, 'removed') as $i => $entry) {
            $removedIds[] = self::id($entry, "removed[$i]");
        }

        return new self($changed, $removedIds);
    }

    /**
     * @param array<mixed> $data
     * @return list<mixed>
     */
    private static function list(array $data, string $key): array
    {
        $list = $data[$key] ?? [];
        if (!is_array($list) || !array_is_list($list)) {
            throw new InvalidArgumentException($key . ': list expected');
        }
        return $list;
    }

    private static function id(mixed $entry, string $name): string
    {
        $id = is_array($entry) ? ($entry['id'] ?? null) : null;
        if (is_int($id)) {
            $id = (string) $id;
        }
        if (!is_string($id) || $id === '') {
            throw new InvalidArgumentException($name . '.id: string expected');
        }
        return $id;
    }

    private static function path(mixed $entry, string $name): string
    {
        $path = is_array($entry) ? ($entry['path'] ?? null) : null;
        if (!is_string($path) || !str_starts_with($path, '/')) {
            throw new InvalidArgumentException(
                $name . '.path: absolute path expected',
            );
        }
        if (str_contains($path, '..') || str_contains($path, "\0")) {
            throw new InvalidArgumentException(
                $name . '.path: invalid path',
            );
        }
        return $path;
    }
}
