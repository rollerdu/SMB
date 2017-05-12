<?php
/**
 * Created by PhpStorm.
 * User: duyang
 * Date: 2017/3/21
 * Time: 11:31
 */

namespace Admin\Controller;
use Think\Controller;


class ProductController extends BaseController{
    public $listRows = 15;
    public function index(){
        $title = I('get.title');
        $cate_id = intval(I('get.cate_id'));
        if($title){
            $where['title'] = array('like','%'.$title.'%');
        }
        if($cate_id){
            $where['cate_id'] = $cate_id;
        }
        $where['isdel'] = 1;
        $model = M("Product");
        $count = $model->where($where)->count();
        $Page = new \Extend\Page($count,$this->listRows);
        $data = $model->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('ctime desc')->select();
        $this->assign('page',$Page->show());
        $this->assign('data',$data);
        $this->assign('title',$title);
        $this->assign('cate_id',$cate_id);
        $this->assign('count',$count);
        $this->display();
    }
    public function add(){
        if(IS_POST){
            //如果用户提交数据
            $_POST['user_id'] = $this->user['id'];
            $_POST['content'] = htmlspecialchars(I('post.editorValue'));
            $model = D("Product");
            if (!$model->create()) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($model->getError());
                exit();
            } else {
                if ($product_id = $model->add($_POST)) {
                    //保存图片
                    $config = C('UploadImgConfig');
                    $config['savePath'] = 'image/';

                    $upload = new \Think\Upload($config);
                    $info = $upload->upload();
                    $info = $info['image'];
                    if(!$info){
                        // $this->error("图片保存失败");
                    }else{
                        $img_name = $config['rootPath'].$info['savepath'].$info['savename'];
                        $exname   = substr($info['savename'],0,strpos($info['savename'],'.'));

                        //生成缩略图
                        $image = new \Think\Image();
                        $image->open($img_name);

                        $thumb_name = $config['rootPath'].$info['savepath'].$exname.$config['width_v'].'-'.$config['height_v'].'.'.$info['ext'];
                        $image->thumb($config['width_v'], $config['height_v'])->save($thumb_name);

                        $imgpath=array(
                            'img'        =>trim($img_name,'.'),
                            'thumb_img' =>trim($thumb_name,'.')
                        );
                        if(!$model->where(array('id'=>$product_id))->save($imgpath)){
                            // $this->error("图片添加失败");
                        }
                    }
                    write_system_log();
                    $this->success("添加成功", U('Video/index'));

                } else {
                    $this->error("添加失败");
                }
            }
        }
        $this->display();
    }
    public function edit(){
        $id = intval(I('param.id'));
        $model = M('Product');
        $where['id'] = $id;
        $where['isdel'] = 1;
        $ret = $model->where($where)->find();
        if(!$ret) $this->error('数据有误');
        if(IS_POST){
            $_POST['content'] = htmlspecialchars(I('post.editorValue'));
            $result = $model->where('id='.$id)->save($_POST);
            if ($result !== false || 0 !== $result ) {
                //图片
                if( !empty($_FILES['image']['name']) ){
                    @unlink($ret['img']);
                    @unlink($ret['thumb_img']);
                    //保存图片
                    $config = C('UploadImgConfig');
                    $config['savePath'] = 'image/';

                    $upload = new \Think\Upload($config);

                    $info = $upload->upload();
                    $info = $info['image'];
                    if(!$info){
                        // $this->error("图片保存失败");
                    }else{
                        $img_name = $config['rootPath'].$info['savepath'].$info['savename'];
                        $exname   = substr($info['savename'],0,strpos($info['savename'],'.'));

                        //生成缩略图
                        $img= new \Think\Image();

                        $img->open($img_name);

                        $thumb_name = $config['rootPath'].$info['savepath'].$exname.$config['width_v'].'-'.$config['height_v'].'.'.$info['ext'];

                        $img->thumb($config['width_v'], $config['height_v'])->save($thumb_name);

                        $imgpath=array(
                            'img'        =>trim($img_name,'.'),
                            'thumb_img' =>trim($thumb_name,'.')
                        );
                        $model->where(array('id'=>$id))->save($imgpath);
                    }
                }
                write_system_log();
                $this->success("更新成功", U('Product/index'));
            } else {
                $this->error("更新失败");
            }
        }
        $ret['content'] =  html_entity_decode(htmlspecialchars_decode($ret['content']));
        $this->assign('data',$ret);
        $this->display();
    }

    public function change_status(){
        $id = intval(I('get.id'));
        if(!$id) $this->error('数据有误，请刷新重试！');
        $model = M('Product');
        $where['id'] = $id;
        $ret = $model->where($where)->find();
        if(!$ret) $this->error('数据有误，请刷新重试！');
        $to = $ret['status'] ? 0 : 1;
        $message = $ret['status'] ? '已下架！' : '已上架！';
        $model->where(array('id'=>$id))->setField(array('status'=>$to));
        write_system_log();
        $this->success($message);
    }
    public function delete($id)
    {
        $model = M('Product');
        $result = $model->where('id='.$id)->setField('isdel',0);

        if($result){
            write_system_log();
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }
}