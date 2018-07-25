<?php
/**
 * Created by PhpStorm.
 * User: yumiwei
 * Date: 2018/7/25
 * Time: 下午9:00
 */
namespace App\Models;

class RedisModel
{
    public $redis;

    public function __construct()
    {
        $this->redis = new \Predis\Client();
        $this->redis->set('weimiyu', 'bar');
        echo $this->redis->get('weimiyu');
    }

    private function _set($keys,$values,$time=3600)
    {
        $this->redis->setex($keys,$time,$values);
    }

    private function _get($keys)
    {
        $this->redis->get($keys);
    }


}
