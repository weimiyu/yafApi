<?php
/**
 * @name Bootstrap
 * @author root
 * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * @see http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
use Yaf\Registry;
use Yaf\Dispatcher;
use Yaf\Application;
use Yaf\Bootstrap_Abstract;

use Illuminate\Events\Dispatcher as LDispatcher;
use Illuminate\Container\Container as LContainer;
use Illuminate\Database\Capsule\Manager as Capsule;

class Bootstrap extends Bootstrap_Abstract
{

    public $config;

    /**
     * 初始化 自动加载
     * @param Dispatcher $dispatcher
     */
    public function _initVendor(Dispatcher $dispatcher)
    {
        define('PROJECT_NAME', 'test');
        require APP_PATH . '/vendor/autoload.php';
    }

    /**
     * 初始化config数组
     * @param Dispatcher $dispatcher
     */
    public function _initConfig(Dispatcher $dispatcher)
    {
        $this->config = Application::app()->getConfig();
        Registry::set('config', $this->config);
    }

    /**
     * 初始化 Eloquent ORM
     * @param Dispatcher $dispatcher
     */
    public function _initDb(Dispatcher $dispatcher)
    {
        $capsule = new Capsule();
        $capsule->addConnection($this->config->database->toArray());
        $capsule->setEventDispatcher(new LDispatcher(new LContainer));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        //构建查询构造器
        //class_alias(Illuminate\Database\Capsule\Manager::class, 'DB');
    }

    /**
     * 在这里注册自己的view控制器，例如smarty,firekylin
     * @param Dispatcher $dispatcher
     */
    public function _initView(Dispatcher $dispatcher)
    {
//        $dispatcher->initView(APP_PATH.'/application/templates/');

        $twig = new \Twig\Adapter(APP_PATH . "/application/views/", $this->config->twig->toArray());
        $dispatcher->setView($twig);
    }

    public function _initPlugin(Dispatcher $dispatcher)
    {
        //注册一个插件
//		$objSamplePlugin = new SamplePlugin();
//		$dispatcher->registerPlugin($objSamplePlugin);

    }


    public function _initRoute(Dispatcher $dispatcher)
    {

    }

}
