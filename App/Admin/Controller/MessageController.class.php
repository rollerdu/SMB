<?php
/**
 * Created by PhpStorm.
 * User: duyang
 * Date: 2017/3/28
 * Time: 13:29
 */

namespace Admin\Controller;


class MessageController extends BaseController{

    public function index(){
        $model = M("Message");
        $count  = $model->count();// 查询满足要求的总记录数
        $Page = new \Extend\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出
        $data = $model->limit($Page->firstRow.','.$Page->listRows)->order('id DESC')->select();
        $this->assign('data', $data);
        $this->assign('count', $count);
        $this->assign('page',$show);
        $this->display();
    }
    public function add(){
        if (IS_POST) {
            //如果用户提交数据
            $model = D("Message");
            if (!$model->create()) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($model->getError());
                exit();
            } else {
                $model->ctime = time();
                $model->etime = time();
                if ($id = $model->add()) {
                    //保存图片
                    $config = C('UploadImgConfig');
                    $config['savePath'] = 'image/';

                    $upload = new \Think\Upload($config);
                    $info = $upload->upload();
                    $info = $info['image'];
                    $img_name = trim($config['rootPath'].$info['savepath'].$info['savename'],'.');
                    $model->where(array('id'=>$id))->save(array('picurl'=>$img_name));
                    write_system_log();
                    $this->success("添加成功");
                } else {
                    $this->error("添加失败");
                }
            }
        }
        $this->display();
    }
    public function edit(){
        $id = intval(I("param.id"));
        $model = M('Message');
        $where['id'] = $id;
        $ret = $model->where($where)->find();
        if (IS_POST) {
            $data = I();
            //更新
            $data['etime'] = time();
            if ($model->where("id=$id")->save($data)) {
                if( !empty($_FILES['image']['name']) ){
                    @unlink($ret['picurl']);
                    //保存图片
                    $config = C('UploadImgConfig');
                    $config['savePath'] = 'image/';

                    $upload = new \Think\Upload($config);

                    $info = $upload->upload();
                    $info = $info['image'];
                    $img_name = trim($config['rootPath'].$info['savepath'].$info['savename'],'.');
                    $model->where(array('id'=>$id))->save(array('picurl'=>$img_name));
                }
                write_system_log();
                $this->success("更新成功");
            } else {
                $this->error("未做任何修改,更新失败");
            }
        }
        $this->assign('data',$ret);
        $this->display();
    }
    public function upstatus(){
        $id = intval(I('get.id'));
        if(!$id) $this->error('数据有误，请刷新重试！');
        $model = M('Message');
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
        $model = M('Message');
        $result = $model->where('id='.$id)->delete();

        if($result){
            write_system_log();
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }

}