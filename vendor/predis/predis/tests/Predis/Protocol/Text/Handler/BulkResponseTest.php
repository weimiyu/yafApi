<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Protocol\Text;

use PredisTestCase;

/**
 *
 */
class BulkResponseTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testZeroLengthBulk()
    {
        $connection = $this->getMockConnectionOfType('Predis\Connection\CompositeConnectionInterface');
        $connection
            ->expects($this->never())
            ->method('readLine');
        $connection
            ->expects($this->once())
            ->method('readBuffer')
            ->with($this->equalTo(2))
            ->will($this->returnValue("\r\n"));

        $handler = new Handler\BulkResponse();

        $this->assertSame('', $handler->handle($connection, '0'));
    }

    /**
     * @group disconnected
     */
    public function testBulk()
    {
        $bulk = 'This is a bulk string.';
        $bulkLengh = strlen($bulk);

        $connection = $this->getMockConnectionOfType('Predis\Connection\CompositeConnectionInterface');
        $connection
            ->expects($this->never())
            ->method('readLine');
        $connection
            ->expects($this->once())
            ->method('readBuffer')
            ->with($this->equalTo($bulkLengh + 2))
            ->will($this->returnValue("$bulk\r\n"));

        $handler = new Handler\BulkResponse();

        $this->assertSame($bulk, $handler->handle($connection, (string) $bulkLengh));
    }

    /**
     * @group disconnected
     */
    public function testNull()
    {
        $connection = $this->getMockConnectionOfType('Predis\Connection\CompositeConnectionInterface');
        $connection
            ->expects($this->never())
            ->method('readLine');
        $connection
            ->expects($this->never())
            ->method('readBuffer');

        $handler = new Handler\BulkResponse();

        $this->assertNull($handler->handle($connection, '-1'));
    }

    /**
     * @group disconnected
     * @expectedException \Predis\Protocol\ProtocolException
     * @expectedExceptionMessage Cannot parse 'invalid' as a valid length for a bulk response [tcp://127.0.0.1:6379]
     */
    public function testInvalidLengthString()
    {
        $connection = $this->getMockConnectionOfType('Predis\Connection\CompositeConnectionInterface', 'tcp://127.0.0.1:6379');
        $connection
            ->expects($this->never())
            ->method('readLine');
        $connection
            ->expects($this->never())
            ->method('readBuffer');

        $handler = new Handler\BulkResponse();

        $handler->handle($connection, 'invalid');
    }

    /**
     * @group disconnected
     * @expectedException \Predis\Protocol\ProtocolException
     * @expectedExceptionMessage Value '-5' is not a valid length for a bulk response [tcp://127.0.0.1:6379]
     */
    public function testInvalidLengthInteger()
    {
        $connection = $this->getMockConnectionOfType('Predis\Connection\CompositeConnectionInterface', 'tcp://127.0.0.1:6379');
        $connection
            ->expects($this->never())
            ->method('readLine');
        $connection
            ->expects($this->never())
            ->method('readBuffer');

        $handler = new Handler\BulkResponse();

        $handler->handle($connection, '-5');
    }
}
