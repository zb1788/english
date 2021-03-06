<?php

namespace Manager\Controller;

use Think\Controller;

/**
 * 单词控制器
 *  
 * @author         gm 
 * @since          1.0 
 */
class QuestionsController extends CheckController {
	public function questions(){
		$unitid=I("unitid/s");
		$unitname=M("rms_unit")->where("ks_code='%s'",$unitid)->find();
		$this->assign("unitname",$unitname["ks_name"]);
		$data=I("data");
		$data = stripslashes(I("data"));
        $data = rtrim($data, '"');
        $data = ltrim($data, '"');
        $words = str_replace('&quot;', '"', $data);
		$this->assign("words",$words);
		$this->assign("unitid",$unitid);
		$this->display();
	}

	public function genQuestions(){
		$data = stripslashes(I("data"));
        $data = rtrim($data, '"');
        $data = ltrim($data, '"');
        $data = str_replace('&quot;', '"', $data);
        $words = json_decode(trim($data,chr(239).chr(187).chr(191)));
		$unitid=I("unitid/s");
		$period=I("period/d");
		$itemsnum=I("itemsnum/d");
		$questions=array();
		foreach($words as $obj){
			$wordid=$obj->keynode;
			$questype=$obj->value;
			foreach($questype as $key=>$value){
				$question=array();
				if($value=='1'){
					continue;
				}else{
					$wordsql="select t.id,s.ukmp3,s.word,z.morphology,z.explains,z.pic,r.r_grade,r.r_volume,r.r_subject,r.r_version from engs_word t left join engs_base_word s  on t.`base_wordid` = s.`id`  left join engs_base_word_explains z on t.base_explainsid=z.id left join engs_rms_unit r on t.ks_code=r.ks_code where t.id=".$wordid;
					$wordrs=M()->query($wordsql);
					$word=$wordrs[0]["word"];
					$explains=$wordrs[0]["morphology"].$wordrs[0]["explains"];
					$grade=$wordrs[0]["r_grade"];
					$volume=$wordrs[0]["r_volume"];
					$subject=$wordrs[0]["r_subject"];
					$version=$wordrs[0]["r_version"];
					$question["questype"]=$key;
					$question["wordid"]=$wordrs[0]["id"];
					$question["period"]=$period;
					//小学
					if($period==0){
						switch($key){
							//首先是第一种就是单词
							case 0:
								//题干是音频
								$question["tcontent"]=$wordrs[0]["ukmp3"];
								$question["mp3"]=$wordrs[0]["ukmp3"];
								$question["tts"]=$wordrs[0]["word"];
								$question["stemtype"]="0";
								$question["word"]=$word;
								$question["itemtype"]='0';
								$itemsarr=array();
								$ids=array();
								$words=array();
								$itemtemp=array();
								$itemtemp[0]=$wordrs[0]["word"];
								array_push($ids,$wordrs[0]["id"]);
								array_push($words,$wordrs[0]["word"]);
								$itemssql="select t.id,s.word,z.morphology,z.explains,z.pic from engs_word t left join engs_base_word s  on t.`base_wordid` = s.id left join engs_base_word_explains z on t.base_explainsid=z.id where t.ks_code='".$unitid."' and t.id!=".$wordid." and MD5(s.word)!='".md5($word)."' and z.morphology='".$wordrs[0]['morphology']."' order by rand() limit ".($itemsnum-1);
								$itemsrs=M()->query($itemssql);
								if(empty($itemsrs)){}else{
									$itemlen=count($itemtemp);
									foreach($itemsrs as $tkey=>$tvalue){
										if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
										$itemtemp[$itemlen+$key]=$tvalue["word"];
									}
										if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											array_push($ids,$tvalue["id"]);
											array_push($words,$tvalue["word"]);
										}
									}
								}
								if(count($itemtemp)<=$itemsnum-1){
									$itemssql="select t.id,s.word,z.morphology,z.explains,z.pic from engs_word t left join engs_base_word s  on t.`base_wordid` = s.id left join engs_base_word_explains z on t.base_explainsid=z.id where t.ks_code='".$unitid."' ";
									if(!empty($ids)){
										$itemssql=$itemssql.'and t.id not in ("'.implode($ids,",").'") ';
									}
									if(!empty($words)){
										$itemssql=$itemssql.'and t.word not in ("'.implode($words,'","').'") ';
									}
									$itemssql=$itemssql."order by rand() limit ".($itemsnum-count($itemsarr));
									$itemsrs=M()->query($itemssql);
									if(empty($itemsrs)){}else{
										$itemlen=count($itemtemp);
										foreach($itemsrs as $tkey=>$tvalue){
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											$itemtemp[$itemlen+$key]=$tvalue["word"];
										}
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
												array_push($ids,$tvalue["id"]);
												array_push($words,$tvalue["word"]);
											}
										}
									}
								}
								if(count($itemtemp)<=$itemsnum-1){
									$itemssql="select t.id,s.word,z.morphology,z.explains,z.pic from engs_word t left join engs_base_word s  on t.`base_wordid` = s.id left join engs_base_word_explains z on t.base_explainsid=z.id where t.ks_code in (select ks_code from engs_rms_unit where r_grade='".$grade."' and r_volume='".$volume."' and r_subject='".$subject."' and r_version='".$version."') ";
									if(!empty($ids)){
										$itemssql=$itemssql.'and t.id not in ("'.implode($ids,",").'") ';
									}
									if(!empty($words)){
										$itemssql=$itemssql.'and t.word not in ("'.implode($words,'","').'") ';
									}
									$itemssql=$itemssql."and z.morphology='".$wordrs[0]['morphology']."' order by rand() limit ".($itemsnum-count($itemsarr));
									$itemsrs=M()->query($itemssql);
									if(empty($itemsrs)){}else{
										$itemlen=count($itemtemp);
										foreach($itemsrs as $tkey=>$tvalue){
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											$itemtemp[$itemlen+$key]=$tvalue["word"];
										}
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
												array_push($ids,$tvalue["id"]);
												array_push($words,$tvalue["word"]);
											}
										}
									}
								}
								if(count($itemtemp)<=$itemsnum-1){
									$itemssql="select t.id,s.word,z.morphology,z.explains,z.pic from engs_word t left join engs_base_word s  on t.`base_wordid` = s.id left join engs_base_word_explains z on t.base_explainsid=z.id where t.ks_code in (select ks_code from engs_rms_unit where r_grade='".$grade."' and r_volume='".$volume."' and r_subject='".$subject."' and r_version='".$version."') ";
									if(!empty($ids)){
										$itemssql=$itemssql.'and t.id not in ("'.implode($ids,",").'") ';
									}
									if(!empty($words)){
										$itemssql=$itemssql.'and t.word not in ("'.implode($words,'","').'") ';
									}
									$itemssql=$itemssql."  order by rand() limit ".($itemsnum-count($itemsarr));
									$itemsrs=M()->query($itemssql);
									if(empty($itemsrs)){}else{
										$itemlen=count($itemtemp);
										foreach($itemsrs as $tkey=>$tvalue){
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											$itemtemp[$itemlen+$key]=$tvalue["word"];
										}
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
												array_push($ids,$tvalue["id"]);
												array_push($words,$tvalue["word"]);
											}
										}
									}
								}
								shuffle($itemtemp);
								$answer="";
								foreach($itemtemp as $sk=>$sv){
									$flag=chr(65+$sk);
									$content=$sv;
									$itemtemps["flag"]=$flag;
									$itemtemps["content"]=$content;
									$itemtemps["itemtype"]='0';
									if($content==$word){
										$answer=$flag;
									}
									array_push($itemsarr,$itemtemps);
								}
								$question["items"]=$itemsarr;
								$question["answer"]=$answer;
								array_push($questions,$question);
							break;
							//汉语意思
							case 1:
							    //题干是图片
								$question["tcontent"]=$wordrs[0]["pic"];
								$question["mp3"]=$wordrs[0]["ukmp3"];
								$question["tts"]=$word;
								// $question["mp3"]="";
								// $question["tts"]="";
								$question["word"]=$word;
								$question["stemtype"]="1";
								$question["itemtype"]='0';
								$itemsarr=array();
								$ids=array();
								$words=array();
								$itemtemp=array();
								$itemtemp[0]=$wordrs[0]["word"];
								array_push($ids,$wordrs[0]["id"]);
								array_push($words,$wordrs[0]["word"]);
								$itemssql="select t.id,s.word,z.morphology,z.explains,z.pic from engs_word t left join engs_base_word s  on s.id=t.base_wordid left join engs_base_word_explains z on t.base_explainsid=z.id where t.ks_code='".$unitid."' and t.id!=".$wordid." and MD5(s.word)!='".md5($word)."' and z.morphology='".$wordrs[0]['morphology']."' order by rand() limit ".($itemsnum-1);
								$itemsrs=M()->query($itemssql);
								if(empty($itemsrs)){}else{
									$itemlen=count($itemtemp);
									foreach($itemsrs as $tkey=>$tvalue){
										if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
										$itemtemp[$itemlen+$key]=$tvalue["word"];
									}
										if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											array_push($ids,$tvalue["id"]);
											array_push($words,$tvalue["word"]);
										}
									}
								}
								if(count($itemtemp)<=$itemsnum-1){
									$itemssql="select t.id,s.word,z.morphology,z.explains,z.pic from engs_word t left join engs_base_word s  on s.id=t.base_wordid left join engs_base_word_explains z on t.base_explainsid=z.id where t.ks_code='".$unitid."' ";
									if(!empty($ids)){
										$itemssql=$itemssql.'and t.id not in ("'.implode($ids,",").'") ';
									}
									if(!empty($words)){
										$itemssql=$itemssql.'and t.word not in ("'.implode($words,'","').'") ';
									}
									$itemssql=$itemssql." order by rand() limit ".($itemsnum-count($itemsarr));
									$itemsrs=M()->query($itemssql);
									if(empty($itemsrs)){}else{
										$itemlen=count($itemtemp);
										foreach($itemsrs as $tkey=>$tvalue){
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											$itemtemp[$itemlen+$key]=$tvalue["word"];
										}
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											array_push($ids,$tvalue["id"]);
											array_push($words,$tvalue["word"]);
										}
										}
									}
								}
								if(count($itemtemp)<=$itemsnum-1){
									$itemssql="select t.id,s.word,z.morphology,z.explains,z.pic from engs_word t left join engs_base_word s  on s.id=t.base_wordid left join engs_base_word_explains z on t.base_explainsid=z.id where t.ks_code in (select ks_code from engs_rms_unit where r_grade='".$grade."' and r_volume='".$volume."' and r_subject='".$subject."' and r_version='".$version."') ";
																	if(!empty($ids)){
										$itemssql=$itemssql.'and t.id not in ("'.implode($ids,",").'") ';
									}
									if(!empty($words)){
										$itemssql=$itemssql.'and t.word not in ("'.implode($words,'","').'") ';
									}
									$itemssql=$itemssql." and z.morphology='".$wordrs[0]['morphology']."' order by rand() limit ".($itemsnum-count($itemsarr));
									$itemsrs=M()->query($itemssql);
									if(empty($itemsrs)){}else{
										$itemlen=count($itemtemp);
										foreach($itemsrs as $tkey=>$tvalue){
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											$itemtemp[$itemlen+$key]=$tvalue["word"];
										}
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											array_push($ids,$tvalue["id"]);
											array_push($words,$tvalue["word"]);
										}
										}
									}
								}
								if(count($itemtemp)<=$itemsnum-1){
									$itemssql="select t.id,s.word,z.morphology,z.explains,z.pic from engs_word t left join engs_base_word s  on s.id=t.base_wordid left join engs_base_word_explains z on t.base_explainsid=z.id where t.ks_code in (select ks_code from engs_rms_unit where r_grade='".$grade."' and r_volume='".$volume."' and r_subject='".$subject."' and r_version='".$version."') ";
									if(!empty($ids)){
										$itemssql=$itemssql.'and t.id not in ("'.implode($ids,",").'") ';
									}
									if(!empty($words)){
										$itemssql=$itemssql.'and t.word not in ("'.implode($words,'","').'") ';
									}
									$itemssql=$itemssql." order by rand() limit ".($itemsnum-count($itemsarr));
									$itemsrs=M()->query($itemssql);
									if(empty($itemsrs)){}else{
										$itemlen=count($itemtemp);
										foreach($itemsrs as $tkey=>$tvalue){
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											$itemtemp[$itemlen+$key]=$tvalue["word"];
										}
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											array_push($ids,$tvalue["id"]);
											array_push($words,$tvalue["word"]);
										}
										}
									}
								}
								shuffle($itemtemp);
								$answer="";
								foreach($itemtemp as $sk=>$sv){
									$flag=chr(65+$sk);
									$content=$sv;
									$itemtemps["flag"]=$flag;
									$itemtemps["content"]=$content;
									$itemtemps["itemtype"]='0';
									if($content==$word){
										$answer=$flag;
									}
									array_push($itemsarr,$itemtemps);
								}
								$question["items"]=$itemsarr;
								$question["answer"]=$answer;
								array_push($questions,$question);
							break;
							//题干是图片的
							case 2:
							    //题干是句子的音频
							    $textsql='select * from engs_text t left join engs_text_chapter s on t.chapterid=s.id where s.ks_code="'.$unitid.'" and t.isdel=1 and s.isdel=1 and  t.encontent like "% '.$word.' %" and t.stateid=1  order by rand() limit 1 ';
								$textrs=M()->query($textsql);
								$question["tcontent"]=$textrs[0]["mp3"].".mp3";
								$question["mp3"]=$textrs[0]["mp3"].".mp3";
								$question["tts"]=$textrs[0]["encontent"];
								$question["itemtype"]='0';
								$text=$textrs[0]["encontent"];
								if(empty($text)){
									break;
								}
								$question["word"]=$word;
								$question["stemtype"]="2";
								$itemsarr=array();
								$ids=array();
								$words=array();
								$itemtemp=array();
								$itemtemp[0]=$text;
								//选出来选项
								$itemssql='select * from engs_text t left join engs_text_chapter s on t.chapterid=s.id where s.ks_code="'.$unitid.'" and t.isdel=1 and s.isdel=1 and t.encontent not like "% '.$word.' %" and t.stateid=1 order by rand()  limit '.($itemsnum-count($itemsarr));
								$itemsrs=M()->query($itemssql);
								if(!empty($itemsrs)){
									$itemlen=count($itemtemp);
									foreach($itemsrs as $tkey=>$tvalue){
										if(!empty($tvalue["id"])&&$tvalue["id"]!=null&&$tvalue["encontent"]!=null&&$tvalue["encontent"]!='null'){
											if(count($itemtemp)<=($itemsnum-1)){
												$itemtemp[$itemlen+$tkey]=$tvalue["encontent"];
											}
										}
									}
								}
								if(count($itemtemp)<=$itemsnum-1){
									$itemssql='select * from engs_text t left join engs_text_chapter s on t.chapterid=s.id  where s.ks_code in (select ks_code from engs_rms_unit where r_grade="'.$grade.'" and r_volume="'.$volume.'" and r_subject="'.$subject.'" and r_version="'.$version.'") and t.isdel=1 and s.isdel=1 and t.encontent not like "% '.$word.' %"  and t.stateid=1 order by rand() limit '.($itemsnum-count($itemsarr));
									$itemsrs=M()->query($itemssql);
									if(!empty($itemsrs)){
										$itemlen=count($itemtemp);
										foreach($itemsrs as $tkey=>$tvalue){
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null&&$tvalue["encontent"]!=null&&$tvalue["encontent"]!='null'){
												if(count($itemtemp)<=($itemsnum-1)){
													$itemtemp[$itemlen+$tkey]=$tvalue["encontent"];
												}
											}
										}
									}
								}
								shuffle($itemtemp);
								$answer="";
								foreach($itemtemp as $sk=>$sv){
									$flag=chr(65+$sk);
									$content=$sv;
									$itemtemps["flag"]=$flag;
									$itemtemps["content"]=$content;
									$itemtemps["itemtype"]='0';
									if($content==$text){
										$answer=$flag;
									}
									array_push($itemsarr,$itemtemps);
								}
								$question["items"]=$itemsarr;
								$question["answer"]=$answer;
								array_push($questions,$question);
							break;
							case 3:
								//题干是音频
								$question["tcontent"]=$wordrs[0]["ukmp3"];
								$question["mp3"]=$wordrs[0]["ukmp3"];
								$question["tts"]=$word;
								$question["stemtype"]="3";
								$question["itemtype"]='1';
								$question["word"]=$word;
								$itemsarr=array();
								$ids=array();
								$words=array();
								$itemtemp=array();
								$itemtemp[0]=$wordrs[0]["word"];
								array_push($ids,$wordrs[0]["id"]);
								array_push($words,$wordrs[0]["word"]);
								$itemssql="select t.id,s.word,z.morphology,z.explains,z.pic from engs_word t left join engs_base_word s  on t.`base_wordid` = s.id left join engs_base_word_explains z on t.base_explainsid=z.id where t.ks_code='".$unitid."' and t.id!=".$wordid." and MD5(s.word)!='".md5($word)."' and z.morphology='".$wordrs[0]['morphology']."' order by rand() limit ".($itemsnum-1);
								$itemsrs=M()->query($itemssql);
								if(empty($itemsrs)){}else{
									$itemlen=count($itemtemp);
									foreach($itemsrs as $tkey=>$tvalue){
										if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
										$itemtemp[$itemlen+$key]=$tvalue["pic"];
									}
										if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											array_push($ids,$tvalue["id"]);
											array_push($words,$tvalue["word"]);
										}
									}
								}
								if(count($itemtemp)<=$itemsnum-1){
									$itemssql="select t.id,s.word,z.morphology,z.explains,z.pic from engs_word t left join engs_base_word s  on t.`base_wordid` = s.id left join engs_base_word_explains z on t.base_explainsid=z.id where t.ks_code='".$unitid."' ";
									if(!empty($ids)){
										$itemssql=$itemssql.'and t.id not in ("'.implode($ids,",").'") ';
									}
									if(!empty($words)){
										$itemssql=$itemssql.'and t.word not in ("'.implode($words,'","').'") ';
									}
									$itemssql=$itemssql."order by rand() limit ".($itemsnum-count($itemsarr));
									$itemsrs=M()->query($itemssql);
									if(empty($itemsrs)){}else{
										$itemlen=count($itemtemp);
										foreach($itemsrs as $tkey=>$tvalue){
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											$itemtemp[$itemlen+$key]=$tvalue["pic"];
										}
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
												array_push($ids,$tvalue["id"]);
												array_push($words,$tvalue["word"]);
											}
										}
									}
								}
								if(count($itemtemp)<=$itemsnum-1){
									$itemssql="select t.id,s.word,z.morphology,z.explains,z.pic from engs_word t left join engs_base_word s  on t.`base_wordid` = s.id left join engs_base_word_explains z on t.base_explainsid=z.id where t.ks_code in (select ks_code from engs_rms_unit where r_grade='".$grade."' and r_volume='".$volume."' and r_subject='".$subject."' and r_version='".$version."') ";
									if(!empty($ids)){
										$itemssql=$itemssql.'and t.id not in ("'.implode($ids,",").'") ';
									}
									if(!empty($words)){
										$itemssql=$itemssql.'and t.word not in ("'.implode($words,'","').'") ';
									}
									$itemssql=$itemssql."and z.morphology='".$wordrs[0]['morphology']."' order by rand() limit ".($itemsnum-count($itemsarr));
									$itemsrs=M()->query($itemssql);
									if(empty($itemsrs)){}else{
										$itemlen=count($itemtemp);
										foreach($itemsrs as $tkey=>$tvalue){
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											$itemtemp[$itemlen+$key]=$tvalue["pic"];
										}
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
												array_push($ids,$tvalue["id"]);
												array_push($words,$tvalue["word"]);
											}
										}
									}
								}
								if(count($itemtemp)<=$itemsnum-1){
									$itemssql="select t.id,s.word,z.morphology,z.explains,z.pic from engs_word t left join engs_base_word s  on t.`base_wordid` = s.id left join engs_base_word_explains z on t.base_explainsid=z.id where t.ks_code in (select ks_code from engs_rms_unit where r_grade='".$grade."' and r_volume='".$volume."' and r_subject='".$subject."' and r_version='".$version."') ";
									if(!empty($ids)){
										$itemssql=$itemssql.'and t.id not in ("'.implode($ids,",").'") ';
									}
									if(!empty($words)){
										$itemssql=$itemssql.'and t.word not in ("'.implode($words,'","').'") ';
									}
									$itemssql=$itemssql."  order by rand() limit ".($itemsnum-count($itemsarr));
									$itemsrs=M()->query($itemssql);
									if(empty($itemsrs)){}else{
										$itemlen=count($itemtemp);
										foreach($itemsrs as $tkey=>$tvalue){
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											$itemtemp[$itemlen+$key]=$tvalue["pic"];
										}
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
												array_push($ids,$tvalue["id"]);
												array_push($words,$tvalue["word"]);
											}
										}
									}
								}
								shuffle($itemtemp);
								$answer="";
								foreach($itemtemp as $sk=>$sv){
									$flag=chr(65+$sk);
									$content=$sv;
									$itemtemps["flag"]=$flag;
									$itemtemps["content"]=$content;
									$itemtemps["itemtype"]='1';
									if($content==$word){
										$answer=$flag;
									}
									array_push($itemsarr,$itemtemps);
								}
								$question["items"]=$itemsarr;
								$question["answer"]=$answer;
								array_push($questions,$question);
							break;
						}
					}else if($period==1){
						//初中的试题
						switch($key){
							//题干是句子
							case 0:
								//题干是音频
								$question["tcontent"]=$wordrs[0]["ukmp3"];
								$question["mp3"]=$wordrs[0]["ukmp3"];
								$question["tts"]=$word;
								$question["stemtype"]="0";
								$question["itemtype"]='0';
								$question["word"]=$word;
								$itemsarr=array();
								$ids=array();
								$words=array();
								$itemtemp=array();
								$itemtemp[0]=$wordrs[0]["morphology"].$wordrs[0]["explains"];
								array_push($ids,$wordrs[0]["id"]);
								array_push($words,$wordrs[0]["word"]);
								$itemssql="select t.id,s.word,z.morphology,z.explains,z.pic from engs_word t left join engs_base_word s  on t.`base_wordid` = s.id left join engs_base_word_explains z on t.base_explainsid=z.id where t.ks_code='".$unitid."' and t.id!=".$wordid." and MD5(s.word)!='".md5($word)."' and z.morphology='".$wordrs[0]['morphology']."' order by rand() limit ".($itemsnum-1);
								$itemsrs=M()->query($itemssql);
								if(empty($itemsrs)){}else{
									$itemlen=count($itemtemp);
									foreach($itemsrs as $tkey=>$tvalue){
										if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
										$itemtemp[$itemlen+$key]=$tvalue["morphology"].$tvalue["explains"];
									}
										if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											array_push($ids,$tvalue["id"]);
											array_push($words,$tvalue["word"]);
										}
									}
								}
								if(count($itemtemp)<=$itemsnum-1){
									$itemssql="select t.id,s.word,z.morphology,z.explains,z.pic from engs_word t left join engs_base_word s  on t.`base_wordid` = s.id left join engs_base_word_explains z on t.base_explainsid=z.id where t.ks_code='".$unitid."' ";
									if(!empty($ids)){
										$itemssql=$itemssql.'and t.id not in ("'.implode($ids,",").'") ';
									}
									if(!empty($words)){
										$itemssql=$itemssql.'and t.word not in ("'.implode($words,'","').'") ';
									}
									$itemssql=$itemssql."order by rand() limit ".($itemsnum-count($itemsarr));
									$itemsrs=M()->query($itemssql);
									if(empty($itemsrs)){}else{
										$itemlen=count($itemtemp);
										foreach($itemsrs as $tkey=>$tvalue){
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											$itemtemp[$itemlen+$key]=$tvalue["morphology"].$tvalue["explains"];
										}
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											array_push($ids,$tvalue["id"]);
											array_push($words,$tvalue["word"]);
										}
										}
									}
								}
								if(count($itemtemp)<=$itemsnum-1){
									$itemssql="select t.id,s.word,z.morphology,z.explains,z.pic from engs_word t left join engs_base_word s  on t.`base_wordid` = s.id left join engs_base_word_explains z on t.base_explainsid=z.id where t.ks_code in (select ks_code from engs_rms_unit where r_grade='".$grade."' and r_volume='".$volume."' and r_subject='".$subject."' and r_version='".$version."') ";
									if(!empty($ids)){
										$itemssql=$itemssql.'and t.id not in ("'.implode($ids,",").'") ';
									}
									if(!empty($words)){
										$itemssql=$itemssql.'and t.word not in ("'.implode($words,'","').'") ';
									}
									$itemssql=$itemssql."and z.morphology='".$wordrs[0]['morphology']."' order by rand() limit ".($itemsnum-count($itemsarr));
									$itemsrs=M()->query($itemssql);
									if(empty($itemsrs)){}else{
										$itemlen=count($itemtemp);
										foreach($itemsrs as $tkey=>$tvalue){
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											$itemtemp[$itemlen+$key]=$tvalue["morphology"].$tvalue["explains"];
										}
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											array_push($ids,$tvalue["id"]);
											array_push($words,$tvalue["word"]);
										}
										}
									}
								}
								if(count($itemtemp)<=$itemsnum-1){
									$itemssql="select t.id,s.word,z.morphology,z.explains,z.pic from engs_word t left join engs_base_word s  on t.`base_wordid` = s.id left join engs_base_word_explains z on t.base_explainsid=z.id where t.ks_code in (select ks_code from engs_rms_unit where r_grade='".$grade."' and r_volume='".$volume."' and r_subject='".$subject."' and r_version='".$version."') ";
									if(!empty($ids)){
										$itemssql=$itemssql.'and t.id not in ("'.implode($ids,",").'") ';
									}
									if(!empty($words)){
										$itemssql=$itemssql.'and t.word not in ("'.implode($words,'","').'") ';
									}
									$itemssql=$itemssql."  order by rand() limit ".($itemsnum-count($itemsarr));
									$itemsrs=M()->query($itemssql);
									if(empty($itemsrs)){}else{
										$itemlen=count($itemtemp);
										foreach($itemsrs as $tkey=>$tvalue){
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											$itemtemp[$itemlen+$key]=$tvalue["morphology"].$tvalue["explains"];
										}
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											array_push($ids,$tvalue["id"]);
											array_push($words,$tvalue["word"]);
										}
										}
									}
								}
								shuffle($itemtemp);
								$answer="";
								foreach($itemtemp as $sk=>$sv){
									$flag=chr(65+$sk);
									$content=$sv;
									$itemtemps["flag"]=$flag;
									$itemtemps["content"]=$content;
									$itemtemps["itemtype"]='0';
									if($content==$explains){
										$answer=$flag;
									}
									array_push($itemsarr,$itemtemps);
								}
								$question["items"]=$itemsarr;
								$question["answer"]=$answer;
								array_push($questions,$question);	
							break;
							//含有这个的单词句子
							case 1:
								//题干是单词
								$question["tcontent"]=$word;
								$question["mp3"]=$wordrs[0]["ukmp3"];
								$question["tts"]=$word;
								$question["stemtype"]="1";
								$question["itemtype"]='0';
								$question["word"]=$word;
								$itemsarr=array();
								$ids=array();
								$words=array();
								$itemtemp=array();
								$itemtemp[0]=$wordrs[0]["morphology"].$wordrs[0]["explains"];
								array_push($ids,$wordrs[0]["id"]);
								array_push($words,$wordrs[0]["word"]);
								$itemssql="select t.id,s.word,z.morphology,z.explains,z.pic from engs_word t left join engs_base_word s  on t.`base_wordid` = s.id left join engs_base_word_explains z on t.base_explainsid=z.id where t.ks_code='".$unitid."' and t.id!=".$wordid." and MD5(s.word)!='".md5($word)."' and z.morphology='".$wordrs[0]['morphology']."' order by rand() limit ".($itemsnum-1);
								$itemsrs=M()->query($itemssql);
								if(empty($itemsrs)){}else{
									$itemlen=count($itemtemp);
									foreach($itemsrs as $tkey=>$tvalue){
										if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
										$itemtemp[$itemlen+$key]=$tvalue["morphology"].$tvalue["explains"];
									}
										if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											array_push($ids,$tvalue["id"]);
											array_push($words,$tvalue["word"]);
										}
									}
								}
								if(count($itemtemp)<=$itemsnum-1){
									$itemssql="select t.id,s.word,z.morphology,z.explains,z.pic from engs_word t left join engs_base_word s  on t.`base_wordid` = s.id left join engs_base_word_explains z on t.base_explainsid=z.id where t.ks_code='".$unitid."' ";
									if(!empty($ids)){
										$itemssql=$itemssql.'and t.id not in ("'.implode($ids,",").'") ';
									}
									if(!empty($words)){
										$itemssql=$itemssql.'and t.word not in ("'.implode($words,'","').'") ';
									}
									$itemssql=$itemssql."order by rand() limit ".($itemsnum-count($itemsarr));
									$itemsrs=M()->query($itemssql);
									if(empty($itemsrs)){}else{
										$itemlen=count($itemtemp);
										foreach($itemsrs as $tkey=>$tvalue){
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											$itemtemp[$itemlen+$key]=$tvalue["morphology"].$tvalue["explains"];
										}
											
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											array_push($ids,$tvalue["id"]);
											array_push($words,$tvalue["word"]);
										}
										}
									}
								}
								if(count($itemtemp)<=$itemsnum-1){
									$itemssql="select t.id,s.word,z.morphology,z.explains,z.pic from engs_word t left join engs_base_word s  on t.`base_wordid` = s.id left join engs_base_word_explains z on t.base_explainsid=z.id where t.ks_code in (select ks_code from engs_rms_unit where r_grade='".$grade."' and r_volume='".$volume."' and r_subject='".$subject."' and r_version='".$version."') ";
									if(!empty($ids)){
										$itemssql=$itemssql.'and t.id not in ("'.implode($ids,",").'") ';
									}
									if(!empty($words)){
										$itemssql=$itemssql.'and t.word not in ("'.implode($words,'","').'") ';
									}
									$itemssql=$itemssql."and z.morphology='".$wordrs[0]['morphology']."' order by rand() limit ".($itemsnum-count($itemsarr));
									$itemsrs=M()->query($itemssql);
									if(empty($itemsrs)){}else{
										$itemlen=count($itemtemp);
										foreach($itemsrs as $tkey=>$tvalue){
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											$itemtemp[$itemlen+$key]=$tvalue["morphology"].$tvalue["explains"];
										}
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											array_push($ids,$tvalue["id"]);
											array_push($words,$tvalue["word"]);
										}
										}
									}
								}
								if(count($itemtemp)<=$itemsnum-1){
									$itemssql="select t.id,s.word,z.morphology,z.explains,z.pic from engs_word t left join engs_base_word s  on t.`base_wordid` = s.id left join engs_base_word_explains z on t.base_explainsid=z.id where t.ks_code in (select ks_code from engs_rms_unit where r_grade='".$grade."' and r_volume='".$volume."' and r_subject='".$subject."' and r_version='".$version."') ";
									if(!empty($ids)){
										$itemssql=$itemssql.'and t.id not in ("'.implode($ids,",").'") ';
									}
									if(!empty($words)){
										$itemssql=$itemssql.'and t.word not in ("'.implode($words,'","').'") ';
									}
									$itemssql=$itemssql."  order by rand() limit ".($itemsnum-count($itemsarr));
									$itemsrs=M()->query($itemssql);
									if(empty($itemsrs)){}else{
										$itemlen=count($itemtemp);
										foreach($itemsrs as $tkey=>$tvalue){
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											$itemtemp[$itemlen+$key]=$tvalue["morphology"].$tvalue["explains"];
										}
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											array_push($ids,$tvalue["id"]);
											array_push($words,$tvalue["word"]);
										}
										}
									}
								}
								shuffle($itemtemp);
								$answer="";
								foreach($itemtemp as $sk=>$sv){
									$flag=chr(65+$sk);
									$content=$sv;
									$itemtemps["flag"]=$flag;
									$itemtemps["content"]=$content;
									$itemtemps["itemtype"]='0';
									if($content==$explains){
										$answer=$flag;
									}
									array_push($itemsarr,$itemtemps);
								}
								$question["items"]=$itemsarr;
								$question["answer"]=$answer;
								array_push($questions,$question);	
							break;
							//挖出来句子进行
							case 2:
								//题干是意思
								$question["tcontent"]=$explains;
								$question["mp3"]=$wordrs[0]["ukmp3"];
								$question["tts"]=$word;
								$question["stemtype"]="2";
								$question["itemtype"]='0';
								$question["word"]=$word;
								$itemsarr=array();
								$ids=array();
								$words=array();
								$itemtemp=array();
								$itemtemp[0]=$wordrs[0]["word"];
								array_push($ids,$wordrs[0]["id"]);
								array_push($words,$wordrs[0]["word"]);
								$itemssql="select t.id,s.word,z.morphology,z.explains,z.pic from engs_word t left join engs_base_word s  on t.`base_wordid` = s.id left join engs_base_word_explains z on t.base_explainsid=z.id where t.ks_code='".$unitid."' and t.id!=".$wordid." and MD5(s.word)!='".md5($word)."' and z.morphology='".$wordrs[0]['morphology']."' order by rand() limit ".($itemsnum-1);
								$itemsrs=M()->query($itemssql);
								if(empty($itemsrs)){}else{
									$itemlen=count($itemtemp);
									foreach($itemsrs as $tkey=>$tvalue){
										if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
										$itemtemp[$itemlen+$key]=$tvalue["word"];
									}
										if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											array_push($ids,$tvalue["id"]);
											array_push($words,$tvalue["word"]);
										}
									}
								}
								if(count($itemtemp)<=$itemsnum-1){
									$itemssql="select t.id,s.word,z.morphology,z.explains,z.pic from engs_word t left join engs_base_word s  on t.`base_wordid` = s.id left join engs_base_word_explains z on t.base_explainsid=z.id where t.ks_code='".$unitid."' ";
									if(!empty($ids)){
										$itemssql=$itemssql.'and t.id not in ("'.implode($ids,",").'") ';
									}
									if(!empty($words)){
										$itemssql=$itemssql.'and t.word not in ("'.implode($words,'","').'") ';
									}
									$itemssql=$itemssql."order by rand() limit ".($itemsnum-count($itemsarr));
									$itemsrs=M()->query($itemssql);
									if(empty($itemsrs)){}else{
										$itemlen=count($itemtemp);
										foreach($itemsrs as $tkey=>$tvalue){
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											$itemtemp[$itemlen+$key]=$tvalue["word"];
										}
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											array_push($ids,$tvalue["id"]);
											array_push($words,$tvalue["word"]);
										}
										}
									}
								}
								if(count($itemtemp)<=$itemsnum-1){
									$itemssql="select t.id,s.word,z.morphology,z.explains,z.pic from engs_word t left join engs_base_word s  on t.`base_wordid` = s.id left join engs_base_word_explains z on t.base_explainsid=z.id where t.ks_code in (select ks_code from engs_rms_unit where r_grade='".$grade."' and r_volume='".$volume."' and r_subject='".$subject."' and r_version='".$version."') ";
									if(!empty($ids)){
										$itemssql=$itemssql.'and t.id not in ("'.implode($ids,",").'") ';
									}
									if(!empty($words)){
										$itemssql=$itemssql.'and t.word not in ("'.implode($words,'","').'") ';
									}
									$itemssql=$itemssql."and z.morphology='".$wordrs[0]['morphology']."' order by rand() limit ".($itemsnum-count($itemsarr));
									$itemsrs=M()->query($itemssql);
									if(empty($itemsrs)){}else{
										$itemlen=count($itemtemp);
										foreach($itemsrs as $tkey=>$tvalue){
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											$itemtemp[$itemlen+$key]=$tvalue["word"];
										}
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											array_push($ids,$tvalue["id"]);
											array_push($words,$tvalue["word"]);
										}
										}
									}
								}
								if(count($itemtemp)<=$itemsnum-1){
									$itemssql="select t.id,s.word,z.morphology,z.explains,z.pic from engs_word t left join engs_base_word s  on t.`base_wordid` = s.id left join engs_base_word_explains z on t.base_explainsid=z.id where t.ks_code in (select ks_code from engs_rms_unit where r_grade='".$grade."' and r_volume='".$volume."' and r_subject='".$subject."' and r_version='".$version."') ";
									if(!empty($ids)){
										$itemssql=$itemssql.'and t.id not in ("'.implode($ids,",").'") ';
									}
									if(!empty($words)){
										$itemssql=$itemssql.'and t.word not in ("'.implode($words,'","').'") ';
									}
									$itemssql=$itemssql."  order by rand() limit ".($itemsnum-count($itemsarr));
									$itemsrs=M()->query($itemssql);
									if(empty($itemsrs)){}else{
										$itemlen=count($itemtemp);
										foreach($itemsrs as $tkey=>$tvalue){
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											$itemtemp[$itemlen+$key]=$tvalue["word"];
										}
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											array_push($ids,$tvalue["id"]);
											array_push($words,$tvalue["word"]);
										}

										}
									}
								}
								shuffle($itemtemp);
								$answer="";
								foreach($itemtemp as $sk=>$sv){
									$flag=chr(65+$sk);
									$content=$sv;
									$itemtemps["flag"]=$flag;
									$itemtemps["content"]=$content;
									$itemtemps["itemtype"]='0';
									if($content==$word){
										$answer=$flag;
									}
									array_push($itemsarr,$itemtemps);
								}
								$question["items"]=$itemsarr;
								$question["answer"]=$answer;
								array_push($questions,$question);	
							break;
							case 3:
								//题干是课文内容
								$textsql='select * from engs_text t left join engs_text_chapter s on t.chapterid=s.id where s.ks_code="'.$unitid.'" and t.isdel=1 and s.isdel=1 and  t.encontent like "%'.$word.'%" and t.stateid=1  order by rand() limit 1 ';
								$textrs=M()->query($textsql);
								$findposition=stripos($textrs[0]["encontent"],$word);
								$iscase=$findposition==0?1:0;
								$findposition=$findposition+strlen($word);
								$question["tcontent"]=str_ireplace($word,"___",substr($textrs[0]["encontent"], 0,$findposition));
								$question["tcontent"]=$question["tcontent"].substr($textrs[0]["encontent"], $findposition);
								if(!empty($question["tcontent"])){
									$question["tcontent"]=$question["tcontent"]."<br/>".$textrs[0]["cncontent"];
									$question["stemtype"]="3";
									// $question["mp3"]="";
									// $question["tts"]="";
									$question["mp3"]=$textrs[0]["mp3"].".mp3";
									$question["tts"]=$textrs[0]["encontent"];
									// $question["mp3"]=$wordrs[0]["ukmp3"];
									// $question["tts"]=$word;
									$question["word"]=$word;
									$question["itemtype"]='0';
									$itemsarr=array();
									$ids=array();
									$words=array();
									$itemtemp=array();
									$itemtemp[0]=$wordrs[0]["word"];
									if($iscase==1){
										$itemtemp[0]=strtoupper(substr($wordrs[0]["word"], 0,1)).substr($wordrs[0]["word"], 1);
									}
									array_push($ids,$wordrs[0]["id"]);
									array_push($words,$wordrs[0]["word"]);
									$itemssql="select t.id,s.word,z.morphology,z.explains,z.pic from engs_word t left join engs_base_word s  on t.`base_wordid` = s.id left join engs_base_word_explains z on t.base_explainsid=z.id where t.ks_code='".$unitid."' and t.id!=".$wordid." and MD5(s.word)!='".md5($word)."' and z.morphology='".$wordrs[0]['morphology']."' order by rand() limit ".($itemsnum-1);
									$itemsrs=M()->query($itemssql);
									if(empty($itemsrs)){}else{
										$itemlen=count($itemtemp);
										foreach($itemsrs as $tkey=>$tvalue){
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
											$itemtemp[$itemlen+$key]=$tvalue["word"];
											if($iscase==1){
												$itemtemp[$itemlen+$key]=strtoupper(substr($tvalue["word"], 0,1)).substr($tvalue["word"], 1);
											}
										}
											if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
												array_push($ids,$tvalue["id"]);
												array_push($words,$tvalue["word"]);
											}
										}
									}
									if(count($itemtemp)<=$itemsnum-1){
										$itemssql="select t.id,s.word,z.morphology,z.explains,z.pic from engs_word t left join engs_base_word s  on t.`base_wordid` = s.id left join engs_base_word_explains z on t.base_explainsid=z.id where t.ks_code='".$unitid."' ";
										if(!empty($ids)){
											$itemssql=$itemssql.'and t.id not in ("'.implode($ids,",").'") ';
										}
										if(!empty($words)){
											$itemssql=$itemssql.'and t.word not in ("'.implode($words,'","').'") ';
										}
										$itemssql=$itemssql."order by rand() limit ".($itemsnum-count($itemsarr));
										$itemsrs=M()->query($itemssql);
										if(empty($itemsrs)){}else{
											$itemlen=count($itemtemp);
											foreach($itemsrs as $tkey=>$tvalue){
												if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
												$itemtemp[$itemlen+$key]=$tvalue["word"];
												if($iscase==1){
													$itemtemp[$itemlen+$key]=strtoupper(substr($tvalue["word"], 0,1)).substr($tvalue["word"], 1);
												}
											}
												if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
												array_push($ids,$tvalue["id"]);
												array_push($words,$tvalue["word"]);
											}
											}
										}
									}
									if(count($itemtemp)<=$itemsnum-1){
										$itemssql="select t.id,s.word,z.morphology,z.explains,z.pic from engs_word t left join engs_base_word s  on t.`base_wordid` = s.id left join engs_base_word_explains z on t.base_explainsid=z.id where t.ks_code in (select ks_code from engs_rms_unit where r_grade='".$grade."' and r_volume='".$volume."' and r_subject='".$subject."' and r_version='".$version."') ";
										if(!empty($ids)){
											$itemssql=$itemssql.'and t.id not in ("'.implode($ids,",").'") ';
										}
										if(!empty($words)){
											$itemssql=$itemssql.'and t.word not in ("'.implode($words,'","').'") ';
										}
										$itemssql=$itemssql."and z.morphology='".$wordrs[0]['morphology']."' order by rand() limit ".($itemsnum-count($itemsarr));
										$itemsrs=M()->query($itemssql);
										if(empty($itemsrs)){}else{
											$itemlen=count($itemtemp);
											foreach($itemsrs as $tkey=>$tvalue){
												if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
													$itemtemp[$itemlen+$key]=$tvalue["word"];
													if($iscase==1){
														$itemtemp[$itemlen+$key]=strtoupper(substr($tvalue["word"], 0,1)).substr($tvalue["word"], 1);
													}
												}
												if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
												array_push($ids,$tvalue["id"]);
												array_push($words,$tvalue["word"]);
											}
											}
										}
									}
									if(count($itemtemp)<=$itemsnum-1){
										$itemssql="select t.id,s.word,z.morphology,z.explains,z.pic from engs_word t left join engs_base_word s  on t.`base_wordid` = s.id left join engs_base_word_explains z on t.base_explainsid=z.id where t.ks_code in (select ks_code from engs_rms_unit where r_grade='".$grade."' and r_volume='".$volume."' and r_subject='".$subject."' and r_version='".$version."') ";
										if(!empty($ids)){
											$itemssql=$itemssql.'and t.id not in ("'.implode($ids,",").'") ';
										}
										if(!empty($words)){
											$itemssql=$itemssql.'and t.word not in ("'.implode($words,'","').'") ';
										}
										$itemssql=$itemssql."  order by rand() limit ".($itemsnum-count($itemsarr));
										$itemsrs=M()->query($itemssql);
										if(empty($itemsrs)){}else{
											$itemlen=count($itemtemp);
											foreach($itemsrs as $tkey=>$tvalue){
												if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
													$itemtemp[$itemlen+$key]=$tvalue["word"];
													if($iscase==1){
														$itemtemp[$itemlen+$key]=strtoupper(substr($tvalue["word"], 0,1)).substr($tvalue["word"], 1);
													}
												}
												if(!empty($tvalue["id"])&&$tvalue["id"]!=null){
												array_push($ids,$tvalue["id"]);
												array_push($words,$tvalue["word"]);
											}
											}
										}
									}
									shuffle($itemtemp);
									$answer="";
									foreach($itemtemp as $sk=>$sv){
										$flag=chr(65+$sk);
										$content=$sv;
										$itemtemps["flag"]=$flag;
										$itemtemps["content"]=$content;
										$itemtemps["itemtype"]='0';
										if($content==$word){
											$answer=$flag;
										}
										array_push($itemsarr,$itemtemps);
									}
									$question["items"]=$itemsarr;
									$question["answer"]=$answer;
									array_push($questions,$question);
								}
							break;
						}
					}
				}
			}
		}
		$this->ajaxReturn($questions);//返回json数据
	}


	public function savePaper(){
		$paperid=I("paperid/d",0);
		if($paperid==0){
			$unitid=I("unitid");
			$unitname=I("unitname");
			$peroid=I("peroid");
			$data=htmlspecialchars_decode(I("data"));
			$question_paper=M("question_paper");
			$question_paper->peroid=$peroid;
			$question_paper->ks_code=$unitid;
			$question_paper->name=$unitname;
			$question_paper->data=$data;
			$question_paper->addtime=Date("Y-m-d H:i:s");
			$question_paper->add();
		}else{
			$unitname=I("unitname");
			$data=htmlspecialchars_decode(I("data"));
			$question_paper=M("question_paper");
			$question_paper->name=$unitname;
			$question_paper->data=$data;
			$question_paper->state=1;
			$question_paper->addtime=Date("Y-m-d H:i:s");
			$question_paper->where("id=%d",$paperid)->save();
		}
		
		//将数据存储到exams中
		
	}

	//复制试卷
	public function copyPaper(){
		$id=I("paperid/d");
		$question_paper=M("question_paper");
		$rs=$question_paper->where("id=%d and isdel=1",$id)->find();
		$arr["name"]=$rs["name"]."副本";
		$arr["data"]=$rs["data"];
		$arr["addtime"]=Date("Y-m-d H:i:s");
		$arr["ks_code"]=$rs["ks_code"];
		$question_paper->add($arr);
	}

	//复制试卷
	public function translatePaper(){
		$id=I("paperid/d");
		$ks_code=I("ks_code/s");
		$question_paper=M("question_paper");
		$question_paper->where("id=%d and isdel=1",$id)->setField("ks_code",$ks_code);
	}
	
	public function getPaperList(){
		$unitid=I("unitid/s");
		$question_paper=M("question_paper");
		$rs=$question_paper->where("ks_code='%s' and isdel=1",$unitid)->select();
		$this->ajaxReturn($rs);
	}
	
	
	public function delPaper(){
		$id=I("id/d");
		$question_paper=M("question_paper");
		$rs=$question_paper->where("id=%d",$id)->setField("isdel","0");
	}
	
	
	public function publishPaper(){
		$id=I("id/d");
		$question_paper=M("question_paper");
		$rs=$question_paper->where("id=%d",$id)->setField("state","2");
	}
	
	
	public function editPaper(){
		$id=I("id/d");
		$question_paper=M("question_paper");
		$rs=$question_paper->where("id=%d",$id)->select();
		$this->ajaxReturn($rs);
	}
	
	
	public function edit(){
		$id=I("paperid/d");
		$question_paper=M("question_paper");
		$rs=$question_paper->where("id=%d and isdel=1",$id)->find();
		$this->assign("unitname",$rs["name"]);
		$this->assign("paperid",$id);
		$this->display();
	}

	public function translate(){
		$id=I("paperid/d");
		$gradeid=I("gradeid/s");
		$termid=I("termid/s");
		$versionid=I("versionid/s");
		$unitid=I("unitid/s");
		$this->assign("paperid",$id);
		$this->assign("gradeid",$gradeid);
		$this->assign("termid",$termid);
		$this->assign("versionid",$versionid);
		$this->assign("unitid",$unitid);
		$this->display();
	}



	/*进行试题的取出*/
	public function getQuestions(){
		$paperid=I("paperid");
		$question_paper=M("question_paper");
		$rs=$question_paper->where("id=%d and isdel=1",$paperid)->find();
		$questions=json_decode(htmlspecialchars_decode($rs["data"]));
		$this->ajaxReturn($questions);
	}

	//进行试题的预览修改
	public function preview(){
		$paperid=I("paperid");
		$question_paper=M("question_paper");
		$rs=$question_paper->where("id=%d and isdel=1",$paperid)->find();
		$this->assign("name",$rs["name"]);
		$this->assign("paperid",$paperid);
		$this->display();
	}
	
}
