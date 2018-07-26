<?php
/**
 * Created by PhpStorm.
 * User: yumiwei
 * Date: 2018/7/2
 * Time: 下午2:11
 */
use App\Models\Admin\Admin;
use Yaf\Controller_Abstract;
use App\Modules\Service\AdminSer as AdminSer;

class AdminController extends Controller_Abstract
{

    public function indexAction()
    {
//        $user = Admin::find(1)->toArray();
//        dd($user);

        //用查询构造器查询
//        $user = DB::table('admin')->find(1);
//        dd($user);
        //引用公共函数
//        human_file('');
        $AdminModel =  new AdminSer();
        $id = 1;
        $data = $AdminModel->getAdmin($id);
        $this->getView()->assign("data", $data);
    }



}
