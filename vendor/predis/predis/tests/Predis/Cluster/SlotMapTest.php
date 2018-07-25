<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Cluster;

use PredisTestCase;

/**
 *
 */
class SlotMapTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testIsValidReturnsTrueOnValidSlot()
    {
        $this->assertTrue(SlotMap::isValid(0));
        $this->assertTrue(SlotMap::isValid(16383));

        $this->assertTrue(SlotMap::isValid(5000));
        $this->assertTrue(SlotMap::isValid('5000'));
    }

    /**
     * @group disconnected
     */
    public function testIsValidReturnsFalseOnInvalidSlot()
    {
        $this->assertFalse(SlotMap::isValid(-1));
        $this->assertFalse(SlotMap::isValid(16384));
    }

    /**
     * @group disconnected
     */
    public function testIsValidRangeReturnsTrueOnValidSlotRange()
    {
        $this->assertTrue(SlotMap::isValidRange(0, 16383));
        $this->assertTrue(SlotMap::isValidRange(2000, 2999));
        $this->assertTrue(SlotMap::isValidRange(3000, 3000));
    }

    /**
     * @group disconnected
     */
    public function testIsValidRangeReturnsFalseOnInvalidSlotRange()
    {
        $this->assertFalse(SlotMap::isValidRange(0, 16384));
        $this->assertFalse(SlotMap::isValidRange(-1, 16383));
        $this->assertFalse(SlotMap::isValidRange(-1, 16384));
        $this->assertFalse(SlotMap::isValidRange(2999, 2000));
    }

    /**
     * @group disconnected
     */
    public function testToArrayReturnsEmptyArrayOnEmptySlotMap()
    {
        $slotmap = new SlotMap();

        $this->assertEmpty($slotmap->toArray());
    }

    /**
     * @group disconnected
     */
    public function testSetSlotsAssignsSpecifiedNodeToSlotRange()
    {
        $slotmap = new SlotMap();

        $slotmap->setSlots(0, 5460, '127.0.0.1:6379');
        $slotmap->setSlots(5461, 10922, '127.0.0.1:6380');
        $slotmap->setSlots(10923, 16383, '127.0.0.1:6381');

        $expectedMap = array_merge(
            array_fill(0, 5461, '127.0.0.1:6379'),
            array_fill(5461, 5462, '127.0.0.1:6380'),
            array_fill(10923, 5461, '127.0.0.1:6381')
        );

        $this->assertSame($expectedMap, $slotmap->toArray());
    }

    /**
     * @group disconnected
     */
    public function testSetSlotsOverwritesSlotRange()
    {
        $slotmap = new SlotMap();

        $slotmap->setSlots(0, 5460, '127.0.0.1:6379');
        $slotmap->setSlots(1000, 2000, '127.0.0.1:6380');

        $expectedMap =
            array_fill(0, 5461, '127.0.0.1:6379') +
            array_fill(1000, 2000, '127.0.0.1:6380');

        $this->assertSame($expectedMap, $slotmap->toArray());
    }

    /**
     * @group disconnected
     */
    public function testSetSlotsAssignsSingleSlotWhenFirstAndLastSlotMatch()
    {
        $slotmap = new SlotMap();

        $slotmap->setSlots(10, 10, '127.0.0.1:6379');

        $this->assertSame(array(10 => '127.0.0.1:6379'), $slotmap->toArray());
    }

    /**
     * @group disconnected
     */
    public function testSetSlotsCastsValueToString()
    {
        $slotmap = new SlotMap();

        $connection = $this->getMockConnection();
        $connection
            ->expects($this->once())
            ->method('__toString')
            ->will($this->returnValue('127.0.0.1:6379'));

        $slotmap->setSlots(10, 10, $connection);

        $this->assertSame(array(10 => '127.0.0.1:6379'), $slotmap->toArray());
    }

    /**
     * @group disconnected
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage Invalid slot range 0-16384 for `127.0.0.1:6379`
     */
    public function testSetSlotsThrowsExceptionOnInvalidSlotRange()
    {
        $slotmap = new SlotMap();

        $slotmap->setSlots(0, 16384, '127.0.0.1:6379');
    }

    /**
     * @group disconnected
     */
    public function testGetSlotsReturnsEmptyArrayOnEmptySlotMap()
    {
        $slotmap = new SlotMap();

        $this->assertEmpty($slotmap->getSlots(3, 11));
    }

    /**
     * @group disconnected
     */
    public function testGetSlotsReturnsDictionaryOfSlotsWithAssignedNodes()
    {
        $slotmap = new SlotMap();

        $slotmap->setSlots(0, 5, '127.0.0.1:6379');
        $slotmap->setSlots(10, 13, '127.0.0.1:6380');

        $expectedMap = array(
            3 => '127.0.0.1:6379',
            4 => '127.0.0.1:6379',
            5 => '127.0.0.1:6379',
            10 => '127.0.0.1:6380',
            11 => '127.0.0.1:6380',
        );

        $this->assertSame($expectedMap, $slotmap->getSlots(3, 11));
    }

    /**
     * @group disconnected
     */
    public function testGetSlotsReturnsEmptyArrayOnEmptySlotRange()
    {
        $slotmap = new SlotMap();

        $slotmap->setSlots(0, 5, '127.0.0.1:6379');
        $slotmap->setSlots(10, 13, '127.0.0.1:6380');

        $this->assertEmpty($slotmap->getSlots(100, 200));
    }

    /**
     * @group disconnected
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage Invalid slot range 0-16384
     */
    public function testGetSlotsThrowsExceptionOnInvalidSlotRange()
    {
        $slotmap = new SlotMap();

        $slotmap->getSlots(0, 16384);
    }

    /**
     * @group disconnected
     */
    public function testIsEmptyReturnsTrueOnEmptySlotMap()
    {
        $slotmap = new SlotMap();

        $this->assertTrue($slotmap->isEmpty());
    }

    /**
     * @group disconnected
     */
    public function testIsEmptyReturnsFalseOnNonEmptySlotMap()
    {
        $slotmap = new SlotMap();

        $slotmap->setSlots(0, 5460, '127.0.0.1:6379');

        $this->assertFalse($slotmap->isEmpty());
    }

    /**
     * @group disconnected
     */
    public function testCountReturnsZeroOnEmptySlotMap()
    {
        $slotmap = new SlotMap();

        $this->assertSame(0, count($slotmap));
    }

    /**
     * @group disconnected
     */
    public function testCountReturnsAssignedSlotsInSlotMap()
    {
        $slotmap = new SlotMap();

        $slotmap->setSlots(0, 5460, '127.0.0.1:6379');
        $this->assertSame(5461, count($slotmap));

        $slotmap->setSlots(5461, 10922, '127.0.0.1:6380');
        $this->assertSame(10923, count($slotmap));

        $slotmap->setSlots(10923, 16383, '127.0.0.1:6381');
        $this->assertSame(16384, count($slotmap));
    }

    /**
     * @group disconnected
     */
    public function testResetEmptiesSlotMap()
    {
        $slotmap = new SlotMap();

        $slotmap->setSlots(0, 5460, '127.0.0.1:6379');
        $slotmap->setSlots(5461, 10922, '127.0.0.1:6380');
        $slotmap->setSlots(10923, 16383, '127.0.0.1:6381');

        $this->assertFalse($slotmap->isEmpty());

        $slotmap->reset();

        $this->assertTrue($slotmap->isEmpty());
    }

    /**
     * @group disconnected
     */
    public function testGetNodesReturnsEmptyArrayOnEmptySlotMap()
    {
        $slotmap = new SlotMap();

        $this->assertEmpty($slotmap->getNodes());
    }

    /**
     * @group disconnected
     */
    public function testGetNodesReturnsArrayOfNodesInSlotMap()
    {
        $slotmap = new SlotMap();

        $slotmap->setSlots(0, 5460, '127.0.0.1:6379');
        $slotmap->setSlots(5461, 10922, '127.0.0.1:6380');
        $slotmap->setSlots(10923, 16383, '127.0.0.1:6381');

        $this->assertSame(array('127.0.0.1:6379', '127.0.0.1:6380', '127.0.0.1:6381'), $slotmap->getNodes());
    }

    /**
     * @group disconnected
     */
    public function testOffsetExistsReturnsTrueOnAssignedSlot()
    {
        $slotmap = new SlotMap();

        $slotmap->setSlots(0, 5460, '127.0.0.1:6379');

        $this->assertTrue(isset($slotmap[0]));
        $this->assertTrue(isset($slotmap[2000]));
    }

    /**
     * @group disconnected
     */
    public function testOffsetExistsReturnsFalseOnAssignedSlot()
    {
        $slotmap = new SlotMap();

        $slotmap->setSlots(0, 5460, '127.0.0.1:6379');

        $this->assertFalse(isset($slotmap[6000]));
    }

    /**
     * @group disconnected
     */
    public function testOffsetExistsReturnsFalseOnInvalidSlot()
    {
        $slotmap = new SlotMap();

        $slotmap->setSlots(0, 5460, '127.0.0.1:6379');

        $this->assertFalse(isset($slotmap[-100]));
        $this->assertFalse(isset($slotmap[16384]));
    }

    /**
     * @group disconnected
     */
    public function testOffsetGetReturnsNodeOfAssignedSlot()
    {
        $slotmap = new SlotMap();

        $slotmap->setSlots(0, 5460, '127.0.0.1:6379');
        $slotmap->setSlots(5461, 10922, '127.0.0.1:6380');
        $slotmap->setSlots(10923, 16383, '127.0.0.1:6381');

        $this->assertSame('127.0.0.1:6379', $slotmap[0]);
        $this->assertSame('127.0.0.1:6380', $slotmap[5461]);
        $this->assertSame('127.0.0.1:6381', $slotmap[10923]);
    }

    /**
     * @group disconnected
     */
    public function testOffsetGetReturnsNullOnUnassignedSlot()
    {
        $slotmap = new SlotMap();

        $slotmap->setSlots(0, 5460, '127.0.0.1:6379');

        $this->assertNull($slotmap[5461]);
    }

    /**
     * @group disconnected
     */
    public function testOffsetGetReturnsNullOnInvalidSlot()
    {
        $slotmap = new SlotMap();

        $slotmap->setSlots(0, 5460, '127.0.0.1:6379');

        $this->assertNull($slotmap[-100]);
        $this->assertNull($slotmap[16384]);
    }

    /**
     * @group disconnected
     */
    public function testOffsetUnsetRemovesSlotAssignment()
    {
        $slotmap = new SlotMap();

        $slotmap->setSlots(0, 5460, '127.0.0.1:6379');

        $this->assertTrue(isset($slotmap[100]));
        unset($slotmap[100]);
        $this->assertFalse(isset($slotmap[100]));
    }

    /**
     * @group disconnected
     */
    public function testOffsetUnsetDoesNotDoAnythingOnUnassignedSlot()
    {
        $slotmap = new SlotMap();

        $slotmap->setSlots(0, 5460, '127.0.0.1:6379');

        $this->assertFalse(isset($slotmap[5461]));
        unset($slotmap[5461]);
        $this->assertFalse(isset($slotmap[5461]));
    }

    /**
     * @group disconnected
     */
    public function testOffsetSetAssignsNodeToSlot()
    {
        $slotmap = new SlotMap();

        $slotmap->setSlots(0, 5460, '127.0.0.1:6379');

        $this->assertSame('127.0.0.1:6380', $slotmap[100] = '127.0.0.1:6380');
        $this->assertSame('127.0.0.1:6380', $slotmap[100]);

        $this->assertNull($slotmap[5461]);
        $this->assertSame('127.0.0.1:6380', $slotmap[5461] = '127.0.0.1:6380');
        $this->assertSame('127.0.0.1:6380', $slotmap[5461]);
    }

    /**
     * @group disconnected
     */
    public function testOffsetSetCastsValueToString()
    {
        $slotmap = new SlotMap();

        $connection = $this->getMockConnection();
        $connection
            ->expects($this->once())
            ->method('__toString')
            ->will($this->returnValue('127.0.0.1:6379'));

        $this->assertSame($connection, $slotmap[0] = $connection);
        $this->assertSame('127.0.0.1:6379', $slotmap[0]);
    }

    /**
     * @group disconnected
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage Invalid slot 16384 for `127.0.0.1:6379`
     */
    public function testOffsetSetThrowsExceptionOnInvalidSlot()
    {
        $slotmap = new SlotMap();

        $slotmap[16384] = '127.0.0.1:6379';
    }

    /**
     * @group disconnected
     */
    public function testGetIteratorReturnsIteratorOverSlotMap()
    {
        $slotmap = new SlotMap();

        $slotmap->setSlots(0, 5460, '127.0.0.1:6379');
        $slotmap->setSlots(5461, 10922, '127.0.0.1:6380');
        $slotmap->setSlots(10923, 16383, '127.0.0.1:6381');

        $expectedMap = array_merge(
            array_fill(0, 5461, '127.0.0.1:6379'),
            array_fill(5461, 5462, '127.0.0.1:6380'),
            array_fill(10923, 5461, '127.0.0.1:6381')
        );

        $this->assertSame($expectedMap, iterator_to_array($slotmap));
    }
}
