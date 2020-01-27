<?php

namespace App\Tests\Repository;

use App\Repository\NoSqlManager;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

final class NoSqlManagerTest extends TestCase
{
    /**
     * @var NoSqlManager
     */
    private $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = new NoSqlManager('../../data/nosql', 'test_table');
    }

    /**
     * @test
     */
    public function save_WithoutId_Exception()
    {
        $toSave = [
            'name' => 'test',
            'nbr'  => 1,
        ];

        $this->expectException(RuntimeException::class);
        $this->manager->save($toSave);
    }

    /**
     * @test
     */
    public function save_WithId_Correct()
    {
        $toSave = [
            'name' => 'test',
            'nbr'  => 1,
            'id'   => 999
        ];

        $this->manager->save($toSave);
        $this->assertFileEquals('../data/expects/read_test_table_999.json', '../../data/nosql/test_table_999.json');
    }

    /**
     * @test
     */
    public function getById_WithNotExistingId_Exception()
    {
        $this->expectException(ResourceNotFoundException::class);
        $this->manager->getById(1);
    }

    /**
     * @test
     */
    public function getById_WithExistingFile_CorrectContent()
    {
        $content = $this->manager->getById(999);
        $this->assertEquals([
            'name' => 'test',
            'nbr'  => 1,
            'id'   => 999
        ], $content);
    }

    /**
     * Removes all files.
     */
    public static function tearDownAfterClass(): void
    {
        unlink('../../data/nosql/test_table_999.json');
    }
}
