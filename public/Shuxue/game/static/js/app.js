webpackJsonp([1],{"2b1k":function(t,e){},"4aA0":function(t,e,i){t.exports=i.p+"static/img/SignClear.f072d76.png"},"4j7q":function(t,e,i){t.exports=i.p+"static/img/cuo.757e220.png"},"65hl":function(t,e){},"E+8s":function(t,e,i){"use strict";var s=i("Eqm3"),n=i.n(s),o=i("4YfN"),a=i.n(o),r=i("YtJ0"),u=i("9rMa"),c={props:["question","nowIndex","isComplete","quesCount"],data:function(){return{filter:-1,filterRight:-1,filterWrong:-1,answerArr:["A","B","C","D","E","F"],isDo:!1,isShowJiexiButton:!1,isShowJiexi:!1,isShowNextButton:!1,isShowTijiaoButton:!0,isShowComplateButton:!1,isShowAnswer:!1,isRight:-1,isError:-1,userSelectArr:[],chooseArr:[]}},mounted:function(){},computed:{isReadOnly:function(){return this.isComplete?(this.chooseArr=this.$store.state.userSelectAnswerArr[this.nowIndex],this.isShowJiexiButton=!0,this.isShowTijiaoButton=!1,this.isShowJiexi=!1,this.isShowAnswer=!0,this.nowIndex>=this.quesCount-1?(this.isShowComplateButton=!0,this.isShowNextButton=!1):(this.isShowNextButton=!0,this.isShowComplateButton=!1),!0):(this.isShowJiexiButton=!1,this.isShowNextButton=!1,this.isShowTijiaoButton=!0,this.isShowJiexi=!1,this.isShowAnswer=!1,!1)},answerFormat:function(){var t=this.$store.state.userSelectAnswerArr[this.nowIndex],e="";for(var i in t)e+=this.answerArr[t[i]];return e}},store:r.a,methods:a()({},Object(u.b)(["add","reduce"]),{choose:function(t){if(this.isDo)return!1;this.isComplete||(this.chooseArr.indexOf(t)>-1?this.chooseArr.splice(this.chooseArr.indexOf(t),1):this.chooseArr.push(t))},findEleInArr:function(t,e){return n.a||(Array.prototype.indexOf=function(t){for(var e=0;e<this.length;e++)if(this[e]==t)return e;return-1}),e.indexOf(t)},submitQues:function(){this.isDo=!0,this.add(this.chooseArr),this.$store.state.writeNum=this.nowIndex;var t=this.chooseArr.sort().join(""),e="",i=this.question.info.answer.sort();for(var s in i)"A"==i[s]?e+="0":"B"==i[s]?e+="1":"C"==i[s]?e+="2":"D"==i[s]?e+="3":"E"==i[s]?e+="4":"F"==i[s]&&(e+="5");t==e?(console.log("right"),this.isRight=1):(console.log("wrong"),this.isRight=0),this.$emit("addAnswerLog",this.question.id,this.isRight,t),this.isError=this.isRight;var n=this;setTimeout(function(){n.isError=-1},1e3),this.isShowJiexiButton=!0,this.isShowTijiaoButton=!1,this.isShowAnswer=!0,this.nowIndex>=this.quesCount-1?(this.isShowComplateButton=!0,this.isShowNextButton=!1):(this.isShowNextButton=!0,this.isShowComplateButton=!1)},nextQuestion:function(){this.isDo=!1,this.$emit("next",this.question.id,this.isRight),this.isReadOnly?(this.isShowJiexiButton=!0,this.isShowTijiaoButton=!1,this.isShowJiexi=!1,this.isShowAnswer=!0,this.nowIndex>=this.quesCount-1?(this.isShowNextButton=!1,this.isShowComplateButton=!0):(this.isShowNextButton=!0,this.isShowComplateButton=!1)):(this.isShowJiexiButton=!1,this.isShowNextButton=!1,this.isShowTijiaoButton=!0,this.isShowJiexi=!1,this.isShowComplateButton=!1,this.isShowAnswer=!1),this.filter=-1,this.isRight=-1,this.chooseArr=[]},showJiexiContent:function(){this.isShowJiexi=!0},preQuestion:function(){this.chooseArr=[],this.filter=-1,this.isDo=!1,this.isShowJiexiButton=!1,this.isShowJiexi=!1,this.isShowTijiaoButton=!1,this.isShowNextButton=!0,this.isRight=-1,this.nowIndex>0&&this.$emit("prev")},gameOver:function(){this.$emit("next",this.question.id,this.isRight)}})},h={render:function(){var t=this,e=t.$createElement,s=t._self._c||e;return s("div",{staticClass:"timu",staticStyle:{display:"block"}},[s("div",{staticClass:"padR"},[s("h5",{staticClass:"textH5",domProps:{innerHTML:t._s(t.question.info.quesName)}}),t._v(" "),""!==t.question.info.quesContent?[1===t.question.info.quesContentType?s("p",{staticClass:"fenx"},[t._v(t._s(t.question.info.quesContent))]):t._e(),t._v(" "),2===t.question.info.quesContentType?s("img",{staticClass:"timu100",attrs:{src:t.question.info.quesContent}}):t._e()]:t._e(),t._v(" "),1===t.question.info.quesChoiceType?[s("ul",{staticClass:"listIww"},t._l(t.question.info.quesChoice,function(e,i){return s("li",{key:e.flag,class:[t.chooseArr.indexOf(i)>-1?"cur":""],staticStyle:{display:"inline-flex",width:"100%"},on:{click:function(e){t.choose(i)}}},[s("em",[t._v(t._s(e.flag)+".")]),t._v(" "),s("span",{domProps:{innerHTML:t._s(e.content)}})])}))]:t._e(),t._v(" "),2===t.question.info.quesChoiceType?[s("ul",{staticClass:"listImage tp"},t._l(t.question.info.quesChoice,function(e,i){return s("li",{key:e.flag,class:["table",t.chooseArr.indexOf(i)>-1?"cur":""],on:{click:function(e){t.choose(i)}}},[s("em",[t._v(t._s(e.flag)+".")]),t._v(" "),s("span",[s("img",{attrs:{src:e.content}})])])}))]:t._e()],2),t._v(" "),t.isShowAnswer?s("div",{staticClass:"padR"},[s("h6",{staticClass:"textH6"},[t._v("\n      您选择的答案"),s("font",{staticClass:"redFont"},[t._v(t._s(t.answerFormat))]),t._v(" ,\n      正确答案"),s("font",{staticClass:"redFont"},[t._v(t._s(this.question.info.answer.join(",")))])],1)]):t._e(),t._v(" "),s("div",{staticClass:"padR"},[t.isShowJiexiButton&&""!=t.question.info.jiexi?s("div",{staticClass:"look",on:{click:t.showJiexiContent}},[s("a",{staticClass:"ckjx"},[t._v("查看解析")])]):t._e(),t._v(" "),t.isShowJiexi?s("div",{staticClass:"look2 pad10"},[t._m(0),t._v(" "),s("p",{domProps:{innerHTML:t._s(t.question.info.jiexi)}})]):t._e()]),t._v(" "),s("transition",{attrs:{name:"bounce"}},[0==t.isError?s("div",{staticClass:"puanduan01"},[s("img",{staticClass:"img100",attrs:{src:i("4j7q")}})]):t._e(),t._v(" "),1==t.isError?s("div",{staticClass:"puanduan01"},[s("img",{staticClass:"img100",attrs:{src:i("xw+O")}})]):t._e()]),t._v(" "),s("div",{staticClass:"mb50"}),t._v(" "),s("div",{staticClass:"nrBtn"},[t.nowIndex>0?s("a",{on:{click:t.preQuestion}},[t._v("上一题")]):t._e(),t._v(" "),t.isShowTijiaoButton?s("a",{on:{click:t.submitQues}},[t._v("提交")]):t._e(),t._v(" "),t.isShowNextButton?s("a",{on:{click:t.nextQuestion}},[t._v("下一题")]):t._e(),t._v(" "),t.isShowComplateButton?s("a",{on:{click:t.gameOver}},[t._v("完成")]):t._e()]),t._v(" "),t.isReadOnly?s("div"):t._e()],1)},staticRenderFns:[function(){var t=this.$createElement,e=this._self._c||t;return e("p",[e("label",[this._v("解析:")])])}]};var l=i("C7Lr")(c,h,!1,function(t){i("hsdY")},null,null);e.a=l.exports},HToJ:function(t,e,i){t.exports=i.p+"static/img/SignOut.b5ae59d.png"},I6m9:function(t,e){},M5bs:function(t,e){},NHnr:function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var s=i("IvJb"),n=i("sEFh"),o={render:function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{attrs:{id:"app"}},[i("Head",{attrs:{time:t.time,now:t.count+1,total:t.questionJson.length,isBegTime:t.isStart,iswrongView:t.iswrongView,isHasQues:t.isHasQues},on:{gameOver:t.gameOver,goback:t.goback,goDel:t.goDel,emptyWrong:t.emptyWrong}}),t._v(" "),i("section",[t.isHasQues?t._e():i("Empty"),t._v(" "),1==t.question.quesType&&1==t.question.info.answerFlag&&t.isHasQues?i("Select",{attrs:{question:t.question,nowIndex:t.count,quesCount:t.questionJson.length,isComplete:t.isWrite},on:{next:t.addCount,prev:t.minusCount,addAnswerLog:t.addQuestionRecord}}):t._e(),t._v(" "),1==t.question.quesType&&2==t.question.info.answerFlag&&t.isHasQues?i("SelectMore",{attrs:{question:t.question,nowIndex:t.count,quesCount:t.questionJson.length,isComplete:t.isWrite},on:{next:t.addCount,prev:t.minusCount,addAnswerLog:t.addQuestionRecord}}):t._e(),t._v(" "),2==t.question.quesType&&t.isHasQues?i("TianKong",{attrs:{question:t.question},on:{addAnswerLog:t.addQuestionRecord,next:t.addCount}}):t._e()],1),t._v(" "),t.isShow?i("Dialog",{attrs:{gonggao:t.gonggao,rcode:t.rcode,plsurl:t.plsurl,userName:t.userName},on:{startGame:t.startGame,close:t.close}}):t._e(),t._v(" "),t.isBack?i("Confirm",{on:{isback:t.isGoback}}):t._e(),t._v(" "),t.isDel?i("ConfirmDel",{on:{isGoDel:t.isGoDel}}):t._e()],1)},staticRenderFns:[]};var a=function(t){i("RDQp")},r=i("C7Lr")(n.a,o,!1,a,null,null).exports,u=i("aozt"),c=i.n(u);i("e5r7"),i("nNBB"),i("I6m9"),i("65hl");s.a.config.productionTip=!1,s.a.prototype.$http=c.a,new s.a({el:"#app",components:{App:r},template:"<App/>"})},Oo4P:function(t,e){},RDQp:function(t,e){},"XG+O":function(t,e,i){"use strict";var s=i("HToJ"),n=i.n(s),o={data:function(){return{img:n.a}},methods:{isback:function(t){this.$emit("isback",t)}}},a={render:function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",[i("div",{staticStyle:{display:"block"},attrs:{id:"bg"}}),t._v(" "),i("div",{staticClass:"deTanImg"},[i("div",{staticClass:"posR"},[i("img",{staticClass:"img100",attrs:{src:t.img}})]),t._v(" "),i("a",{staticClass:"ktBtn kyello",on:{click:function(e){t.isback(1)}}},[t._v("退出")]),t._v(" "),i("a",{staticClass:"ktBtn kgreen",on:{click:function(e){t.isback(0)}}},[t._v("取消")])])])},staticRenderFns:[]};var r=i("C7Lr")(o,a,!1,function(t){i("baHn")},null,null);e.a=r.exports},YtJ0:function(t,e,i){"use strict";var s=i("IvJb"),n=i("9rMa");s.a.use(n.a);e.a=new n.a.Store({state:{userSelectAnswerArr:[],writeNum:-1},mutations:{add:function(t,e){t.userSelectAnswerArr.push(e)},reduce:function(t){t.count--}}})},baHn:function(t,e){},dLxl:function(t,e){},deIM:function(t,e,i){"use strict";var s={props:["gonggao","plsurl","rcode","userName"],mounted:function(){},methods:{startGame:function(){this.$emit("startGame")},startVideo:function(t,e,i){var s=document.location.protocol+"//"+e+"/youjiao/doMutiplePlay.do?jsoncallback=?";$.getJSON(s,{rcode:t,userName:i,filterType:2,outType:1},function(t){window.location.href=t.jsonList[0].list[0].path})},close:function(){this.$emit("close")}}},n={render:function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{staticClass:"bbBlueNew",attrs:{id:"bgremark"}},[i("header",[i("div",{staticClass:"pad10-0"},[i("a",{staticClass:"guan",on:{click:t.close}},[i("i",{staticClass:"icon-cuo02"})])])]),t._v(" "),i("section",[i("div",{staticStyle:{"overflow-y":"auto"},attrs:{id:"wrapperNew"}},[i("div",{attrs:{id:"scroller"}},[i("div",{staticClass:"cgnr pad10",domProps:{innerHTML:t._s(t.gonggao)}})])]),t._v(" "),i("div",{attrs:{id:"die"}}),t._v(" "),i("div",{staticClass:"bottom center padB20"},[""!=t.rcode?i("a",{staticClass:"btnS",on:{click:function(e){t.startVideo(t.rcode,t.plsurl,t.userName)}}},[t._v("学习微课")]):t._e(),t._v(" "),i("a",{staticClass:"btnSS",on:{click:t.startGame}},[t._v("开始闯关")])])])])},staticRenderFns:[]};var o=i("C7Lr")(s,n,!1,function(t){i("M5bs")},null,null);e.a=o.exports},e5r7:function(t,e){},hsWu:function(t,e){},hsdY:function(t,e){},i5qY:function(t,e,i){"use strict";var s,n=i("AA3o"),o=i.n(n),a=i("xSur"),r=i.n(a),u=function(){function t(e,i){o()(this,t),this.el=e,this.speed=i,this.canvas=document.getElementById(this.el),this.context=this.canvas.getContext("2d"),this.centerX=this.canvas.width/2,this.centerY=this.canvas.height/2,this.rad=2*Math.PI/this.speed,this.r=18,this.lineColor="#fff"}return r()(t,[{key:"blueCircle",value:function(t){this.context.save(),this.context.strokeStyle="#19a0a7",this.context.lineWidth=3,this.context.beginPath(),this.context.arc(this.centerX,this.centerY,this.r,-Math.PI/2,-Math.PI/2+t*this.rad,!1),this.context.stroke(),this.context.closePath(),this.context.restore()}},{key:"whiteCircle",value:function(){this.context.save(),this.context.beginPath(),this.context.lineWidth=4,this.context.strokeStyle=this.lineColor,this.context.arc(this.centerX,this.centerY,this.r,0,2*Math.PI,!1),this.context.stroke(),this.context.closePath(),this.context.restore()}},{key:"text",value:function(t){this.context.save(),this.context.strokeStyle=this.lineColor,this.context.font="20px Arial",this.speed>99?this.context.strokeText(t.toFixed(0),this.centerX-18,this.centerY+8):this.speed>9&&this.speed<100?this.context.strokeText(t.toFixed(0),this.centerX-10,this.centerY+8):this.context.strokeText(t.toFixed(0),this.centerX-5,this.centerY+8),this.context.stroke(),this.context.restore()}},{key:"drawFrame",value:function(){var t=this,e=setInterval(function(){t.speed>=0?(t.context.clearRect(0,0,t.canvas.width,t.canvas.height),t.whiteCircle(),t.text(t.speed),t.blueCircle(t.speed),t.speed-=1,t.speed<=10&&(t.context.strokeStyle="#ffef6a",t.speed)):clearInterval(e)},1e3)}}]),t}(),c={props:["time"],data:function(){return{}},mounted:function(){s=new u("canvas",this.time);var t=this,e=setInterval(function(){s.speed>=0?(s.speed<=10&&(s.lineColor="#ffef6a"),s.context.clearRect(0,0,s.canvas.width,s.canvas.height),s.whiteCircle(),s.text(s.speed),s.blueCircle(s.speed),s.speed-=1):(clearInterval(e),t.gameOver())},1e3)},methods:{gameOver:function(){this.$emit("gameOver")}}},h={render:function(){this.$createElement;this._self._c;return this._m(0)},staticRenderFns:[function(){var t=this.$createElement,e=this._self._c||t;return e("div",[e("canvas",{staticStyle:{"border-radius":"100%","vertical-align":"middle"},attrs:{id:"canvas",width:"40",height:"40"}})])}]};var l={components:{Process:i("C7Lr")(c,h,!1,function(t){i("dLxl")},null,null).exports},props:["time","now","total","isBegTime","iswrongView","isHasQues"],computed:{},methods:{gameOver:function(){this.$emit("gameOver")},goback:function(){this.$emit("goback")},goDel:function(){this.$emit("goDel")}}},_={render:function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("header",{staticClass:"head"},[i("a",{staticClass:"head-left",on:{click:t.goback}},[i("i",{staticClass:"icon-back"}),t._v("返回")]),t._v(" "),t.iswrongView?t._e():i("h1",[t.isBegTime?t._e():i("Process",{attrs:{time:t.time},on:{gameOver:t.gameOver}})],1),t._v(" "),t.iswrongView&&0!=t.total?i("h1",[t._v(t._s(t.now)+"/"+t._s(t.total))]):t._e(),t._v(" "),t.iswrongView||0==t.total?t._e():i("a",{staticClass:"head-right"},[t._v(t._s(t.now)+"/"+t._s(t.total))]),t._v(" "),t.iswrongView&&t.isHasQues?i("a",{staticClass:"head-right",on:{click:t.goDel}},[t._v("清空")]):t._e()])},staticRenderFns:[]};var d=i("C7Lr")(l,_,!1,function(t){i("Oo4P")},null,null);e.a=d.exports},iVvP:function(t,e,i){"use strict";var s={render:function(){var t=this.$createElement;return(this._self._c||t)("div",{staticClass:"timu",staticStyle:{display:"block"}},[this._v("\n  暂无试题\n")])},staticRenderFns:[]};var n=i("C7Lr")({},s,!1,function(t){i("hsWu")},null,null);e.a=n.exports},j66c:function(t,e){},k6rM:function(t,e,i){"use strict";var s=i("4aA0"),n=i.n(s),o={data:function(){return{img:n.a}},methods:{isback:function(t){this.$emit("isGoDel",t)}}},a={render:function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",[i("div",{staticStyle:{display:"block"},attrs:{id:"bg"}}),t._v(" "),i("div",{staticClass:"deTanImg"},[i("div",{staticClass:"posR"},[i("img",{staticClass:"img100",attrs:{src:t.img}})]),t._v(" "),i("a",{staticClass:"ktBtn kyello",on:{click:function(e){t.isback(1)}}},[t._v("清空")]),t._v(" "),i("a",{staticClass:"ktBtn kgreen",on:{click:function(e){t.isback(0)}}},[t._v("取消")])])])},staticRenderFns:[]};var r=i("C7Lr")(o,a,!1,function(t){i("xwpV")},null,null);e.a=r.exports},nNBB:function(t,e){},sEFh:function(module,__webpack_exports__,__webpack_require__){"use strict";var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_helpers_extends__=__webpack_require__("4YfN"),__WEBPACK_IMPORTED_MODULE_0_babel_runtime_helpers_extends___default=__webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_babel_runtime_helpers_extends__),__WEBPACK_IMPORTED_MODULE_1__vuex_store__=__webpack_require__("YtJ0"),__WEBPACK_IMPORTED_MODULE_2_vuex__=__webpack_require__("9rMa"),__WEBPACK_IMPORTED_MODULE_3__components_Head__=__webpack_require__("i5qY"),__WEBPACK_IMPORTED_MODULE_4__components_Select__=__webpack_require__("wi0O"),__WEBPACK_IMPORTED_MODULE_5__components_SelectMore__=__webpack_require__("E+8s"),__WEBPACK_IMPORTED_MODULE_6__components_TianKong__=__webpack_require__("yp5x"),__WEBPACK_IMPORTED_MODULE_7__components_Dialog__=__webpack_require__("deIM"),__WEBPACK_IMPORTED_MODULE_8__components_Confirm__=__webpack_require__("XG+O"),__WEBPACK_IMPORTED_MODULE_9__components_ConfirmDel__=__webpack_require__("k6rM"),__WEBPACK_IMPORTED_MODULE_10__components_Empty__=__webpack_require__("iVvP");function GetQueryString(t){var e=new RegExp("(^|&)"+t+"=([^&]*)(&|$)"),i=window.location.search.substr(1).match(e);return null!=i?decodeURI(decodeURI(unescape(i[2]))):null}function checkLocalStorage(){if(!window.localStorage)return alert("您的手机不支持预览功能"),!1}function setLocalStorage(t,e){checkLocalStorage(),window.localStorage.setItem(t,e)}function getLocalStorage(t){checkLocalStorage();var e=window.localStorage.getItem(t);return decodeURI(e)}var stageid=GetQueryString("stageid"),genreid=GetQueryString("genreid"),userstageid=GetQueryString("userstageid"),type=GetQueryString("type"),iswrong=GetQueryString("iswrong"),urlCallBack=GetQueryString("urlCallBack"),title=getLocalStorage("sx_title"),gradeid=GetQueryString("gradeid"),subjectid=GetQueryString("subjectid"),moduleid=GetQueryString("moduleid"),ks_short_name=getLocalStorage("ks_short_name"),backToStageUrl="stagelist?genreid="+genreid+"&gradeid="+gradeid+"&subjectid="+subjectid+"&moduleid="+moduleid+"&urlCallBack="+encodeURIComponent(urlCallBack)+"&ks_short_name="+encodeURI(encodeURI(title));if(1==iswrong)var getUrl="../Game/getWrongView?genreid="+genreid;else var getUrl="../Game/getQuesView?stageid="+stageid+"&type="+type;var debug="zaixian";if("dev"==debug)var getUrl="api/getQues";if("houtai"==debug){var id=GetQueryString("id");if("ques"==type)var getUrl="../Index/getQuesAloneView?quesid="+id;else var getUrl="../Index/getQuesView?stageid="+id}function play(){count=0;try{UXinJSInterfaceSpeech.playAudio(domain+"nan/"+voiceArr[count]+".mp3")}catch(t){}}__webpack_exports__.a={name:"App",components:{Head:__WEBPACK_IMPORTED_MODULE_3__components_Head__.a,Select:__WEBPACK_IMPORTED_MODULE_4__components_Select__.a,SelectMore:__WEBPACK_IMPORTED_MODULE_5__components_SelectMore__.a,TianKong:__WEBPACK_IMPORTED_MODULE_6__components_TianKong__.a,Dialog:__WEBPACK_IMPORTED_MODULE_7__components_Dialog__.a,Confirm:__WEBPACK_IMPORTED_MODULE_8__components_Confirm__.a,ConfirmDel:__WEBPACK_IMPORTED_MODULE_9__components_ConfirmDel__.a,Empty:__WEBPACK_IMPORTED_MODULE_10__components_Empty__.a},store:__WEBPACK_IMPORTED_MODULE_1__vuex_store__.a,data:function(){return{questionArr:[],questionJson:"",question:{id:"",quesType:"",info:""},isShow:!1,isBack:!1,isDel:!1,gonggao:"",plsurl:"",rcode:"",userName:"",time:0,count:0,isGameOver:!1,rightNum:0,isWrite:!1,isHasQues:!0}},computed:{iswrongView:function(){return 1==iswrong},isStart:function(){return!(this.questionJson.length>0)||(0==this.time||this.isShow)}},methods:__WEBPACK_IMPORTED_MODULE_0_babel_runtime_helpers_extends___default()({},Object(__WEBPACK_IMPORTED_MODULE_2_vuex__.b)(["add","reduce"]),{getQuestion:function getQuestion(){1==iswrong&&(this.isWrite=!0);var self=this;this.$http.get(getUrl).then(function(response){if(0==response.data.ques.length)return self.isHasQues=!1,!1;if(self.time=1*response.data.stage[0].totaltime,""==response.data.stage[0].remark?self.isShow=!1:(self.isShow=!0,self.gonggao=response.data.stage[0].remark,self.rcode=response.data.stage[0].r_code,self.plsurl=response.data.pls_url,self.userName=response.data.userName),self.questionJson=response.data.ques,self.question.id=response.data.ques[self.count].id,self.question.quesType=response.data.ques[self.count].questype,self.question.info=eval("("+response.data.ques[self.count].content+")"),1==iswrong)for(var i in response.data.useranswer)self.add(response.data.useranswer[i]);2==type&&(self.question.voice=eval("("+self.questionJson[self.count].voice+")"),void 0!==self.question.voice&&(voiceArr=self.question.voice,self.isShow||play()))}).catch(function(t){console.log(t)})},getNextQuestion:function getNextQuestion(){this.question.id=this.questionJson[this.count].id,this.question.quesType=this.questionJson[this.count].questype,this.question.info=eval("("+this.questionJson[this.count].content+")"),2==type&&(this.question.voice=eval("("+this.questionJson[this.count].voice+")"),void 0!==this.questionJson[this.count].voice&&(voiceArr=this.question.voice,play()))},addCount:function(t,e){try{UXinJSInterfaceSpeech.stopAudio()}catch(t){}1==e&&this.rightNum++;if(this.count<this.questionJson.length-1)this.count++,this.$store.state.writeNum<this.count&&1!=iswrong&&(this.isWrite=!1),this.isGameOver||this.getNextQuestion();else{if("houtai"==debug)return alert("结束"),!1;window.location.href=1==iswrong?backToStageUrl:"result?rightNum="+this.rightNum+"&stageid="+stageid+"&type="+type+"&userstageid="+userstageid+"&total="+this.questionJson.length+"&genreid="+genreid+"&moduleid="+moduleid+"&gradeid="+gradeid+"&subjectid="+subjectid+"&urlCallBack="+encodeURIComponent(urlCallBack)}},minusCount:function minusCount(){this.count--,this.isWrite=!0,this.question.id=this.questionJson[this.count].id,this.question.quesType=this.questionJson[this.count].questype,this.question.info=eval("("+this.questionJson[this.count].content+")")},gameOver:function(){if("houtai"==debug)return alert("时间到"),!1;window.location.href="result?rightNum="+this.rightNum+"&stageid="+stageid+"&type="+type+"&userstageid="+userstageid+"&total="+this.questionJson.length+"&genreid="+genreid+"&moduleid="+moduleid+"&gradeid="+gradeid+"&subjectid="+subjectid+"&urlCallBack="+encodeURIComponent(urlCallBack),this.isGameOver=!0},startGame:function(){2==type&&play(),this.isShow=!1},goback:function(){1==iswrong?window.location.href=backToStageUrl:(document.getElementsByTagName("body")[0].className="bover",this.isBack=!0)},isGoback:function(t){document.getElementsByTagName("body")[0].className="",0==t?this.isBack=!1:(this.isBack=!1,window.location.href=backToStageUrl)},close:function(){window.location.href=backToStageUrl},goDel:function(){document.getElementsByTagName("body")[0].className="bover",this.isDel=!0},isGoDel:function(t){document.getElementsByTagName("body")[0].className="",0==t?this.isDel=!1:(this.isDel=!1,this.emptyWrong())},addQuestionRecord:function(t,e,i){this.$http.get("../Game/addUserAnswer?userstageid="+userstageid+"&quesid="+t+"&isright="+e+"&type="+type+"&userselect="+i+"&stageid="+stageid).then(function(t){}).catch(function(t){console.log(t)})},emptyWrong:function(){this.isHasQues=!1,this.questionJson="",this.$http.get("../Game/emptyWrongNotes?genreid="+genreid).then(function(t){}).catch(function(t){})}}),mounted:function(){this.getQuestion()}}},wi0O:function(t,e,i){"use strict";var s=i("Eqm3"),n=i.n(s),o=i("4YfN"),a=i.n(o),r=i("YtJ0"),u=i("9rMa"),c={props:["question","nowIndex","isComplete","quesCount"],data:function(){return{filter:-1,filterRight:-1,filterWrong:-1,answerArr:["A","B","C","D","E","F"],isDo:!1,isShowJiexiButton:!1,isShowJiexi:!1,isShowNextButton:!1,isShowTijiaoButton:!0,isShowComplateButton:!1,isShowAnswer:!1,isRight:-1,isError:-1,userSelectArr:[]}},mounted:function(){},computed:{isReadOnly:function(){return this.isComplete?(this.filter=1*this.$store.state.userSelectAnswerArr[this.nowIndex],this.isShowJiexiButton=!0,this.isShowTijiaoButton=!1,this.isShowJiexi=!1,this.isShowAnswer=!0,this.nowIndex>=this.quesCount-1?(this.isShowComplateButton=!0,this.isShowNextButton=!1):(this.isShowNextButton=!0,this.isShowComplateButton=!1),!0):(this.isShowJiexiButton=!1,this.isShowNextButton=!1,this.isShowTijiaoButton=!0,this.isShowJiexi=!1,this.isShowAnswer=!1,!1)}},store:r.a,methods:a()({},Object(u.b)(["add","reduce"]),{choose:function(t){if(this.isDo)return!1;this.isComplete||(this.filter=t)},findEleInArr:function(t,e){return n.a||(Array.prototype.indexOf=function(t){for(var e=0;e<this.length;e++)if(this[e]==t)return e;return-1}),e.indexOf(t)},submitQues:function(){if(this.isDo=!0,this.add(this.filter),this.$store.state.writeNum=this.nowIndex,void 0!==this.question.info.quesChoice[this.filter]&&this.question.info.quesChoice[this.filter].flag==this.question.info.answer?(console.log("right"),this.isRight=1):(console.log("wrong"),this.isRight=0),void 0===this.question.info.quesChoice[this.filter])var t="";else t=this.filter;this.$emit("addAnswerLog",this.question.id,this.isRight,t),this.isError=this.isRight;var e=this;setTimeout(function(){e.isError=-1},1e3),this.isShowJiexiButton=!0,this.isShowTijiaoButton=!1,this.isShowAnswer=!0,this.nowIndex>=this.quesCount-1?(this.isShowComplateButton=!0,this.isShowNextButton=!1):(this.isShowNextButton=!0,this.isShowComplateButton=!1),setTimeout(function(){var t=document.body.scrollHeight;window.scroll(0,t)},200)},nextQuestion:function(){this.isDo=!1,this.$emit("next",this.question.id,this.isRight),this.isReadOnly?(this.isShowJiexiButton=!0,this.isShowTijiaoButton=!1,this.isShowJiexi=!1,this.isShowAnswer=!0,this.nowIndex>=this.quesCount-1?(this.isShowNextButton=!1,this.isShowComplateButton=!0):(this.isShowNextButton=!0,this.isShowComplateButton=!1)):(this.isShowJiexiButton=!1,this.isShowNextButton=!1,this.isShowTijiaoButton=!0,this.isShowJiexi=!1,this.isShowComplateButton=!1,this.isShowAnswer=!1),this.filter=-1,this.isRight=-1},showJiexiContent:function(){this.isShowJiexi=!0,setTimeout(function(){var t=document.body.scrollHeight;window.scroll(0,t)},200)},preQuestion:function(){this.filter=-1,this.isDo=!1,this.isShowJiexiButton=!1,this.isShowJiexi=!1,this.isShowTijiaoButton=!1,this.isShowNextButton=!0,this.isRight=-1,this.nowIndex>0&&this.$emit("prev")},gameOver:function(){this.$emit("next",this.question.id,this.isRight)}})},h={render:function(){var t=this,e=t.$createElement,s=t._self._c||e;return s("div",{staticClass:"timu",staticStyle:{display:"block"}},[s("div",{staticClass:"padL"},[s("h5",{staticClass:"textH5",domProps:{innerHTML:t._s(t.question.info.quesName)}}),t._v(" "),""!==t.question.info.quesContent?[1===t.question.info.quesContentType?s("p",{staticClass:"fenx"},[t._v(t._s(t.question.info.quesContent))]):t._e(),t._v(" "),2===t.question.info.quesContentType?s("img",{staticClass:"timu100",attrs:{src:t.question.info.quesContent}}):t._e()]:t._e(),t._v(" "),1===t.question.info.quesChoiceType?[s("ul",{staticClass:"listIww"},t._l(t.question.info.quesChoice,function(e,i){return s("li",{key:e.flag,class:[i===t.filter?"cur":""],staticStyle:{display:"inline-flex",width:"100%",margin:"3px 0"},on:{click:function(e){t.choose(i)}}},[s("em",[t._v(t._s(e.flag)+".")]),t._v(" "),s("span",{domProps:{innerHTML:t._s(e.content)}})])}))]:t._e(),t._v(" "),2===t.question.info.quesChoiceType?[s("ul",{staticClass:"listImage tp"},t._l(t.question.info.quesChoice,function(e,i){return s("li",{key:e.flag,class:["table",i===t.filter?"cur":""],on:{click:function(e){t.choose(i)}}},[s("em",[t._v(t._s(e.flag)+".")]),t._v(" "),s("span",[s("img",{attrs:{src:e.content}})])])}))]:t._e()],2),t._v(" "),t.isShowAnswer?s("div",{staticClass:"padR"},[s("h6",{staticClass:"textH6"},[t._v("\n      您选择的答案"),s("font",{staticClass:"redFont"},[t._v(t._s(this.answerArr[this.filter]))]),t._v(" ,\n      正确答案"),s("font",{staticClass:"redFont"},[t._v(t._s(this.question.info.answer.join(",")))])],1)]):t._e(),t._v(" "),s("div",{staticClass:"padR"},[t.isShowJiexiButton&&""!=t.question.info.jiexi?s("div",{staticClass:"look",on:{click:t.showJiexiContent}},[s("a",{staticClass:"ckjx",attrs:{id:"jiexie"}},[t._v("查看解析")])]):t._e(),t._v(" "),t.isShowJiexi?s("div",{staticClass:"look2 pad10"},[t._m(0),t._v(" "),s("p",{domProps:{innerHTML:t._s(t.question.info.jiexi)}})]):t._e()]),t._v(" "),s("transition",{attrs:{name:"bounce"}},[0==t.isError?s("div",{staticClass:"puanduan01"},[s("img",{staticClass:"img100",attrs:{src:i("4j7q")}})]):t._e(),t._v(" "),1==t.isError?s("div",{staticClass:"puanduan01"},[s("img",{staticClass:"img100",attrs:{src:i("xw+O")}})]):t._e()]),t._v(" "),s("div",{staticClass:"mb50"}),t._v(" "),s("div",{staticClass:"nrBtn"},[t.nowIndex>0?s("a",{on:{click:t.preQuestion}},[t._v("上一题")]):t._e(),t._v(" "),t.isShowTijiaoButton?s("a",{on:{click:t.submitQues}},[t._v("提交")]):t._e(),t._v(" "),t.isShowNextButton?s("a",{on:{click:t.nextQuestion}},[t._v("下一题")]):t._e(),t._v(" "),t.isShowComplateButton?s("a",{on:{click:t.gameOver}},[t._v("完成")]):t._e()]),t._v(" "),t.isReadOnly?s("div"):t._e()],1)},staticRenderFns:[function(){var t=this.$createElement,e=this._self._c||t;return e("p",[e("label",[this._v("解析:")])])}]};var l=i("C7Lr")(c,h,!1,function(t){i("2b1k")},null,null);e.a=l.exports},"xw+O":function(t,e,i){t.exports=i.p+"static/img/dui.0c97d9d.png"},xwpV:function(t,e){},yp5x:function(t,e,i){"use strict";var s=i("ZLEe"),n=i.n(s),o={props:["question"],data:function(){return{findex:-1,lindex:-1,answerInput:[],answerArr:[],isRight:!1,filter:0,isComplete:!1}},computed:{questionNew:function(){if(2==this.question.info.quesContentType){for(var t in this.question.info.quesContent)if(this.question.info.quesContent[t].length>1)for(var e in this.question.info.quesContent[t])"#"==this.question.info.quesContent[t][e]&&(this.answerArr.push(t+"-"+e),-1==this.findex&&(this.findex=t),-1==this.lindex&&(this.lindex=e))}else if(1==this.question.info.quesContentType||3==this.question.info.quesContentType)for(var t in this.question.info.quesContent)"#"==this.question.info.quesContent[t]&&(this.answerArr.push(t),-1==this.findex&&(this.findex=t));return this.question}},methods:{choose:function(t,e){this.findex=t,this.lindex=e,this.answerArr=[]},chongP:function(t){this.findex=t,this.answerArr=[]},objKeySort:function(t){for(var e=n()(t).sort(),i=[],s=0;s<e.length;s++)i[e[s]]=t[e[s]];return i},changeNext:function(){if(!this.isComplete){if(this.question.info.answer.length>1){var t="";for(var e in this.objKeySort(this.answerInput))t+=this.answerInput[e]+"#";if(t=t.substring(0,t.length-1),"string"==typeof this.question.info.answer[0])t==this.question.info.answer.join("#")&&(this.isRight=!0);else for(var e in this.question.info.answer)if(t==this.question.info.answer[e].join("#")){this.isRight=!0;break}}else{var i=!0;for(var e in this.answerArr)if(console.log(this.answerInput[this.answerArr[e]]),console.log(this.question.info.answer[0]),this.answerInput[this.answerArr[e]]!=this.question.info.answer[0]){i=!1;break}this.isRight=i}this.isRight?(this.filter=1,this.$emit("addAnswerLog",this.question.id,1),this.$emit("next",this.question.id,1)):(this.filter=2,this.$emit("addAnswerLog",this.question.id,0),this.$emit("next",this.question.id,0)),this.isComplete=!0;var s=this;setTimeout(function(){s.findex=-1,s.lindex=-1,s.answerInput=[],s.answerArr=[],s.isRight=!1,s.filter=0,s.isComplete=!1},1e3)}},tijiao:function(t){if(this.isComplete)return!1;-1!=t?(console.log(this.answerInput),1==this.question.info.quesContentType||3==this.question.info.quesContentType?void 0!==this.answerInput[this.findex]?this.$set(this.answerInput,this.findex,this.answerInput[this.findex].toString()+t.toString()):this.$set(this.answerInput,this.findex,t):(console.log("xx"),console.log(this.answerInput[this.findex+"-"+this.lindex]),void 0!==this.answerInput[this.findex+"-"+this.lindex]?this.$set(this.answerInput,this.findex+"-"+this.lindex,this.answerInput[this.findex+"-"+this.lindex].toString()+t.toString()):this.$set(this.answerInput,this.findex+"-"+this.lindex,t))):1==this.question.info.quesContentType||3==this.question.info.quesContentType?this.$set(this.answerInput,this.findex,""):this.$set(this.answerInput,this.findex+"-"+this.lindex,"")}}},a={render:function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",[i("div",{staticClass:"timu",staticStyle:{display:"block"}},[""!=t.questionNew.info.quesName?i("p",{staticClass:"fenx"},[t._v(t._s(t.questionNew.info.quesName))]):t._e(),t._v(" "),1==t.questionNew.info.quesContentType?i("div",{staticClass:"fenx"},t._l(t.questionNew.info.quesContent,function(e,s){return i("span",{key:s,staticClass:"fend"},["#"!=e?i("label",[t._v(t._s(e))]):t._e(),t._v(" "),"#"==e?i("label",[i("label",{class:["fkong",s==t.findex?"item":""],on:{click:function(e){t.chongP(s)}}},[t._v(t._s(t.answerInput[s]))])]):t._e()])})):t._e(),t._v(" "),2==t.questionNew.info.quesContentType?i("div",{staticClass:"fenx"},t._l(t.questionNew.info.quesContent,function(e,s){return i("span",{key:s,staticClass:"fens"},[1==e.length?[t._v("\r\n          "+t._s(e)+"\r\n        ")]:t._e(),t._v(" "),t._l(e,function(n,o){return e.length>1?["#"==n?i("label",{key:o,class:["fkong",t.findex==s&&t.lindex==o?"item":""],on:{click:function(e){t.choose(s,o)}}},[t._v(t._s(t.answerInput[s+"-"+o]))]):t._e(),t._v(" "),"#"!=n&&"/"!=n?i("label",{key:o},[t._v(t._s(n))]):t._e()]:t._e()})],2)})):t._e(),t._v(" "),3==t.question.info.quesContentType?i("div",{staticClass:"fenx"},t._l(t.questionNew.info.quesContent,function(e,s){return i("span",{key:s,staticClass:"fend"},["#"!=e&&"|"!=e?i("label",[t._v(t._s(e))]):t._e(),t._v(" "),"|"==e?i("label",[t._v("······")]):t._e(),t._v(" "),"#"==e?i("label",[i("label",{class:["fkong",s==t.findex?"item":""],on:{click:function(e){t.chongP(s)}}},[t._v(t._s(t.answerInput[s]))])]):t._e()])})):t._e(),t._v(" "),1==t.filter?i("div",{staticClass:"panduan"},[i("i",{staticClass:"icon-correct04"})]):t._e(),t._v(" "),2==t.filter?i("div",{staticClass:"panduan"},[i("i",{staticClass:"icon-error02"})]):t._e()]),t._v(" "),i("div",{staticClass:"keyboard",staticStyle:{display:"block"}},[i("table",{attrs:{border:"1",cellspacing:"0",cellpadding:"0"}},[i("tr",[i("td",{on:{click:function(e){t.tijiao(1)}}},[i("a",[t._v("1")])]),t._v(" "),i("td",{on:{click:function(e){t.tijiao(2)}}},[i("a",[t._v("2")])]),t._v(" "),i("td",{on:{click:function(e){t.tijiao(3)}}},[i("a",[t._v("3")])]),t._v(" "),i("td",{on:{click:function(e){t.tijiao(-1)}}},[i("a",{staticClass:"redFont"},[t._v("删除")])])]),t._v(" "),i("tr",[i("td",{on:{click:function(e){t.tijiao(4)}}},[i("a",[t._v("4")])]),t._v(" "),i("td",{on:{click:function(e){t.tijiao(5)}}},[i("a",[t._v("5")])]),t._v(" "),i("td",{on:{click:function(e){t.tijiao(6)}}},[i("a",[t._v("6")])]),t._v(" "),i("td",{staticClass:"dd",attrs:{rowspan:"3",valign:"middle"},on:{click:t.changeNext}},[i("span",[t._v("下一题")])])]),t._v(" "),i("tr",[i("td",{on:{click:function(e){t.tijiao(7)}}},[i("a",[t._v("7")])]),t._v(" "),i("td",{on:{click:function(e){t.tijiao(8)}}},[i("a",[t._v("8")])]),t._v(" "),i("td",{on:{click:function(e){t.tijiao(9)}}},[i("a",[t._v("9")])])]),t._v(" "),i("tr",[i("td",{on:{click:function(e){t.tijiao(0)}}},[i("a",[t._v("0")])]),t._v(" "),i("td",{on:{click:function(e){t.tijiao(".")}}},[i("a",[t._v(".")])]),t._v(" "),i("td",{on:{click:function(e){t.tijiao("00")}}},[i("a",[t._v("00")])])])])])])},staticRenderFns:[]};var r=i("C7Lr")(o,a,!1,function(t){i("j66c")},null,null);e.a=r.exports}},["NHnr"]);