<?php

namespace Live\Collection;

use PHPUnit\Framework\TestCase;

class MemoryCollectionTest extends TestCase
{
    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function objectCanBeConstructed()
    {
        $collection = new MemoryCollection();
        return $collection;
    }

    /**
     * @test
     * @depends objectCanBeConstructed
     * @doesNotPerformAssertions
     */
    public function dataCanBeAdded()
    {
        $collection = new MemoryCollection();
        $collection->set('index1', 'value', '5');
        $collection->set('index2', 5);
        $collection->set('index3', true);
        $collection->set('index4', 6.5);
        $collection->set('index5', ['data']);
    }

     /**
     * @test
     * @depends dataCanBeAdded
     */
    public function dataCanBeRetrieved()
    {
        $collection = new MemoryCollection();
        $collection->set('index1', 'value');

        $this->assertEquals('value', $collection->get('index1'));
    }

    /**
     * @test
     * @depends objectCanBeConstructed
     */
    public function inexistentIndexShouldReturnDefaultValue()
    {
        $collection = new MemoryCollection();

        $this->assertNull($collection->get('index1'));
        $this->assertEquals('defaultValue', $collection->get('index1', 'defaultValue'));
    }

    /**
     * @test
     * @depends objectCanBeConstructed
     */
    public function newCollectionShouldNotContainItems()
    {
        $collection = new MemoryCollection();
        $this->assertEquals(0, $collection->count());
    }

    /**
     * @test
     * @depends dataCanBeAdded
     */
    public function collectionWithItemsShouldReturnValidCount()
    {
        $collection = new MemoryCollection();
        $collection->set('index1', 'value');
        $collection->set('index2', 5);
        $collection->set('index3', true);

        $this->assertEquals(3, $collection->count());
    }

    /**
     * @test
     * @depends collectionWithItemsShouldReturnValidCount
     */
    public function collectionCanBeCleaned()
    {
        $collection = new MemoryCollection();
        $collection->set('index', 'value');
        $this->assertEquals(1, $collection->count());

        $collection->clean();
        $this->assertEquals(0, $collection->count());
    }

    /**
     * @test
     * @depends dataCanBeAdded
     */
    public function addedItemShouldExistInCollection()
    {
        $collection = new MemoryCollection();
        $collection->set('index', 'value');

        $this->assertTrue($collection->has('index'));
    }

    /**
     * @test
     */
    public function canIndexExpiration()
    {
        $collection = new MemoryCollection();
        $collection->set('indexB', 'value', 10);

        $this->assertTrue($collection->hasExpirationTime('indexB'));
    }

    /**
     * @depends canIndexExpiration
     * @test
     */
    public function canNotIndexExpiration()
    {
        $collection = new MemoryCollection();
        $collection->set('indexB', 'value', 0);

        $this->assertFalse($collection->hasExpirationTime('indexB'));
    }

    /**
     * @test
     */
    public function noExistsIndexExpiration()
    {
        $collection = new MemoryCollection();
        $collection->set('indexB', 'value', 20);

        $this->assertFalse($collection->hasExpirationTime('indexC'));
    }

    /**
     * @test
     * @depends objectCanBeConstructed
     */
    public function expirationTimeShouldReturnDefaultValue()
    {
        $collection = new MemoryCollection();
        $collection->set('indexB', 'value', 0);

        $this->assertEquals('defaultValue', $collection->get('indexB', 'defaultValue'));
    }
}
