<?php
/**
 * Created by pengxingyuan.
 * Date: 2018/8/8
 * Time: 17:31
 */

namespace app\common\model;


use think\Model;

/**文章管理模型
 * Class Article
 * @package app\common\model
 */
class Article extends Model
{
    protected $pk='id';


    /**添加文章
     * @param $data
     * @return false|int
     */
    public function insertArticle($data){
        $data['add_time']=time();

        $validate = new \app\common\validate\Article();
        $result = $validate->check($data);
        if (!$result) {
            $this->error = $validate->getError();
            return false;
        }

        $result=$this->save($data);
        return  $result;
    }
    /**
     * 编辑文章
     */
    public function updateArticle($data){

        $validate = new \app\common\validate\Article();
        $result = $validate->check($data);
        if (!$result) {
            $this->error = $validate->getError();
            return false;
        }

        $result=$this->isUpdate(true)->save($data);
        return  $result;
    }
}