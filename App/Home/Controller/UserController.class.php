<?php
/**
 * Created by PhpStorm.
 * User: duyang
 * Date: 2017/4/13
 * Time: 14:15
 */

namespace Home\Controller;


class UserController extends BaseController{

    public $pageSize = 15;
    public function getUserInfo(){
        $user_id = I("param.user_id",0,'intval');
        if($this->Checkticket($user_id)){
            $model = D("User");
            $data = $model->getUserInfo($user_id);

            $this->ajaxReturn(array('msg'=>"请求成功!",'code'=>"0",'data'=>$data));
        }
    }
    /**
     * Func:  用户修改密码接口
     * Param: password:密码
     */
    public function updateUserPassword(){
        $user_id = I("param.user_id",0,'intval');
        if($this->Checkticket($user_id)){
            $password    = I('password')   ? trim(I('password'))     :  ''; //密码
            $password1   = I('password1')   ? trim(I('password1'))     :  ''; //新密码
            $password2   = I('password2')   ? trim(I('password2'))     :  ''; //重复密码
            if(!$password || !$password1 || !$password2){
                $this->ajaxReturn(array('msg'=>"参数不能为空!",'code'=>"3005"));
            }
            if($password1 != $password2){
                $this->ajaxReturn(array('msg'=>"两次密码不一致!",'code'=>"3006"));
            }
            $model = D("User");
            $data = $model->getUserInfo($user_id);
            if(md5($password) != $data['password']){
                $this->ajaxReturn(array('msg'=>"原密码输入错误!",'code'=>"3007"));
            }
            $ret = M("User")->where(array('id'=>$user_id))->setField('password',md5($password1));
            if($ret||$ret == 0){
                $this->ajaxReturn(array('msg'=>"修改成功!",'code'=>"0"));
            }else{
                $this->ajaxReturn(array('msg'=>"修改失败!",'code'=>"3008"));
            }
        }

    }


    /**
     * Func:  更新用户(基础)信息
     * Param: userid:用户id,
     * Param: ticket验证是否登录
     */
    public function updateUserInfo()
    {
        $userid                   = I("param.user_id",0,'intval');
        $param['username']        = I('name')             ? trim(I('name'))             : ''; //昵称·
        $param['gender']          = I('gender')           ? trim(I('gender'))           : ''; //性别·
        $param['age']             = I('param.age',0,'intval');
        $param['provid']          = I('param.provid',0,'intval');
        $param['cityid']          = I('param.cityid',0,'intval');
        $param['address']    = I('address')     ? trim(I('address'))     : ''; //家庭地址·

        $arr = array_filter($param);
        if(!in_array($param['gender'],['男','女'])){
            $this->ajaxReturn(array('msg'=>"性别请输入男或女!",'code'=>"3019"));
        }
        if(count($arr) < 1){
            $this->ajaxReturn(array('msg'=>"参数不能为空!",'code'=>"3009"));
        }
        if(true == $this->Checkticket($userid)){
            $user = M('User');
            $user->where(array('id'=>$userid))->save($arr);
            $userInfo = D('User')->getUserInfo($userid,$type = 1);
            unset($userInfo['remark'],$userInfo['ctime'],$userInfo['etime']);
            $this->ajaxReturn(array('msg'=>"更新成功!",'code'=>"0",'data'=>$userInfo));
        }
    }

    /*
     * 我的订单
     * */
    public function getUserOrders(){
        $user_id = I("param.user_id",0,'intval');
        if($this->Checkticket($user_id)){
            $page = I("param.page",1,'intval');
            $model = M("Order");
            $limit = ($page-1)*$this->pageSize;
            $model->where(array('status'=>'unpay','ctime'=>array('lt',time()-C("ORDER_EFFECTIVE_TIME"))))->save(array('status'=>'timeout','etime'=>time()));
            $order = $model->where(array('userid'=>$user_id,'isdel'=>1))
                ->limit("$limit,$this->pageSize")
                ->select();
            if($order && is_array($order)){
                foreach ($order as $it) {
                    $trade[] = $it['trade_sn'];
                }
                $detail = M("OrderDetail")->where(array('trade_sn'=>array('in',$trade)))
                    ->field("id,trade_sn,productid,cate_id,title,intro,img,thumb_img,num detail_num,usage_count,money")
                    ->select();
                foreach($order as $key => $val){
                    foreach($detail as $v){
                        if($val['trade_sn'] == $v['trade_sn']){
                            $order[$key]['detail'][] = $v;
                        }
                    }
                }

            }
            $this->ajaxReturn(array('msg'=>'查询成功','code'=>"0",'data'=>$order ? $order : array()));

        }
    }
    /*
     * BMI肥胖指数测定
     * */
    public function setBmiInfo(){
        $user_id = I("param.user_id",0,'intval');
        if($this->Checkticket($user_id)){
            $weight = I("param.weight",0.00,"floatval");
            $height = I("param.height",0.00,"floatval");
            if(!$weight || !$height){
                $this->ajaxReturn(array('msg'=>"参数有误!",'code'=>"3001"));
            }
            $smi =  sprintf("%.2f",$weight/($height * $height));

            $add = D("UserBmi")->add(array('userid'=>$user_id,'height'=>$height,'weight'=>$weight,'bmi'=>$smi));
            if($add){
                $this->ajaxReturn(array('msg'=>"插入成功!",'code'=>"0"));
            }else{
                $this->ajaxReturn(array('msg'=>"插入失败!",'code'=>"3002"));
            }
        }
    }
    /*
     *ESS 嗜睡量表 日间多量表
     * */
    public function setEssInfo(){
        $user_id = I("param.user_id",0,'intval');
        if($this->Checkticket($user_id)){
            $question1 = I("param.question1",0,"intval");
            $question2 = I("param.question2",0,"intval");
            $question3 = I("param.question3",0,"intval");
            $question4 = I("param.question4",0,"intval");
            $question5 = I("param.question5",0,"intval");
            $question6 = I("param.question6",0,"intval");
            $question7 = I("param.question7",0,"intval");
            $question8 = I("param.question8",0,"intval");
            $ess       = I("param.ess");
            if(!$question1 || !$question2|| !$question3|| !$question4|| !$question5|| !$question6|| !$question7|| !$question8 ||!$ess){
                $this->ajaxReturn(array('msg'=>"参数有误!",'code'=>"3003"));
            }

            $add = D("UserEss")->add(
                array(
                    'userid'=>$user_id,
                    'question1'=>$question1,
                    'question2'=>$question2,
                    'question3'=>$question3,
                    'question4'=>$question4,
                    'question5'=>$question5,
                    'question6'=>$question6,
                    'question7'=>$question7,
                    'question8'=>$question8,
                    'ess'=>$ess));
            if($add){
                $this->ajaxReturn(array('msg'=>"插入成功!",'code'=>"0"));
            }else{
                $this->ajaxReturn(array('msg'=>"插入失败!",'code'=>"3004"));
            }
        }
    }

    /*
     * 获取默认收货地址
     * */
    public function getUserDefaultAddress(){
        $user_id = I("param.user_id",0,'intval');
        if($this->Checkticket($user_id)){
            $data = M("Address")->where(array('userid'=>$user_id))->order('is_use desc')->find();
            if($data){
                $this->ajaxReturn(array('msg'=>"查询成功!",'code'=>"0",'data'=>$data));
            }else{
                $this->ajaxReturn(array('msg'=>"插入失败!",'code'=>"3005"));
            }
        }
    }
    /*
     * 添加收货地址
     * */
    public function addUserAddress(){
        $user_id = I("param.user_id",0,'intval');
        if($this->Checkticket($user_id)){
            $model = M("Address");
            $count = $model->where(array('userid'=>$user_id))->count();
            if($count >= 20){
                $this->ajaxReturn(array('msg'=>"您的收货地址数量已超限!",'code'=>"3006"));
            }
            if($count == 0){
                $data['is_use'] = 1;
            }
            $data['truename']       = I("param.truename",'','trim');
            $data['userid']         = $user_id;
            $data['mobile']         = I("param.mobile",'','trim');
            $data['provid']         = I("param.provid",0,'intval');
            $data['cityid']         = I("param.cityid",0,'intval');
            $data['townid']         = I("param.townid",0,'intval');
            $data['address']        = I("param.address",'','trim');
            if(!$data['truename'] || !$data['mobile'] || !$data['provid'] || !$data['cityid'] || !$data['townid'] || !$data['address']){
                $this->ajaxReturn(array('msg'=>"请填全信息!",'code'=>"3007"));
            }
            $data['provname'] = M('Region')->where(array('region_id'=>$data['provid']))->getField('region_name');
            $data['cityname'] = M('Region')->where(array('region_id'=>$data['cityid']))->getField('region_name');
            $data['townname'] = M('Region')->where(array('region_id'=>$data['townid']))->getField('region_name');
            $data = $model->add($data);
            if($data){
                $this->ajaxReturn(array('msg'=>"插入成功!",'code'=>"0"));
            }else{
                $this->ajaxReturn(array('msg'=>"插入失败!",'code'=>"3008"));
            }
        }
    }
    /*
     * 获取收货地址列表
     * */
    public function getUserAddressList(){
        $user_id = I("param.user_id",0,'intval');
        if($this->Checkticket($user_id)) {
            $model = M("Address");
            $data = $model->where(array('userid'=>$user_id))->order('is_use desc,id desc')->select();
            if($data){
                $this->ajaxReturn(array('msg'=>"查询成功!",'code'=>"0",'data'=>$data));
            }else{
                $this->ajaxReturn(array('msg'=>"没有数据!",'code'=>"3009"));
            }
        }
    }
    /*
     * 删除收货地址
     * */
    public function deleteUserAddress(){
        $user_id = I("param.user_id",0,'intval');
        if($this->Checkticket($user_id)) {
            $id = I("param.id",0,'intval');
            if(!$id) $this->ajaxReturn(array('msg'=>"数据有误!",'code'=>"3010"));
            $model = M("Address");
            $data = $model->where(array('userid'=>$user_id,'id'=>$id))->delete();
            if($data){
                $this->ajaxReturn(array('msg'=>"删除成功!",'code'=>"0"));
            }else{
                $this->ajaxReturn(array('msg'=>"删除失败!",'code'=>"3011"));
            }
        }
    }
    /*
     * 设置默认地址
     * */
    public function setDefaultAddress(){
        $user_id = I("param.user_id",0,'intval');
        if($this->Checkticket($user_id)) {
            $id = I("param.id",0,'intval');
            if(!$id) $this->ajaxReturn(array('msg'=>"数据有误!",'code'=>"3012"));
            $model = M("Address");
            $model->where(array('userid'=>$user_id))->setField('is_use',0);
            $data = $model->where(array('userid'=>$user_id,'id'=>$id))->setField('is_use',1);
            if($data){
                $this->ajaxReturn(array('msg'=>"设置成功!",'code'=>"0"));
            }else{
                $this->ajaxReturn(array('msg'=>"设置失败!",'code'=>"3013"));
            }
        }
    }
    /*
     * 修改收货地址
     * */
    public function editUserAddress(){
        $user_id = I("param.user_id",0,'intval');
        $id      = I("param.id",0,'intval');
        if($this->Checkticket($user_id)){
            $model = M("Address");
            $data = $model->where(array('userid'=>$user_id,'id'=>$id))->find();
            if(!$data){
                $this->ajaxReturn(array('msg'=>"数据有误!",'code'=>"3014"));
            }
            $get['truename']       = I("param.truename",'','trim');
            $get['mobile']         = I("param.mobile",'','trim');
            $get['provid']         = I("param.provid",0,'intval');
            $get['cityid']         = I("param.cityid",0,'intval');
            $get['townid']         = I("param.townid",0,'intval');
            $get['address']        = I("param.address",'','trim');
            $arr = array_filter($get);
            if(count($arr) == 0){
                $this->ajaxReturn(array('msg'=>"无数据可修改!",'code'=>"3015"));
            }
            if($arr['provid']){
                $arr['provname'] = M('Region')->where(array('region_id'=>$arr['provid']))->getField('region_name');
            }
            if($arr['cityid']){
                $arr['cityname'] = M('Region')->where(array('region_id'=>$arr['cityid']))->getField('region_name');
            }
            if($arr['townid']){
                $arr['townname'] = M('Region')->where(array('region_id'=>$arr['townid']))->getField('region_name');
            }
            $ret = $model->where(array('userid'=>$user_id,'id'=>$id))->save($arr);
            if($ret || $ret == 0){
                $this->ajaxReturn(array('msg'=>"修改成功!",'code'=>"0"));
            }else{
                $this->ajaxReturn(array('msg'=>"修改失败!",'code'=>"3016"));
            }
        }
    }
    /*
     * 广告消息
     * */
    public function getMessageList(){
        $page = I("param.page",1,'intval');
        $limit = ($page-1)*$this->pageSize;
        $data = M('Message')->where(array('status'=>1))
            ->limit("$limit,$this->pageSize")
            ->select();
        if($data && is_array($data)){
            foreach($data as $key => $val){
                $data[$key]['picurl'] = C("SITE_URL").$val['picurl'];
            }

        }
        $this->ajaxReturn(array('msg'=>"查询成功!",'code'=>"0",'data'=>$data ? $data : []));


    }
    /*
     * 添加、获取用户胸带编号
     * */
    public function getSetUserGirdleSn()
    {
        $user_id = I("param.user_id", 0, 'intval');
        if ($this->Checkticket($user_id)) {
            $model = M("GirdleSn");
            if(IS_POST){
                $number = I("post.number",'','trim');
                if(!$number){
                    $this->ajaxReturn(array('msg'=>"胸带编码有误!",'code'=>"3021"));
                }
                $check = $model->where(array('number'=>$number,'userid'=>$user_id))->find();
                if($check){
                    $this->ajaxReturn(array('msg'=>"该胸带已添加!",'code'=>"3022"));
                }
                $ret = $model->add(array('userid'=>$user_id,'number'=>$number));
                if($ret){
                    $this->ajaxReturn(array('msg'=>"添加成功!",'code'=>"0"));
                }else{
                    $this->ajaxReturn(array('msg'=>"添加失败!",'code'=>"3023"));
                }
            }
            $data = $model->where(array('userid'=>$user_id))->select();
            if($data && is_array($data)){
                $this->ajaxReturn(array('msg'=>"查询成功!",'code'=>"0",'data'=>$data));
            }else{
                $this->ajaxReturn(array('msg'=>"暂无数据!",'code'=>"3020"));
            }
        }
    }

}