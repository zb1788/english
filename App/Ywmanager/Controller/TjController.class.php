<?php
namespace Ywmanager\Controller;
use Think\Controller;
class TjController extends Controller {
    public function module(){
        $this->display();
    }


    public function getData(){
        $area = I('area/s','');
        $grade = I('grade/s','');
        $term = I('term/s','');
        $subject = I('subject/s','');
        $version = I('version/s','');
        $beg = I('beg/s','');
        $end = I('end/s','');


        if($area == 'tj'){
            $Model=M('',null,'mysql://enjsbvcom:gT_Q_qMbiVR9eQGp@192.168.109.245/db_english');
        }else if($area == 'yc'){
           // $Model=M('',null,'mysql://envcom:ebbjKT425K2cdac_@10.181.155.238/db_english');
        }else if($area == 'jx'){
            //$Model=M('',null,'mysql://envcom:ebbjKT425K2cdac_@117.169.19.236/db_english');
        }else{
            exit();
        }

        $sql_where = ' where ';
        if($grade == '0000'){
            $sql_where .= '';
        }else{
            $sql_where .= ' gradecode= "'.$grade.'" and ';
        }
        if($term == '0000'){
            $sql_where .= '';
        }else{
            $sql_where .= '';
        }
        if($subject == '0000'){
            $sql_where .= ' subjectid<>"0000" and ';
        }else if($subject <>'0002'){
            $sql_where .= ' subjectid= "'.$subject.'" and moduleid<>13 and ';
        }else{
            $sql_where .= ' subjectid= "'.$subject.'" and';
        }

        if($beg!=''&&$end!=''){
            $sql_where .= ' datetime>="'.$beg.'" and datetime<="'.$end.'" and';

        }
        if($beg==''&&$end!=''){
            $sql_where .= ' datetime<="'.$end.'" and';
        }
        if($beg!=''&&$end==''){
            $sql_where .= ' datetime>="'.$beg.'" and';
        }
        $sql_where = trim(trim($sql_where),'and');


        $sql = 'SELECT
                    DISTINCT engs_menu_modules.id,engs_menu_modules.`title`,
                    USER .`count`,USER.subjectid
                FROM
                    engs_menu_config
                LEFT JOIN engs_menu_modules ON engs_menu_config.`menuid` = engs_menu_modules.id
                LEFT JOIN (
                    SELECT
                        moduleid,(SELECT  r_subject FROM engs_menu_config WHERE menuid=engs_user_modules_unit_log.moduleid LIMIT 1) subjectid,
                        COUNT(*) AS COUNT
                    FROM
                        engs_user_modules_unit_log '.$sql_where.'
                    GROUP BY
                        moduleid
                ) AS USER ON USER .`moduleid` = engs_menu_modules.id
                WHERE
                    engs_menu_config.`ifuser` = 1
                AND engs_menu_config.`id` IS NOT NULL
                AND engs_menu_modules.id IS NOT NULL
                AND engs_menu_modules.url IS NOT NULL
                ORDER BY
                    engs_menu_modules.id';
                     // echo $sql;


        $sql1 = 'SELECT
                    DISTINCT engs_menu_modules.id, engs_menu_modules.`title`,
                    USER .`count`,USER.subjectid
                FROM
                    engs_menu_config
                LEFT JOIN engs_menu_modules ON engs_menu_config.`menuid` = engs_menu_modules.id
                LEFT JOIN (
                    SELECT
                        moduleid,(SELECT  r_subject FROM engs_menu_config WHERE menuid=engs_user_modules_unit_log.moduleid LIMIT 1) subjectid,
                        COUNT(DISTINCT username) AS COUNT
                    FROM
                        engs_user_modules_unit_log '.$sql_where.'
                    GROUP BY
                        moduleid
                ) AS USER ON USER .`moduleid` = engs_menu_modules.id
                WHERE
                    engs_menu_config.`ifuser` = 1
                AND engs_menu_config.`id` IS NOT NULL
                AND engs_menu_modules.id IS NOT NULL
                AND engs_menu_modules.url IS NOT NULL
                ORDER BY
                    engs_menu_modules.id';
                     // echo $sql1;exit;

        $data = $Model->query($sql);//总访问次数
        $data1 = $Model->query($sql1);//总访问人数

        $data2 = array();//平均每人访问次数

        $total = 0;//总访问次数
        $totalPeople = 0;//总访问人数

        foreach($data as $key=>$v){
            if($v['title']=='快乐学拼音'||$v['title']=='字母乐园'||$v['title']=='快乐背古诗'){
                unset($data[$key]);
                unset($data1[$key]);
                continue;
            }
            if($v['count'] == null){
                unset($data[$key]);
                unset($data1[$key]);
            }else{
                $format_num = sprintf("%.2f",$v['count']/$data1[$key]['count']);
                array_push($data2,$format_num);
            }
            // $total = $total + $v['count'];
            // $totalPeople = $totalPeople + $data1[$key]['count'];

        }

        $sql_total = 'select count(id) as c, count(distinct username) as p from engs_user_modules_unit_log '.$sql_where;
       // echo $sql1;exit;
        $data_total = $Model->query($sql_total);


        $info = array();
        $info['count'] = $data;
        $info['people'] = $data1;
        $info['average'] = $data2;
        $info['total'] = $data_total[0]['c'];
        $info['totalPeople'] = $data_total[0]['p'];

        $this->ajaxReturn($info);
    }



    public function getLogByDay(){
        $area = I('area/s','');
        $beg = I('beg/s','');
        $end = I('end/s','');
        $type = I('type/d',0);//1:月，2：日
        if($area == 'tj'){
            $Model=M('',null,'mysql://enjsbvcom:gT_Q_qMbiVR9eQGp@192.168.117.104/db_english');
        }else if($area == 'yc'){
            $Model=M('',null,'mysql://envcom:ebbjKT425K2cdac_@10.181.155.238/db_english');
        }else if($area == 'jx'){
            $Model=M('',null,'mysql://envcom:ebbjKT425K2cdac_@117.169.19.236/db_english');
        }else{
            exit();
        }

        if($type == 2){
            // if(date('d',time())<29){

            // }else{

            // }

            if($beg ==''||$end==''){
                $time = date('Y-m-d H:i:s',strtotime('-1 month'));
                $where = ' where m.datetime>"'.$time.'" ';
            }else{
                $where = ' where m.datetime>="'.$beg.'" and m.datetime<="'.$end.'" ';
            }

            $sql = 'SELECT count(m.username) as count,date(m.datetime) as date FROM engs_user_modules_unit_log m '.$where.' GROUP BY date(m.datetime);';
            $sql_p = 'SELECT count(DISTINCT m.username) as count,date(m.datetime) as date FROM engs_user_modules_unit_log m '.$where.' GROUP BY date(m.datetime);';
        }else if($type == 1){
            $sql = 'SELECT count(m.username) as count,DATE_FORMAT(m.datetime,"%Y-%m") as date FROM engs_user_modules_unit_log m GROUP BY DATE_FORMAT(m.datetime,"%Y-%m");';
            $sql_p = 'SELECT count(DISTINCT m.username) as count,DATE_FORMAT(m.datetime,"%Y-%m") as date FROM engs_user_modules_unit_log m GROUP BY DATE_FORMAT(m.datetime,"%Y-%m");';
        }else if($type == 3){
            if($beg == ''){
                $time = date('Y-m-d',time());
            }else{
                $time = date('Y-m-d',strtotime($beg));
            }

            $sql = 'SELECT count(m.username) as count,DATE_FORMAT(m.datetime,"%Y-%m-%d %H") as date FROM engs_user_modules_unit_log m where m.datetime like "'.$time.'%"  GROUP BY DATE_FORMAT(m.datetime,"%Y-%m-%d %H");';
            $sql_p = 'SELECT count(DISTINCT m.username) as count,DATE_FORMAT(m.datetime,"%Y-%m-%d %H") as date FROM engs_user_modules_unit_log m where  m.datetime like "'.$time.'%"  GROUP BY DATE_FORMAT(m.datetime,"%Y-%m-%d %H");';
        }


        $data = $Model->query($sql);//每天/月访问次数
        $data_p = $Model->query($sql_p);//每天/月访问人数

        $info['count'] = $data;
        $info['people'] = $data_p;

        $this->ajaxReturn($info);


    }


    /**
    *按年级统计
    */
    public function getDataByGrade(){
        $area = I('area/s','');
        $beg = I('beg/s','');
        $end = I('end/s','');

        $where = ' ';
        if($beg !== ''){
            $where .= ' and m.datetime>="'.$beg.'"';
        }

        if($end !== ''){
            $where .= ' and m.datetime<="'.$end.'"';
        }
        if($area == 'tj'){
            $Model=M('',null,'mysql://enjsbvcom:gT_Q_qMbiVR9eQGp@192.168.117.104/db_english');
        }else if($area == 'yc'){
            $Model=M('',null,'mysql://envcom:ebbjKT425K2cdac_@10.181.155.238/db_english');
        }else if($area == 'jx'){
            $Model=M('',null,'mysql://envcom:ebbjKT425K2cdac_@117.169.19.236/db_english');
        }else{
            exit();
        }
        $sql = 'select m.gradecode,count(m.gradecode) as count from engs_user_modules_unit_log m WHERE m.gradecode is not null and m.gradecode<>"undefined" '.$where.' GROUP BY m.gradecode;';
        $data = $Model->query($sql);
        $this->ajaxReturn($data);
    }


    public function getArea(){
        $area = I('area/s','');

        if($area == 'tj'){
            $Model=M('',null,'mysql://enjsbvcom:gT_Q_qMbiVR9eQGp@192.168.117.104/db_english');
        }else if($area == 'yc'){
            $Model=M('',null,'mysql://envcom:ebbjKT425K2cdac_@10.181.155.238/db_english');
        }else if($area == 'jx'){
            $Model=M('',null,'mysql://envcom:ebbjKT425K2cdac_@117.169.19.236/db_english');
        }else{
            exit();
        }

        $sql = "SELECT DISTINCT areacode,areaname FROM engs_yjt_url ORDER BY (replace(areacode,'.','')+0);";
        $data = $Model->query($sql);
        $this->ajaxReturn($data);
    }

    public function getModule(){
        $area = I('area/s','');

        if($area == 'tj'){
            $Model=M('',null,'mysql://enjsbvcom:gT_Q_qMbiVR9eQGp@192.168.117.104/db_english');
        }else if($area == 'yc'){
            $Model=M('',null,'mysql://envcom:ebbjKT425K2cdac_@10.181.155.238/db_english');
        }else if($area == 'jx'){
            $Model=M('',null,'mysql://envcom:ebbjKT425K2cdac_@117.169.19.236/db_english');
        }else{
            exit();
        }

        $sql = 'SELECT id,title FROM engs_menu_modules ';
        $data = $Model->query($sql);
        $this->ajaxReturn($data);
    }

    public function getLogByDayByModule(){
        $area = I('area/s','');
        $localAreaCode = I('localAreaCode/s','');
        $moduleid = I('module/d',0);
        $beg = I('beg/s','');
        $end = I('end/s','');
        $type = I('type/d',0);//1:月，2：日
        if($area == 'tj'){
            $Model=M('',null,'mysql://enjsbvcom:gT_Q_qMbiVR9eQGp@192.168.117.104/db_english');
        }else if($area == 'yc'){
            $Model=M('',null,'mysql://envcom:ebbjKT425K2cdac_@10.181.155.238/db_english');
        }else if($area == 'jx'){
            $Model=M('',null,'mysql://envcom:ebbjKT425K2cdac_@117.169.19.236/db_english');
        }else{
            exit();
        }

        if($type == 2){
            // if(date('d',time())<29){

            // }else{

            // }

            $where = ' where m.moduleid="'.$moduleid.'" and ';
            if($beg ==''||$end==''){
                $time = date('Y-m-d H:i:s',strtotime('-1 month'));
                $where .= '  m.datetime>"'.$time.'" ';
            }else{
                $where .= '  m.datetime>="'.$beg.'" and m.datetime<="'.$end.'" ';
            }

            $where .= ' and m.localareacode="'.$localAreaCode.'" ';

            $sql = 'SELECT count(m.username) as count,date(m.datetime) as date FROM engs_user_modules_unit_log m '.$where.' GROUP BY date(m.datetime);';
            $sql_p = 'SELECT count(DISTINCT m.username) as count,date(m.datetime) as date FROM engs_user_modules_unit_log m '.$where.' GROUP BY date(m.datetime);';
        }else if($type == 1){
            $sql = 'SELECT count(m.username) as count,DATE_FORMAT(m.datetime,"%Y-%m") as date FROM engs_user_modules_unit_log m GROUP BY DATE_FORMAT(m.datetime,"%Y-%m");';
            $sql_p = 'SELECT count(DISTINCT m.username) as count,DATE_FORMAT(m.datetime,"%Y-%m") as date FROM engs_user_modules_unit_log m GROUP BY DATE_FORMAT(m.datetime,"%Y-%m");';
        }else if($type == 3){
            if($beg == ''){
                $time = date('Y-m-d',time());
            }else{
                $time = date('Y-m-d',strtotime($beg));
            }

            $sql = 'SELECT count(m.username) as count,DATE_FORMAT(m.datetime,"%Y-%m-%d %H") as date FROM engs_user_modules_unit_log m where m.datetime like "'.$time.'%"  GROUP BY DATE_FORMAT(m.datetime,"%Y-%m-%d %H");';
            $sql_p = 'SELECT count(DISTINCT m.username) as count,DATE_FORMAT(m.datetime,"%Y-%m-%d %H") as date FROM engs_user_modules_unit_log m where  m.datetime like "'.$time.'%"  GROUP BY DATE_FORMAT(m.datetime,"%Y-%m-%d %H");';
        }


        $data = $Model->query($sql);//每天/月访问次数
        $data_p = $Model->query($sql_p);//每天/月访问人数

        $info['count'] = $data;
        $info['people'] = $data_p;

        $this->ajaxReturn($info);


    }









}
