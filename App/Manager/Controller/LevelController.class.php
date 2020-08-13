<?php

namespace Manager\Controller;

use Think\Controller;

/**
 * 单词控制器
 *  
 * @author         gm 
 * @since          1.0 
 */
class LevelController extends Controller {

    
    public function index() {
    	$gameid=I("gameid");
    	$wordgame=M("word_game");
    	$rs=$wordgame->where("id=%d",$gameid)->find();
    	$this->assign("name",$rs["name"]);
    	$this->assign("gameid",$gameid);
    	$this->assign("peroid",$rs["peroid"]);
    	$this->assign("grades",$rs["grades"]);
        $this->display();
    }

    public function uploads(){
        $gameid=I("gameid");
        $this->assign('gameid',$gameid);
        $this->display();
    }
	
    public function synchronizationDataBase(){
	exec("/usr/local/etc/boz/dbsync.sh", $res);
	var_dump($res);
    }

    public function saveGamePic(){
        $gameid=I("gameid");
        $pic=I("pic");
        M("word_game")->where("id=%d",$gameid)->setField("pic",$pic);
    }

    //修改学段
    public function changePeroid(){
    	$gameid=I("gameid/d");
    	$peroid=I("peroid/d");
    	$wordgame=M("word_game");
    	$wordgame->where("id=%d",$gameid)->setField("peroid",$peroid);
    }

    //修改适配年级
    public function changeGrades(){
    	$gameid=I("gameid/d");
    	$grades=I("grades/s");
    	$wordgame=M("word_game");
    	$wordgame->where("id=%d",$gameid)->setField("grades",$grades);
    }

    //修改适配年级
    public function changeName(){
    	$gameid=I("gameid/d");
    	$name=I("name/s");
    	$wordgame=M("word_game");
    	$wordgame->where("id=%d",$gameid)->setField("name",$name);
    }
	
    public function publishGame(){
        $ispub=I("ispub");
        $id=I("id");
        M("word_game")->where("id=%d",$id)->setField("status",$ispub);
    }

     //文件下载
    public function downloaddemofile(){
        $filename="./uploads/game/demo/htdemo.xls";
        downfile($filename,"htdemo.xls");
    }

    //修改关卡名称
    public function changeGameLevelName(){
        $levelid=I("levelid/d");
        $name=I("name/s");
        $wordgamelevel=M("word_game_level");
        $wordgame=M("word_game");
        if(!empty($levelid)){
            $wordgamelevel->where("id=%d",$levelid)->setField("name",$name);
        }else{
            $gameid=I("gameid");
            $wordgamelevel->gameid=$gameid;
            $wordgamelevel->name=$name;
            $wordgamelevel->add();
            //修改game表中的关卡数量
            $sql="update engs_word_game set levelnum=levelnum+1 where id=%d";
            M()->execute($sql,$gameid);
        }
    }

    //修改游戏的题型
    public function changeGameQuestype(){
        $gameid=I("gameid/d");
        $questypes=I("questypes/s");
        $wordgame=M("word_game");
        $wordgame->where("id=%d",$gameid)->setField("questypes",$questypes);
    }
    //修改游戏的备选
    public function changeGameBackupQuestype(){
        $gameid=I("gameid/d");
        $questypes=I("questypes/s");
        $wordgame=M("word_game");
        $wordgame->where("id=%d",$gameid)->setField("backupquestypes",$questypes);
    }

    //修改单词个数
    public function changePerWordNum(){
        $gameid=I("gameid/d");
        $perwordnum=I("perwordnum/s");
        $wordgame=M("word_game");
        $wordgame->where("id=%d",$gameid)->setField("perwordnum",$perwordnum);
    }

    //获取试题列表
    public function getQuestionsList(){
    	$levelid=I("levelid/d");
    	$gamelevel=M("word_game_level");
    	$ret=array();
        $arr=array();
    	if(!empty($levelid)){
    		$result=$gamelevel->where("id=%d",$levelid)->find();
    		$arr=json_decode($result["questions"],true);
    	}
    	$this->ajaxReturn($arr);
    }

    //编辑模式
    public function getQuestionsEditList(){
        $levelid=I("levelid/d");
        $gamelevel=M("word_game_level");
        $ret=array();
        $arr=array();
        if(!empty($levelid)){
            $result=$gamelevel->where("id=%d",$levelid)->find();
            $arr=json_decode($result["questions"],true);
            $ret=array();
            foreach($arr as $key=>$value){
                $temp=array();
                $temp["id"]=$key;
                $temp["word"]=$value["word"];
                array_push($ret, $temp);
            }
            $arrs=sortt($ret,"word");
            $arrret=array();
            foreach($arrs as $key=>$value){
                $arrret[$value["id"]]=$arr[$value["id"]];
            }
            $arr=$arrret;
        }
        $this->ajaxReturn($arr);
    }

    //获取单词列表
    public function getLevelWordList(){
        $levelid=I("levelid/d");
        $gamelevel=M("word_game_level");
        $ret=array();
        if(!empty($levelid)){
            $result=$gamelevel->where("id=%d",$levelid)->find();
            $ret=json_decode($result["words"],true);
        }
        $this->ajaxReturn($ret);
    }

    //删除试题
    public function delLevelQuestion(){
        $levelid=I("levelid/d");
        $id=I("id");
        $levelrs=M("word_game_level")->where("id=%d",$levelid)->find();
        $questions=json_decode($levelrs["questions"],true);
        unset($questions[$id]);
        M("word_game_level")->questions=json_encode($questions);
        M("word_game_level")->quesnum=$levelrs["quesnum"]-1;
        M("word_game_level")->where("id=%d",$levelid)->save();
    }

    //刷新试题
    public function refreshQuestion(){
        $levelid=I("levelid/d");
    		$id=I("id");
        //查询这个单词的意思
        $levelrs=M("word_game_level")->where("id=%d",$levelid)->find();
        //查询闯关的学段
        $game=M("word_game")->where("id=%d",$levelrs["gameid"])->find();
        $peroid=$game["peroid"];
        $questions=json_decode($levelrs["questions"],true);
        $question=$questions[$id];
        $word=D("word");
        $wordid=$question["wordid"];
        $explainid=$question["explainid"];
        $questype=$question["questype"];
        $arr=$word->getReciteWordQuestion($questype,$wordid,$explainid,$peroid);
				$question["word"]=$arr["word"];
				$question["tncontent"]=$arr["tncontent"];
        $question["items"]=$arr["items"];
        $question["answer"]=$arr["answer"];
        $questions[$id]=$question;
        M("word_game_level")->where("id=%d",$levelid)->setField("questions",json_encode($questions));
    }

    //获取试题
    public function getQuestion(){
        $levelid=I("levelid");
        $id=I("id");
        $gamelevel=M("word_game_level");
        $result=$gamelevel->where("id=%d",$levelid)->find();
        $questions=json_decode($result["questions"]);
        $question=$questions->$id;
        $this->ajaxReturn($question);

    }

    //编辑试题
    public function editQuestion(){
    	$levelid=I("levelid");
    	$id=I("id");
        $explainid=I("explainid");
        $tncontent=I("tncontent");
        $analysis=I("analysis");
	$tncontent=htmlspecialchars_decode(I("tncontent"));
        $analysis=htmlspecialchars_decode(I("analysis"));
        $anaid=I("anaid");
        $title=I("title");
        $items = stripslashes(I("items"));
        $items = rtrim($items, '"');
        $items = ltrim($items, '"');
        $items = str_replace('&quot;', '"', $items);
        $items = json_decode($items);
        $questype=I("questype");
        //不同的题型更新的不相同
    	$gamelevel=M("word_game_level");
    	$result=$gamelevel->where("id=%d",$levelid)->find();
        if($questype!=9&&$questype!=10){
            if(!empty($id)){
                $questions=json_decode($result["questions"]);
                $question=$questions->$id;
                $question=(array)$question;

                if($questype==1){
                    $question["analysis"]=$analysis;
                    $question["items"]=array();
                    $itemindex=0;
                    foreach($items as $obj){
                        $question["items"][$itemindex]->flag=$obj->flag;
                        $question["items"][$itemindex]->content=$obj->content;
                        $question["items"][$itemindex]->typeid=$obj->typeid;
                        if($obj->ischecked=='1'){
                            $question["answer"]=$obj->flag;
                        }
                        $itemindex=$itemindex+1;
                    }
                }
                if($questype!=1&&!empty($tncontent)){
                    $question["tncontent"]=$tncontent;
                }
                $questions->$id=$question;
                $gamelevel->where("id=%d",$levelid)->setField("questions",json_encode($questions));
            }else{
                $questions=$result["questions"];
                $temp["content"]=$question->content;
                $items=array();
                foreach($question->items as $obj){
                    $tem["flag"]=$obj->flag;
                    $tem["content"]=$obj->content;
                    if($obj->content==1){
                        $answer=$tem["flag"];
                    }
                    array_push($items, $tem);
                }
                $temp["content"]=$items;
                $temp["answer"]=$answer;
                $temp["analysis"]=$question->analysis;
                $questions[$question->id]=$temp;
                $gamelevel->where("id=%d",$levelid)->setField("questions",$questions);
            }
        }else{
            $questions=json_decode($result["questions"],true);
            $words=json_decode($result["words"],true);
            $temp["tncontent"]=$tncontent;
            $temp["explainid"]=$explainid;
            $temp["questype"]=$questype;
            //获取单一一次释义
            $rs=$this->getWordExplainsById($id,$explainid);
            $temp["word"]=$rs["word"];
            $temp["wordid"]=$id;
            $questions->$id=$question;
            $qitems=array();
            foreach($items as $obj){
                $tem["flag"]=$obj->flag;
                $tem["content"]=$obj->content;
                $tem["typeid"]=$obj->typeid;
                if($obj->ischecked==1){
                    $answer=$tem["flag"];
                }
                array_push($qitems, $tem);
            }
            $temp["items"]=$qitems;
            $temp["answer"]=$answer;
            $temp["analysis"]=$analysis;
            $temp["useranswer"]="";
            $temp["iserror"]="";
            $questions[$id]=$temp;
            // "wordid": 2305,
            $temps["wordid"]=$id;
            $temps["word"]=$rs["word"];
            $temps["morphology"]=$rs["morphology"];
            $temps["explainid"]=$explainid;
            $temps["explains"]=$rs["explains"];
            $temps["ukmark"]=$rs["ukmark"];
            $words[$id]=$temps;
            $gamelevel->questions=json_encode($questions);
            $gamelevel->words=json_encode($words);
            $gamelevel->where("id=%d",$levelid)->save();
            if($questype==10){
                if(empty($anaid)){
                    M("word_analysis")->title=$title;
                    M("word_analysis")->content=$analysis;
                    M("word_analysis")->add();
                }
            }
        }
    	

    }

    //删除试题
    public function delQuestion(){
    	$wordid=I("id");
    	$levelid=I("levelid");
    	$gamelevel=M("word_game_level");
    	$bresult=$gamelevel->where("id=%d",$levelid)->find();
    	$bquestions=json_decode($bresult["questions"]);
        $bwords=json_decode($bresult["words"]);
    	unset($bquestions->$wordid);
        unset($bwords->$wordid);
        if(empty((array)($bquestions))){
            $bquestions="";
            $bwords="";
        }else{
            $bquestions=json_encode($bquestions);
            $bwords=json_encode($bwords);
        }
        $gamelevel->questions=$bquestions;
        $gamelevel->words=$bwords;
        $gamelevel->quesnum=$bresult["quesnum"]-1;
    	$gamelevel->where("id=%d",$levelid)->save();
    }

    //更改关卡
    public function changeLevel(){
    	$questionid=I("id");
    	$blevelid=I("blevelid");
    	$alevelid=I("alevelid");
    	$gamelevel=M("word_game_level");
    	$bresult=$gamelevel->where("id=%d",$blevelid)->find();
    	$bquestions=json_decode($bresult["questions"]);
    	$question=$bquestions->$questionid;
        $bwords=json_decode($bresult["words"]);
        $word=$bwords->$questionid;
    	unset($bquestions->$questionid);
        unset($bwords->$questionid);
        if(empty((array)($bquestions))){
            $bquestions="";
            $bwords="";
        }else{
            $bquestions=json_encode($bquestions);
            $bwords=json_encode($bwords);
        }
        $gamelevel->questions=$bquestions;
        $gamelevel->words=$bwords;
        $gamelevel->quesnum=$bresult["quesnum"]-1;
        $gamelevel->where("id=%d",$blevelid)->save();
    	$aresult=$gamelevel->where("id=%d",$alevelid)->find();
    	$aquestions=(array)json_decode($aresult["questions"]);
        $awords=(array)json_decode($aresult["words"]);
    	$aquestions[$questionid]=$question;
        $awords[$questionid]=$word;
        $aquestions=json_encode($aquestions);
        $awords=json_encode($awords);
        $gamelevel->questions=$aquestions;
        $gamelevel->words=$awords;
        $gamelevel->quesnum=$aresult["quesnum"]+1;
        $gamelevel->where("id=%d",$alevelid)->save();
    }

    //删除关卡
    public function delLevel(){
        $levelid=I("levelid/d");
    	$gamelevel=M("word_game_level");
        //删除关卡的时候需要跟新游戏的关卡数以及单词的数量
        $wordgame=M("word_game");
        $rs=$gamelevel->where("id=%d",$levelid)->find();
        $wordnum=$rs["wordnum"];
        $sql="update engs_word_game set wordnum=wordnum-".$wordnum.",levelnum=levelnum-1 where id=".$rs["gameid"];
        M()->execute($sql);
        //删除关卡
    	$gamelevel->where("id=%d",$levelid)->delete();

    }

    //删除关卡
    public function editLevel(){
    	$levelid=I("id");
    	$gameid=I("gameid");
    	$name=I("name");
    	$gamelevel=M("word_game_level");
    	if(empty($levelid)){
    		$gamelevel->name=$name;
    		$gamelevel->gameid=$gameid;
    		$gamelevel->add();
            $sql="update table engs_word_game set levelnum=levelnum+1 where id=".$gameid;
            M()->execute($sql);
            $ret["num"]=1;
    	}else{
    		$gamelevel->where("id=%d",$levelid)->setField("name",$name);
            $ret["num"]=0;
    	}
        $this->ajaxReturn($ret);
    }

	
    //导入单词
    public function importChuWord() {
        Vendor("PHPExcel");
        $filename = I("filename");
        $gameid = I("gameid");
        $arr_rs = $this->importChuWordExcel($filename,$gameid); //获取导入的结果集
        $iserr = 0;
        $errmsg = "";
        $i = 0;
        foreach ($arr_rs as $row => $v) {
            if ($v["iserr"]) {
                $iserr = 1;
                $errmsg.= $v["errmsg"] . '<br>';
            } else {
                $i++;
            }
        }
        $data["iserr"] = $iserr;
        $data["errmsg"] = $errmsg;
        $data["sucnum"] = $i;
        $this->ajaxReturn($data);
    }

    /**
     *  importWordExcel  
     *  读取Excel文件并将文件中的单词插入数据库
     * @param   filename表示文件存放路径
     * @return  array 
     */
    protected function importChuWordExcel($filename,$gameid) {
        $arr_excel = readExcel($filename);
        $game=M("word_game");
        $gamelevel=M("word_game_level");
        $baseword=M("base_word");
        $basewordexplains=M("base_word_explains");
        $arr=array();
        $grade=$game->where("id=%d",$gameid)->find();
        //查询每关的单词数
        //$perwordnum=$grade["perwordnum"];
        $levelnum=0;
        //A B  
        $arrcolumn = array('A','C');
        $i = 0;
        $wordnum=0;
        $iserr = false;
        $gamewordlist=array();
        foreach ($arr_excel as $row => $v) {
            $temp=array();
            if (empty($v["Column"]['A'])||empty($v["Column"]['C'])) {
                unset($arr_excel[$row]);
                continue;
            }
            $j=$v["Column"]['C'];
            /* 必填列判断 */
            foreach ($arrcolumn as $cv) {
                $v["iserr"] = false;
                if (empty($v["Column"][$cv])) {
                    $v["iserr"] = true;
                    $v["errmsg"] = '第' . $row . '行' . $cv . '列为空';
                    $arr_excel[$row] = $v;
                    $iserr = true;
                    break;
                }
            }
            if ($v["iserr"]) {
                continue;
            }
            $wordnum=$wordnum+1;
            $word=trim($v["Column"]["A"]);
            $temp["word"]=$word;
            $temp["isdo"]=0;
            //判断单词是是否在baseword
            $isword=trim($v["Column"]["B"]);
            if(empty($isword)){$isword=1;}else{$isword=0;}
            $basers=$baseword->where("word='%s' and isword=%d",$word,$isword)->find();
            if(empty($basers)){
                $v["iserr"] = true;
                $v["errmsg"] = '第' . $row . '行单词不存在';
                $arr_excel[$row] = $v;
                $iserr = true;
                continue;
            }
            $temp["wordid"]=$basers["id"];
	    $temp["word"]=$basers["word"];
            //查询意思
            $explainrs=$basewordexplains->where("base_wordid=%d",$basers["id"])->find();
            if(empty($explainrs)){
                $temp["morphology"]="";
                $temp["explainid"]=0;
                $temp["explains"]="";
            }else{
                $temp["morphology"]=$explainrs["morphology"];
                $temp["explainid"]=$explainrs["id"];
                $temp["explains"]=$explainrs["explains"];
            }
            $temp["ukmark"]=$basers["ukmark"];
            $i++;
            //开始比对数据
            $isword=trim($v["Column"]["B"]);
            if(empty($isword)){
                $temp["isword"]=1;
            }else if($isword=='0'){
                $temp["isword"]=1;
            }else if($isword=='1'){
                $temp["isword"]=0;
            }
            $arr[$i]=$temp;
            $wordids='w'.$temp["wordid"];
            $explainids='e'.$temp["explainid"];
            $id=md5($wordids.$explainids);
            $gamewordlist[$id]=$temp;
            //$ret[$id]=$temp;
            //查询是否有这一关
            $name="第".Chinese_Money_Max($j)."关";
            $ret=array();
            $rs=$gamelevel->where("name='%s' and gameid=%d",$name,$gameid)->find();
            if(empty($rs)){
                $ret[$id]=$temp;
                $gamelevel->gameid=$gameid;
                $gamelevel->name="第".Chinese_Money_Max($j)."关";
                $gamelevel->wordnum=1;
                $gamelevel->words=json_encode($ret);
                $gamelevel->add();
                $levelnum=$levelnum+1;
            }else{
                $ret=json_decode($rs["words"],true);
                $ret[$id]=$temp;
                $gamelevel->wordnum=$rs["wordnum"]+1;
                $gamelevel->words=json_encode($ret);
                $gamelevel->where("id=%d",$rs["id"])->save();
            }
            
        }
        //数据库中保存一份原始数据
        $game->levelnum=$levelnum;
        $game->wordlists=json_encode($gamewordlist);
        $game->wordnum=$wordnum;
        $game->where("id=%d",$gameid)->save();
        return $arr_excel;
    }

	

    //关卡列表
    public function getLevelList(){
    	$gameid=I("gameid");
    	$gamelevel=M("word_game_level");
    	$result=$gamelevel->where("gameid=%d",$gameid)->order("sortid,id")->select();
    	$this->ajaxReturn($result);
    }

    //删除元素
    public function deleteElement(){
    	$type=I("type/d");
    	$id=I("id/d");
    	//type 0表示删除的是关卡
    	if(type==0){
    		$this->delLevel($id);
    	}
    }


    //添加单词
    public function editWord(){
    	$levelid=I("levelid/d");
    	$wordid=I("wordid/d");
    	$explainid=I("explainid/d");
    	//查询单词库中是否有这个单词
    	$baseword=M("base_word");
    	$word=D("word");
    	$basewordexplains=M("base_word_explains");
    	$gamelevel=M("word_game_level");
		$wordrs=$baseword->where("id=%d",$wordid)->find();
		$explainrs=$basewordexplains->where("id=%d",$explainid)->find();
		//type 0表示删除的是关卡
		$wordname=$wordrs["word"];
		$ukmark=$wordrs["ukmark"];
		$explains=$explainrs["explains"];
		$morphology=$explainrs["morphology"];
		//将这个单词添加到words中查询这个level中
		$levelrs=$gamelevel->where("id=%d",$levelid)->find();
        $gameid=$levelrs["gameid"];
		if(empty($levelrs["words"])){
			$words=array();
		}else{
			$words=json_decode($levelrs["words"],true);
		}
		if(empty($levelrs["words"])){
			$questions=array();
		}else{
			$questions=json_decode($levelrs["questions"],true);
		}
		
		$arr["wordid"]=$wordid;
		$arr["word"]=$wordname;
		$arr["morphology"]=$morphology;
        	$arr["explainid"]=$explainid;
		$arr["explains"]=$explains;
		$arr["ukmark"]=$ukmark;
		$words[$wordid]=$arr;
		$words=json_encode($words);
		$game=M("word_game")->where("id=%d",$gameid)->find();
        	$peroid=$game["peroid"];
		//此时将一个试题直接插入数据库
		$question=$word->getReciteWordQuestion(1,$wordid,$explainid,$peroid);
		$questions[$wordid]=$question;
		$questions=json_encode($questions);
		$gamelevel->words=$words;
		$gamelevel->questions=$questions;
        $gamelevel->quesnum=$levelrs["quesnum"]+1;
		$gamelevel->where("id=%d",$levelid)->save();
        $sql="update engs_word_game set wordnum=wordnum+1 where id=".$gameid;
        M()->execute($sql);

    }

    //获取单词意思
    public function getWordExplains(){
    	$name=I("name");
    	$sql="Select t.* from engs_base_word_explains t left join engs_base_word s on t.base_wordid=s.id  where s.word='%s'";
    	$rs=M()->query($sql,$name);
    	$this->ajaxReturn($rs);
    }

    //获取意思
    public function getWordinfoByWordidExplainid(){
        $wordid=I("wordid/d");
        $explainid=I("explainid/d");
        $ret=$this->getWordExplainsById($wordid,$explainid);
        $this->ajaxReturn($ret);
    }

    //获取单词意思
    private function getWordExplainsById($wordid,$explainid){
        $sql="Select s.*,t.* from engs_base_word s left join engs_base_word_explains t on t.base_wordid=s.id  where s.id=%d and t.id=%d";
        $rs=M()->query($sql,$wordid,$explainid);
        //随机找一个释义的例句
        $sql="select * from engs_base_word_explains_example where explainid=%d order by rand() limit 0,1";
        $ers=M()->query($sql,$explainid);
        $ret=$rs[0];
        if(empty($ers)){
            $ret["examples"]="";
        }else{
            $ret["examples"]=$ers[0]["encontent"];
        }
        $filepath=C("word_pic_path").$ret["pic"];
        $image_info = getimagesize($filepath);
        $base64_image_content = "data:{$image_info['mime']};base64," . chunk_split(base64_encode(file_get_contents($filepath)));
        $ret["content"]=$base64_image_content;
        return $ret;
    }

    //获取单词的试题类型
    public  function getQuestypeList(){
        $wordid=I("wordid");
        $explainid=I("explainid");
        $rs=M("word_game_questype")->where("isuse=1")->select();
        //根据单词查拼写题是否存在
        $wordrs=M("base_word")->where("id=%d",$wordid)->find();
        $explainrs=M("base_word_explains_example")->where("explainid=%d",$explainid)->find();
        //看是否有单词拼写题
        if(empty($explainrs)){
            unset($rs[5]);unset($rs[7]);unset($rs[8]);
        }
        if(empty($wordrs["letters"])){
            //没有单词拼写试题6
            unset($rs[6]);
        }
        $this->ajaxReturn($rs);
    }

    //获取游戏列表
    public function getGameList(){
        $peroid=I("peroid/d");
        $title=I("title/s");
        if(empty($title)){
            $sql="Select * from  engs_word_game t where t.peroid=".$peroid." order by t.sortid";
        }else{
            $sql="Select * from  engs_word_game t where t.peroid=".$peroid." and t.title='%".$title."%' order by t.sortid";  
        }
        $rs=M()->query($sql);
        $this->ajaxReturn($rs);
    }

    //编辑添加游戏
    public function editGame(){
        $id=I("id");
        $perwordnum=I("perwordnum");
        $title=I("name");
        $questype=I("questype");
        $wordgame=M("word_game");
        if(empty($id)){
            $wordgame->peroid=$peroid;
            $wordgame->name=$title;
            $wordgame->add();
        }else{
            $wordgame->name=$title;
            $wordgame->where("id=%d",$id)->save();
        }
    }

    //删除游戏
    public function delGame(){
        $id=I("id");
        $wordgame=M("word_game");
        $gamelevel=M("word_game_level");
        $wordgame->where("id=%d",$id)->delete();
        $gamelevel->where("gameid=%d",$id)->delete();
    }

    //游戏复制
    public function copyGame(){
        $id=I("id");
        $wordgame=M("word_game");
        $wordgamelevel=M("word_game_level");
        $gamers=$wordgame->where("id=%d",$id)->find();
        $maxgamers=$wordgame->field("max(id) as maxid")->find();
        $maxid=$maxgamers["maxid"];
        $wordgame->id=$maxid+1;
        $wordgame->name=$gamers["name"]."copy";
        $wordgame->levelnum=$gamers["levelnum"];
        $wordgame->wordnum=$gamers["wordnum"];
        $wordgame->perwordnum=$gamers["perwordnum"];
        $wordgame->isstar=$gamers["isstar"];
        $wordgame->peroid=$gamers["peroid"];
        $wordgame->grades=$gamers["grades"];
        $wordgame->pic=$gamers["pic"];
        $gameid=$wordgame->add();
        $levelrs=$wordgamelevel->where("gameid=%d",$id)->select();
        foreach($levelrs as $key=>$value){
            $wordgamelevel->gameid=$gameid;
            $wordgamelevel->name=$value["name"];
            $wordgamelevel->questions=$value["questions"];
            $wordgamelevel->words=$value["words"];
            $wordgamelevel->quesnum=$value["quesnum"];
            $wordgamelevel->add();
        }
    }

    //复制关卡
    public function copyLevel(){
        $levelid=I("levelid/d");
        $gamelevel=M("word_game_level");
        $rs=$gamelevel->where("id=%d",$levelid)->find();
        $gameid=$rs["gameid"];
        $wordnum=$rs["wordnum"];
        $quesnum=$rs["quesnum"];
        $words=$rs["words"];
        $questions=$rs["questions"];
        $difficulty=$rs["difficulty"];
        $name=$rs["name"];
        $sortid=$rs["sortid"];
        $sql="insert into engs_word_game_level(gameid,wordnum,quesnum,words,questions,difficulty,name,sortid) ";
        $sql=$sql."value (".$gameid.",".$wordnum.",".$quesnum.",'".$words."','".$questions."',".$difficulty.",'".$name."副本"."',".$sortid.")";
        M()->execute($sql);
    }

    //改变难度
    public function changeDifficulty(){
        $difficulty=I("difficulty");
        $levelid=I("levelid");
        M("word_game_level")->where("id=%d",$levelid)->setField("difficulty",$difficulty);
    }

    //清空关卡内容
    public function clearLevelWords(){
        $levelid=I("levelid/d");
        $gamelevel=M("word_game_level");
        $gamelevel->words="";
        $gamelevel->questions="";
        $gamelevel->wordnum=0;
        $gamelevel->quesnum=0;
        $gamelevel->where("id=%d",$levelid)->save();
    }


    //游戏排序
    public function listup() {
        $data = stripslashes(I("data"));
        $data = rtrim($data, '"');
        $data = ltrim($data, '"');
        $data = str_replace('&quot;', '"', $data);
        $res = json_decode($data);
        $peroid = I('peroid/s',0);
        $wordgame=M("word_game");
        foreach ($res as $obj) {          
            $id = $obj->id;
            $sortid = $obj->sortid;
            $wordgame->sortid = $sortid;
            $result = $wordgame->where("id=%d", $id)->save();
        }
        $arr_return["msg"] = 1;
        $arr_return["err"] = "修改成功";
    }


    //游戏加精
    public function changeStar() {
        $id=I("id");
        $isstar=I("isstar");
        $wordgame=M("word_game");
        $result = $wordgame->where("id=%d", $id)->setField("isstar",$isstar);
    }

    //导入单词
    public function importWord() {
        Vendor("PHPExcel");
        $filename = I("filename");
        $gameid = I("gameid");
        $arr_rs = $this->importWordExcel($filename,$gameid); //获取导入的结果集
        $iserr = 0;
        $errmsg = "";
        $i = 0;
        foreach ($arr_rs as $row => $v) {
            if ($v["iserr"]) {
                $iserr = 1;
                $errmsg.= $v["errmsg"] . '<br>';
            } else {
                $i++;
            }
        }
        $data["iserr"] = $iserr;
        $data["errmsg"] = $errmsg;
        $data["sucnum"] = $i;
        $this->ajaxReturn($data);
    }

    /**
     *  importWordExcel  
     *  读取Excel文件并将文件中的单词插入数据库
     * @param   filename表示文件存放路径
     * @return  array 
     */
    protected function importWordExcel($filename,$gameid) {
        $arr_excel = readExcel($filename);
        $game=M("word_game");
        $gamelevel=M("word_game_level");
        $baseword=M("base_word");
        $basewordexplains=M("base_word_explains");
        $arr=array();
        $grade=$game->where("id=%d",$gameid)->find();
        //查询每关的单词数
        $perwordnum=$grade["perwordnum"];
        //A B  
        $arrcolumn = array('A');
        $i = 0;
        $iserr = false;
        foreach ($arr_excel as $row => $v) {
            $temp=array();
            if (empty($v["Column"]['A'])) {
                unset($arr_excel[$row]);
                continue;
            }
            /* 必填列判断 */
            foreach ($arrcolumn as $cv) {
                $v["iserr"] = false;
                if (empty($v["Column"][$cv])) {
                    $v["iserr"] = true;
                    $v["errmsg"] = '第' . $row . '行' . $cv . '列为空';
                    $arr_excel[$row] = $v;
                    $iserr = true;
                    break;
                }
            }
            if ($v["iserr"]) {
                continue;
            }
            $word=trim($v["Column"]["A"]);
            $temp["word"]=$word;
            $temp["isdo"]=0;
            //判断单词是是否在base
            $isword=trim($v["Column"]["B"]);
            if(empty($isword)){$isword=1;}else{$isword=0;}
            $basers=$baseword->where("word='%s' and isword=%d",$word,$isword)->find();
            if(empty($basers)){
                $v["iserr"] = true;
                $v["errmsg"] = '第' . $row . '行单词不存在';
                $arr_excel[$row] = $v;
                $iserr = true;
                continue;
            }
	    $temp["word"]=$basers["word"];
            $temp["wordid"]=$basers["id"];
            //查询意思
	    $explainrs=$basewordexplains->where("base_wordid=%d",$basers["id"])->find();
            if(empty($explainrs)){
                $temp["morphology"]="";
                $temp["explainid"]=0;
                $temp["explains"]="";
            }else{
                $temp["morphology"]=$explainrs["morphology"];
                $temp["explainid"]=$explainrs["id"];
                $temp["explains"]=$explainrs["explains"];
            }
            $temp["ukmark"]=$basers["ukmark"];
            $i++;
            //开始比对数据
            $isword=trim($v["Column"]["B"]);
            if(empty($isword)){
                $temp["isword"]=1;
            }else if($isword=='0'){
                $temp["isword"]=1;
            }else if($isword=='1'){
                $temp["isword"]=0;
            }
            $arr[$i]=$temp;
        }
        //将数组分配到不同的组中
        shuffle($arr);
        shuffle($arr);
        $page=ceil($i/$perwordnum);
        $game->levelnum=$page;
        $game->wordnum=$i;
        $game->where("id=%d",$gameid)->save();
        $json=array();
        for($j=0;$j<$page;$j++){
            $start=$j*$perwordnum;
            if($i>($start+$perwordnum)){
                $length=$perwordnum;
            }else{
                $length=$i-$start;
            }
            $json[$j]=array_slice($arr,$start,$length);
            $ret=array();
            $levelwordnum=0;
            foreach($json[$j] as $kk=>$kv){
                $wordids='w'.$kv["wordid"];
                $explainids='e'.$kv["explainid"];
                $id=md5($wordids.$explainids);
                $ret[$id]=$kv;
                $levelwordnum=$levelwordnum+1;
            }
            $json[$j]=$ret;
            //保存到关卡的数组中
            $gamelevel->gameid=$gameid;
            $gamelevel->name="第".Chinese_Money_Max($j+1)."关";
            $gamelevel->gameid=$gameid;
            $gamelevel->wordnum=$levelwordnum;
            $gamelevel->words=json_encode($json[$j]);
            $gamelevel->add();
        }
        //数据库中保存一份原始数据
        $game->where("id=%d",$gameid)->setField("wordlists",json_encode($json));
        return $arr_excel;
    }

    //获取单词ID的意思
    public function getExplainListByWordid(){
        $wordid=I("wordid");
        $rs=M("base_word_explains")->where("base_wordid=%d",$wordid)->select();
        $this->ajaxReturn($rs);
    }

    //获取单词名称的意思
    public function getExplainListByWord(){
        $word=I("word");
        $isword=I("isword/d",1);
        $wrs=M("base_word")->where("word='%s' and isword=%d",$word,$isword)->find();
        if(empty($wrs)){
            $explainrs=array();
        }else{
            $explainrs=M("base_word_explains")->where("base_wordid=%d ",$wrs["id"])->select();
        }
        $this->ajaxReturn($explainrs);
    }

    //修改单词的意思
    public function changeGameLevelWordExplainid(){
        $levelid=I("levelid/d");
        $explainid=I("explainid/d");
        $wordid=I("wordid");
        $rs=M("word_game_level")->where("id=%d",$levelid)->find();
        $words=json_decode($rs["words"]);
        $explainrs=M("base_word_explains")->where("id=%d",$explainid)->find();
        $words->$wordid->explainid=$explainid;
        $words->$wordid->morphology=$explainrs["morphology"];
        $words->$wordid->explains=$explainrs["explains"];
        M("word_game_level")->where("id=%d",$levelid)->setField("words",json_encode($words));
    }

    //修改单词的意思改变图片
    public function changeGameLevelQuestionByExplainid(){
        $levelid=I("levelid/d");
        $explainid=I("explainid/d");
        $id=I("id");
        $word=D("word");
        $rs=M("word_game_level")->where("id=%d",$levelid)->find();
	$game=M("word_game")->where("id=%d",$rs["gameid"])->find();
        $peroid=$game["peroid"];
        $questions=json_decode($rs["questions"],true);
        $question=$questions[$id];
        $wordid=$question["wordid"];
        $explainid=$explainid;
        $questype=$question["questype"];
        $arr=$word->getReciteWordQuestion($questype,$wordid,$explainid,$peroid);
        $questions[$id]["explainid"]=$explainid;
        $questions[$id]["explains"]=$arr["explains"];
        $questions[$id]["items"]=$arr["items"];
        $questions[$id]["answer"]=$arr["answer"];
        M("word_game_level")->where("id=%d",$levelid)->setField("questions",json_encode($questions));
    }

    //修改单词的题型
    public function changeGameLevelQuestionByQuestype(){
        $levelid=I("levelid/d");
        $questype=I("questype/d");
        $id=I("id");
        $word=D("word");
        $rs=M("word_game_level")->where("id=%d",$levelid)->find();
	$game=M("word_game")->where("id=%d",$rs["gameid"])->find();
        $peroid=$game["peroid"];
        $questions=json_decode($rs["questions"],true);
        $question=$questions[$id];
        $wordid=$question["wordid"];
        $questype=$questype;
        $explainid=$question["explainid"];
        $arr=$word->getReciteWordQuestion($questype,$wordid,$explainid,$peroid);
        $questions[$id]["questype"]=$questype;
        $questions[$id]["items"]=$arr["items"];
        $questions[$id]["answer"]=$arr["answer"];
        M("word_game_level")->where("id=%d",$levelid)->setField("questions",json_encode($questions));
    }

    //试题顺序的修改
    public function upSortLevelQuestionList(){
        $levelid=I("levelid");
        $data = stripslashes(I("data"));
        $data = rtrim($data, '"');
        $data = ltrim($data, '"');
        $data = str_replace('&quot;', '"', $data);
        $res = json_decode($data,true);
        //二维数组排序
        $res=sortt($res,"sortid","int");
        $rs=M("word_game_level")->where("id=%d",$levelid)->find();
        $questions=json_decode($rs["questions"],true);
        $questionsarr=array();
        foreach($res as $key=>$value){
            $id=$value["id"];
            $questionsarr[$id]=$questions[$id];
        }
        $questionsarr=json_encode($questionsarr);
        M("word_game_level")->questions=$questionsarr;
        M("word_game_level")->where("id=%d",$levelid)->save();
    }

    //修改意思
    public function changeWordExplain(){
        $wordid=I("id");
        $levelid=I("levelid");
        $questype=I("questype");
        $explainid=I("explainid");
        $rs=M("word_game_level")->where("id=%d",$levelid)->find();
	$game=M("word_game")->where("id=%d",$rs["gameid"])->find();
        $peroid=$game["peroid"];
        $words=json_decode($rs["words"]);
        $questions=json_decode($rs["questions"]);
        //查询
        $explainrs=M("base_word_explains")->where("id=%d",$explainid)->find();
        $words->$wordid->explainid=$explainid;
        $words->$wordid->morphology=$explainrs["morphology"];
        $words->$wordid->explains=$explainrs["explains"];
        $word=D("word");
        $question=$word->getReciteWordQuestion($questype,$wordid,$explainid,$peroid);
        //将数据刷新到数据库中
        $questions->$wordid=$question;
        $questions=json_encode($questions);
        M("word_game_level")->where("id=%d",$levelid)->setField("questions",$questions);
        M("word_game_level")->where("id=%d",$levelid)->setField("words",json_encode($words));
    }

    //文件下载
    public function downloadfile(){
        $filename="./uploads/game/demo/demo.xls";
        downfile($filename,"demo.xls");
    }

    //关卡修改
    public function levelListUp(){
        $data = stripslashes(I("data"));
        $data = rtrim($data, '"');
        $data = ltrim($data, '"');
        $data = str_replace('&quot;', '"', $data);
        $res = json_decode($data);
        $wordgamelevel=M("word_game_level");
        foreach ($res as $obj) {          
            $id = $obj->id;
            $sortid = $obj->sortid;
            $wordgamelevel->where("id=%d", $id)->setField("sortid",$sortid);
        }
        $arr_return["msg"] = 1;
        $arr_return["err"] = "修改成功";
    }

    //修改单词问题顺序
    public function upSortQuestions(){
        $levelid=I("levelid");
        $data = stripslashes(I("data"));
        $data = rtrim($data, '"');
        $data = ltrim($data, '"');
        $data = str_replace('&quot;', '"', $data);
        $res = json_decode($data);
        $rs=M("word_game_level")->where("id=%d",$levelid)->find();
        $questions=json_decode($rs["questions"],true);
        $words=json_decode($rs["words"],true);
        foreach($res as $obj){
            $wordid=$obj->id;
            $wordsarr->$wordid=$words[$wordid];
            $questionsarr->$wordid=$questions[$wordid];
        }
        $questions=json_encode($questionsarr);
        $words=json_encode($wordsarr);
        M("word_game_level")->questions=$questions;
        M("word_game_level")->words=$words;
        M("word_game_level")->where("id=%d",$levelid)->save();
    }
	
	//刷新试题
    public function refreshBaseQuestion($levelid,$id){
     //    $levelid=I("levelid/d");
        // $id=I("id");
        //查询这个单词的意思
        $levelrs=M("word_game_level")->where("id=%d",$levelid)->find();
        $questions=json_decode($levelrs["questions"],true);
        $question=$questions[$id];
        $word=D("word");
        $wordid=$question["wordid"];
        $explainid=$question["explainid"];
        $questype=$question["questype"];
        $arr=$word->getReciteWordQuestion($questype,$wordid,$explainid);
	$question["tncontent"]=$arr["tncontent"];
	$question["word"]=$arr["word"];
        $question["items"]=$arr["items"];
        $question["answer"]=$arr["answer"];
        $questions[$id]=$question;
        M("word_game_level")->where("id=%d",$levelid)->setField("questions",json_encode($questions));
    }

    //刷新管卡下面的瓶邪试题
    public function refreshGameLevelQuestions(){
        set_time_limit(0);
        $id=I("id");
        $rs=M("word_game_level")->where("gameid=%d and questions is not null",$id)->select();
        foreach($rs as $key=>$value){
            $questions=json_decode($value["questions"],true);
            foreach($questions as $sk=>$sv){
                $questype=$sv["questype"];
                $id=$sk;
                $levelid=$value["id"];
                if($questype=='6'){
                    $this->refreshBaseQuestion($levelid,$id);
                }
            }
        }
    }

    //搜索解析
    public function getAnalysisByTitle(){
        $ret=array();
        $sql="Select title from engs_word_analysis t";
        $ret=M()->query($sql);
        $result=array();
        foreach($ret as $key=>$value){
            array_push($result, $value["title"]);
        }
        $this->ajaxReturn($result);
    }

    //获取内容
    public function getAnalyByTitle(){
        $title=I("title");
        $sql="Select * from engs_word_analysis t where t.title='".$title."'";
        $ret=M()->query($sql);
        $this->ajaxReturn($ret[0]);
    }

    //编辑
    public function leveledit(){
        $gameid=I("gameid/0");
        $name="";
        $id=$gameid;
        $quesnum=5;
        $grades="0000000000";
        $peroid="0";
        $backupquestypes="0";
        $perwordnum=5;
        $queslist=array();
        if(!empty($gameid)){
            $gamers=M("word_game")->where("id=%d",$gameid)->find();
            $name=$gamers["name"];
            $grades=$gamers["grades"];
            $peroid=$gamers["peroid"];
            $perwordnum=$gamers["perwordnum"];
            $backupquestypes=$gamers["backupquestypes"];
            $questypes=$gamers["questypes"];
            if(!empty($questypes)){
                $queslist=explode("|", $questypes);
            }
            if(empty($backupquestypes)){
                $backupquestypes=0;
            }
        }else{
            M("word_game")->peroid=0;
            $gameid=M("word_game")->add();
        }
        $rs=M("word_game_questype")->where("isuse=1")->select();
        foreach($rs as $key=>$value){
            $index=array_search($value["cindex"], $queslist);
            $rs[$key]["ischecked"]=1;
            if($index===false){
                $rs[$key]["ischecked"]=0;
            }
        }
        $this->assign("queslist",$rs);
        $this->assign("name",$name);
        $this->assign("peroid",$peroid);
        $this->assign("grades",$grades);
        $this->assign("gameid",$gameid);
        $this->assign("perwordnum",$perwordnum);
        $this->assign("backupquestypes",$backupquestypes);
        $this->display();
    }

    public function getGameLevelList(){
        $gameid=I("gameid");
        $wordgamelevel=M();
        $sql="select engs_word_game.name as gamename,engs_word_game_level.id,engs_word_game_level.name as levelname,engs_word_game_level.wordnum,engs_word_game_level.quesnum,engs_word_game_level.difficulty from engs_word_game_level left join engs_word_game on engs_word_game_level.gameid=engs_word_game.id where engs_word_game_level.gameid=%d order by engs_word_game_level.sortid,engs_word_game_level.id";
        $rs=$wordgamelevel->query($sql,$gameid);
        $this->ajaxReturn($rs);
    }


    //删除单词的列表
    public function delLevelWord(){
        $levelid=I("levelid/d",0);
        $wordid=I("wordid/s");
        $wordGameLevel=M("word_game_level");
        $rs=$wordGameLevel->where("id=%d",$levelid)->find();
        $words=json_decode($rs["words"],true);
        unset($words[$wordid]);
        $words=json_encode($words);
        $wordGameLevel->words=$words;
        $wordGameLevel->wordnum=$rs["wordnum"]-1;
        $wordGameLevel->where("id=%d",$levelid)->save();
        $sql="update engs_word_game set wordnum=wordnum-1 where id=%d";
        M()->execute($sql,$rs["gameid"]);
    }

    //导入关卡单词
    public function importLevelWord(){
        Vendor("PHPExcel");
        $filename = I("filename");
        $levelid = I("levelid");
        $arr_rs = $this->importLevelWordExcel($filename,$levelid); //获取导入的结果集
        $iserr = 0;
        $errmsg = "";
        $i = 0;
        foreach ($arr_rs as $row => $v) {
            if ($v["iserr"]) {
                $iserr = 1;
                $errmsg.= $v["errmsg"] . '<br>';
            } else {
                $i++;
            }
        }
        $data["iserr"] = $iserr;
        $data["errmsg"] = $errmsg;
        $data["sucnum"] = $i;
        $this->ajaxReturn($data);
    }

    /**
     *  importWordExcel  
     *  读取Excel文件并将文件中的单词插入数据库
     * @param   filename表示文件存放路径
     * @return  array 
     */
    protected function importLevelWordExcel($filename,$levelid) {
        $arr_excel = readExcel($filename);
        $game=M("word_game");
        $gamelevel=M("word_game_level");
        $baseword=M("base_word");
        $basewordexplains=M("base_word_explains");
        $levelrs=$gamelevel->where("id=%d",$levelid)->find();
        $words=json_decode($levelrs,true);
        $gameid=$levelrs["gameid"];
        $arr=array();
        $grade=$game->where("id=%d",$gameid)->find();
        //查询每关的单词数
        $perwordnum=$grade["perwordnum"];
        //A B  
        $arrcolumn = array('A');
        $i = 0;
        $iserr = false;
        foreach ($arr_excel as $row => $v) {
            $temp=array();
            if (empty($v["Column"]['A'])) {
                unset($arr_excel[$row]);
                continue;
            }
            /* 必填列判断 */
            foreach ($arrcolumn as $cv) {
                $v["iserr"] = false;
                if (empty($v["Column"][$cv])) {
                    $v["iserr"] = true;
                    $v["errmsg"] = '第' . $row . '行' . $cv . '列为空';
                    $arr_excel[$row] = $v;
                    $iserr = true;
                    break;
                }
            }
            if ($v["iserr"]) {
                continue;
            }
            $word=trim($v["Column"]["A"]);
            $temp["word"]=$word;
            $temp["isdo"]=0;
            //判断单词是是否在baseword
            $basers=$baseword->where("word='%s'",$word)->find();
            if(empty($basers)){
                $v["iserr"] = true;
                $v["errmsg"] = '第' . $row . '行单词不存在';
                $arr_excel[$row] = $v;
                $iserr = true;
                continue;
            }
            $temp["wordid"]=$basers["id"];
            //查询意思
            $explainrs=$basewordexplains->where("base_wordid=%d",$basers["id"])->find();
            if(empty($explainrs)){
                $temp["morphology"]="";
                $temp["explainid"]=0;
                $temp["explains"]="";
            }else{
                $temp["morphology"]=$explainrs["morphology"];
                $temp["explainid"]=$explainrs["id"];
                $temp["explains"]=$explainrs["explains"];
            }
            $temp["ukmark"]=$basers["ukmark"];
            $i++;
            //开始比对数据
            $isword=trim($v["Column"]["B"]);
            if(empty($isword)){
                $temp["isword"]=1;
            }else if($isword=='0'){
                $temp["isword"]=1;
            }else if($isword=='1'){
                $temp["isword"]=0;
            }
            $flag=true;
            $explainidrs=array();
            while($flag){
                //验证单词是否存在
                $wordids='w'.$temp["wordid"];
                $explainids='e'.$temp["explainid"];
                $id=md5($wordids.$explainids);
                $rs=$words[$id];
                array_push($explainidrs, $temp["explainid"]);
                if(empty($rs)){
                    $arr[$id]=$temp;
                    $flag=false;
                }else{
                    $map['id']  = array('not in',$explainidrs);
                    $map['base_wordid']=$temp["wordid"];
                    $explainrss=$basewordexplains->where($map)->find();
                    //查询这个单词的释义
                    if(empty($explainrss)){
                        $temp["morphology"]="";
                        $temp["explainid"]=0;
                        $temp["explains"]="";
                        $v["iserr"] = true;
                        $v["errmsg"] = '第' . $row . '行单词已经存在';
                        $arr_excel[$row] = $v;
                        $iserr = true;
                        $flag=false;
                    }else{
                        $temp["morphology"]=$explainrss["morphology"];
                        $temp["explainid"]=$explainrss["id"];
                        $temp["explains"]=$explainrss["explains"];
                    }
                }  
            }   
        }
        //将数组分配到不同的组中
        shuffle($arr);
        $usql="update engs_word_game_level set wordnum=wordnum+".$i." where id=".$levelid;
        M()->execute($usql);
        foreach($arr as $key=>$value){
            $wordids='w'.$value["wordid"];
            $explainids='e'.$value["explainid"];
            $id=md5($wordids.$explainids);
            $words[$id]=$value;
        }
        //保存到关卡的数组中
        $gamelevel->words=json_encode($words);
        $gamelevel->where("id=%d",$levelid)->save();
        //数据库中保存一份原始数据
        $gsql="update engs_word_game set wordnum=wordnum+".$i." where id=".$gameid;
        M()->execute($gsql);
        return $arr_excel;
    }

    //修改次序的列表
    public function upSortLevelList(){
        $data = stripslashes(I("data"));
        $data = rtrim($data, '"');
        $data = ltrim($data, '"');
        $data = str_replace('&quot;', '"', $data);
        $res = json_decode($data,true);
        foreach($res as $key=>$obj){
            $id=$obj["id"];
            $sortid=$obj["sortid"];
            M("word_game_level")->where("id=%d",$id)->setField("sortid",$sortid);
        }
    }

    //修改单词的顺序
    public function upSortLevelWordList(){
        $levelid=I("levelid");
        $data = stripslashes(I("data"));
        $data = rtrim($data, '"');
        $data = ltrim($data, '"');
        $data = str_replace('&quot;', '"', $data);
        $res = json_decode($data,true);
        //根据一个字段进行排序
        $res=sortt($res,"sortid","int");
        $rs=M("word_game_level")->where("id=%d",$levelid)->find();
        $words=json_decode($rs["words"],true);
        foreach($res as $obj){
            $wordid=$obj["id"];
            $wordsarr[$wordid]=$words[$wordid];
        }
        $words=json_encode($wordsarr);
        M("word_game_level")->words=$words;
        M("word_game_level")->where("id=%d",$levelid)->save();
    }

    //获取单词类型
    public function getWordTypeByWord(){
        $word=I("word/s");
        $sql="select distinct isword from engs_base_word where word='%s' order by isword desc";
        $rs=M()->query($sql,$word);
        $this->ajaxReturn($rs);
    }

    //添加单词
    public function addLevelWords(){
        $wordid=I("wordid/d",0);
        $explainid=I("explainid/d",0);
        $levelid=I("levelid/d");
        if(empty($wordid)||empty($explainid)){
            $ret["succ"]=0;
            $ret["msg"]="添加失败";
        }else{
            $baseword=M("base_word");
            $basewordexplains=M("base_word_explains");
            $basers=$baseword->where("id=%d",$wordid)->find();
            $temp["word"]=$basers["word"];
            $temp["wordid"]=$basers["id"];
            $temp["isword"]=$basers["isword"];
            $temp["ukmark"]=$basers["ukmark"];
            $explainrs=$basewordexplains->where("id=%d",$explainid)->find();
            $temp["morphology"]=$explainrs["morphology"];
            $temp["explainid"]=$explainrs["id"];
            $temp["explains"]=$explainrs["explains"];
            $temp["isdo"]=0;
            $wordids='w'.$temp["wordid"];
            $explainids='e'.$temp["explainid"];
            $id=md5($wordids.$explainids);
            //判断有没有
            $gamelevel=M("word_game_level");
            $rs=$gamelevel->where("id=%d",$levelid)->find();
            $words=json_decode($rs["words"],true);
            if(empty($words[$id])){
                $words[$id]=$temp;
                $gamelevel->words=json_encode($words);
                $gamelevel->wordnum=$rs["wordnum"]+1;
                $gamelevel->where("id=%d",$levelid)->save();
                $sql="update engs_word_game set wordnum=wordnum+1 where id=%d";
                M()->execute($sql,$rs["gameid"]);
                $ret["succ"]=1;
                $ret["msg"]="添加成功";
            }else{
                $ret["succ"]=0;
                $ret["msg"]="单词已经存在";
            }
        }
        $this->ajaxReturn($ret);
    }

    //生成试题
    public function geneLevelQuestion(){
        $levelid=I("levelid/d");
        $wordGameLevel=M("word_game_level");
        $word=D("word");
        $sql="select * from engs_word_game_level left join engs_word_game on engs_word_game_level.gameid=engs_word_game.id where engs_word_game_level.id=%d";
        $rs=M()->query($sql,$levelid);
        //必须的题型和备注的题型
        $questypes=$rs[0]["questypes"];
	$peroid=$rs[0]["peroid"];
        $questypes=explode("|", $questypes);

        $questypescount=count($questypes);
        $backupquestypes=$rs[0]["backupquestypes"];
        //生成主要的题型
        $words=json_decode($rs[0]["words"],true);
        $questions=array();
        $error=array();
        $quesnum=0;
        foreach($words as $key=>$value){
            $wordid=$value["wordid"];;
            $explainid=$value["explainid"];
            $quescount=0;
            $flag=false;
            // if($value["isdo"]=='1'){
            //     continue;
            // }
            for($i=0;$i<($questypescount-1);$i++){
                $questype=$questypes[$i];
                $questiony=$word->getReciteWordQuestion($questype,$wordid,$explainid,$peroid);
                if(!empty($questiony)){
                    $flag=true;
                    $quesnum=$quesnum+1;
                    $quescount=$quescount+1;
                    $id='w'.$wordid.'e'.$explainid.'q'.$questype.'d'.Date("HmdHis");
                    $id=md5($id);
                    $questions[$id]=$questiony;
                    $words[$key]["isdo"]=1;
                }
            }

            if($quescount<($questypescount-1)){
                if(!empty($backupquestypes)){
                    $questionn=$word->getReciteWordQuestion($backupquestypes,$wordid,$explainid,$peroid);
                    if(!empty($questionn)){
                        $quesnum=$quesnum+1;
                        $flag=true;
                        $id='w'.$wordid.'e'.$explainid.'q'.$backupquestypes.'d'.Date("HmdHis");
                        $id=md5($id);
                        $questions[$id]=$questionn;
                        $words[$key]["isdo"]=1;
                    }
                }
            }
        }
        //洗牌算法
        shuffle($questions);
        $arr=array();
        foreach($questions as $key=>$value){
            $id='w'.$value["wordid"].'e'.$value["explainid"].'q'.$value["questype"].'d'.Date("HmdHis");
            $id=md5($id);
            $arr[$id]=$value;
        }
        if(!empty($arr)){
            $wordGameLevel->quesnum=$quesnum;
            $wordGameLevel->words=json_encode($words);
            $wordGameLevel->questions=json_encode($arr);
            $wordGameLevel->where("id=%d",$levelid)->save();
        }
        $ret["msg"]=$error;
        $ret["succ"]=1;
        if(empty($error)){
            $ret["succ"]=0;
        }
        $this->ajaxReturn($ret);
    }


    //保存试题
    public function saveQuestion(){
        $levelid=I("levelid");
        $id=I("id");
        $wordid=I("wordid");
        $explainid=I("explainid");
        $question=I("question");
        $data = stripslashes($question);
        $data = rtrim($data, '"');
        $data = ltrim($data, '"');
        $data = str_replace('&quot;', '"', $data);
        $res = json_decode($data);
        $analysis=$res->analysis;
        $tncontent=$res->tncontent;
	$analysis=htmlspecialchars_decode($res->analysis);
        $tncontent=htmlspecialchars_decode($res->tncontent);
        $questype=$res->questype;
        $answer=$res->answer;
        $items=$res->items;
        $wordinfors=$this->getWordExplainsById($wordid,$explainid);
        $question=array();
        $levelrs=M("word_game_level")->where("id=%d",$levelid)->find();
        if(empty($id)){
            //不需要保存题干的
            if($questype==0||$questype==1||$questype==2||$questype==3||$questype==4||$questype==6){
                $questions=$levelrs["questions"];
                $questions=json_decode($questions,true);
                //$question["tncontent"]=$tncontent;
		if($questype==6){
                    $question["tncontent"]=explode(",",$wordinfors["letters"]);
                }else{
                    $question["tncontent"]=$tncontent;
                }
                $question["mp3"]=$wordinfors["usmp3"];
                $question["wordid"]=$wordid;
                $question["explainid"]=$explainid;
                $question["word"]=$wordinfors["word"];
                $question["questype"]=$questype;
                foreach($items as $key=>$value){
                    $item[$key]["flag"]=$value->flag;
                    $item[$key]["typeid"]=$value->itemtype;
                    if( $item[$key]["typeid"]=='1'){
                        //获取图片的base64编码
                        $item[$key]["path"]=$value->content;
                        $filepath=$value->content;
                        $image_info = getimagesize($filepath);
                        $base64_image_content = "data:{$image_info['mime']};base64," . chunk_split(base64_encode(file_get_contents($filepath)));
                        $item[$key]["content"]=$base64_image_content;
                    }else{
                        $item[$key]["content"]=$value->content;
                    }
                }
                $question["items"]=$item;
                $question["answer"]=$answer;
                $question["analyais"]=$analyais;
                $question["useranswer"]="";
                $question["iserror"]="";
                $id="w".$wordid."e".$explainid."q".$questype."d".Date("YmdHis");
                $id=md5($id);
                $questions[$id]=$question;
                //修改单词数量
                M("word_game_level")->questions=json_encode($questions);
                M("word_game_level")->quesnum=$levelrs["quesnum"]+1;
                M("word_game_level")->where("id=%d",$levelid)->save();
            }else if($questype==5||$questype==7||$questype==8){
                $questions=$levelrs["questions"];
                $questions=json_decode($questions,true);
                $question["tncontent"]=$tncontent;
                $question["mp3"]=$wordinfors["ukmp3"];
                $question["wordid"]=$wordid;
                $question["explainid"]=$explainid;
                $question["word"]=$wordinfors["word"];
                $question["questype"]=$questype;
                foreach($items as $key=>$value){
                    $item[$key]["flag"]=$value->flag;
                    $item[$key]["typeid"]=$value->itemtype;
                    if( $item[$key]["typeid"]=='1'){
                        //获取图片的base64编码
                        $item[$key]["path"]=$value->content;
                        $filepath=$value->content;
                        $image_info = getimagesize($filepath);
                        $base64_image_content = "data:{$image_info['mime']};base64," . chunk_split(base64_encode(file_get_contents($filepath)));
                        $item[$key]["content"]=$base64_image_content;
                    }else{
                        $item[$key]["content"]=$value->content;
                    }
                }
                $question["items"]=$item;
                $question["answer"]=$answer;
                $question["useranswer"]="";
                $question["analyais"]=$analyais;
                $question["iserror"]="";
                $id="w".$wordid."e".$explainid."q".$questype."d".Date("YmdHis");
                $id=md5($id);
                $questions[$id]=$question;
                //修改单词数量
                M("word_game_level")->questions=json_encode($questions);
                M("word_game_level")->quesnum=$levelrs["quesnum"]+1;
                M("word_game_level")->where("id=%d",$levelid)->save();
            }else if($questype==9){
                //需要保存解析
                $questions=$levelrs["questions"];
                $questions=json_decode($questions,true);
                $question["tncontent"]=$tncontent;
                $question["mp3"]=$wordinfors["ukmp3"];
                $question["wordid"]=$wordid;
                $question["explainid"]=$explainid;
                $question["word"]=$wordinfors["word"];
                $question["questype"]=$questype;
                foreach($items as $key=>$value){
                    $item[$key]["flag"]=$value->flag;
                    $item[$key]["typeid"]=$value->itemtype;
                    if( $item[$key]["typeid"]=='1'){
                        //获取图片的base64编码
                        $item[$key]["path"]=$value->content;
                        $filepath=$value->content;
                        $image_info = getimagesize($filepath);
                        $base64_image_content = "data:{$image_info['mime']};base64," . chunk_split(base64_encode(file_get_contents($filepath)));
                        $item[$key]["content"]=$base64_image_content;
                    }else{
                        $item[$key]["content"]=$value->content;
                    }
                }
                $question["items"]=$item;
                $question["answer"]=$answer;
                $question["analysis"]=$analysis;
                $question["useranswer"]="";
                $question["iserror"]="";
                $id="w".$wordid."e".$explainid."q".$questype."d".Date("YmdHis");
                $id=md5($id);
                $questions[$id]=$question;
                //修改单词数量
                M("word_game_level")->questions=json_encode($questions);
                M("word_game_level")->quesnum=$levelrs["quesnum"]+1;
                M("word_game_level")->where("id=%d",$levelid)->save();

            }else if($questype==10){
                //保存辨析的
                //需要保存解析
                $questions=$levelrs["questions"];
                $questions=json_decode($questions,true);
                $question["tncontent"]=$tncontent;
                $question["mp3"]=$wordinfors["ukmp3"];
                $question["wordid"]=$wordid;
                $question["explainid"]=$explainid;
                $question["word"]=$wordinfors["word"];
                $question["questype"]=$questype;
                foreach($items as $key=>$value){
                    $item[$key]["flag"]=$value->flag;
                    $item[$key]["typeid"]=$value->itemtype;
                    if( $item[$key]["typeid"]=='1'){
                        //获取图片的base64编码
                        $item[$key]["path"]=$value->content;
                        $filepath=$value->content;
                        $image_info = getimagesize($filepath);
                        $base64_image_content = "data:{$image_info['mime']};base64," . chunk_split(base64_encode(file_get_contents($filepath)));
                        $item[$key]["content"]=$base64_image_content;
                    }else{
                        $item[$key]["content"]=$value->content;
                    }
                }
                $question["items"]=$item;
                $question["answer"]=$answer;
                $question["analyais"]=$analyais;
                $question["useranswer"]="";
                $question["iserror"]="";
                $id="w".$wordid."e".$explainid."q".$questype."d".Date("YmdHis");
                $id=md5($id);
                $questions[$id]=$question;
                //修改单词数量
                M("word_game_level")->questions=json_encode($questions);
                M("word_game_level")->quesnum=$levelrs["quesnum"]+1;
                M("word_game_level")->where("id=%d",$levelid)->save();
            }
            

        }else{
            if($questype==0||$questype==1||$questype==2||$questype==3||$questype==4||$questype==6){
                $questions=$levelrs["questions"];
                $questions=json_decode($questions,true);
                foreach($items as $key=>$value){
                    $item[$key]["flag"]=$value->flag;
                    $item[$key]["typeid"]=$value->itemtype;
                    if( $item[$key]["typeid"]=='1'){
                        $item[$key]["path"]=$value->content;
                        //获取图片的base64编码
                        $filepath=$value->content;
                        $image_info = getimagesize($filepath);
                        $base64_image_content = "data:{$image_info['mime']};base64," . chunk_split(base64_encode(file_get_contents($filepath)));
                        $item[$key]["content"]=$base64_image_content;
                    }else{
                        $item[$key]["content"]=$value->content;
                    }
                }
                $questions[$id]["items"]=$item;
                $questions[$id]["answer"]=$answer;
                $questions[$id]["analysis"]=$analysis;
                M("word_game_level")->questions=json_encode($questions);
                M("word_game_level")->where("id=%d",$levelid)->save();
            }else if($questype==5||$questype==7||$questype==8){
                $questions=$levelrs["questions"];
                $questions=json_decode($questions,true);
                foreach($items as $key=>$value){
                    $item[$key]["flag"]=$value->flag;
                    $item[$key]["typeid"]=$value->itemtype;
                    if( $item[$key]["typeid"]=='1'){
                        $item[$key]["path"]=$value->content;
                        //获取图片的base64编码
                        $filepath=$value->content;
                        $image_info = getimagesize($filepath);
                        $base64_image_content = "data:{$image_info['mime']};base64," . chunk_split(base64_encode(file_get_contents($filepath)));
                        $item[$key]["content"]=$base64_image_content;
                    }else{
                        $item[$key]["content"]=$value->content;
                    }
                }
                $questions[$id]["tncontent"]=$tncontent;
                $questions[$id]["items"]=$item;
                $questions[$id]["answer"]=$answer;
                $questions[$id]["analysis"]=$analysis;
                M("word_game_level")->questions=json_encode($questions);
                M("word_game_level")->where("id=%d",$levelid)->save();
            }else if($questype==9){
                $analysis=$res->analysis;
                $questions=$levelrs["questions"];
                $questions=json_decode($questions,true);
                foreach($items as $key=>$value){
                    $item[$key]["flag"]=$value->flag;
                    $item[$key]["typeid"]=$value->itemtype;
                    if( $item[$key]["typeid"]=='1'){
                        $item[$key]["path"]=$value->content;
                        //获取图片的base64编码
                        $filepath=$value->content;
                        $image_info = getimagesize($filepath);
                        $base64_image_content = "data:{$image_info['mime']};base64," . chunk_split(base64_encode(file_get_contents($filepath)));
                        $item[$key]["content"]=$base64_image_content;
                    }else{
                        $item[$key]["content"]=$value->content;
                    }
                }
                $questions[$id]["tncontent"]=$tncontent;
                $questions[$id]["analysis"]=$analysis;
                $questions[$id]["items"]=$item;
                $questions[$id]["answer"]=$answer;
                M("word_game_level")->questions=json_encode($questions);
                M("word_game_level")->where("id=%d",$levelid)->save();
            }else if($questype==10){
                $quesargue=$res->quesargue;
                $questions=$levelrs["questions"];
                $questions=json_decode($questions,true);
                foreach($items as $key=>$value){
                    $item[$key]["flag"]=$value->flag;
                    $item[$key]["typeid"]=$value->itemtype;
                    if( $item[$key]["typeid"]=='1'){
                        $item[$key]["path"]=$value->content;
                        //获取图片的base64编码
                        $filepath=$value->content;
                        $image_info = getimagesize($filepath);
                        $base64_image_content = "data:{$image_info['mime']};base64," . chunk_split(base64_encode(file_get_contents($filepath)));
                        $item[$key]["content"]=$base64_image_content;
                    }else{
                        $item[$key]["content"]=$value->content;
                    }
                }
                $questions[$id]["analysis"]=$analysis;
                $questions[$id]["tncontent"]=$tncontent;
                $questions[$id]["items"]=$item;
                $questions[$id]["answer"]=$answer;
                M("word_game_level")->questions=json_encode($questions);
                M("word_game_level")->where("id=%d",$levelid)->save();
            }
        }
        
    }

    //试题编辑
    public function questionedit(){
        $levelid=I("levelid");
        $id=I("id");
        $levelrs=M("word_game_level")->where("id=%d",$levelid)->find();
        $questions=json_decode($levelrs["questions"],true);
        $question=$questions[$id];
        $wordid=$question["wordid"];
        $explainid=$question["explainid"];
        $questype=$question["questype"];
        $tncontent=$question["tncontent"];
        $analysis=$question["analysis"];
        $typeid=empty($question["items"][0]["typeid"])?0:$question["items"][0]["typeid"];
        $items=$question["items"];
        $answer=$question["answer"];
        $this->assign("wordid",$wordid);
        $this->assign("explainid",$explainid);
        $this->assign("questype",$questype);
        $this->assign("tncontent",$tncontent);
        $this->assign("typeid",$typeid);
        $this->assign("items",$items);
        $this->assign("answer",$answer);
        $this->assign("analysis",$analysis);
        $this->display();
    }

    public function randUpSort(){
        $levelid=I("levelid");
        $levelrs=M("word_game_level")->where("id=%d",$levelid)->find();
        $questions=json_decode($levelrs["questions"],true);
        $ids=array_keys($questions);
        shuffle($ids);
        $questionarr=array();
        foreach($ids as $key=>$value){
            $questionarr[$value]=$questions[$value];
        }
        M("word_game_level")->where("id=%d",$levelid)->setField("questions",json_encode($questionarr));
    }

    public function wordmove(){
        $levelid=I("levelid");
        $gameid=M("word_game_level")->where("id=%d",$levelid)->find();
        $rs=M("word_game_level")->where("gameid=%d",$gameid["gameid"])->select();
        $this->assign("levellist",$rs);
        $this->assign("levelid",$levelid);
        $this->display();
    }

    public function changeLevelWord(){
        $oldlevel=I("newlevel/d");
        $newlevel=I("oldlevel/d");
        $bid=I("bid");
        $direct=I("direct/s");
        if($direct=='r'){
            $oldlevel=I("oldlevel/d");
            $newlevel=I("newlevel/d");
            $bid=I("bid");
        }
        $newrs=M("word_game_level")->where("id=%d",$newlevel)->find();
        $oldrs=M("word_game_level")->where("id=%d",$oldlevel)->find();
        $newwords=json_decode($newrs["words"],true);
        $oldwords=json_decode($oldrs["words"],true);
        
        $newwords[$bid]=$oldwords[$bid];
        unset($oldwords[$bid]);
        M("word_game_level")->where("id=%d",$newlevel)->setField("words",json_encode($newwords));
        //跟新单词数量
        $sql="update engs_word_game_level set wordnum=wordnum+1 where id=%d";
        M()->execute($sql,$newlevel);
        M("word_game_level")->where("id=%d",$oldlevel)->setField("words",json_encode($oldwords));
        //跟新单词数量
        $sql="update engs_word_game_level set wordnum=wordnum-1 where id=%d";
        M()->execute($sql,$oldlevel);
    }


    // public function dodata(){
    //     $sql="select * from engs_word_game_level";
    //     $rs=M()->query($sql);
    //     foreach($rs as $key=>$value){
    //         $words=json_decode($value["words"],true);
    //         $arr=array();
    //         foreach($words as $k=>$v){
    //             $wordids='w'.$v["wordid"];
    //             $explainids='e'.$v["explainid"];
    //             $id=md5($wordids.$explainids);
    //             $arr[$id]=$v;
    //         }
    //         M("word_game_level")->where("id=%d",$value["id"])->setField("words",json_encode($arr));
    //     }
    // }




}
