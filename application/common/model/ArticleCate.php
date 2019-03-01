<?php
/**
 * Created by pengxingyuan.
 * Date: 2018/8/8
 * Time: 16:05
 */

namespace app\common\model;


use think\Model;

/**文章分类模型
 * Class ArticleCate
 * @package app\common\model
 */
class ArticleCate extends Model
{
    protected $pk='id';

    /**添加分类
     * @param $data
     * @return false|int
     */
    public function insertCate($data){

        $validate = new \app\common\validate\ArticleCate();
        $result = $validate->check($data);
        if (!$result) {
            $this->error = $validate->getError();
            return false;
        }

        $result=$this->save($data);
        return $result;
    }

    /**编辑分类
     * @param $data
     * @return false|int
     */
    public function updateCate($data){

        $validate = new \app\common\validate\ArticleCate();
        $result = $validate->check($data);
        if (!$result) {
            $this->error = $validate->getError();
            return false;
        }

        $result=$this->isUpdate(true)->save($data);
        return $result;
    }
}