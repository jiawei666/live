<?php

/**
 * Created by PhpStorm.
 * User: User
 * Date: 2016/9/26
 * Time: 15:45
 */

namespace Components\Repair\Model;

use Onemla\OnemlaHelper;
use Onemla\OnemlaModel;
use Onemla\OnemlaRequest;
use Org\Page\Page;
use Components\User\Model\UserModel;
use Components\Repair\Model\RepairRecordModel;

class RepairOrderModel extends OnemlaModel {

    /**
     * 工单列表
     */
    public function getList() {
        $repair_number = OnemlaRequest::getVar("repair_number"); //用户名称
        $type = OnemlaRequest::getVar("type"); //分类
        
        $where['repair_number'] = array("like", "%" . $repair_number . "%");
        if ($type) {
            $where['type'] = $type;
        }


        $count = $this->where($where)->count();
        $Page = new Page($count, $listRows = 10, array('repair_number' => $repair_number, 'type' => $type));
        $limit = '' . $Page->firstRow . ',' . $Page->listRows;

        $list = $this->alias("a")
                ->where($where)
                ->join("tb_user as u on u.id = a.user_id")
                ->field("u.user_name,a.*")
                ->limit($limit)
                ->order('create_time desc')
                ->select();
                $record_model = new RepairRecordModel();
                foreach ($list as $key => $value) {
                    $record_list = $record_model->get_list_by_id($value['id']);
                    $list[$key]['re_type'] = $record_list['type'];
                }
        $msg = array(
            'list' => $list,
            'count' => $count,
            'page_show' => $Page->AdminShow(),
            'repair_number' => $repair_number,
            'type' => $type,
        );
        return $msg;
    }
    
    /**
     * 
     * @return string
     * 会员工单列表
     */
    public function getMemberList() {
        $repair_number = OnemlaRequest::getVar("repair_number"); //用户名称
        $type = OnemlaRequest::getVar("type"); //分类
        
        $where['repair_number'] = array("like", "%" . $repair_number . "%");
        $where['user_id'] = OnemlaHelper::getUserId();
        if ($type) {
            $where['type'] = $type;
        }


        $count = $this->where($where)->count();
        $Page = new Page($count, $listRows = 10, array('repair_number' => $repair_number, 'type' => $type));
        $limit = '' . $Page->firstRow . ',' . $Page->listRows;

        $list = $this->alias("a")
                ->where($where)
                ->join("tb_user as u on u.id = a.user_id")
//                ->join("tb_repair_reply as rr on rr.repair_id = a.id")
                ->field("u.user_name,a.*")
                ->limit($limit)
                ->order('create_time desc')
                ->select();
                $record_model = new RepairRecordModel();
                foreach ($list as $key => $value) {
                    $record_list = $record_model->get_list_by_id($value['id']);
                    $list[$key]['re_type'] = $record_list['type'];
                }
        $msg = array(
            'list' => $list,
            'count' => $count,
            'page_show' => $Page->AdminShow(),
            'repair_number' => $repair_number,
            'type' => $type,
        );
        return $msg;
    }

    public function edit() {
        $info = OnemlaRequest::getVar("");
        $exts_limit=array(
            'exts' =>  array('jpg', 'gif', 'png', 'jpeg','txt','doc','docx','bmp'),
            'maxSize' => 2097152,
            );
        $file = upload_img($exts_limit, $rootPath = "Res/Uploads/repair");


        //上传错误则直接return错误信息
        if($file['error'] != '') {
           if($file['error'] == '没有文件被上传！') {
               //这个if语句将跳过
           } else {
               return  $msg = array("code" => "upload_error", "msg" => $file['error']);
               exit;
           }
        }
        $image = $file['info']['image']['savename'];

         //自动生成HD190316001编号
        $time = date('Ymd') ;
        $time = substr($time,2);
        $last_num = $this->getfield('max(repair_number)');
        if( $last_num == ''){
            $repair_num = $last_num = 'GD'.$time.'001';
        }else{
            $daytime = substr($last_num,2,6);
            if($time == $daytime){
                $number = substr($last_num,2)+1;
                $repair_num = 'GD'.$number ;
            } else{
                $repair_num = 'GD'.$time.'001';
            }
        }

        //新增
        if (empty($info['id'])) {
            $data = array(
                'user_id' => OnemlaHelper::getUserId(),
                'repair_number' => $repair_num,
                'type' => $info['type'],
                'introduction' =>html_entity_decode($info['introduction']),
                'image' => $image,
                'contact' => $info['contact'],
                'phone' => $info['phone'],
                'email' => $info['email'],
                'update_time' => date("Y-m-d H:i:s"),
                'create_time' => date("Y-m-d H:i:s"),
            );
            $id = $this->add($data);
            if ($id) {
                $msg = array("code" => "add_success", "msg" => "新增成功");
                return $msg;
            } else {
                $msg = array("code" => "add_error", "msg" => "新增失败");
                return $msg;
            }
        }
        //修改
        else {
            $rInfo = $this->where(array("id" => $info['id']))->find();
            $image_url = $image == '' ? '' : delAdminFile($rInfo['image'], $pathUrl = "Res/Uploads/repair/"); //删除本地图

            $data = array(
                'user_id' => OnemlaHelper::getUserId(),
                'repair_number' => $rInfo['repair_number'],
                'type' => $info['type'],
                'introduction' =>html_entity_decode($info['introduction']),
                'image' => $image == '' ? $rInfo['image'] : $image,
                'contact' => $info['contact'],
                'phone' => $info['phone'],
                'email' => $info['email'],
//                'status' => 1,//1.待审核  2.已审核  3.不通过
                'update_time' => date("Y-m-d H:i:s"),
                'create_time' => date("Y-m-d H:i:s"),
            );
            $id = $this->where(array("id" => $info['id']))->save($data);
            if ($id == "1") {
                unlink($image_url);
                $msg = array("code" => "edit_success", "msg" => "修改成功");
                return $msg;
            } else {
                $msg = array("code" => "edit_error", "msg" => "修改失败");
                return $msg;
            }
        }
    }

    public function delRepair($id){
        $recordModel = new RepairRecordModel();
        $recordModel->delRecord($id);//删除工单的聊天记录
        $file = $this->where(array('id'=>$id))->getField('image');
        $fileUrl = delAdminFile($file,'Res/Uploads/repair/');
        unlink($fileUrl);//删除工单附件
        $res = $this->where(array('id'=>$id))->delete();//删除工单
        return $res;
    }

    public function get_detail(){
        $where['a.id'] = OnemlaRequest::getVar("id");
        $list = $this->alias("a")->where($where)->join("left join tb_user as u on u.id = a.user_id")->field("u.user_name,a.*")->find();
        return $list;
    }

}
