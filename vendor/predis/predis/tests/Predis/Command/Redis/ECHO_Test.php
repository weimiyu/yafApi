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
 * @group realm-connection
 */
class ECHO_Test extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\Redis\ECHO_';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'ECHO';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('message');
        $expected = array('message');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $raw = 'message';
        $expected = 'message';

        $command = $this->getCommand();

        $this->assertSame($expected, $command->parseResponse($raw));
    }

    /**
     * @group connected
     */
    public function testAlwaysReturnsThePassedMessage()
    {
        $redis = $this->getClient();

        $message = 'Can you hear me?';

        $this->assertSame($message, $redis->echo($message));
    }
}
