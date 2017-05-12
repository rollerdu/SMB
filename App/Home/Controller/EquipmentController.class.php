<?php
/**
 * Created by PhpStorm.
 * User: duyang
 * Date: 2017/4/11
 * Time: 10:19
 */

namespace Home\Controller;


class EquipmentController extends BaseController{
    public $wsdlUrl = "https://dcwebservices.smartlinkwarehouse.com/smartcodeservice.asmx?WSDL";
    /*
     *呼吸机smartCode码解析
     *
     * */
    public function smartCode(){
        $user_id            = I('user_id')   ? intval(I('user_id'))  : ''; //用户id
        $codeType           = intval(I("codeType"));
        $smartCode          = trim(I("param.smartCode"));
        $serialCheck        = I("serialCheck") ? true : false;
        $serialNumber       = trim(I("serialNumber"));

        if(!$user_id || !$smartCode || !in_array($codeType,array(0,1,2,3))){
            $this->ajaxReturn(array('msg'=>"参数不能为空!",'code'=>"2001"));
        }
        if($serialCheck && !$serialNumber){
            $this->ajaxReturn(array('msg'=>"参数有误!",'code'=>"2005"));
        }
        if($this->Checkticket($user_id)){
            $soap = new \SoapClient($this->wsdlUrl);
            $parseSmartCode = new \stdClass();
            $parseSmartCode->codeType = $codeType;
            $parseSmartCode->code = $smartCode;
            $parseSmartCode->serialCheck = $serialCheck;
            $parseSmartCode->serialNumber = $serialNumber;

            $scTransport = $soap->ParseSmartCode($parseSmartCode);
            $scTranData = $scTransport->ParseSmartCodeResult;
            if($scTranData->Errors != 0){
                $this->ajaxReturn(array('msg'=>"该smartcode码无效!",'code'=>"2002"));
            }
            $check = M("Smartcode")->where(array('userid'=>$user_id,'SmartCode'=>$scTranData->SmartCode))->find();
            if($check){
                $this->ajaxReturn(array('msg'=>"重复记录!",'code'=>"0",'data'=>$check));
            }
            $data['userid']                         = $user_id;
            $data['percentdaysatleastxhours']       = $scTranData->PercentDaysAtLeastXHours;
            $data['daycount']                       = $scTranData->DayCount;
            $data['daysatleastxhours']              = $scTranData->DaysAtLeastXHours;
            $data['percentilepressure95th']         = $scTranData->PercentilePressure95Th;
            $data['percentilepressure90th']         = $scTranData->PercentilePressure90Th;
            $data['ahi']                            = $scTranData->Ahi;
            $data['pressureplateautime']            = $scTranData->PressurePlateauTime;
            $data['leaktime']                       = $scTranData->LeakTime;
            $data['noi']                            = $scTranData->Nri;
            $data['epi']                            = $scTranData->Epi;
            $data['smartcode']                      = $scTranData->SmartCode;
            $data['percentilemachineinspiration']   = $scTranData->PercentileMachineInspiration;
            $data['percentilemachineexpiration']    = $scTranData->PercentileMachineExpiration;
            $data['adherencescore']                 = $scTranData->AdherenceScore;
            $data['whilebreathinghours']            = $scTranData->WhileBreathingHours;
            $data['minimumusethreshold']            = $scTranData->MinimumUseThreshold;
            $data['titrationmode']                  = $scTranData->TitrationMode;
            $ret = M("Smartcode")->add($data);
            if($ret){
                $this->ajaxReturn(array('msg'=>"请求成功!",'code'=>"0",'data'=>$data));
            }else{
                $this->ajaxReturn(array('msg'=>"操作失败!",'code'=>"2003"));
            }
        }

    }
    /*
     * 获取用户胸带使用次数
     * */
    public function get_gridle_times(){
        $user_id            = I('user_id')   ? intval(I('user_id'))  : ''; //用户id
        if($this->Checkticket($user_id)){
            $times = M("GirdleTime")->where(array('userid'=>$user_id))->getField('times');
            $times = $times > 1 ? $times : 0;
            $this->ajaxReturn(array('msg'=>"请求成功!",'code'=>"0",'data'=>$times));
        }
    }
    /*
     * 胸带使用次数清零
     * */
    public function clean_girdle_times(){
        $user_id            = I('user_id')   ? intval(I('user_id'))  : ''; //用户id
        if($this->Checkticket($user_id)){
            $times = M("GirdleTime")->where(array('userid'=>$user_id))->setField('times',0);
            $this->ajaxReturn(array('msg'=>"请求成功!",'code'=>"0"));
        }
    }
    /*
     * 胸带上传数据文件
     * */
    public function upload_girdle_data(){
        $user_id            = I('user_id')   ? intval(I('user_id'))  : ''; //用户id
        if($this->Checkticket($user_id)){
            if(!$_FILES['pic0']['tmp_name']){
                $this->ajaxReturn(array('msg'=>"请选着上传的文件!",'code'=>"2004"));
            }
            $upload 			= new \Think\Upload();
            $upload->maxSize 	= 30485760;
            $upload->exts		= array('txt');
            $upload->rootPath 	= './Attachment/Girdle/';
            $upload->savePath   = '';
            $upload->subName 	= array('date', 'Ymd');
            //上传用户端信息
            $uinfo 			= $upload->upload();
            if (!$uinfo){
                $this->ajaxReturn(array('msg'=>$upload->getError(),'code'=>"2005"));
            }
            foreach ($uinfo as $k => $v) {
                $voicePath[] = "/Attachment/Girdle/" . $v['savepath'] . $v['savename'];
            }
            $data['file_url'] = json_encode($voicePath);
            $data['userid']     = $user_id;
            $ret = M("GirdleFile")->add($data);
            if($ret){
                $this->ajaxReturn(array('msg'=>"上传成功!",'code'=>"0"),'JSON');
            }else{
                $this->ajaxReturn(array('msg'=>"上传失败!",'code'=>"2006"),'JSON');
            }
        }
    }

}