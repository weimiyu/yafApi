<?php
/**
 * Created by PhpStorm.
 * User: yumiwei
 * Date: 2018/7/16
 * Time: 下午5:05
 */

use Yaf\Controller_Abstract;
use App\Models\RedisModel as RedisModel;

class RedisController extends Controller_Abstract
{

    public function indexAction()
    {
//        $redis = new Redis();
//        $redis->connect('192.168.33.64',6379);
//        $redis->set('test','hello world!');
//        echo $redis->get('test');

//        $client = new Predis\Client();
//        $client->set('weimiyu', 'bar');
//        echo $client->get('weimiyu');
//        exit;
          new RedisModel();
    }
}