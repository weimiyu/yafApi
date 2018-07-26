<?php
/**
 * Created by PhpStorm.
 * User: yumiwei
 * Date: 2018/7/25
 * Time: 下午4:18
 */
namespace App\Modules\Service;

use App\Models\Admin\Admin;

class AdminSer
{

    public function getAdmin($id)
    {
        echo 'service';
        $user = Admin::find($id)->toArray();
        dd($user);
    }
}