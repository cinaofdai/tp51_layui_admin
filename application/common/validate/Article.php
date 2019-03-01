<?php
/**
 * Created by pengxingyuan.
 * Date: 2018/8/8
 * Time: 18:24
 */

namespace app\common\validate;


use think\Db;

class Article extends BaseValidate
{
    protected $rule = [
        'title' => 'require|max:50',
        'author' => 'require|max:20',
        'cid' => 'gt:0',
        //'keyword' => 'require',
        'content' => 'require'
    ];

    protected $message = [
        'title.require' => '标题必须',
        'title.max' => '标题过长',
        'cid.gt' => '请选择分类',
        'author.require' => '作者必须',
        'author.max' => '作者名字过长',
        //'keyword.require' => '关键字必须',
        'content.require' => '内容必须'
    ];

}