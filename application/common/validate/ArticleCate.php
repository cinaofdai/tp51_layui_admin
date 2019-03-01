<?php
/**
 * Created by pengxingyuan.
 * Date: 2018/8/8
 * Time: 16:24
 */

namespace app\common\validate;




class ArticleCate extends BaseValidate
{
    protected $rule =   [
        'name'   =>'require|max:20',
        'sort'   => 'require|number',
    ];

    protected $message  =   [
        'name.require' => '分类名称必须',
        'name.max' => '分类名称过长',
        'sort.require'     => '分类排序必须',
        'sort.number'     => '分类排序必须是数字',
    ];
}