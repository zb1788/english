<?php

namespace Manager\Controller;

use Think\Controller;

class BookController extends Controller {
    
    public function getBookList(){
        $ks_code=I("ks_code");
        $listrs=M("book")->where("ks_code='%s' and isdel=1",$ks_code)->order("sortid,id")->select();
        $sql="SELECT T2.id,T2.name FROM (SELECT @r AS _id,(SELECT @r := parentid FROM engs_book_cataglory WHERE id = _id) AS parent_id,@l := @l + 1 AS lvl FROM (SELECT @r := ".$ks_code.",@l := 0) vars,engs_book_cataglory h WHERE @r <> 0) T1 JOIN engs_book_cataglory T2 ON T1._id = T2.id ORDER BY T1.lvl DESC ";
        $rs=M()->query($sql);

        $data["data"]=$listrs;
        foreach($listrs as $key=>$value){
            $listrs[$key]["parentname"]=$rs[0]["name"].".".$rs[1]["name"].".".$rs[2]["name"];
        }
        $this->ajaxReturn($listrs);
    }


    public function bookpiclist(){
        $id=I("id");
        $this->assign("pageid",$id);
        $this->display();
    }
    
    public function publish(){
        $id=I("id");
        $checked=I("checked");
        $listrs=M("book")->where("id=%d",$id)->setField("status",$checked);
    }
    //获取书籍
     //获取书籍
    public function findBookByTitle(){
        $content=I("content/s");
        if(!empty($content)){
            $sql="select * from engs_book t where t.isdel=1 and t.name like '%".$content."%'";
            $listrs=M("book")->query($sql);
            foreach($listrs as $key=>$value){
                $sql="SELECT T2.id,T2.name FROM (SELECT @r AS _id,(SELECT @r := parentid FROM engs_book_cataglory WHERE id = _id) AS parent_id,@l := @l + 1 AS lvl FROM (SELECT @r := ".$value["ks_code"].",@l := 0) vars,engs_book_cataglory h WHERE @r <> 0) T1 JOIN engs_book_cataglory T2 ON T1._id = T2.id ORDER BY T1.lvl DESC ";
                $rs=M()->query($sql);
                $listrs[$key]["parentname"]=$rs[0]["name"].".".$rs[1]["name"].".".$rs[2]["name"];
            }
        }else{
            $listrs=array();
        }
        $this->ajaxReturn($listrs);
    }

    public function delBook(){
        $id=I("id");
        $listrs=M("book")->where("id=%d",$id)->setField("isdel","0");
    }

    public function delBookPic(){
        $id=I("id");
        $listrs=M("book_pic")->where("id=%d",$id)->setField("isdel","0");
    }

    public function edit(){
        $id=I("id",0);
        if(!empty($id)){
            $listrs=M("book")->where("id=%d",$id)->find();
            $catrs=M("book_cataglory")->where("id=%d",$listrs["ks_code"])->find();
            $this->assign("name",$listrs["name"]);
            $this->assign("filename",$listrs["pic"]);
            $this->assign("catname",$catrs["name"]);
            $this->assign("ks_code",$catrs["id"]);
        }else{
            $this->assign("name","");
            $this->assign("filename","");
            $this->assign("catname","");
            $this->assign("ks_code",0);
        }
        $this->assign("id",$id);
        $this->display();
    }


    public function editBook(){
        $ks_code=I("ks_code");
        $id=I("id/d",0);
        $name=I("name");
        $filename=I("filename");
        $book=M("book");
        $arr=array();
        if(empty($id)){
            $book->ks_code=$ks_code;
            $book->name=$name;
            $book->pic=$filename;
            $bid=$book->add();
            if($bid){
                $arr["succ"]=1;
            }else{
                $arr["succ"]=0;
            }
        }else{
            $book->ks_code=$ks_code;
            $book->name=$name;
            $book->pic=$filename;
            $bid=$book->where("id=%d",$id)->save();
            if($bid){
                $arr["succ"]=1;
            }else{
                $arr["succ"]=0;
            }
        }
        $this->ajaxReturn($arr);
    }


    public function editBookImg(){
        $ks_code=I("ks_code");
        $id=I("id");
        $bookdata=I("book");
        $book=M("book");
        $arr=array();
        if(empty($id)){
            $book->name=$bookdata->name;
            $book->wordlist=$bookdata->words;
            $book->pic=$bookdata->pic;
            $bid=$book->add();
            if($bid){
                $arr["succ"]=1;
            }else{
                $arr["succ"]=0;
            }
        }else{
            $book->name=$bookdata->name;
            $book->wordlist=$bookdata->words;
            $book->pic=$bookdata->pic;
            $bid=$book->where("id=%d",$id)->save();
            if($bid){
                $arr["succ"]=1;
            }else{
                $arr["succ"]=0;
            }
        }
        $this->ajaxReturn($arr);
    }



    public function editBookImgQuestion(){
        $id=I("id");
        $bookid=I("bookid");
        $page=I("page");
        $questions=I("questions");
        $book=M("book");
        $arr=array();
        if(empty($id)){
            $book->name=$bookdata->name;
            $book->wordlist=$bookdata->words;
            $book->pic=$bookdata->pic;
            $bid=$book->add();
            if($bid){
                $arr["succ"]=1;
            }else{
                $arr["succ"]=0;
            }
        }else{
            $book->name=$bookdata->name;
            $book->wordlist=$bookdata->words;
            $book->pic=$bookdata->pic;
            $bid=$book->where("id=%d",$id)->save();
            if($bid){
                $arr["succ"]=1;
            }else{
                $arr["succ"]=0;
            }
        }
        $this->ajaxReturn($arr);
    }


    public function getBookPicList(){
        $bookid=I("bookid");
        $bookpic=M("book_pic");
        $arr=$bookpic->where("bookid=%d and isdel=1",$bookid)->order("pageindex,id")->select();
        foreach($arr as $key=>$value){
            //页面内容
            $pagecontent=M("book_page_content")->where("bookpicid=%d",$value["id"])->order("sortid,id")->select();
            $arr[$key]["pagecontent"]=$pagecontent;
            //页面单词
            $sql="SELECT p.`id`,t.`word`,s.`explains`,s.`morphology` FROM engs_book_page_words p  LEFT JOIN engs_base_word t  ON p.`wordid` = t.`id`  LEFT JOIN engs_base_word_explains s ON s.`id` = p.`explainsid` WHERE  p.bookpicid= ".$value["id"]." ORDER BY p.sortid,p.id";
            $pagewords=M()->query($sql);
            $arr[$key]["pagewords"]=$pagewords;
            //页面问题
            $pagequestions=M("book_page_questions")->where("bookpicid=%d",$value["id"])->order("sortid,id")->select();
            foreach($pagequestions as $kk=>$vv){
                $pagequestions[$kk]["itemslist"]=json_decode($vv["items"]);
            }
            $arr[$key]["pagequestions"]=$pagequestions;
        }
        $this->ajaxReturn($arr);
    }

    public function bookSavePic(){
        $data=I("data");
        $bookid=I("bookid");
        $data = stripslashes($data);
        $data = rtrim($data, '"');
        $data = ltrim($data, '"');
        $data = str_replace('&quot;', '"', $data);
        $data = json_decode($data);
        $bookpic=M("book_pic");
        $key=1;
        foreach($data as $value){
            $bookpic->bookid=$bookid;
            $bookpic->filename=$value;
            $bookpic->pageindex=$key;
            $key=$key+1;
            $bookpic->add();
        }
    }


    public function bookPicEdit(){
        $id=I("id");
        $bookid=I("bookid");
        $filename=I("filename");
        $bookpic=M("book_pic");
        if(empty($id)){
            $bookpicrs=$bookpic->where("bookid=%d",$id)->field("max(pageindex) as pageindex")->find();
            $bookpic->bookid=$bookid;
            $bookpic->filename=$filename;
            $bookpic->pageindex=$bookpicrs["pageindex"]+1;
            $bookpic->add();
        }else{
            $bookpic->bookid=$bookid;
            $bookpic->filename=$filename;
            $bookpic->where("id=%d",$id)->save();
        }
    }

    public function setPageContent(){
        $id=I("id/d",0);
        $pageid=I("pageid/d",0);
        $encontent=I("encontent/s","");
        $cncontent=I("cncontent/s","");
        $filename=I("filename");
        $bookpiccontent=M("book_page_content");
        if(empty($id)){
            $bookpiccontent->bookpicid=$pageid;
            $bookpiccontent->encontent=htmlspecialchars_decode($encontent);
            $bookpiccontent->cncontent=$cncontent;
            $bookpiccontent->filename=$filename;
            $id=$bookpiccontent->add();
        }else{
            $bookpiccontent->bookpicid=$pageid;
            $bookpiccontent->encontent=htmlspecialchars_decode($encontent);
            $bookpiccontent->cncontent=$cncontent;
            $bookpiccontent->filename=$filename;
            $bookpiccontent->where("id=%d",$id)->save();
        }
        $arr["content"]=$content;
        $arr["id"]=$id;
        $arr["mp3"]=$filename;
        $this->ajaxReturn($arr);

    }


    public function setPageWord(){
        $id=I("id/d",0);
        $pageid=I("pageid/d",0);
        $explainsid=I("explainsid/d",0);
        $bookpicword=M("book_page_words");
        $rs=$bookpicword->where("bookpicid=%d and wordid=%d and explainsid=%d",$pageid,$id,$explainsid)->select();
        if(empty($rs)){
            $bookpicword->bookpicid=$pageid;
            $bookpicword->wordid=$id;
            $bookpicword->explainsid=$explainsid;
            $id=$bookpicword->add();
        }
    }

    public function delPageWord(){
        $id=I("id/d",0);
        $bookpicword=M("book_page_words");
        $bookpicword->where("id=%d",$id)->delete();
    }

    public function wordslist(){
        $pageid=I("pageid");
        $this->assign("pageid",$pageid);
        $this->display();
    }


    public function delPageContent(){
        $id=I("id/d",0);
        $bookpiccontent=M("book_page_content");
        $bookpiccontent->where("id=%d",$id)->delete();
    }


    public function delPageQuestion(){
        $id=I("id/d",0);
        $bookpiccontent=M("book_page_questions");
        $bookpiccontent->where("id=%d",$id)->delete();
    }

    public function setPageQuestion(){
        $id=I("id/d","");
        $tcontent=I("tcontent");
        $pageid=I("pageid");
        $answer=I("answer");
        $data=I("items");
        $data = stripslashes($data);
        $data = rtrim($data, '"');
        $data = ltrim($data, '"');
        $items = str_replace('&quot;', '"', $data);
        // $items = json_decode($data);
        $book_page_questions=M("book_page_questions");
        if(empty($id)){
            $book_page_questions->bookpicid=$pageid;
            $book_page_questions->tncontent=$tcontent;
            $book_page_questions->items=$items;
            $book_page_questions->answer=$answer;
            $book_page_questions->add();
        }else{
            $book_page_questions->tncontent=$tcontent;
            $book_page_questions->items=$items;
            $book_page_questions->answer=$answer;
            $book_page_questions->where("id=%d",$id)->save();
        }
        
    }

    public function getPicContentList(){
        $pageid=I("pageid/d");
        $pagecontent=M("book_page_content")->where("bookpicid=%d",$pageid)->order("sortid,id")->select();
        $this->ajaxReturn($pagecontent);
    }

    public function getPicWordList(){
        $pageid=I("pageid/d");
        $sql="SELECT p.`id`,t.`word`,s.`explains`,s.`morphology` FROM engs_book_page_words p  LEFT JOIN engs_base_word t  ON p.`wordid` = t.`id`  LEFT JOIN engs_base_word_explains s ON p.`explainsid` = s.`id` WHERE  p.bookpicid= ".$pageid." ORDER BY p.sortid,p.id";
        $pagewords=M()->query($sql);
        $this->ajaxReturn($pagewords);
    }

    public function getPicQuestionList(){
        $pageid=I("pageid/d");
        $pagequestions=M("book_page_questions")->where("bookpicid=%d",$pageid)->order("sortid,id")->select();
        foreach($pagequestions as $kk=>$vv){
            $pagequestions[$kk]["itemslist"]=json_decode($vv["items"]);
        }
        $this->ajaxReturn($pagequestions);
    }


    public function findPageContent(){
        $pageid=I("pageid");
        $data=M("book_page_content")->where("id=%d",$pageid)->find();
        $this->ajaxReturn($data);
    }


    public function findPageQuestion(){
        $pageid=I("pageid");
        $data=M("book_page_questions")->where("id=%d",$pageid)->find();
        $data["itemslist"]=json_decode($data["items"]);
        $this->ajaxReturn($data);
    }

    public function getBaseWordList(){
        $page=I("page/d",0);
        $name=trim(I("name"));
        $User = M(); // 实例化User对象
        $sql="select * from engs_base_word t where t.word like '".$name."%'";
        $rs=M()->query($sql);
        $count      = count($rs);// 查询满足要求的总记录数
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $ssql="select * from engs_base_word t where t.word like '".$name."%' limit ".($page*25).",25";
        $list = $User->query($ssql);
        foreach($list as $key=>$value){
            $esql="select * from engs_base_word_explains t where t.base_wordid=".$value["id"];
            $ers=$User->query($esql);
            $list[$key]["explains"]=$ers;
        }
        $show="<div>";
        if($page!=0){
            $show=$show.'<a class="start" href="javascript:getWordList(\''.$name.'\',0);">首页</a>';
            $show=$show.'<a class="next" href="javascript:getWordList(\''.$name.'\','.($page-1).');"><<</a>';
        }
        for($i=0;$i<ceil($count/25);$i++){
            if($i==$page){
                $show=$show.'<span class="current">'.($i+1).'</span>';
            }else{
                $show=$show.'<a class="num" href="javascript:getWordList(\''.$name.'\','.$i.');">'.($i+1).'</a>';
            }
        }
        if($page!=(ceil($count/25)-1)){
            $show=$show.'<a class="next" href="javascript:getWordList(\''.$name.'\','.($page+1).');">>></a>';
            $show=$show.'<a class="end" href="javascript:getWordList(\''.$name.'\','.(ceil($count/25)-1).');">尾页</a>';
        }
        $show=$show.'</div>';
        $arr["list"]=$list;
        $arr["page"]=$show;
        $this->ajaxReturn($arr);
    }


    //获取分类节点
    public function getNodes(){
        $id=I("id/d",0);
        $book_cataglory=M("book_cataglory");
        $rs=$book_cataglory->where("parentid=%d",$id)->order("sortid")->select();
        $ret=array();
        foreach($rs as $key=>$value){
            $ret[$key]["id"]=$value["id"];
            $ret[$key]["name"]=$value["name"];
            //查看下面时候有子类
            $childrs=$book_cataglory->where("parentid=%d",$value["id"])->order("sortid,id")->count();
            if($childrs>0){
                $ret[$key]["isParent"]=true;
            }else{
                $ret[$key]["isParent"]=false;
            } 
        }
        $this->ajaxReturn($ret);
    }

    //编辑节点
    public function editNodes(){
        $name=I("name");
        $pid=I("pid");
        $id=I("id/d",0);
        $book_cataglory=M("book_cataglory");
        if(empty($id)){
            $book_cataglory->name=$name;
            $book_cataglory->parentid=$pid;
            $id=$book_cataglory->add();
        }else{
            $book_cataglory->name=$name;
            $book_cataglory->where("id=%d",$id)->save();
        }
        $ret["id"]=$id;
        $this->ajaxReturn($ret);
    }

    //删除节点
    public function removeNodes(){
        $id=I("id/d");
        //删除孩子
        $book_cataglory=M("book_cataglory");
        $book_cataglory->where("parentid=%d",$id)->delete();
        $book_cataglory->where("id=%d",$id)->delete();
    }

    //获取试题
    public function getQuestionById(){
        $id=I("id/d",0);
        $bookpagequestions=M("book_page_questions");
        $content="";
        $items=array();
        if(empty($id)){
            $item["content"]="";
            $item["ischecked"]=0;
            array_push($items,$item);
        }else{
            $rs=$bookpagequestions->where("id=%d",$id)->find();
            $content=$rs["tncontent"];
            $questionitems=json_decode($rs["items"],true);
            foreach($questionitems as $key=>$value){
                $item["content"]=$value["content"];
                $item["ischecked"]=0;
                if($value["content"]==$rs["answer"]){
                    $item["ischecked"]=1;
                }
                array_push($items,$item);
            }
        }
        $ret["tncontent"]=$content;
        $ret["items"]=$items;
        $this->ajaxReturn($ret);
    }

    public function questionedit(){
        $id=I("id/d",0);
        $picid=I("picid/d",0);
        $this->assign("id",$id);
        $this->assign("picid",$picid);
        $this->display();
    }


    public function questioneditbyid(){
        $id=I("id");
        $picid=I("picid");
        $tncontent=I("tncontent");
        $data=I("data");
        $data = stripslashes($data);
        $data = rtrim($data, '"');
        $data = ltrim($data, '"');
        $data = str_replace('&quot;', '"', $data);
        $question=json_decode($data,true);
        // $items = json_decode($data);
        $book_page_questions=M("book_page_questions");
        if(empty($id)){
            $book_page_questions->bookpicid=$picid;
            $book_page_questions->tncontent=html_entity_decode($tncontent);
            $items=$question;
            $quesitmes=array();
            $answer="";
            foreach($items as $key=>$value){
                $item["content"]=$value["content"];
                array_push($quesitmes,$item);
                if($value["ischecked"]==1){
                    $answer=$value["content"];
                }
            }
            $book_page_questions->items=json_encode($quesitmes);
            $book_page_questions->answer=$answer;
            $book_page_questions->add();
        }else{
            $book_page_questions->tncontent=html_entity_decode($tncontent);
            $items=$question;
            $quesitmes=array();
            $answer="";
            foreach($items as $key=>$value){
                $item["content"]=$value["content"];
                array_push($quesitmes,$item);
                if($value["ischecked"]==1){
                    $answer=$value["content"];
                }
            }
            $book_page_questions->items=json_encode($quesitmes);
            $book_page_questions->answer=$answer;
            $book_page_questions->where("id=%d",$id)->save();
        }

    }
	public function bookpiclistup(){
        $data = stripslashes(I("data"));
        $data = rtrim($data, '"');
        $data = ltrim($data, '"');
        $data = str_replace('&quot;', '"', $data);
        $data = json_decode($data,true);
        $book_pic=M("book_pic");
        foreach($data as $key=>$obj){
            $book_pic->pageindex=$obj["sortid"];
            $book_pic->where("id=%d",$obj["id"])->save();
        }

    }

    public function bookwordlistup(){
        $data = stripslashes(I("data"));
        $data = rtrim($data, '"');
        $data = ltrim($data, '"');
        $data = str_replace('&quot;', '"', $data);
        $data = json_decode($data,true);
        $book_pic=M("book_page_wortds");
        foreach($data as $key=>$obj){
            $book_pic->sortid=$obj["sortid"];
            $book_pic->where("id=%d",$obj["id"])->save();
        }

    }

    public function booklistup(){
        $data = stripslashes(I("data"));
        $data = rtrim($data, '"');
        $data = ltrim($data, '"');
        $data = str_replace('&quot;', '"', $data);
        $data = json_decode($data,true);
        $book_pic=M("book");
        foreach($data as $key=>$obj){
            $book_pic->sortid=$obj["sortid"];
            $book_pic->where("id=%d",$obj["id"])->save();
        }

    }
    public  function  addSortBookPic(){
        $id=I("id");
        $bookid=I("bookid");
        $filename=I("filename");
        if(empty($id)){
            M()->execute("update engs_book_pic set pageindex=pageindex+1 where bookid=".$bookid);
            $bookpic=M("book_pic");
            $bookpic->bookid=$bookid;
            $bookpic->filename=$filename;
            $bookpic->pageindex=1;
            $bookpic->add();
        }else{
            //查询上一个的sortid
            $rs=M("book_pic")->where("id=%d",$id)->find();
            //查询这本书几的bookid
            $bookid=$rs["bookid"];
            $sortid=$rs["sortid"];
            //查询所有的在进行更新
            $allrs=M("book_pic")->where("bookid=%d",$bookid)->order("pageindex,id")->select();
            $flag=false;
            foreach($allrs as $key=>$value){
                if($value["id"]==$id){
                    $flag=true;
                }else{
                    if($flag){
                        M()->execute("update engs_book_pic set pageindex=pageindex+1 where id=".$value["id"]);
                    }
                } 
            }
            //添加当前的图片
            $bookpic=M("book_pic");
            $data["bookid"]=$bookid;
            $data["filename"]=$filename;
            $data["pageindex"]=$rs["pageindex"]+1;
            $bookpic->add($data);
        }
    }
     
}

?>
