<?php
/**
 * Created by PhpStorm.
 * User: yumiwei
 * Date: 2018/7/2
 * Time: 下午3:48
 */
namespace App\Models\Admin;

use App\Models\BaseModel;

class Admin extends BaseModel
{

    //表名
    public $table = 'admin';

    // 此字段自动转换成 Carbon 实例
    protected $dates = ['deleted_at'];

    // 允许批量赋值的字段
    protected $fillable = ['name', 'password'];


}