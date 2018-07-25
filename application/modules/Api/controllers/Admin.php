<?php
/**
 * Created by PhpStorm.
 * User: yumiwei
 * Date: 2018/7/2
 * Time: 下午2:11
 */
use Yaf\Controller_Abstract;
use App\Models\Admin\Admin;
use App\Modules\Service\Admin as AdminSer;

class AdminController extends Controller_Abstract
{

    public function getParamAction()
    {
        echo 'get';
        $id = $this->getRequest()->getQuery('id');
        $name = $this->getRequest()->getQuery('name');
    }

    public function postParamAction()
    {
        echo 'post';
        $id = $this->getRequest()->getPost('id');
        $name = $this->getRequest()->getPost('name');
        var_dump($id,$name);
    }
}
