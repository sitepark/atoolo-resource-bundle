<?php

declare(strict_types=1);

namespace Atoolo\Resource\Test\Change;

use Atoolo\Resource\Change\ResourceChange;
use Atoolo\Resource\Change\ResourceChanges;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(ResourceChanges::class)]
#[CoversClass(ResourceChange::class)]
class ResourceChangesTest extends TestCase
{
    public function testFromArray(): void
    {
        $changes = ResourceChanges::fromArray([
            'changed' => [
                ['id' => '1', 'path' => '/a.php'],
                ['id' => 2, 'path' => '/b.php.translations/en_US.php'],
            ],
            'removed' => [['id' => '3']],
        ]);

        $this->assertEquals(
            new ResourceChanges(
                [
                    new ResourceChange('1', '/a.php'),
                    new ResourceChange('2', '/b.php.translations/en_US.php'),
                ],
                ['3'],
            ),
            $changes,
        );
    }

    public function testFromArrayWithoutLists(): void
    {
        $this->assertTrue(ResourceChanges::fromArray([])->isEmpty());
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function invalidData(): array
    {
        return [
            'no object' => ['x'],
            'changed no list' => [['changed' => ['a' => 1]]],
            'missing id' => [['changed' => [['path' => '/a.php']]]],
            'empty id' => [['removed' => [['id' => '']]]],
            'relative path' => [['changed' => [['id' => '1', 'path' => 'a.php']]]],
            'path traversal' => [
                ['changed' => [['id' => '1', 'path' => '/../a.php']]],
            ],
            'removed no object' => [['removed' => ['1']]],
        ];
    }

    #[DataProvider('invalidData')]
    public function testFromArrayWithInvalidData(mixed $data): void
    {
        $this->expectException(InvalidArgumentException::class);
        ResourceChanges::fromArray($data);
    }

    public function testToArrayRoundTrip(): void
    {
        $changes = new ResourceChanges(
            [new ResourceChange('1', '/a.php')],
            ['2'],
        );
        $this->assertEquals(
            $changes,
            ResourceChanges::fromArray($changes->toArray()),
        );
    }

    public function testCount(): void
    {
        $changes = new ResourceChanges(
            [new ResourceChange('1', '/a.php')],
            ['2', '3'],
        );
        $this->assertSame(3, $changes->count());
    }

    public function testChangedPaths(): void
    {
        $changes = new ResourceChanges([
            new ResourceChange('1', '/a.php'),
            new ResourceChange('1', '/a.php'),
            new ResourceChange('2', '/b.php'),
        ]);
        $this->assertSame(['/a.php', '/b.php'], $changes->changedPaths());
    }

    public function testMergeKeepsBoth(): void
    {
        $merged = (new ResourceChanges([new ResourceChange('1', '/a.php')]))
            ->merge(new ResourceChanges([new ResourceChange('2', '/b.php')], ['3']));

        $this->assertEquals(
            new ResourceChanges(
                [
                    new ResourceChange('1', '/a.php'),
                    new ResourceChange('2', '/b.php'),
                ],
                ['3'],
            ),
            $merged,
        );
    }

    public function testMergeLaterRemovalWins(): void
    {
        $merged = (new ResourceChanges([
            new ResourceChange('1', '/a.php'),
            new ResourceChange('1', '/a.php.translations/en_US.php'),
        ]))->merge(new ResourceChanges([], ['1']));

        $this->assertEquals(new ResourceChanges([], ['1']), $merged);
    }

    public function testMergeLaterChangeWins(): void
    {
        $merged = (new ResourceChanges([], ['1']))
            ->merge(new ResourceChanges([new ResourceChange('1', '/a.php')]));

        $this->assertEquals(
            new ResourceChanges([new ResourceChange('1', '/a.php')]),
            $merged,
        );
    }

    public function testMergeRemovesDuplicates(): void
    {
        $merged = (new ResourceChanges([new ResourceChange('1', '/a.php')], ['2']))
            ->merge(new ResourceChanges([new ResourceChange('1', '/a.php')], ['2']));

        $this->assertEquals(
            new ResourceChanges([new ResourceChange('1', '/a.php')], ['2']),
            $merged,
        );
    }
}
