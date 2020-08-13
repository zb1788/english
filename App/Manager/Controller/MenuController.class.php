<?php
namespace Manager\Controller;
use Think\Controller;
class MenuController extends CheckController {
    public function index(){
        $this->display();
    }

    public function getGrade(){
        $m = M('db_config.menu_dictionary','engs_');
        $data = $m->where('dictionary_code="grade"')->select();
        $this->ajaxReturn($data);
    }

    public function getSubject(){
        $m = M('menu_dictionary');
        $data = $m->where('dictionary_code="subject"')->select();
        $this->ajaxReturn($data);
    }

    /**
     * 分页展示课程信息
     */
    public function fenye(){
        $levelid=I('levelid/s','');
        $subject=I('subject/s','');
        $isrecommend=I('isrecommend/s','');
        $Model = M();
        // $Model = M('',null,'mysql://root:123456@127.0.0.1/db_config');
        $sql_where='where m.levelid="'.$levelid.'" and m.r_subject="'.$subject.'" and m.isrecommend='.$isrecommend;

        $sql1='select l.id,l.title,l.remark,t.sortid,(case when t.menuid is not null then 1 else 0 end) as status,t.isrecommend,t.id as bid,t.isnew from engs_menu_modules l left join (select m.menuid,m.sortid,m.isrecommend,m.id,m.isnew from engs_tj_menu_config m '.$sql_where.') t on l.id = t.menuid where l.r_subject_recommend="'.$subject.'"';
        $sql_order=' order by l.id ';

        $data=$Model->query($sql1.$sql_order);
        $this->ajaxReturn($data);
    }

    public function ispad(){
        $bid = I('bid/d',0);
        $ispad = I('ispad/d',0);

        $Model = M('',null,'mysql://vcom:yjt_yyl20160309@127.0.0.1/db_config');
        // $Model = M('',null,'mysql://root:123456@127.0.0.1/db_config');
        $sql = 'update engs_menu_config set isrecommend="'.$ispad.'" where id="'.$bid.'"';
        $Model->execute($sql);
    }

    public function delModel(){
        $bid = I('id/d',0);
        $Model = M('',null,'mysql://vcom:yjt_yyl20160309@127.0.0.1/db_config');
        // $Model = M('',null,'mysql://root:123456@127.0.0.1/db_config');

        $sql1 = 'delete from engs_menu_modules where id="'.$bid.'";';
        $sql2 = 'delete from engs_menu_config where menuid="'.$bid.'"';

        $Model->execute($sql1);
        $Model->execute($sql2);
    }

    public function getAllModels(){
        $Model = M('',null,'mysql://vcom:yjt_yyl20160309@127.0.0.1/db_config');
        // $Model = M('',null,'mysql://root:123456@127.0.0.1/db_config');
        $sql = 'select * from engs_menu_modules order by id asc';
        $data = $Model->query($sql);
        $this->ajaxReturn($data);
    }

    public function edit(){
        $type = I('type/s','');
        $id = I('bid/d',0);

        $this->assign('types',$type);
        $this->assign('bid',$id);
        $this->display();
    }

    public function getModelInfo(){
        $bid = I('bid/d',0);
        $Model = M('',null,'mysql://vcom:yjt_yyl20160309@127.0.0.1/db_config');
        // $Model = M('',null,'mysql://root:123456@127.0.0.1/db_config');

        $sql = 'select * from engs_menu_modules where id="'.$bid.'"';
        $data = $Model->query($sql);
        $this->ajaxReturn($data);
    }


    public function addModelToDb(){
        $modelname = I('modelname/s','');
        $modelurl  = I('modelurl/s','');
        $modelstyle = I('modelstyle/s','');
        $modeltype = I('modeltype/d',0);
        $modelremark = I('modelremark/s','');

        $type = I('type/s','');
        $bid = I('bid/d',0);

        $Model = M('',null,'mysql://vcom:yjt_yyl20160309@127.0.0.1/db_config');
        // $Model = M('',null,'mysql://root:123456@127.0.0.1/db_config');


        if($type == 'add'){
            //添加
            $sql = 'INSERT INTO `db_config`.`engs_menu_modules` ( `title`, `url`, `pic`, `remark`, `style`, `typeid`, `sortid`, `type`) VALUES ( "'.$modelname.'", "'.$modelurl.'", NULL, "'.$modelremark.'", "'.$modelstyle.'", "0", NULL, "'.$modeltype.'");';
            $Model->execute($sql);
        }else{
            //修改
            $sql = 'UPDATE `db_config`.`engs_menu_modules` set title="'.$modelname.'" , url="'.$modelurl.'", remark="'.$modelremark.'", style="'.$modelstyle.'" , type="'.$modeltype.'" where id="'.$bid.'"';
            $Model->execute($sql);
        }
        echo 'ok';
    }

    public function addConfig(){
        $levelid=I('levelid/d',0);
        $subject=I('subject/s','');
        $isrecommend=I('isrecommend/d',0);
        $strjson = I('strjson/s','');
        $strjson = str_replace('&quot;', '"', $strjson);//替换引号
        $Arr =  json_decode($strjson,true);
        // var_dump($Arr);exit;

        $m = M('tj_menu_config');

        $m->where('levelid="%d" and r_subject="%s" and isrecommend="%d"',$levelid,$subject,$isrecommend)->delete();
        foreach($Arr as $v){
            $v['levelid'] = $levelid;
            $v['r_subject'] = $subject;
            $v['isrecommend'] = $isrecommend;
            $m->add($v);
        }

    }


    public function dodata(){
            $grade=array("0001","0002","0003","0004","0005","0006","0007","0008","0009","0010");
            $grades=array("一年级","二年级","三年级","四年级","五年级","六年级","七年级","八年级","九年级","高中");
            $subject=array("0001","0003","0002","0004","0005","0010","0011","0012","0013","0009");
            $subjects=array("语文","英语","数学","物理","化学","生物","地理","政治","历史","品德");
            $styles=array("bGreen radius8","bYellow radius8","bZi radius8","bGreen radius8","bYellow radius8","bZi radius8","bGreen radius8","bYellow radius8","bZi radius8","bGreen radius8");
            $arr=array();
            for($i=0;$i<10;$i++){
                $temp=array();
                $temp["gradeid"]=$grade[$i];
                $temp["title"]=$grades[$i];
                $temp["subjects"]=array();
                foreach($subject as $key=>$value){
                    $subjectarr=array();
                    $subjectarr["subjectid"]=$value;
                    $subjectarr["title"]=$subjects[$key];
                    $subjectarr["style"]=$styles[$key];
                    $sql="SELECT * FROM engs_menu_config LEFT JOIN engs_menu_modules ON engs_menu_config.`menuid`=engs_menu_modules.`id` WHERE engs_menu_config.`r_grade`= '%s' AND engs_menu_config.`r_subject`='%s' and engs_menu_modules.url is not null ORDER BY engs_menu_config.sortid";
                    $Model = M('',null,'mysql://vcom:yjt_yyl20160309@127.0.0.1/db_config');
                    $rs=$Model->query($sql,$grade[$i],$value);
                    $subjectarr["modules"]=$rs;
                    array_push($temp["subjects"], $subjectarr);
                }
                array_push($arr,$temp);
            }
            $this->ajaxReturn($arr);
        }


    public function getModules(){
        $m = M('db_config.menu_modules','engs_');
        $data = $m->select();
        $this->ajaxReturn($data);
    }







}
