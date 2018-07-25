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
 * @group realm-zset
 */
class ZINCRBY_Test extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\Redis\ZINCRBY';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'ZINCRBY';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key', 1.0, 'member');
        $expected = array('key', 1.0, 'member');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $this->assertSame('1', $this->getCommand()->parseResponse('1'));
    }

    /**
     * @group connected
     */
    public function testIncrementsScoreOfMemberByFloat()
    {
        $redis = $this->getClient();

        $this->assertSame('1', $redis->zincrby('letters', 1, 'member'));
        $this->assertSame('0', $redis->zincrby('letters', -1, 'member'));
        $this->assertSame('0.5', $redis->zincrby('letters', 0.5, 'member'));
        $this->assertSame('-10', $redis->zincrby('letters', -10.5, 'member'));
    }

    /**
     * @group connected
     * @expectedException \Predis\Response\ServerException
     * @expectedExceptionMessage Operation against a key holding the wrong kind of value
     */
    public function testThrowsExceptionOnWrongType()
    {
        $redis = $this->getClient();

        $redis->set('foo', 'bar');
        $redis->zincrby('foo', 1, 'bar');
    }
}
