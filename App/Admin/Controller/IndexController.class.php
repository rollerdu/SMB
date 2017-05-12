<?php
namespace Admin\Controller;
use Admin\Controller;

class IndexController extends BaseController{
    

    public function index(){
//var_dump($_COOKIE);exit;
        $this->display();
    }


    public function welcome(){
        $role_id = get_admin_role_id();
        $adminid = get_current_admin_id();
        $where = " 1=1 ";
        if($role_id == 1){
            $agent_ids = M("Agent")->where(array('managerid'=>$adminid))->getField('id',true);
            $str_agent = implode(',',$agent_ids);
            $doctor_ids = M("Doctor")->where(array('agentid'=>array('in',$str_agent)))->getField('id',true);
//            $where['u.doctorid'] = array('in',implode(',',$doctor_ids));
            $where = "`u.doctorid` in (implode(',',$doctor_ids)) ";
        }elseif($role_id == 2){
            $doctor_ids = M("Doctor")->where(array('agentid'=>$adminid))->getField('id',true);
//            $where['u.doctorid'] = array('in',implode(',',$doctor_ids));
            $where = "`u.doctorid` in (implode(',',$doctor_ids)) ";
        }elseif($role_id == 3){
//            $where['u.doctorid'] = $adminid;
            $where = "u.doctorid = $adminid ";
        }
//        $where['u.status'] = 1;
        $where .= " and u.status = 1";
        //性别人数统计
        $model = M("User");
        $gender_chart = $model->alias('u')->where($where)->group('u.gender')->field('u.gender,count(*) count')->select();
        foreach($gender_chart as $k => $v){
            $gender[] = array('name'=>$v['gender']."(".$v['count']."人)",'y'=>intval($v['count']));
        }

        //各省份患者统计
        $prov_chart = $model->alias('u')->where($where)->group('u.provid')->join(C('DB_PREFIX').'region r on r.region_id=u.provid','LEFT')
            ->field("count(*) count,r.region_name")
            ->select();
        foreach($prov_chart as $ke => $va){
            if($va['region_name'] == '') $va['region_name'] = "未分配";
            $prov[] = [$va['region_name'],intval($va['count'])];
        }

        //各年龄段患者统计

        $age_sql = "select nnd,count(*) count from (select case when age<20 then '0-20' when age>=20 and age<30 then '20-30'
                    when age>=30 and age<40 then '30-40' when age>=40 and age<50 then '40-50'  when age>=50 and age<60 then '50-60'
                    when age>=60 and age<70 then '60-70' when age>=70 and age<80 then '70-80' when age>=80 and age<90 then '80-90'
                    when age>=90 and age<100 then '90-100' when age>=100 then '大于100' end as nnd,id from j_user u WHERE $where)
                     a group by nnd";

        $age_chart = M()->query($age_sql);
        foreach($age_chart as $kk => $vv){
            $age[] = array('name'=>$vv['nnd']."岁(".$vv['count']."人)",'y'=>intval($vv['count']));
        }


        $this->assign('age',json_encode($age));
        $this->assign('prov',json_encode($prov));
        $this->assign('gender_chart',json_encode($gender));
        $this->display();
    }
    /*
     * 日志列表
     * */
    public function logs(){
        $model = M('Log');
        $role_id = get_admin_role_id();
        $adminid = get_current_admin_id();
        if($role_id == 1){
            //区域经理查看配属代理商和医生的日志
            $agent_ids = M("Agent")->where(array('managerid'=>$adminid))->getField('id',true);
            $str_agent = implode(',',$agent_ids);
            $doctor_ids = M("Doctor")->where(array('agentid'=>array('in',$str_agent)))->getField('id',true);
            $str_doctor = implode(',',$doctor_ids);

            $where_1['roleid'] = 2;
            $where_1['userid'] = array('in',$str_agent);
            $where_2['roleid'] = 3;
            $where_2['userid'] = array('in',$str_doctor);
            $where['_complex'] = array(
                $where_1,
                $where_2,
                '_logic' => 'or'
            );

        }elseif($role_id == 2){
            //代理商有权查看撇书医生的日志
            $doctor_ids = M("Doctor")->where(array('agentid'=>$adminid))->getField('id',true);
            $where['userid'] = array('in',implode(',',$doctor_ids));
            $where['roleid'] = 3;
        }elseif($role_id == 4){
            //超级管理员能查看全部
            $where = "1=1";

        }elseif($role_id > 4){
            //普通管理员有权查看自己的日志
            $where['userid'] = $adminid;

        }else{
            $this->error("您无权限查看日志");
        }
        $count  = $model->where($where)->count();// 查询满足要求的总记录数
        $Page = new \Extend\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出
        $data = $model->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('id desc')->select();
//        echo ($model->getLastSql());exit;
        $this->assign('data', $data);
        $this->assign('page',$show);
        $this->display();
    }

    /**
     * 广告图列表
     */
    public function adpicture(){
        //获取结果
        $list  	= M('advertise')->field('id,title,picurl,etime,status')->order('id asc')->select();
        $this->assign('list',$list);
        $this->display();
    }
    /**
     * 广告图添加
     */
    public function adadd(){

        $model 	= M('advertise');
        $id 	= I('param.id',0,'intval');

        //查询编辑信息
        $detail = $model->where(array('id'=>$id))->find();
        if (!$detail){
            $this->error("请勿非法操作");
        }
        if (IS_POST){
            //过滤参数
            $data['title'] 		= I('post.title');
            $data['status'] 		= intval(I('post.status')) ? 1 : 0;
            $data['description'] = I("post.description",'','trim');
            //必填验证
            if (!$data['title']){
                $this->error('请填写广告图名称');
            }
            //更新
            $result = $model->where(array('id'=>$id))->save($data);
            if ($result !== false || 0 !== $result ) {
                //图片
                if (!empty($_FILES['image']['name'])) {
                    //保存图片
                    $config = C('UploadImgConfig');
                    $config['savePath'] = 'image/';

                    $upload = new \Think\Upload($config);

                    $info = $upload->upload();
                    $info = $info['image'];
//                    var_dump($info);exit;
                    if (!$info) {
                         $this->error("图片保存失败");
                    } else {
                        $img_name = $config['rootPath'] . $info['savepath'] . $info['savename'];
                        $exname = substr($info['savename'], 0, strpos($info['savename'], '.'));

                        //生成缩略图
                        $img= new \Think\Image();

                        $img->open($img_name);

                        $thumb_name = $config['rootPath'].$info['savepath'].$exname.$config['width_v'].'-'.$config['height_v'].'.'.$info['ext'];

                        $img->thumb($config['width_v'], $config['height_v'])->save($thumb_name);

                        $imgpath = array(
                            'picurl' => trim($img_name, '.'),
//                            'thumb_img' =>trim($thumb_name,'.')
                        );
                        $model->where(array('id' => $id))->save($imgpath);
                    }
                }
                write_system_log();
                $this->success('编辑成功');
                exit;
            }else{
                $this->error('编辑失败');
            }

        }
        $this->assign('detail',$detail);
        $this->display();
    }
    /**
     * 广告上下架
     */
    public function adstatus(){
        $model 	= M('advertise');
        $id 	= I('get.id',0,'intval');
        $to 	= I('get.to',0,'intval');
        $to 	= $to ? 1 : 0;

        $wh['id'] = $id;
        $check = $model->where($wh)->find();
        if($check){
            $model->where(array('id'=>$id))->data(array('status'=>$to))->save();
        }
            write_system_log();
        $this->success('操作成功');
    }
    public function girdle(){
        //处理上传
        $detail = M('setting')->where(array('id'=>1))->find();
        if(IS_POST){
            if(!$_FILES['hex']['tmp_name']){
                $this->error('请选着上传的文件！');
            }
            $upload 			= new \Think\Upload();
            $upload->maxSize 	= 30485760;
            $upload->exts		= array('hex');
            $upload->rootPath 	= './Attachment/HEX/';
            $upload->savePath   = '/';
            $upload->subName 	= array('date', 'Ymd');
            //上传用户端信息
            $uinfo 			= $upload->upload();
            if (!$uinfo){
                $this->error($upload->getError());
            }
//				$data['ufileurl'] = $uinfo['savepath'].$uinfo['savename'];
            $data['ufileurl'] =  '/Attachment/HEX/'.date('Ymd',time()).'/'.$uinfo['hex']['savename'];
            $data['ufilemd5'] = md5_file('./'.$data['ufileurl']);
            if($detail['ufilemd5']==$data['ufilemd5']){
                $this->error('用户端上传文件与当前版本相同');
            }
            $data['uversion'] = strval($detail['uversion']+0.1);
            //更新信息
            $data['etime'] 		= time();
            M('setting')->where(array('id'=>$detail['id']))->data($data)->save();
            $detail = M('setting')->where(array('id'=>1))->find();
            $this->success('更新成功');
        }
        $this->assign('detail',$detail);
        $this->display();
    }
    public function company(){
        $data = M("Company")->find(1);
        if(IS_POST){
            $_POST = I("POST.");
            $ret = M("Company")->where("id=$data[id]")->save(array('content'=>json_encode($_POST)));
            if($ret){
                $this->success('修改成功');
            }else{
                $this->error('修改失败或未做任何修改');
            }
        }
        $this->assign('data',json_decode($data['content'],true));
        $this->display();

    }
}
