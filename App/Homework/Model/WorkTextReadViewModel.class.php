<?php
namespace Homework\Model;
use Think\Model\ViewModel;
class WorkTextReadViewModel extends ViewModel {
   public $viewFields = array(
     'HomeworkTextRead'=>array('id','homeworkid'),
     'TextChapter'=>array('id'=>'chapterid','chapter','sortid','isdel'=>'cisdel','issection','isevaluate','_on'=>'HomeworkTextRead.chapterid=TextChapter.id'),
     'Text'=>array('id'=>'textid','sectionid','enbefore','encontent','cncontent','sortid','stateid','isdel'=>'tisdel','mp3','_on'=>'TextChapter.id=Text.chapterid'),
   );
}