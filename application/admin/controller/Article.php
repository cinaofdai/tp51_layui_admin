<?php
/**
 * Created by pengxingyuan.
 * Date: 2018/8/8
 * Time: 16:55
 */

namespace app\admin\controller;

use app\common\controller\BaseAdmin;
use think\Db;
use think\exception\HttpException;
use think\facade\Config;

/**文章管理控制器
 * Class Article
 * @package app\admin\controller
 */
class Article extends BaseAdmin
{
    /**
     * 文章列表
     */
    public function index(){
        if (request()->isAjax()){
            $list = $this->search('article_view');
            return $list;
        }else{
            return $this->fetch();
        }
    }
    /**
     * 文章添加
     */
    public function add(){
        if (request()->isAjax()){
            if(request()->isPost()){
                $data = request()->post();
                $model=new \app\common\model\Article();
                if($model->insertArticle($data)){
                    return ['status'=>true,'message'=>'文章添加成功'];
                }else{
                    return ['status'=>false,'message'=>$model->getError()];
                }
            }else{
                //文章分类数据
                $cate = Db::name('article_cate')->order('id','asc')->select();
                $cate = \Data::tree($cate,'name');
                foreach ($cate as $value){
                    $lists[] = $value;
                }
                return $lists;
            }
        }else{
            return $this->fetch();
        }
    }
    /**
     * 文章编辑
     */
    public function edit(){
        if (request()->isAjax()){
            if(request()->isGet()){
                $data['model'] = Db::name('article')->find(request()->get('id'));
                //$data['model']['content'] = \Util::replace_url($data['model']['content'],Config::get('qiniu.uploadUrl'));
                //所有分类
                $cate = Db::name('article_cate')->order('id','asc')->select();
                $cate = \Data::tree($cate,'name');
                foreach ($cate as $value){
                    $lists[] = $value;
                }
                $data['cate']  =  $lists;
                return $data?$data:false;
            }else{
                $data = request()->post();
                //$data['content'] = \Util::remove_url($data['content'],Config::get('qiniu.uploadUrl'));

                $model = new \app\common\model\Article();
                if($model->updateArticle($data)){
                    return ['status'=>true,'message'=>'文章编辑成功'];
                }else{
                    return ['status'=>false,'message'=>$model->getError()];
                }
            }
        }else{
            $this->assign('edit_id',request()->get('id'));
            return $this->fetch();
        }
    }
    /**
     * 文章删除
     */
    public function remove(){
        $ids = request()->post('ids');
        if(request()->isAjax()){
            $result = Db::name('article')->delete($ids);
            if($result){
                return ['status'=>true,'message'=>'删除成功!'];
            }else{
                return ['status'=>false,'message'=>'删除失败!'];
            }
        }else{
            throw new HttpException(502,'请求不合法');
        }
    }
}