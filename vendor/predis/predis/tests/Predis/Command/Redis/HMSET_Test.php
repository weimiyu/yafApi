<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Command\Redis;

/**
 * @group commands
 * @group realm-hash
 */
class HMSET_Test extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\Redis\HMSET';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'HMSET';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key', 'field1', 'value1', 'field2', 'value2');
        $expected = array('key', 'field1', 'value1', 'field2', 'value2');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsFieldsValuesAsSingleArray()
    {
        $arguments = array('key', array('field1' => 'value1', 'field2' => 'value2'));
        $expected = array('key', 'field1', 'value1', 'field2', 'value2');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $this->assertSame('OK', $this->getCommand()->parseResponse('OK'));
    }

    /**
     * @group connected
     * @requiresRedisVersion >= 2.0.0
     */
    public function testSetsSpecifiedFieldsOfHash()
    {
        $redis = $this->getClient();

        $this->assertEquals('OK', $redis->hmset('metavars', 'foo', 'bar', 'hoge', 'piyo'));
        $this->assertSame(array('foo' => 'bar', 'hoge' => 'piyo'), $redis->hgetall('metavars'));

        $this->assertEquals('OK', $redis->hmset('metavars', 'foo', 'barbar', 'lol', 'wut'));
        $this->assertSame(array('foo' => 'barbar', 'hoge' => 'piyo', 'lol' => 'wut'), $redis->hgetall('metavars'));
    }

    /**
     * @group connected
     * @requiresRedisVersion >= 2.0.0
     */
    public function testSetsTheSpecifiedField()
    {
        $redis = $this->getClient();

        $redis->hmset('metavars', 'foo', 'bar', 'hoge', 'piyo', 'lol', 'wut');

        $this->assertSame(array('foo' => 'bar', 'hoge' => 'piyo', 'lol' => 'wut'), $redis->hgetall('metavars'));
    }

    /**
     * @group connected
     * @requiresRedisVersion >= 2.0.0
     * @expectedException \Predis\Response\ServerException
     * @expectedExceptionMessage Operation against a key holding the wrong kind of value
     */
    public function testThrowsExceptionOnWrongType()
    {
        $redis = $this->getClient();

        $redis->set('metavars', 'bar');
        $redis->hmset('metavars', 'foo', 'bar');
    }
}
