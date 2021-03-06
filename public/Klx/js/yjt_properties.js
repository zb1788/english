
/*
 * 注意：
 * 这里只是举了个示例，具体参数值，需要根据实际情况定义
 */


/*郑州、新河南、河北、黑龙江、吉林、江西、辽宁、本溪、天津、山东、甘肃、陕西、山西、安徽、
 江苏、广西、广东、云南、福建、襄阳、北镇、湖南、大同  */
var mapObj = {};//外层集合对象，存放地区编码以及对应的地区系统编码域名集合对象

//测试
var map_test = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_test["portal"]="http://henan.czbanbantong.com";
map_test["sso"]="http://192.168.164.95";
map_test["domain"]="czbanbantong.com";
mapObj["111."]=map_test;
//河南
var map_henan = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_henan["portal"]="http://henan.czbanbantong.com";
map_henan["pls"]="http://plshenan.czbanbantong.com";
map_henan["ub"]="http://ubhenan.czbanbantong.com";
map_henan["cms"]="http://cms.czbanbantong.com";
map_henan["tms"]="http://tmshenan.czbanbantong.com";
map_henan["sso"]="http://ssohenan.czbanbantong.com";
map_henan["ilearn"]="http://ilearnhenan.czbanbantong.com";
map_henan["eng"]="http://en.czbanbantong.com";
map_henan["domain"]="czbanbantong.com";

	//河南的其他系统域名可以根据上面的继续往下配置
mapObj["25."]=map_henan;
//许昌
var map_xc = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_xc["portal"]="http://xc.czbanbantong.com";
map_xc["pls"]="http://plsxc.czbanbantong.com";
map_xc["ub"]="http://ubxc.czbanbantong.com";
map_xc["cms"]="http://cms.czbanbantong.com";
map_xc["tms"]="http://tmsxc.czbanbantong.com";
map_xc["tqms"]="http://tqmsxc.czbanbantong.com";
map_xc["sso"]="http://ssoxc.czbanbantong.com";
map_xc["ilearn"]="http://ilearnxc.czbanbantong.com";
map_xc["eng"]="http://en.czbanbantong.com";
map_xc["domain"]="czbanbantong.com";

	//河南的其他系统域名可以根据上面的继续往下配置
mapObj["27."]=map_xc;

var map_yc = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_yc["portal"]="http://yc.czbanbantong.com";
map_yc["pls"]="http://plsyc.czbanbantong.com";
map_yc["ub"]="http://ubyc.czbanbantong.com";
map_yc["cms"]="http://cms.czbanbantong.com";
map_yc["tms"]="http://tmsyc.czbanbantong.com";
map_yc["tqms"]="http://tqmsyc.czbanbantong.com";
map_yc["sso"]="http://ssoyc.czbanbantong.com";
map_yc["ilearn"]="http://ilearnyc.czbanbantong.com";
map_yc["eng"]="http://en.czbanbantong.com";
map_yc["statpv"]="statpvyc.czbanbantong.com";
map_yc["qikan"]="http://bbtrrtxs.vip.qikan.com";
map_yc["nbsy"]="http://nbyc.czbanbantong.com/experiment/index/wuli?sso=b502be4bad42c52a5bfbf6e4775ab4e40a1f89119f60218f7d0feb6e27880bef&sso_ip=65a13da4931d082a596f9bb4167e6a7a";
map_yc["gkzy"]="http://zz.api.ks5u.com";
map_yc["pingk"]="http://pkyc.czbanbantong.com";
mapObj["40."]=map_yc;

var map_zhengzhou = {};   //内层集合对象，存放河北地区系统编码以及对应的域名
map_zhengzhou["portal"]="http://zz.zzedu.net.cn";
map_zhengzhou["pls"]="http://plszz.zzedu.net.cn";
map_zhengzhou["ub"]="http://ubzz.zzedu.net.cn";
map_zhengzhou["cms"]="http://cmszz.zzedu.net.cn";
map_zhengzhou["tms"]="http://tmszz.zzedu.net.cn";
map_zhengzhou["tqms"]="http://tqmszz.zzedu.net.cn";
map_zhengzhou["sso"]="http://ssozz.zzedu.net.cn";
map_zhengzhou["ilearn"]="http://ilearnzz.zzedu.net.cn";
map_zhengzhou["domain"]="zzedu.net.cn";


	//郑州的其他系统域名可以根据上面的继续往下配置
mapObj["1."]=map_zhengzhou;

//mapObj["1."]["pls"]; //获取

//福建
var map_fj = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_fj["portal"]="http://fj.czbanbantong.com";
map_fj["pls"]="http://plsfj.czbanbantong.com";
map_fj["ub"]="http://ubfj.czbanbantong.com";
map_fj["cms"]="http://cms.czbanbantong.com";
map_fj["tms"]="http://tmsfj.czbanbantong.com";
map_fj["tqms"]="http://tqmsfj.czbanbantong.com";
map_fj["sso"]="http://ssofj.czbanbantong.com";
map_fj["ilearn"]="http://ilearnfj.czbanbantong.com";
map_fj["eng"]="http://en.czbanbantong.com";
map_fj["domain"]="czbanbantong.com";
mapObj["2."]=map_fj;

//河北
var map_he = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_he["portal"]="http://he.czbanbantong.com";
map_he["pls"]="http://plshe.czbanbantong.com";
map_he["ub"]="http://ubhe.czbanbantong.com";
map_he["cms"]="http://cms.czbanbantong.com";
map_he["tms"]="http://tmshe.czbanbantong.com";
map_he["tqms"]="http://tqmshe.czbanbantong.com";
map_he["sso"]="http://ssohe.czbanbantong.com";
map_he["ilearn"]="http://ilearnhe.czbanbantong.com";
map_he["eng"]="http://en.czbanbantong.com";
map_he["domain"]="czbanbantong.com";
mapObj["3."]=map_he;
//黑龙江
var map_hl = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_hl["portal"]="http://hl.czbanbantong.com";
map_hl["pls"]="http://plshl.czbanbantong.com";
map_hl["ub"]="http://ubhl.czbanbantong.com";
map_hl["cms"]="http://cms.czbanbantong.com";
map_hl["tms"]="http://tmshl.czbanbantong.com";
map_hl["tqms"]="http://tqmshl.czbanbantong.com";
map_hl["sso"]="http://ssohl.czbanbantong.com";
map_hl["ilearn"]="http://ilearnhl.czbanbantong.com";
map_hl["eng"]="http://en.czbanbantong.com";
map_hl["domain"]="czbanbantong.com";
mapObj["6."]=map_hl;

//黑龙江龙江县
var map_lj = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_lj["portal"]="http://lj.czbanbantong.com";
map_lj["pls"]="http://plshl.czbanbantong.com";
map_lj["ub"]="http://ubhl.czbanbantong.com";
map_lj["cms"]="http://cms.czbanbantong.com";
map_lj["tms"]="http://tmshl.czbanbantong.com";
map_lj["tqms"]="http://tqmshl.czbanbantong.com";
map_lj["sso"]="http://ssolj.czbanbantong.com";
map_lj["ilearn"]="http://ilearnhl.czbanbantong.com";
map_lj["eng"]="http://en.czbanbantong.com";
map_lj["statpv"]="statpvhl.czbanbantong.com";
map_lj["qikan"]="http://bbtrrtxs.vip.qikan.com";
map_lj["nbsy"]="http://nbhl.czbanbantong.com/experiment/index/wuli?sso=56880e711d0b62e8fdcb0a04beff8e41ce1a779fa54281ae&sso_ip=09cbab7a85d5bad24812470aa25e7a63";
map_lj["gkzy"]="http://zz.api.ks5u.com";
map_lj["pingk"]="http://pkhl.czbanbantong.com";
mapObj["39."]=map_lj;

//大兴安岭
var map_dxal = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_dxal["portal"]="http://dxal.czbanbantong.com";
map_dxal["pls"]="http://plsdxal.czbanbantong.com";
map_dxal["ub"]="http://ubhl.czbanbantong.com";
map_dxal["cms"]="http://cms.czbanbantong.com";
map_dxal["tms"]="http://tmshl.czbanbantong.com";
map_dxal["tqms"]="http://tqmshl.czbanbantong.com";
map_dxal["sso"]="http://ssodxal.czbanbantong.com";
map_dxal["ilearn"]="http://ilearnhl.czbanbantong.com";
map_dxal["eng"]="http://en.czbanbantong.com";
map_dxal["statpv"]="statpvhl.czbanbantong.com";
map_dxal["qikan"]="http://bbtrrtxs.vip.qikan.com";
map_dxal["nbsy"]="http://nbhl.czbanbantong.com/experiment/index/wuli?sso=56880e711d0b62e8fdcb0a04beff8e41ce1a779fa54281ae&sso_ip=09cbab7a85d5bad24812470aa25e7a63";
map_dxal["gkzy"]="http://zz.api.ks5u.com";
map_dxal["pingk"]="http://pkhl.czbanbantong.com";
mapObj["41."]=map_dxal;

//山东
var map_sd = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_sd["portal"]="http://sd.czbanbantong.com";
map_sd["pls"]="http://plssd.czbanbantong.com";
map_sd["ub"]="http://ubsd.czbanbantong.com";
map_sd["cms"]="http://cms.czbanbantong.com";
map_sd["tms"]="http://tmssd.czbanbantong.com";
map_sd["tqms"]="http://tqmssd.czbanbantong.com";
map_sd["sso"]="http://ssosd.czbanbantong.com";
map_sd["ilearn"]="http://ilearnsd.czbanbantong.com";
map_sd["eng"]="http://en.czbanbantong.com";
map_sd["domain"]="czbanbantong.com";
mapObj["4."]=map_sd;

//吉林
var map_jl = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_jl["portal"]="http://jl.czbanbantong.com";
map_jl["pls"]="http://plsjl.czbanbantong.com";
map_jl["ub"]="http://ubjl.czbanbantong.com";
map_jl["cms"]="http://cms.czbanbantong.com";
map_jl["tms"]="http://tmsjl.czbanbantong.com";
map_jl["tqms"]="http://tqmsjl.czbanbantong.com";
map_jl["sso"]="http://ssojl.czbanbantong.com";
map_jl["ilearn"]="http://ilearnjl.czbanbantong.com";
map_jl["domain"]="czbanbantong.com";
map_jl["eng"]="http://en.czbanbantong.com";

mapObj["7."]=map_jl;

//浙江
var map_zj = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_zj["portal"]="http://zj.czbanbantong.com";
map_zj["pls"]="http://plszj.czbanbantong.com";
map_zj["ub"]="http://ubzj.czbanbantong.com";
map_zj["cms"]="http://cms.czbanbantong.com";
map_zj["tms"]="http://tmszj.czbanbantong.com";
map_zj["tqms"]="http://tqmszj.czbanbantong.com";
map_zj["sso"]="http://ssozj.czbanbantong.com";
map_zj["ilearn"]="http://ilearnzj.czbanbantong.com";
map_zj["eng"]="http://en.czbanbantong.com";
map_zj["domain"]="czbanbantong.com";
mapObj["8."]=map_zj;
//江西
var map_jx = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_jx["portal"]="http://jx.czbanbantong.com";
map_jx["pls"]="http://plsjx.czbanbantong.com";
map_jx["ub"]="http://ubjx.czbanbantong.com";
map_jx["cms"]="http://cms.czbanbantong.com";
map_jx["tms"]="http://tmsjx.czbanbantong.com";
map_jx["tqms"]="http://tqmsjx.czbanbantong.com";
map_jx["sso"]="http://ssojx.czbanbantong.com";
map_jx["ilearn"]="http://ilearnjx.czbanbantong.com";
map_jx["eng"]="http://en.czbanbantong.com";
map_jx["domain"]="czbanbantong.com";
mapObj["13."]=map_jx;
//辽宁
var map_ln = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_ln["portal"]="http://ln.czbanbantong.com";
map_ln["pls"]="http://plsln.czbanbantong.com";
map_ln["ub"]="http://ubln.czbanbantong.com";
map_ln["cms"]="http://cms.czbanbantong.com";
map_ln["tms"]="http://tmsln.czbanbantong.com";
map_ln["tqms"]="http://tqmsln.czbanbantong.com";
map_ln["sso"]="http://ssoln.czbanbantong.com";
map_ln["ilearn"]="http://ilearnln.czbanbantong.com";
map_ln["eng"]="http://en.czbanbantong.com";
map_ln["domain"]="czbanbantong.com";
mapObj["16."]=map_ln;

//本溪
//辽宁
var map_bx = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_bx["portal"]="http://bxjyw.czbanbantong.com";
map_bx["pls"]="http://plsln.czbanbantong.com";
map_bx["ub"]="http://ubln.czbanbantong.com";
map_bx["cms"]="http://cms.czbanbantong.com";
map_bx["tms"]="http://tmsln.czbanbantong.com";
map_bx["tqms"]="http://tqmsln.czbanbantong.com";
map_bx["sso"]="http://ssoln.czbanbantong.com";
map_bx["ilearn"]="http://ilearnln.czbanbantong.com";
map_bx["eng"]="http://en.czbanbantong.com";
map_bx["domain"]="czbanbantong.com";
mapObj["30."]=map_bx;
//天津
var map_tj = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_tj["portal"]="http://tj.czbanbantong.com";
map_tj["pls"]="http://plstj.czbanbantong.com";
map_tj["ub"]="http://ubtj.czbanbantong.com";
map_tj["cms"]="http://cms.czbanbantong.com";
map_tj["tms"]="http://tmstj.czbanbantong.com";
map_tj["tqms"]="http://tqmstj.czbanbantong.com";
map_tj["sso"]="http://ssotj.czbanbantong.com";
map_tj["ilearn"]="http://ilearntj.czbanbantong.com";
map_tj["eng"]="http://en.czbanbantong.com";
map_tj["domain"]="czbanbantong.com";
mapObj["11."]=map_tj;

//北京
var map_bj = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_bj["portal"]="http://bj.czbanbantong.com";
map_bj["pls"]="http://plstj.czbanbantong.com";
map_bj["ub"]="http://ubtj.czbanbantong.com";
map_bj["cms"]="http://cms.czbanbantong.com";
map_bj["tms"]="http://tmstj.czbanbantong.com";
map_bj["tqms"]="http://tqmstj.czbanbantong.com";
map_bj["sso"]="http://ssobj.czbanbantong.com";
map_bj["ilearn"]="http://ilearntj.czbanbantong.com";
map_bj["eng"]="http://en.czbanbantong.com";
map_bj["domain"]="czbanbantong.com";
mapObj["24."]=map_bj;


//甘肃
var map_gs = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_gs["portal"]="http://gs.czbanbantong.com";
map_gs["pls"]="http://plsgs.czbanbantong.com";
map_gs["ub"]="http://ubgs.czbanbantong.com";
map_gs["cms"]="http://cms.czbanbantong.com";
map_gs["tms"]="http://tmsgs.czbanbantong.com";
map_gs["tqms"]="http://tqmsgs.czbanbantong.com";
map_gs["sso"]="http://ssogs.czbanbantong.com";
map_gs["ilearn"]="http://ilearngs.czbanbantong.com";
map_gs["eng"]="http://en.czbanbantong.com";
map_gs["domain"]="czbanbantong.com";
mapObj["5."]=map_gs;
//新疆
var map_xj = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_xj["portal"]="http://xj.czbanbantong.com";
map_xj["pls"]="http://plsgs.czbanbantong.com";
map_xj["ub"]="http://ubgs.czbanbantong.com";
map_xj["cms"]="http://cms.czbanbantong.com";
map_xj["tms"]="http://tmsgs.czbanbantong.com";
map_xj["tqms"]="http://tqmsgs.czbanbantong.com";
map_xj["sso"]="http://ssoxj.czbanbantong.com";
map_xj["ilearn"]="http://ilearngs.czbanbantong.com";
map_xj["eng"]="http://en.czbanbantong.com";
map_xj["domain"]="czbanbantong.com";
mapObj["22."]=map_xj;

//青海
var map_qh = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_qh["portal"]="http://gs.czbanbantong.com";
map_qh["pls"]="http://plsgs.czbanbantong.com";
map_qh["ub"]="http://ubgs.czbanbantong.com";
map_qh["cms"]="http://cms.czbanbantong.com";
map_qh["tms"]="http://tmsgs.czbanbantong.com";
map_qh["tqms"]="http://tqmsgs.czbanbantong.com";
map_qh["sso"]="http://ssoqh.czbanbantong.com";
map_qh["ilearn"]="http://ilearngs.czbanbantong.com";
map_qh["eng"]="http://en.czbanbantong.com";
map_qh["domain"]="czbanbantong.com";
mapObj["26."]=map_qh;
//陕西
var map_sn = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_sn["portal"]="http://sn.czbanbantong.com";
map_sn["pls"]="http://plssn.czbanbantong.com";
map_sn["ub"]="http://ubsn.czbanbantong.com";
map_sn["cms"]="http://cms.czbanbantong.com";
map_sn["tms"]="http://tmssn.czbanbantong.com";
map_sn["tqms"]="http://tqmssn.czbanbantong.com";
map_sn["sso"]="http://ssosn.czbanbantong.com";
map_sn["ilearn"]="http://ilearnsn.czbanbantong.com";
map_sn["eng"]="http://en.czbanbantong.com";
map_sn["domain"]="czbanbantong.com";
mapObj["18."]=map_sn;

//宁夏
var map_nx = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_nx["portal"]="http://nx.czbanbantong.com";
map_nx["pls"]="http://plssn.czbanbantong.com";
map_nx["ub"]="http://ubsn.czbanbantong.com";
map_nx["cms"]="http://cms.czbanbantong.com";
map_nx["tms"]="http://tmssn.czbanbantong.com";
map_nx["tqms"]="http://tqmssn.czbanbantong.com";
map_nx["sso"]="http://ssonx.czbanbantong.com";
map_nx["ilearn"]="http://ilearnsn.czbanbantong.com";
map_nx["eng"]="http://en.czbanbantong.com";
map_nx["domain"]="czbanbantong.com";
mapObj["20."]=map_nx;

//西藏
var map_xz = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_xz["portal"]="http://xz.czbanbantong.com";
map_xz["pls"]="http://plssn.czbanbantong.com";
map_xz["ub"]="http://ubsn.czbanbantong.com";
map_xz["cms"]="http://cms.czbanbantong.com";
map_xz["tms"]="http://tmssn.czbanbantong.com";
map_xz["tqms"]="http://tqmssn.czbanbantong.com";
map_xz["sso"]="http://ssoxz.czbanbantong.com";
map_xz["ilearn"]="http://ilearnsn.czbanbantong.com";
map_xz["eng"]="http://en.czbanbantong.com";
map_xz["domain"]="czbanbantong.com";
mapObj["21."]=map_xz;
//山西
var map_sx = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_sx["portal"]="http://sx.czbanbantong.com";
map_sx["pls"]="http://plssx.czbanbantong.com";
map_sx["ub"]="http://ubsx.czbanbantong.com";
map_sx["cms"]="http://cms.czbanbantong.com";
map_sx["tms"]="http://tmssx.czbanbantong.com";
map_sx["tqms"]="http://tqmssx.czbanbantong.com";
map_sx["sso"]="http://ssosx.czbanbantong.com";
map_sx["ilearn"]="http://ilearnsx.czbanbantong.com";
map_sx["eng"]="http://en.czbanbantong.com";
map_sx["domain"]="czbanbantong.com";
mapObj["10."]=map_sx;

//内蒙
var map_nm = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_nm["portal"]="http://nm.czbanbantong.com";
map_nm["pls"]="http://plssx.czbanbantong.com";
map_nm["ub"]="http://ubsx.czbanbantong.com";
map_nm["cms"]="http://cms.czbanbantong.com";
map_nm["tms"]="http://tmssx.czbanbantong.com";
map_nm["tqms"]="http://tqmssx.czbanbantong.com";
map_nm["sso"]="http://ssonm.czbanbantong.com";
map_nm["ilearn"]="http://ilearnsx.czbanbantong.com";
map_nm["eng"]="http://en.czbanbantong.com";
map_nm["domain"]="czbanbantong.com";
mapObj["23."]=map_nm;
//安徽
var map_ah = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_ah["portal"]="http://ah.czbanbantong.com";
map_ah["pls"]="http://plsah.czbanbantong.com";
map_ah["ub"]="http://ubah.czbanbantong.com";
map_ah["cms"]="http://cms.czbanbantong.com";
map_ah["tms"]="http://tmsah.czbanbantong.com";
map_ah["tqms"]="http://tqmsah.czbanbantong.com";
map_ah["sso"]="http://ssoah.czbanbantong.com";
map_ah["ilearn"]="http://ilearnah.czbanbantong.com";
map_ah["eng"]="http://en.czbanbantong.com";
map_ah["domain"]="czbanbantong.com";
mapObj["15."]=map_ah;
//江苏
var map_js = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_js["portal"]="http://js.czbanbantong.com";
map_js["pls"]="http://plsjs.czbanbantong.com";
map_js["ub"]="http://ubjs.czbanbantong.com";
map_js["cms"]="http://cms.czbanbantong.com";
map_js["tms"]="http://tmsjs.czbanbantong.com";
map_js["tqms"]="http://tqmsjs.czbanbantong.com";
map_js["sso"]="http://ssojs.czbanbantong.com";
map_js["ilearn"]="http://ilearnjs.czbanbantong.com";
map_js["eng"]="http://en.czbanbantong.com";
map_js["domain"]="czbanbantong.com";
mapObj["14."]=map_js;
//广西
var map_gx = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_gx["portal"]="http://gx.czbanbantong.com";
map_gx["pls"]="http://plsgx.czbanbantong.com";
map_gx["ub"]="http://ubgx.czbanbantong.com";
map_gx["cms"]="http://cms.czbanbantong.com";
map_gx["tms"]="http://tmsgx.czbanbantong.com";
map_gx["tqms"]="http://tqmsgx.czbanbantong.com";
map_gx["sso"]="http://ssogx.czbanbantong.com";
map_gx["ilearn"]="http://ilearngx.czbanbantong.com";
map_gx["eng"]="http://en.czbanbantong.com";
map_gx["domain"]="czbanbantong.com";
mapObj["12."]=map_gx;

//桂林
var map_guilin = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_guilin["portal"]="http://guilin.czbanbantong.com";
map_guilin["pls"]="http://plsgx.czbanbantong.com";
map_guilin["ub"]="http://ubgx.czbanbantong.com";
map_guilin["cms"]="http://cms.czbanbantong.com";
map_guilin["tms"]="http://tmsgx.czbanbantong.com";
map_guilin["tqms"]="http://tqmsgx.czbanbantong.com";
map_guilin["sso"]="http://ssoguilin.czbanbantong.com";
map_guilin["ilearn"]="http://ilearngx.czbanbantong.com";
map_guilin["eng"]="http://en.czbanbantong.com";
map_guilin["crm"]="http://crmgx.czbanbantong.com";
map_guilin["domain"]="czbanbantong.com";
mapObj["32."]=map_guilin;
//广东
var map_gd = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_gd["portal"]="http://gd.czbanbantong.com";
map_gd["pls"]="http://plsgd.czbanbantong.com";
map_gd["ub"]="http://ubgd.czbanbantong.com";
map_gd["cms"]="http://cms.czbanbantong.com";
map_gd["tms"]="http://tmsgd.czbanbantong.com";
map_gd["tqms"]="http://tqmsgd.czbanbantong.com";
map_gd["sso"]="http://ssogd.czbanbantong.com";
map_gd["ilearn"]="http://ilearngd.czbanbantong.com";
map_gd["eng"]="http://en.czbanbantong.com";
map_gd["domain"]="czbanbantong.com";
mapObj["17."]=map_gd;

//海南
var map_hi = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_hi["portal"]="http://hi.czbanbantong.com";
map_hi["pls"]="http://plsgd.czbanbantong.com";
map_hi["ub"]="http://ubgd.czbanbantong.com";
map_hi["cms"]="http://cms.czbanbantong.com";
map_hi["tms"]="http://tmsgd.czbanbantong.com";
map_hi["tqms"]="http://tqmsgd.czbanbantong.com";
map_hi["sso"]="http://ssohi.czbanbantong.com";
map_hi["ilearn"]="http://ilearngd.czbanbantong.com";
map_hi["eng"]="http://en.czbanbantong.com";
map_hi["domain"]="czbanbantong.com";
mapObj["29."]=map_hi;
//云南
var map_yn = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_yn["portal"]="http://yn.czbanbantong.com";
map_yn["pls"]="http://plsyn.czbanbantong.com";
map_yn["ub"]="http://ubyn.czbanbantong.com";
map_yn["cms"]="http://cms.czbanbantong.com";
map_yn["tms"]="http://tmsyn.czbanbantong.com";
map_yn["tqms"]="http://tqmsyn.czbanbantong.com";
map_yn["sso"]="http://ssoyn.czbanbantong.com";
map_yn["ilearn"]="http://ilearnyn.czbanbantong.com";
map_yn["eng"]="http://en.czbanbantong.com";
map_yn["domain"]="czbanbantong.com";
mapObj["9."]=map_yn;

//襄阳
var map_xy = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_xy["portal"]="http://xy.czbanbantong.com";
map_xy["pls"]="http://plsxy.czbanbantong.com";
map_xy["ub"]="http://ubxy.czbanbantong.com";
map_xy["cms"]="http://cms.czbanbantong.com";
map_xy["tms"]="http://tmsxy.czbanbantong.com";
map_xy["tqms"]="http://tqmsxy.czbanbantong.com";
map_xy["sso"]="http://ssoxy.czbanbantong.com";
map_xy["ilearn"]="http://ilearnxy.czbanbantong.com";
map_xy["eng"]="http://en.czbanbantong.com";
map_xy["domain"]="czbanbantong.com";
mapObj["19."]=map_xy;
//北镇
var map_jz = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_jz["portal"]="http://jz.czbanbantong.com";
map_jz["pls"]="http://plsjz.czbanbantong.com";
map_jz["ub"]="http://ubjz.czbanbantong.com";
map_jz["cms"]="http://cms.czbanbantong.com";
map_jz["tms"]="http://tmsjz.czbanbantong.com";
map_jz["tqms"]="http://tqmsjz.czbanbantong.com";
map_jz["sso"]="http://ssojz.czbanbantong.com";
map_jz["ilearn"]="http://ilearnjz.czbanbantong.com";
map_jz["eng"]="http://en.czbanbantong.com";
map_jz["domain"]="czbanbantong.com";
mapObj["31."]=map_jz;
//湖南
var map_hn = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_hn["portal"]="http://hn.czbanbantong.com";
map_hn["pls"]="http://plshn.czbanbantong.com";
map_hn["ub"]="http://ubhn.czbanbantong.com";
map_hn["cms"]="http://cms.czbanbantong.com";
map_hn["tms"]="http://tmshn.czbanbantong.com";
map_hn["tqms"]="http://tqmshn.czbanbantong.com";
map_hn["sso"]="http://ssohn.czbanbantong.com";
map_hn["ilearn"]="http://ilearnhn.czbanbantong.com";
map_hn["eng"]="http://en.czbanbantong.com";
map_hn["domain"]="czbanbantong.com";
mapObj["28."]=map_hn;

//湖北20150527增加
var map_hb = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_hb["portal"]="http://hb.czbanbantong.com";
map_hb["pls"]="http://plshn.czbanbantong.com";
map_hb["ub"]="http://ubhn.czbanbantong.com";
map_hb["cms"]="http://cms.czbanbantong.com";
map_hb["tms"]="http://tmshn.czbanbantong.com";
map_hb["tqms"]="http://tqmshn.czbanbantong.com";
map_hb["sso"]="http://ssohb.czbanbantong.com";
map_hb["ilearn"]="http://ilearnhn.czbanbantong.com";
map_hb["eng"]="http://en.czbanbantong.com";
map_hb["statpv"]="statpvhn.czbanbantong.com";
map_hb["qikan"]="http://bbtrrtxs.vip.qikan.com";
map_hb["nbsy"]="http://nbhn.czbanbantong.com/experiment/index/wuli?sso=a36811c7ccbbec77aab20fd7b91f821c6de530a9f8a7b944&sso_ip=a375f58263714f6bbd7923d9a6b7341f";
map_hb["gkzy"]="http://zz.api.ks5u.com";
map_hb["pingk"]="http://pkhn.czbanbantong.com";
mapObj["37."]=map_hb;
//湖北汉阳20151215增加
var map_hbhy = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_hbhy["portal"]="http://hbhy.czbanbantong.com";
map_hbhy["pls"]="http://plshn.czbanbantong.com";
map_hbhy["ub"]="http://ubhn.czbanbantong.com";
map_hbhy["cms"]="http://cms.czbanbantong.com";
map_hbhy["tms"]="http://tmshn.czbanbantong.com";
map_hbhy["tqms"]="http://tqmshn.czbanbantong.com";
map_hbhy["sso"]="http://ssohbhy.czbanbantong.com";
map_hbhy["ilearn"]="http://ilearnhn.czbanbantong.com";
map_hbhy["eng"]="http://en.czbanbantong.com";
map_hbhy["statpv"]="statpvhn.czbanbantong.com";
map_hbhy["qikan"]="http://bbtrrtxs.vip.qikan.com";
map_hbhy["nbsy"]="http://nbhn.czbanbantong.com/experiment/index/wuli?sso=a36811c7ccbbec77aab20fd7b91f821c6de530a9f8a7b944&sso_ip=a375f58263714f6bbd7923d9a6b7341f";
map_hbhy["gkzy"]="http://zz.api.ks5u.com";
map_hbhy["pingk"]="http://pkhn.czbanbantong.com";
mapObj["38."]=map_hbhy;

//山西大同
var map_dt = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_dt["portal"]="http://dt.czbanbantong.com";
map_dt["pls"]="http://plsdt.czbanbantong.com";
map_dt["ub"]="http://ubdt.czbanbantong.com";
map_dt["cms"]="http://cms.czbanbantong.com";
map_dt["tms"]="http://tmsdt.czbanbantong.com";
map_dt["tqms"]="http://tqmsdt.czbanbantong.com";
map_dt["sso"]="http://ssodt.czbanbantong.com";
map_dt["ilearn"]="http://ilearndt.czbanbantong.com";
map_dt["eng"]="http://en.czbanbantong.com";
map_dt["domain"]="czbanbantong.com";
mapObj["33."]=map_dt;

//科威
var map_kw = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_kw["portal"]="http://kw.czbanbantong.com";
map_kw["pls"]="http://plskw.czbanbantong.com";
map_kw["ub"]="http://ubkw.czbanbantong.com";
map_kw["cms"]="http://cms.czbanbantong.com";
map_kw["tms"]="http://tmskw.czbanbantong.com";
map_kw["tqms"]="http://tqmskw.czbanbantong.com";
map_kw["sso"]="http://ssokw.czbanbantong.com";
map_kw["ilearn"]="http://ilearnkw.czbanbantong.com";
map_kw["eng"]="http://en.czbanbantong.com";
map_kw["domain"]="czbanbantong.com";
mapObj["34."]=map_kw;

//鹤壁
var map_hebi = {};   //内层集合对象，存放河南地区系统编码以及对应的域名
map_hebi["portal"]="http://hebi.czbanbantong.com";
map_hebi["pls"]="http://plshenan.czbanbantong.com";
map_hebi["ub"]="http://ubhenan.czbanbantong.com";
map_hebi["cms"]="http://cms.czbanbantong.com";
map_hebi["tms"]="http://tmshenan.czbanbantong.com";
map_hebi["tqms"]="http://tqmshenan.czbanbantong.com";
map_hebi["sso"]="http://ssohebi.czbanbantong.com";
map_hebi["ilearn"]="http://ilearnhenan.czbanbantong.com";
map_hebi["eng"]="http://en.czbanbantong.com";
map_hebi["statpv"]="statpvhenan.czbanbantong.com";
map_hebi["qikan"]="http://bbtrrtxs.vip.qikan.com";
map_hebi["nbsy"]="http://nbhenan.czbanbantong.com/experiment/index/wuli?sso=b502be4bad42c52a5bfbf6e4775ab4e40a1f89119f60218f7d0feb6e27880bef&sso_ip=65a13da4931d082a596f9bb4167e6a7a";
map_hebi["gkzy"]="http://zz.api.ks5u.com";
map_hebi["pingk"]="http://pkhenan.czbanbantong.com";
mapObj["35."]=map_hebi;

//德阳 20150512增加
var map_dy = {};
map_dy["portal"]="http://dy.czbanbantong.com";
map_dy["pls"]="http://plsdy.czbanbantong.com";
map_dy["ub"]="http://ubdy.czbanbantong.com";
map_dy["cms"]="http://cms.czbanbantong.com";
map_dy["tms"]="http://tmsdy.czbanbantong.com";
map_dy["tqms"]="http://tqmsdy.czbanbantong.com";
map_dy["sso"]="http://ssody.czbanbantong.com";
map_dy["ilearn"]="http://ilearndy.czbanbantong.com";
map_dy["eng"]="http://en.czbanbantong.com";
map_dy["statpv"]="statpvdy.czbanbantong.com";
map_dy["qikan"]="http://bbtrrtxs.vip.qikan.com";
map_dy["nbsy"]="http://nbdy.czbanbantong.com/experiment/index/wuli?sso=187abcf0039b63bd82bf0a21462ffddfbb643376ee9b0d46&sso_ip=a375f58263714f6b2a951f97163bdaf0";
map_dy["gkzy"]="http://zz.api.ks5u.com";
map_dy["pingk"]="http://pkdy.czbanbantong.com";
mapObj["36."]=map_dy;



function redirectfun(type){
    //var userarea=$.cookie('localAreaCode');
    //var pls=mapObj[userarea]['pls'];
    var url='';
    if(type==1){
        //快乐学生字
        url='../Klxsz/index';
    }else if(type==2){
        //快乐学拼音
        url='../Klxpy/index';
    }else if(type==3){
        //快乐学字母
        url='../Klxzm/index';
    }else if(type==4){
        //成语词典
        url='../Cydict/index';
    }else if(type==5){
        //在线字典
        url='../Dictpc/index';
    }else if(type==6){
        //可可小爱
		
        //url=pls+'/youjiaoplay/xxkaPlay.do?ksId=0001110a01';
		getPlsUrl('/youjiaoplay/xxkaPlay.do?ksId=0001110a01');
    }else if(type==7){
        //十万个为什么
        //url=pls+'/youjiaoplay/whyPlay.do?ksId=0001111002';
		getPlsUrl('/youjiaoplay/whyPlay.do?ksId=0001111002');
    }else if(type==8){
        //必备古诗
        //url=pls+'/youjiaoplay/poem.do?ksId=0001450901';
		getPlsUrl('/youjiaoplay/poem.do?ksId=0001450901');
    }else if(type==9){
    	//英语同步练
    	url='../../index';
    }else if(type==10){
    	//小奥数
    	return false;
    }else if(type==11){
    	//口算卡
    	return false;
    }
	if(type<6){
		window.open(url);
	}
}

function getPlsUrl(str){
	$.ajax({
		type:'GET',
		url:'../Klxpy/getPlsUrl',
		data:{ran:Math.random()},
		dataType:'json',
		success:function(data){
			window.location.href = window.location.protocol+'//'+data+str;
		},
		error:function(e){
		}
	})
}