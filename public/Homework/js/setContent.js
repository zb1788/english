require.config({
　baseUrl: "../../public/Homework/js",
　paths: {
　　　"jquery": "jquery-1.7.2.min",
　　　"mui": "mui.min",
　}
});
//文件主要是进行不同体型的展示
define(['jquery','mobile','mui'],function($,mobile,mui){  
    var studentContent = {}; //推荐方式  
    var studentContentData = function(data){  
        return data;
    };
    //Dom元素初始化
    var initDom=function(dom,attrarr){
    	var dom=$(dom);
    	$.each(attrarr,function(key,value){
    		dom.attr(value.id,value.val);
    	});
    	return dom;
    }
    //第一题出现遮罩
    var create=function(b,t) {
        var c = document.createElement("div");
        c.innerHTML = t,
        c.setAttribute("class",b);
        return c
    }
    //$.pop.show("ttip","","ttip_wz","下面将开始听力训练");
    var show=function(a,ta,b,tb){
        var ttip=this.create(a,ta);
        var ttip_wz=this.create(b,tb);
        $(ttip).attr("style","background-color:#333; display:block; position:absolute; top: 0; left:0; color:#fff;  opacity:0.7; width:100%; display:none; box-shadow: 1px 1px 5px #555; text-align:center; z-index:1000;");
        $(ttip_wz).attr("style","width:100%;text-align:center;position:absolute; color:#fff; top: 40%; padding: 0 30px; z-index: 1001; font-size: 1.2em; line-height: 2em;");
        $(ttip).css('height',$(window).height());
        // console.log(ttip);
        document.body.appendChild(ttip);
        document.body.appendChild(ttip_wz);
        $(ttip).fadeIn(300).delay(500).fadeOut(300);
        $(ttip_wz).fadeIn(300).delay(500).fadeOut(300);
    }
    //动态创建语音
    var setVoice=function(data,clickevent){
    	var attrarr=[];
    	var temp={};
    	temp.id="class";
    	temp.val="lanren";
    	attrarr.push(temp);
        temp={};
    	temp.id="id";
    	temp.val="lanren";
    	attrarr.push(temp);
        temp={};
    	temp.id="style";
    	temp.val="text-align:center;margin-top:10px;";
    	attrarr.push(temp);
    	var div =initDom("<div></div>",attrarr);
    	attrarr=[];
    	temp={};
    	temp.id="style";
    	temp.val="width:98%;margin-left:auto;margin-right:auto;padding-top:10px;";
    	attrarr.push(temp);
    	var vdiv=initDom("<div></div>",attrarr);
    	vdiv.appendTo(div);
        //左边的波浪
    	attrarr=[];
    	temp={};
    	temp.id="class";
    	temp.val="sy-left";
    	attrarr.push(temp);
    	var lspan=initDom("<span></span>",attrarr);
    	lspan.appendTo(vdiv);
    	attrarr=[];
    	temp={};
    	temp.id="src";
    	temp.val="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKwAAABnCAYAAACdHqmvAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjc5MUNBQzZFNTdCRTExRTY4OTQ3RjhFRkEwQThDQUIzIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjc5MUNBQzZGNTdCRTExRTY4OTQ3RjhFRkEwQThDQUIzIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6NzkxQ0FDNkM1N0JFMTFFNjg5NDdGOEVGQTBBOENBQjMiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6NzkxQ0FDNkQ1N0JFMTFFNjg5NDdGOEVGQTBBOENBQjMiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz5DOUrSAAAQsklEQVR42uxdW4ykRRU+9fcOswOssCuIizcUL/GCosRIolGMMT4pGhMveIsvavTBF6PEGzwYQyQxMTFGvOKDD8R4S9RoxIAoCeIGkJvIKrssN12Zda8wMztdZR37lFNTc05V/T3d09P/nJOc9D893f+tvvrqO6dO/W2cc6CmNi1mFLBqClg1NQWsmpoCVk0Bq6Y2sIuvu/z/OPO+Cmi3vPuqofbZ6G1VG7Od4v0c708ZBd4UsGpjHcG9P9/7R72/iUC7Ltum97Szw3DPu42H4mGH4XUS4oXe3+f9Od7v8n5YGVYttad6f533F3qfnfC5nOF9O7HrdpUEalybvsY70umHvJ894fMJLG9oWwGrtkY37vb+dO/P9b5jk5zTyHqjWvdsJvLN0sZWAasmmYuCLbcJziWwrFHAqm32djWROwWs2mZnWNPVnqg2HqZtNknncQpYtRyrjRQom4Vtdaaru5LARK+iJQUqq3KlI5oZ445v/HERe/1wvNpjKcN2l2HbBDqY/jrX+4th9Hlbl2QIEHPne/8AvbbCoAK2uwwLxF41w/Au7+/w/jnvF4wYFzFrGxrV8Vjf9f526iwqCSZl0RALKcNtcPGJJeDVMOxO76/2fhEM6g9uhREl+un4TaSnG+ogaGcpw26e6ByLPbAOdHaC51DUsGRY2XUqDGpX58YcaMXn5Ia5KLXRG4IV6z8/AoN6UDOBc3AtQTU0iCqYntseKmugkmA8hhVSb/X+Wu//8H4fRcTjkh89BhBtZ5fGlf6KZUk6PesUsJvDZohl52C81VKG9Ocr6e/bvR+F1XlY0xKsbszn69YDWgXs+BpmJMUeFez1cu9X0rG+4P3mIUDnEhaUmHyYxYSuAFajgJ28bdQ8viEmDwv8Tk86SgwwQ8DjshaGvp/rZBiQ4UqGE96Pt8wixGCNO0frGEoBO35AbeRxGqbDOJIm59L2Y96fFBhWmsrF/b/M+/u93+H9l97nBSYOudZ+AlIQdOzWA6wQfLjKIWucgY8dFWhz+d3CMfC8zvP+Yfr7e97/xny/yeyrRwEkAvYFpJXnBYnyPO9v8P577w8KAdfQGYltHWIyDD5eRTcBWeAQbEzhRxr43MEMgaPUrDjsY870PxGLcY0fH/sZMJgYwO//1vv9DGD7mfMN7y0WOmGYxfqK9097/3oCVMt06tAhV5GNRDRdAWwIPr5I2zjF+MdxpJKEY19AgQ9Q4DM/psALteob6Xg/JuDFwYtNQNaL9Occneus0OlKenKJ7udSfF890OKimYZ0LtBrj7lXXHC6q5ZsusSwmD46k/4+c4P1IwLpjOg8DnHM5xu3SXViYBJG/3FVTHF+d5/3v0vRexpwMdkASKTLGpaOzslFbB7vY4bOCUeYA95PMvfdZEYBm5CNodebJLLp0kyXgcnMKKW6TyrrQyC/hIKfGaEtSlVMM9Qx5kgWcKkjENJGa6ZqqcRPCtT+dz99hwnbJ5hzehqdL44uF0aZBk5OcOdpok6+k3xHrh27NjU7srVDI0hpxY0fWOSz3t8JK8UfnP7LVTFxgYvUli7zaqjjXEpD8XZmnw2ClYb8Gcos2OQcUBu/GbHv/aV0DjbTeYABLCQdqqi/umKTrK53DMPHzNZQdI3guIhYkmuLWP81ldcYd1InMH7KtqgtL/H+Ne+fgEGRjok6VxPpT0O6dxtznPD5OXKusMUxzGsZxrU17delPGzccK1L46K0UWiw/hCpsRi4NgHZLHmoioqDFleRnoIMu0rgjOVJk0T4O4lZd0XnY6JgrU/n1hBOTmNYfoGkgqVgTEq12QLDVhPNtg4yrGOiU3F6MQEjggmr7vHBZX/y/mjlzUyliBVYLmWYOWLTQ0mjmsrrlIIb7juWYVrp3E+hAKpP57edPD32Mn3ORmkx07KTAZRn2TqrYUvXhAB5VsQW6Y3dTUMkDpWv54CfMSuwuxH0J57nK7xfTq9NBeO4DACcoJ9zuVXDfC68onx5r/fPM+eXAxuXa03PwTKywdbIuq5Nza7RQslMFOo2rFH9tvffMKmTwHhzNFSalseGSP9ZIfoOoOhRp7jM+0Pe76lgWC636zKdtUnkgWF0Y8O8HyYILqNg8a/AP+sgpz1zqbQmIw3M1AI2GcYBystN4gveBisP0D1GNwmj4rd5/5f3G2DtnPqw6bE08Okn4EnTS3H+NpQi1iTvc+CQGluqjnIJ4JrkOIdJlxqGfZuKId9khntOIlXp2Wlg2FLhBgfYhob3t9D7v/J+kIKEZdqHqx1yKeG/aphLOoxljl+K5CVAluQOtw9XmWLjWC5lWCekxpzQAaQca6qfbeZcTObeTRVg8QLOg0HhBl7QtTCo3ncVUTIm399Ff99PgH2C/nci7IMi4fTGxVp0B6WkDO3nWIHhpMJplwFtbZRsGS3ZCDrRZIIzKYBrmP3lpphzrAlJWit3va4rDIsX8UwYPKAX7XpYW2kkCftTYaXaf05gl8DeRsgI4Psv8v4p+vtq77dVBEUNyIn7odI5FVmCElC4/KhpyYQSg0KSZZDapWZUM9MMWKAUy2y0zeUvAeTaSytEzuhYCvcx2u81sDIXHhc0n0Usj3Y2yPP2JtGZLqPtpDVODaPjewm7QgYwXAVWCXC5/9fMPjWZa0gDwiYTZFVNHkxrlgABtpO06HFG/KfDnBFyjviEaqzdxJK9P3j/C3OsXnSjA3ga0rX9hFVydaVNZhg3QioIMxXxei1TCKZcplO4QgBWo4G5qN5mdG+JjVuv7ZomwMZaCotI3gODBz5cT9qUu6mu0LAzdA9OUjAm3bi4QARlBE4uPBsGkwsHmSheSj81QsNJx8SU0hW0jWWLjxfAmu6/YaJwiekB8pMPhsmx1qyCdRUpq5JWloegTQrUeMjrESt+EAaFF6dlWMFkbqCJgLpIbG2YoSl+r0ea9+MwmFy4hN4zCUik6WFuDt1kghhMd2Gp5Bk0CtQO5RIguYyGxKI1x3AZsJkWLJqmBKcasJzmCcn9E5EkAObGSYEE14A2w0b9CIizdPxTYW3drYG1FU0uMyRCYciUGt5W5EFD53aVQWqTyQRYIaiqeQCHy0iQXCwwtZLACZGkIXa0FXlDaZFeeM8Kmi8eykt5QlfRSOnQbaF+bVYumnbCNXMJeQngNnM9TcJ83LFL12CSuAAqGHnqGTY0QpgA2BWlrmoSz2kZoBHYxQn5zVwCPZ3+lAKMuGa0EVJOJtNhXUZyuEx+1bQMfFL27AkxQa8ysIo7rJT6stAhDRvfhKMkB3bA6trSmMFMIQeZNr7L5EiNkIIxBaaVWNAw77nCUCkFJy5JxUlAgApWMyDPXtmMXKgt2pHumYXK4vtpkASLxKiOgqTwGvKS25gLt5lAywkAlIbGdL8m04guw+IA+dmi3OjA6U1Ivtf6KSrMiOGYfLIUHEnn2Qj5XID8KoSqGubNDli8oIe930J/PyzoTy7fyLHpYZIShzNa02TylaGDzMJKJT4wAOUqlRao8z1BHU5iTygwEPfZvsCukGHT3EgU31tL98xUprKcQBKuImVZ1LDTANj93r8FK8Uvjhp/iRrfChefDrHYqFhSiJMOv6ZGcJloPrd2ajvlcGeZYTde4x8Dbq/3Pd7/7P0Iwyy54RhAfoiaVFiTCyLj9JskV44TQcxTrvkcAZRNQbPbipGmUysOMD/6QHKD8BGWuH79togt44bpM4yLNw7rOh+ElWUdkNGOThi2sDTxd7SP2yJ2y02x4vfwp9e/DIMHYBxiOgR+JkwMzCcgqqmoKskICSgW+Nk2BOl3YPBYIqzVvbRif5zcaAqxQaqH+9MOWA40OIX6JWr4Q9H7lmGPeLhZIg+fSbXgAoFmgYDFVfBjQ+Ljfn5C4F8mxjxC/z8C/APPjsFKITQQ08cNhvv5GR3/JpINKVAt5Fc0lGorrBAMNUwwt0Dne18FO+ZSWTYTMHLX0OsCYFPDLMHdUWP0CBBH6KLx/6dXsFBa8oeS4xvef0G6mWNqBNIj5OE9ZM8raJ+4vVtg2lItLI4c++i4PQZ0TSZ/KpUtGiZVB8n+pBkmbo2cLeSIoQK8NiPDpjdLwK0qiFYhOGbIvTICzcUt2duSJsZh/nZYPbtlQV5z5Yjlb4w+u7tF5Jtq3VzmAQqZBMik8qTIvxZwIKTUJJ3NlS5KWQMjfK4zDMvd8Bg0cWS9qjGSRwMdizTwCQFkDZSXj0sMJa7QFTqeBCQD+YkIVxiquWXe3OcsRxjRc2VdRSfJ5ZKbQmYBOicJMqwbgyY0zlI0jHNgwGDiKtq+WwDPcVhZZfCk0Ji5/GatNk/3yy0Zl56KGBfnSIxnC1kGA3I9Qbj2BbofixWBF6d7rSBnAPgH2nWWYTkgYPBygLbnhV6N719P28sCaFBXXkf/2guFR6ZHD1AzlexZIxUgieYd5Ott2wzVAHIReHzse+le7aGANM0yuIoca1M4v6lf0zUU6xLQcCnN1XQz9ma028kCqDAQ+xFtH2uRMzTr/H8K/DBqhAdcLAkalwt0Sstecqmw8B3MGHyVAtrDDBhz11Wacq5eMtTlR8Yfo+Bpvc/cOhmlzoZJxZmKz5Qs6FQcjjEnjcXjjyZgtcIo0gBfuMIFnbnzwYB0P5RzwLbiPhhBlnQmDzus2Qkd11XKgppf3I6//2/vP4DBTN09sHrK1hS0dAN8NZYRIvfaDuYEdnaMDMhVcsGWkQSjCt5GaG0eSpcbho+Sh20Meu4niWOp/bjh9jgFSUdhpWCoZiKhDetLwVXD3IOajmu3bNA1KjCOYD8lnZqTK9hwd8Igt4yfuUsIcNJVDpak0A/p+8jK57fIGrTJzJR0ajrKcNO0oTMeiTqbU4bdeClyMnqVbDkT+DmKxm9owdYBAPgo+W8Syz5ZSGe1kSepLZK+fwJWF/RIAVa/0DGBtq0CdmMNWWIfBUePZYKTA8QuBzLDc19ivCj/nOpTBNJBJt0FBVZtW9CPhUDfh8FPHN0M8no4G3VOTvemM4WqYTfYECzXwuCZXvcKjRDKHf8Jg8qz5XXIDlsA2xKsTHosMqy6TCy5QF5rODuIS9330H5nGY38MGU0HoCV2URT0zEVsBtni0lwJA3fjxJg++vQz9JDlOP/I2hujbbj7zQ0Iuyh9NfeltmVk8AXpIfOgMf9DF3nQcgvYFTATtBqn381qt8SM4JsCEXw18DqIvggASzpTyyXxJmsh2C06cD5SC6gbed0dG0HVcBOv3FPv04tLYKfSaL3ZWL7R2D9P2zClQ7awuigDDspq2WKMeSBHZSXuHOfj+uB3Xquk4LAprKTAQzxu2oK2G4xrWv5+XHMBLocuKPMhjLsFtXJcTHKUShUkzGgGTXz1/6E01CmgJ1uS2fD7hyCNd0IwVX7JG2jgN26DIuzYTdC+x/Uq3nE5jDnMx9lB2rWf2laawuCtj8k0DF7EFYJj8Iw2/BTAmtY+ZuTDa3ZXQG7Na1PrPxJGCxVr/3FxxqJgumz/RD99GkmOGz9bDcF7BTbOgKkMMv2c6j8UeKWoC1JE9WwahsiJUZ5bAWs2tQyvgJWbbLAqpQErZm20eZTmyBgQQGrNi36eSjgKmDVJglYq4BVmyZrFLBq06RhNehS2/QWP2n88bayQNNaahtttfUGCli1TcOwpXoDWUs45/QWqk2P+FXAqilg1dQUsGpqClg1BayamgJWTU0Bq6aAVVNTwKqpKWDVptL+K8AAIc2KI/Rd2QIAAAAASUVORK5CYII=";
        attrarr.push(temp);
        temp={};
        temp.id="height";
    	temp.val="40px";
        attrarr.push(temp);
        var limg=initDom("<img></img>",attrarr);
        lspan.append(limg);
        //<a class="edi-sy audio-btn" id="audio-btn" quesid="'+data.parent.question.id+'" type="0"><img class="sy-click" id="sy-click" src="../../public/Homework/images/sy.png" height="40px;"></a>
        //中间的点击区域
        attrarr=[];
        temp={};
        temp.id="class";
        temp.val="edi-sy audio-btn";
        attrarr.push(temp);
        temp={};
        temp.id="id";
        temp.val="audio-btn";
        attrarr.push(temp);
        temp={};
        temp.id="quesid";
        temp.val=data.id;
        attrarr.push(temp);
        temp={};
        temp.id="type";
        temp.val="0";
        attrarr.push(temp);
        var ma=initDom("<a></a>",attrarr);
        ma.appendTo(vdiv);
        attrarr=[];
        temp={};
        temp.id="src";
        temp.val="../../public/Homework/images/sy.png";
        attrarr.push(temp);
        temp={};
        temp.id="height";
        temp.val="40px";
        attrarr.push(temp);
        temp={};
        temp.id="class";
        temp.val="sy-click";
        attrarr.push(temp);
        temp={};
        temp.id="id";
        temp.val="sy-click";
        attrarr.push(temp);
        temp={};
        temp.id="loc";
        temp.val=data.loc;
        attrarr.push(temp);
        temp={};
        temp.id="mp3";
        temp.val=data.mp3;
        attrarr.push(temp)
        var mimg=initDom("<img></img>",attrarr);
        mimg.bind("tap",clickevent);
        ma.append(mimg);
        //右边的波浪
        attrarr=[];
        temp={};
        temp.id="class";
        temp.val="sy-right";
        attrarr.push(temp);
        var rspan=initDom("<span></span>",attrarr);
        rspan.appendTo(vdiv);
        attrarr=[];
        temp={};
        temp.id="src";
        temp.val="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKwAAABnCAYAAACdHqmvAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjc0RUUyRUVCNTdCRjExRTZCREFCREM2QURGMjI2MzgyIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjc0RUUyRUVDNTdCRjExRTZCREFCREM2QURGMjI2MzgyIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6NzRFRTJFRTk1N0JGMTFFNkJEQUJEQzZBREYyMjYzODIiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6NzRFRTJFRUE1N0JGMTFFNkJEQUJEQzZBREYyMjYzODIiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz7iozswAAAQpElEQVR42uxdWaykRRU+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";
        attrarr.push(temp);
        temp={};
        temp.id="height";
        temp.val="40px";
        attrarr.push(temp);
        var rimg=initDom("<img></img>",attrarr);
        rspan.append(rimg);
    	//进行语音的监听事件
    	return div;
    }
    //听力训练选择题
    var choiceExamsQuestion=function(data){
        //设置头部信息
        var attrarr=[];
        var temp={};
        temp.id="class";
        temp.val="parent";
        attrarr.push(temp);
        temp.id="id";
        temp.class="parent";
        attrarr.push(temp);
        // temp.id="quescount";
        // temp.val=data.quescount;
        // attrarr.push(temp);
        temp.id="stemid";
        temp.val=data.question.stemid;
        attrarr.push(temp);
        temp.id="style";
        temp.val="height:100%;margin-left: 0px;";
        attrarr.push(temp);
        var parentdiv =initDom("<div></div>",attrarr);
        var stemdiv=$("<div></div>");
        stemdiv.addClass("tigan");
        stemdiv.attr("style","margin-top:10px;margin-left:10px;font-family: times;text-align:left;font-size:0.8em;color: #8f8f94;");
        //进行图片的控制
        try{
            stemdiv.getElementsByClassName("tigan").find("img").css("width","100%");
        }catch(e){
            
        }
        stemdiv.html(data.question.stemcontent);
        parentdiv.append(stemdiv);
    	//判断是否有音频1表示需要音频 0表示不需要音频
    	var voicediv="";
    	if(data.question.isvoice=='1'){
    		voicediv=setVoice(data.question,examsPlayEvent);
            parentdiv.append(voicediv);
    	}
    	//选择题展示
    	var div=$("<div></div>");
    	div.addClass("title tigan");
    	div.attr("style","margin-top:30px;margin-left:30px;font-size: 1.0em;font-family: times;color:black;text-align:center;");
        // console.log(data.question.tcontent)
        div.html(data.question.tcontent);
        parentdiv.append(div);
    	var ul=$("<ul></ul>");
    	ul.addClass("mui-table-view xuanze");
    	ul.attr("style","margin-top: 30px;background-color: while;text-align:center;");
        var answerflag="";
    	mui.each(data.question.questions_items,function(index,item){
    		var li=$("<li></li>");
    		li.addClass("edi-cell mui-media");
    		li.attr("quesid",data.question.id);
    		li.attr("itemflag",item.flag);
    		li.attr("answerid",data.answer[0].quesansid);
    		li.attr("homeworkid",data.question.homeworkid);
    		li.attr("examsid",data.question.examsid);
    		li.attr("quizid",data.question.quizid);
    		li.attr("typeid",data.question.typeid);
            li.bind("tap",choiceExamsEvent);
    		var a=$("<a></a>");
    		a.attr("quesid",data.question.id);
    		a.attr("itemflag",item.flag);
    		a.attr("answerid",data.answer[0].quesansid);
    		a.attr("homeworkid",data.question.homeworkid);
    		a.attr("examsid",data.question.examsid);
    		a.attr("quizid",data.question.quizid);
    		a.attr("typeid",data.question.typeid);
    		li.html(a);
    		//a.appendTo(li);
    		var div=$("<div></div>");
    		div.addClass("mui-media-body");
            div.attr("style","float:left;position:absolute;");
            if(data.question.itemtype=='1'){
    		    div.attr("style","float:left;position:absolute;line-height: 100px;");
            }

    		a.append(div);
    		var tdiv=$("<div></div>");
    		tdiv.addClass("items");
            var tdiv=$("<div></div>");
            tdiv.addClass("items");
            //tdiv.attr("style","width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;");
            if(issubmit=='0'){
                //未提交作业的展示
                if(item.flag==data.answer[0].answer){
                    tdiv.attr("style","width:30px;height:30px;background-color:#2bc8a0; border-radius:25px;display: inline-block;");
                }else{
                    tdiv.attr("style","width:30px;height:30px;background-color:white;border-radius:25px;display:inline-block;border:1px solid gray;");
                }
            }else if(issubmit=='1'){
                //已提交作业的展示
                if(item.flag==data.answer[0].answer&&item.content==data.answer[0].quesanswer){
                    answerflag=item.flag;
                    tdiv.attr("style","width:30px;height:30px;background-color:#2bc8a0; border-radius:25px;display: inline-block;");
                }else if(item.flag!=data.answer[0].answer&&item.content==data.answer[0].quesanswer){
                    answerflag=item.flag;
                    tdiv.attr("style","width:30px;height:30px;background-color:#2bc8a0; border-radius:25px;display: inline-block;");
                }else if(item.flag==data.answer[0].answer&&item.content!=data.answer[0].quesanswer){
                    tdiv.attr("style","width:30px;height:30px;background-color:#FE5A59;border-radius:25px;display:inline-block;border:1px solid gray;");
                }else{
                    tdiv.attr("style","width:30px;height:30px;background-color:white;border-radius:25px;display:inline-block;border:1px solid gray;");
                }

            }
    		//tdiv.attr("style","width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;");
            div.append(tdiv);
    		var span=$("<span></span>");
    		span.attr("style","height:30px; line-height:30px; display:block; color:#666; text-align:center;");
    		span.attr("itemflag",item.flag);
            span.text(item.flag);
    		tdiv.append(span);
    		if(data.question.itemtype=='1'){
    			var imgdiv=$("<img></img>");
    			imgdiv.addClass("itemimg");
    			imgdiv.attr("style","float:left;margin-left:40px;");
    			imgdiv.attr("height","90px");
    			imgdiv.attr("width","120px");
    			imgdiv.attr("src",resource+"/uploads/"+item.content);
			}else{
				var imgdiv=$("<div></div>");
    			imgdiv.addClass("itemimg");
    			imgdiv.attr("style","float:left;margin-left:40px;color:black;white-space: initial;text-overflow: inherit;/* overflow: hidden; */text-align: left;");
    			imgdiv.html(item.content);
			}
			a.append(imgdiv);
			li.appendTo(ul);
    	});
        parentdiv.append(ul);
        if(issubmit=='1'){
            //个人作答情况
            if(type!=1&&studentid!=""){
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="mui-content-padded";
                attrarr.push(temp);
                temp={};
                temp.id="style";
                temp.val="font-size:100%;color:black;";
                attrarr.push(temp);
                var userdatadiv=initDom("<div></div>",attrarr);
                userdatadiv.appendTo(parentdiv);
                attrarr=[];
                temp={};
                temp.id="style";
                temp.val="color: #8f8f94;";
                attrarr.push(temp);
                var userdatafont=initDom("<font></font>",attrarr);
                var userquestionanswer="正确答案是"+answerflag;
                if(data.answer[0].answer==''||data.answer[0].answer==undefined){
                    userquestionanswer=userquestionanswer+"，您未作答";
                }else{
                    userquestionanswer=userquestionanswer+"，您的答案"+data.answer[0].answer;
                }
                userdatafont.text(userquestionanswer);
                userdatafont.appendTo(userdatadiv);
            }
            //答案情况
            if(type==1){
                attrarr=[];
                temp={};0
                temp.id="class";
                temp.val="mui-content-padded";
                attrarr.push(temp);
                temp={};
                temp.id="style";
                temp.val="font-size:100%;color: #8f8f94;margin-top: 30px;";
                attrarr.push(temp);
                var userclassdiv=initDom("<div></div>",attrarr);
                userclassdiv.appendTo(parentdiv);
                attrarr=[];
                var userclassh5=initDom("<h5></h5>",attrarr);
                userclassh5.text("作答情况");
                userclassdiv.append(userclassh5);
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="mui-table-view mui-table-view-chevron";
                attrarr.push(temp);
                temp={};
                temp.id="style";
                temp.val="margin-top: 10px;";
                attrarr.push(temp);
                var userclassul=initDom("<ul></ul>",attrarr);
                 userclassul.appendTo(userclassdiv);
                 var itemsarray=["A","B","C"];
                 for(var i=0;i<3;i++){
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="mui-table-view-cell mui-collapse";
                    attrarr.push(temp);
                    temp={};
                    temp.id="itemflag";
                    temp.val=itemsarray[i];
                    attrarr.push(temp);
                    temp={};
                    temp.id="quesid";
                    temp.val=data.question.id;
                    attrarr.push(temp);
                    temp={};
                    temp.id="homeworkid";
                    temp.val=data.question.homeworkid;
                    attrarr.push(temp);
                    var userclassli=initDom("<li></li>",attrarr);
                    userclassli.bind("tap",function(){
                        if(isOverdue!='false'){
                            return false;
                        }
                        var obj=$(this);
                        $(this).find("ul").empty();
                        attrarr=[];
                        temp={};
                        temp.id="class";
                        temp.val="mui-table-view mui-table-view-chevron";
                        attrarr.push(temp);
                        var itemuserul=initDom("<ul></ul>",attrarr);
                        $(this).append(itemuserul);
                        attrarr=[];
                        temp={};
                        temp.id="class";
                        temp.val="mui-table-view-cell";
                        attrarr.push(temp);
                        var itemuserli=initDom("<li></li>",attrarr);
                        itemuserul.append(itemuserli);
                        var answer=$(this).attr("itemflag");
                        var quesid=$(this).attr("quesid");
                        var homeworkid=$(this).attr("homeworkid");
                        //主要进行ajax的数据获取
                        $.getJSON("getAnswerUserList",{classid:classid,answer:answer,quesid:quesid,homeworkid:homeworkid,typeid:"0",ran:Math.random()},function(data){
                            if(data.length>0){
                                $.each(data,function(k,v){
                                    attrarr=[];
                                    temp={};
                                    temp.id="type";
                                    temp.val="button";
                                    attrarr.push(temp);
                                    temp={};
                                    temp.id="style";
                                    temp.val="margin-top: 10px;margin-left: 10px;float:left;";
                                    attrarr.push(temp);
                                    temp={};
                                    temp.id="studentid";
                                    temp.val=v.studentid;
                                    attrarr.push(temp);
                                    var itemuserbutton=initDom("<button></button>",attrarr);
                                    itemuserbutton.text(v.studentname);
                                    itemuserli.append(itemuserbutton);;
                                });
                            }else{
                                $(obj).find("ul").remove();
                                mui.toast("作答人数为0人");
                            }
                            
                        });
                    });
                    userclassli.appendTo(userclassul);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="mui-navigate-right";
                    attrarr.push(temp);
                    temp={};
                    temp.id="href";
                    temp.val="javascript:void(0);";
                    attrarr.push(temp);
                    temp={};
                    temp.id="style";
                    if(itemsarray[i]==answerflag){
                        temp.val="color: #2bc8a0;";
                    }else{
                        temp.val="color: #FE5A59;";
                    }
                    attrarr.push(temp);
                    var userclassa=initDom("<a></a>",attrarr);
                    userclassa.html(itemsarray[i]+'.作答人数'+parseInt((parseInt(data.summary[itemsarray[i]])*parseInt(data.answer[0].summary.answernum))/100)+'人，占比'+data.summary[itemsarray[i]]+'%');
                    userclassli.append(userclassa);
                }
            }
            


            //展示个人的数据
            attrarr=[];
            temp={};
            temp.id="class";
            temp.val="mui-content-padded";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="font-size:100%;color: #8f8f94;margin-top: 30px;";
            attrarr.push(temp);
            var userdiv=initDom("<div></div>",attrarr);
            userdiv.appendTo(parentdiv);
            attrarr=[];
            temp={};
            temp.id="style";
            temp.val="margin-bottom:10px;";
            attrarr.push(temp);
            var userh5=initDom("<h5></h5>",attrarr);
            userh5.text("班级数据");
            userdiv.append(userh5);
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-table-view mui-grid-view";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="background-color: white;";
            attrarr.push(temp);
            var userul=initDom("<ul></ul>",attrarr);
            userdiv.append(userul);
            
            //展示作答人数的数据
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-table-view-cell";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="width: 32%;";
            attrarr.push(temp);
            var userli=initDom("<li></li>",attrarr);
            userul.append(userli);
            attrarr=[]
            temp={};
            temp.id="href";
            temp.val="javascript:void(0);";
            attrarr.push(temp);
            var usera=initDom("<a></a>",attrarr);
            userli.append(usera);
            attrarr=[]
            temp={};
            temp.id="style";
            temp.val="font-size:100%;";
            attrarr.push(temp);
            var userspan=initDom("<a></a>",attrarr);
            userspan.text("作答人数");
            usera.append(userspan);
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-media-body";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="font-size:100%;color: #8f8f94;";
            attrarr.push(temp);
            var usernumdiv=initDom("<div></div>",attrarr);
            var datanum=0;
            if(data.answer[0].summary.answernum!=undefined){
                datanum=data.answer[0].summary.answernum;
            }
            usernumdiv.text(datanum);
            usera.append(usernumdiv);

            //展示及格人数的数据
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-table-view-cell";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="width: 32%;";
            attrarr.push(temp);
            var userli=initDom("<li></li>",attrarr);
            userul.append(userli);
            attrarr=[]
            temp={};
            temp.id="href";
            temp.val="javascript:void(0);";
            attrarr.push(temp);
            var usera=initDom("<a></a>",attrarr);
            userli.append(usera);
            attrarr=[]
            temp={};
            temp.id="style";
            temp.val="font-size:100%;";
            attrarr.push(temp);
            var userspan=initDom("<a></a>",attrarr);
            userspan.text("正确率");
            usera.append(userspan);
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-media-body";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="font-size:100%;color: #8f8f94;";
            attrarr.push(temp);
            var usernumdiv=initDom("<div></div>",attrarr);
            var datanum=0;
            if(data.answer[0].summary.accrate!=undefined){
                datanum=data.answer[0].summary.accrate;
            }
            usernumdiv.text(datanum);
            usera.append(usernumdiv);

            //展示最高分的数据
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-table-view-cell";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="width: 32%;";
            attrarr.push(temp);
            var userli=initDom("<li></li>",attrarr);
            userul.append(userli);
            attrarr=[]
            temp={};
            temp.id="href";
            temp.val="javascript:void(0);";
            attrarr.push(temp);
            var usera=initDom("<a></a>",attrarr);
            userli.append(usera);
            attrarr=[]
            temp={};
            temp.id="style";
            temp.val="font-size:100%;";
            attrarr.push(temp);
            var userspan=initDom("<a></a>",attrarr);
            userspan.text("易错项");
            usera.append(userspan);
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-media-body";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="font-size:100%;color: #8f8f94;";
            attrarr.push(temp);
            var usernumdiv=initDom("<div></div>",attrarr);
            var datanum="";
            if(data.answer[0].summary.erroranswer!=undefined){
                datanum=data.answer[0].summary.erroranswer;
            }
            if(datanum==""){
               usernumdiv.html("<font size='0.7em'>无</font>");
            }else{
               usernumdiv.text(datanum); 
            }
            usera.append(usernumdiv);
            attrarr=[];
            temp={};0
            temp.id="class";
            temp.val="mui-content-padded";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="font-size:100%;color: #8f8f94;";
            attrarr.push(temp);
            temp={};
            temp.id="id";
            temp.val="listen";
            attrarr.push(temp);
            var ttsdiv=initDom("<div></div>",attrarr);
            ttsdiv.appendTo(parentdiv);
            attrarr=[];
            var ttsclassh5=initDom("<h5></h5>",attrarr);
            ttsclassh5.text("听力材料");
            ttsdiv.append(ttsclassh5);
            attrarr=[];
            temp={};
            temp.id="class";
            temp.val="listen";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="font-size:100%;color: #8f8f94;";
            attrarr.push(temp);
            var ttssuserul=initDom("<ul></ul>",attrarr);
            ttsdiv.append(ttssuserul);
            mui.each(data.questts,function(k,v){
                attrarr=[];
                var ttsclassli=initDom("<li></li>",attrarr);
                ttssuserul.append(ttsclassli);
                attrarr=[];
                temp={};
                temp.id="style";
                temp.val="font-size:100%;color: #8f8f94;";
                attrarr.push(temp);
                var ttsh5=initDom("<h5></h5>",attrarr);
                if(v.flag_content==''){
                    ttsh5.html(v.tts_content);
                }else{
                    ttsh5.html('<strong>'+v.flag_content+'</strong>:'+v.tts_content);
                }
                ttsclassli.append(ttsh5);
            });
        }
		return parentdiv;
    }

    //填空题
    var blankExamsQuestion=function(data){
        //待更新
    }

    //判断题
    var trueOrFalseExamsQuestion=function(data){
        //设置头部信息
        var attrarr=[];
        var temp={};
        temp.id="class";
        temp.val="parent";
        attrarr.push(temp);
        temp.id="id";
        temp.class="parent";
        attrarr.push(temp);
        // temp.id="quescount";
        // temp.val=data.quescount;
        // attrarr.push(temp);
        temp.id="stemid";
        temp.val=data.stemid;
        attrarr.push(temp);
        temp.id="style";
        temp.val="height:100%;margin-left: 0px;";
        attrarr.push(temp);
        var parentdiv =initDom("<div></div>",attrarr);
        var stemdiv=$("<div></div>");
        stemdiv.addClass("tigan");
        stemdiv.attr("style","margin-top:10px;margin-left:10px;font-family: times;text-align:left;font-size:0.8em;color: #8f8f94;");
        stemdiv.html(data.question.stemcontent);
        parentdiv.append(stemdiv);
        //判断是否有音频1表示需要音频 0表示不需要音频
        var voicediv="";
        if(data.question.isvoice=='1'){
            voicediv=setVoice(data.question,examsPlayEvent);
            parentdiv.append(voicediv);
        }
        //选择题展示
        
        var div=$("<div></div>");
        div.addClass("title tigan");
        div.attr("style","margin-top:30px;margin-left:30px;font-size: 1.0em;font-family: times;color:black;text-align:center;");
        div.html(data.question.tcontent);
        parentdiv.append(div);
        var ul=$("<ul></ul>");
        ul.addClass("mui-table-view xuanze");
        ul.attr("style","margin-top: 30px;background-color: while;text-align:center;");
        mui.each(data.question.questions_items,function(index,item){
            var li=$("<li></li>");
            li.addClass("edi-cell mui-media");
            li.attr("quesid",data.question.id);
            li.attr("itemflag",item.value);
            li.attr("answerid",data.answer[0].quesansid);
            li.attr("homeworkid",data.question.homeworkid);
            li.attr("examsid",data.question.examsid);
            li.attr("quizid",data.question.quizid);
            li.attr("typeid",data.question.typeid);
            li.bind("tap",choiceExamsEvent);
            var a=$("<a></a>");
            a.attr("quesid",data.question.id);
            a.attr("itemflag",item.value);
            a.attr("answerid",data.answer[0].quesansid);
            a.attr("homeworkid",data.question.homeworkid);
            a.attr("examsid",data.question.examsid);
            a.attr("quizid",data.question.quizid);
            a.attr("typeid",data.question.typeid);
            li.html(a);
            //a.appendTo(li);
            var div=$("<div></div>");
            div.addClass("mui-media-body");
            div.attr("style","float:left;position:absolute;");
            a.append(div);
            var tdiv=$("<div></div>");
            tdiv.addClass("items");
            tdiv.attr("style","width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;");
            div.append(tdiv);
            if(issubmit=='0'){
                //未提交作业的展示
                if(item.value==data.answer[0].answer){
                    tdiv.attr("style","width:30px;height:30px;background-color:#2bc8a0; border-radius:25px;display: inline-block;");
                }else{
                    tdiv.attr("style","width:30px;height:30px;background-color:white;border-radius:25px;display:inline-block;border:1px solid gray;");
                }
            }else if(issubmit=='1'){
                //已提交作业的展示
                if(item.value==data.answer[0].answer&&item.value==data.answer[0].quesanswer){
                    answerflag=item.flag;
                    tdiv.attr("style","width:30px;height:30px;background-color:#2bc8a0; border-radius:25px;display: inline-block;");
                }else if(item.value!=data.answer[0].answer&&item.value==data.answer[0].quesanswer){
                    answerflag=item.flag;
                    tdiv.attr("style","width:30px;height:30px;background-color:#2bc8a0; border-radius:25px;display: inline-block;");
                }else if(item.value==data.answer[0].answer&&item.value!=data.answer[0].quesanswer){
                    tdiv.attr("style","width:30px;height:30px;background-color:#FE5A59;border-radius:25px;display:inline-block;border:1px solid gray;");
                }else{
                    tdiv.attr("style","width:30px;height:30px;background-color:white;border-radius:25px;display:inline-block;border:1px solid gray;");
                }

            }
            var span=$("<span></span>");
            span.attr("style","height:30px; line-height:30px; display:block; color:#666; text-align:center;");
            span.attr("itemflag",item.flag);
            span.text(item.flag);
            tdiv.append(span);
            if(data.itemtype=='1'){
                var imgdiv=$("<img></img>");
                imgdiv.addClass("itemimg");
                imgdiv.attr("style","float:left;margin-left:40px;");
                imgdiv.attr("height","90px");
                imgdiv.attr("width","120px");
                imgdiv.attr("src",item.content);
            }else{
                var imgdiv=$("<div></div>");
                imgdiv.addClass("itemimg");
                imgdiv.attr("style","float:left;margin-left:40px;color:black;white-space: initial;text-overflow: inherit;/* overflow: hidden; */text-align: left;");
                imgdiv.html(item.content);
            }
            a.append(imgdiv);
            li.appendTo(ul);
        });
        parentdiv.append(ul);

        if(issubmit=='1'){
             //个人作答情况
            if(type!=1&&studentid!=""){
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="mui-content-padded";
                attrarr.push(temp);
                temp={};
                temp.id="style";
                temp.val="font-size:100%;color:black;";
                attrarr.push(temp);
                var userdatadiv=initDom("<div></div>",attrarr);
                userdatadiv.appendTo(parentdiv);
                attrarr=[];
                temp={};
                temp.id="style";
                temp.val="color: #8f8f94;";
                attrarr.push(temp);
                var userdatafont=initDom("<font></font>",attrarr);
                var userquestionanswer="正确答案是";
                if(data.answer[0].quesanswer=='0'){
                    userquestionanswer=userquestionanswer+"False";
                }else if(data.answer[0].quesanswer=='1'){
                    userquestionanswer=userquestionanswer+"True";
                }
                if(data.answer[0].answer==''||data.answer[0].answer==undefined){
                    userquestionanswer=userquestionanswer+"，您未作答";
                }else{
                    userquestionanswer=userquestionanswer+"，您的答案";
                    if(data.answer[0].answer=="0"){
                        userquestionanswer=userquestionanswer+"False";
                    }else{
                        userquestionanswer=userquestionanswer+"True";
                    }
                }
                userdatafont.text(userquestionanswer);
                userdatafont.appendTo(userdatadiv);
            }

            //答案情况
            if(type==1){
                attrarr=[];
                temp={};0
                temp.id="class";
                temp.val="mui-content-padded";
                attrarr.push(temp);
                temp={};
                temp.id="style";
                temp.val="font-size:100%;color: #8f8f94;margin-top: 30px;";
                attrarr.push(temp);
                var userclassdiv=initDom("<div></div>",attrarr);
                userclassdiv.appendTo(parentdiv);
                attrarr=[];
                var userclassh5=initDom("<h5></h5>",attrarr);
                userclassh5.text("作答情况");
                userclassdiv.append(userclassh5);
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="mui-table-view mui-table-view-chevron";
                attrarr.push(temp);
                temp={};
                temp.id="style";
                temp.val="margin-top: 10px;";
                attrarr.push(temp);
                var userclassul=initDom("<ul></ul>",attrarr);
                 userclassul.appendTo(userclassdiv);
                 var itemsarray=["A","B"];
                 for(var i=0;i<2;i++){
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="mui-table-view-cell mui-collapse";
                    attrarr.push(temp);
                    temp={};
                    temp.id="itemflag";
                    temp.val=i;
                    attrarr.push(temp);
                    temp={};
                    temp.id="quesid";
                    temp.val=data.question.id;
                    attrarr.push(temp);
                    temp={};
                    temp.id="homeworkid";
                    temp.val=data.question.homeworkid;
                    attrarr.push(temp);
                    var userclassli=initDom("<li></li>",attrarr);
                    userclassli.bind("tap",function(){
                        if(isOverdue!='false'){
                            return false;
                        }
                        var obj=$(this);
                        $(this).find("ul").empty();
                        attrarr=[];
                        temp={};
                        temp.id="class";
                        temp.val="mui-table-view mui-table-view-chevron";
                        attrarr.push(temp);
                        var itemuserul=initDom("<ul></ul>",attrarr);
                        $(this).append(itemuserul);
                        attrarr=[];
                        temp={};
                        temp.id="class";
                        temp.val="mui-table-view-cell";
                        attrarr.push(temp);
                        var itemuserli=initDom("<li></li>",attrarr);
                        itemuserul.append(itemuserli);
                        var answer=$(this).attr("itemflag");
                        var quesid=$(this).attr("quesid");
                        var homeworkid=$(this).attr("homeworkid");
                        //主要进行ajax的数据获取
                        $.getJSON("getAnswerUserList",{classid:classid,answer:answer,quesid:quesid,homeworkid:homeworkid,typeid:"0",ran:Math.random()},function(data){
                            if(data.length>0){
                                $.each(data,function(k,v){
                                    attrarr=[];
                                    temp={};
                                    temp.id="type";
                                    temp.val="button";
                                    attrarr.push(temp);
                                    temp={};
                                    temp.id="style";
                                    temp.val="margin-top: 10px;margin-left: 10px;float:left;";
                                    attrarr.push(temp);
                                    temp={};
                                    temp.id="studentid";
                                    temp.val=v.studentid;
                                    attrarr.push(temp);
                                    var itemuserbutton=initDom("<button></button>",attrarr);
                                    itemuserbutton.text(v.studentname);
                                    itemuserli.append(itemuserbutton);;
                                });
                            }else{
                                $(obj).find("ul").remove();
                                mui.toast("作答人数为0人");
                            }
                            
                        });
                    });
                    userclassli.appendTo(userclassul);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="mui-navigate-right";
                    attrarr.push(temp);
                    temp={};
                    temp.id="href";
                    temp.val="javascript:void(0);";
                    attrarr.push(temp);
                    temp={};
                    temp.id="style";
                    if(itemsarray[i]==answerflag){
                        temp.val="color: #2bc8a0;";
                    }else{
                        temp.val="color: #FE5A59;";
                    }
                    attrarr.push(temp);
                    var userclassa=initDom("<a></a>",attrarr);
                    userclassa.html(itemsarray[i]+'.作答人数'+parseInt((parseInt(data.summary[itemsarray[i]])*parseInt(data.answer[0].summary.answernum))/100)+'人，占比'+data.summary[itemsarray[i]]+'%');
                    userclassli.append(userclassa);
                }
            }
        //展示班级的数据
        attrarr=[];
        temp={};
        temp.id="class";
        temp.val="mui-content-padded";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="font-size:100%;color: #8f8f94;";
        attrarr.push(temp);
        var userdiv=initDom("<div></div>",attrarr);
        userdiv.appendTo(parentdiv);
        attrarr=[];
        temp={};
        temp.id="style";
        temp.val="margin-bottom:10px;";
        attrarr.push(temp);
        var userh5=initDom("<h5></h5>",attrarr);
        userh5.text("班级数据");
        userdiv.append(userh5);
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="mui-table-view mui-grid-view";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="background-color: white;";
        attrarr.push(temp);
        var userul=initDom("<ul></ul>",attrarr);
        userdiv.append(userul);
        
        //展示作答人数的数据
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="mui-table-view-cell";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="width: 32%;";
        attrarr.push(temp);
        var userli=initDom("<li></li>",attrarr);
        userul.append(userli);
        attrarr=[]
        temp={};
        temp.id="href";
        temp.val="javascript:void(0);";
        attrarr.push(temp);
        var usera=initDom("<a></a>",attrarr);
        userli.append(usera);
        attrarr=[]
        temp={};
        temp.id="style";
        temp.val="font-size:100%;";
        attrarr.push(temp);
        var userspan=initDom("<a></a>",attrarr);
        userspan.text("作答人数");
        usera.append(userspan);
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="mui-media-body";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="font-size:100%;color: #8f8f94;";
        attrarr.push(temp);
        var usernumdiv=initDom("<div></div>",attrarr);
        var datanum=0;
        if(data.answer[0].summary.answernum!=undefined){
            datanum=data.answer[0].summary.answernum;
        }
        usernumdiv.text(datanum);
        usera.append(usernumdiv);

        //展示及格人数的数据
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="mui-table-view-cell";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="width: 32%;";
        attrarr.push(temp);
        var userli=initDom("<li></li>",attrarr);
        userul.append(userli);
        attrarr=[]
        temp={};
        temp.id="href";
        temp.val="javascript:void(0);";
        attrarr.push(temp);
        var usera=initDom("<a></a>",attrarr);
        userli.append(usera);
        attrarr=[]
        temp={};
        temp.id="style";
        temp.val="font-size:100%;";
        attrarr.push(temp);
        var userspan=initDom("<a></a>",attrarr);
        userspan.text("及格率");
        usera.append(userspan);
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="mui-media-body";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="font-size:100%;color: #8f8f94;";
        attrarr.push(temp);
        var usernumdiv=initDom("<div></div>",attrarr);
        var datanum=0;
        if(data.answer[0].summary.accrate!=undefined){
            datanum=data.answer[0].summary.accrate;
        }
        usernumdiv.text(datanum);
        usera.append(usernumdiv);

        //展示最高分的数据
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="mui-table-view-cell";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="width: 32%;";
        attrarr.push(temp);
        var userli=initDom("<li></li>",attrarr);
        userul.append(userli);
        attrarr=[]
        temp={};
        temp.id="href";
        temp.val="javascript:void(0);";
        attrarr.push(temp);
        var usera=initDom("<a></a>",attrarr);
        userli.append(usera);
        attrarr=[]
        temp={};
        temp.id="style";
        temp.val="font-size:100%;";
        attrarr.push(temp);
        var userspan=initDom("<a></a>",attrarr);
        userspan.text("易错项");
        usera.append(userspan);
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="mui-media-body";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="font-size:100%;color: #8f8f94;";
        attrarr.push(temp);
        var usernumdiv=initDom("<div></div>",attrarr);
        var datanum=0;
        if(data.answer[0].summary.erroranswer!=undefined){
            datanum=data.answer[0].summary.erroranswer;
        }
        usernumdiv.text(datanum);
        usera.append(usernumdiv);
        attrarr=[];
        temp={};0
        temp.id="class";
        temp.val="mui-content-padded";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="font-size:100%;color: #8f8f94;";
        attrarr.push(temp);
        temp={};
        temp.id="id";
        temp.val="listen";
        attrarr.push(temp);
        var ttsdiv=initDom("<div></div>",attrarr);
        ttsdiv.appendTo(parentdiv);
        attrarr=[];
        var ttsclassh5=initDom("<h5></h5>",attrarr);
        ttsclassh5.text("听力材料");
        ttsdiv.append(ttsclassh5);
        attrarr=[];
        temp={};
        temp.id="class";
        temp.val="listen";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="font-size:100%;color: #8f8f94;";
        attrarr.push(temp);
        var ttssuserul=initDom("<ul></ul>",attrarr);
        ttsdiv.append(ttssuserul);
        mui.each(data.questts,function(k,v){
            attrarr=[];
            var ttsclassli=initDom("<li></li>",attrarr);
            ttssuserul.append(ttsclassli);
            attrarr=[];
            temp={};
            temp.id="style";
            temp.val="font-size:100%;color: #8f8f94;";
            attrarr.push(temp);
            var ttsh5=initDom("<h5></h5>",attrarr);
            if(v.flag_content==''){
                ttsh5.html(v.tts_content);
            }else{
                ttsh5.html('<strong>'+v.flag_content+'</strong>:'+v.tts_content);
            }
            ttsclassli.append(ttsh5);
        });
    }
        return parentdiv;
    }


    //排序题
    var sequenceExamsQuestion=function(data){
        //待更新
    }


    //单词拼写题
    var wordSpellQuestion=function(data){
        //设置头部信息
        var attrarr=[];
        var temp={};
        temp.id="class";
        temp.val="parent";
        attrarr.push(temp);
        temp.id="id";
        temp.class="parent";
        attrarr.push(temp);
        temp.id="style";
        temp.val="height:100%;margin-left: 0px;";
        attrarr.push(temp);
        var parentdiv =initDom("<div></div>",attrarr);
        //判断是否有音频1表示需要音频 0表示不需要音频
        var voicediv="";
        if(data.question.isvoice=='1'){
            var voicedata={};
            voicedata.loc=word_mp3_url;
            voicedata.mp3=data.question.mp3;
            voicediv=setVoice(voicedata,mp3PlayEvent);
            parentdiv.append(voicediv);
        }
        //questionhtml=questionhtml+'<center style="margin-top:10px;font-size:1.2em;color:black;">'+data.parent.question.explains+'</center>';
        //选择题展示
        attrarr=[];
        temp={};
        temp.id="style";
        temp.val="margin-top:10px;font-size:1.2em;color:black;";
        attrarr.push(temp);
        var center =initDom("<center></center>",attrarr);
         center.text(data.question.explains);
        parentdiv.append(center);
        var div=$("<div></div>");
        div.attr("style","margin-top:20px;");
        parentdiv.append(div);
        var wdsdiv=$("<div></div>");
        wdsdiv.addClass("mui-segmented-control segmentedControl");
        wdsdiv.attr("id","segmentedControl");
        wdsdiv.attr("style","border:none;margin-top:10px;text-align: center;");
        div.append(wdsdiv);
        var ul=$("<ul></ul>");
        ul.attr("style","padding:4px 0;margin:0;");
        wdsdiv.append(ul);
        //给出添加active
        var wordindex=0;
        var useranswerspell=",";
        if(data.answer.answer==null){
            useranswerspell=data.answer.words;
        }else{
            useranswerspell=data.answer.answer.split(",");
        }
        mui.each(data.answer.words,function(index,v){
            //提交和未提交的情况
            if(issubmit=="1"&&type!="1"){
                var li=$("<li></li>");
                //li.addClass("mui-table-view-cell mui-media");
                li.attr("style","display:inline-block;padding:0;");
                li.bind("tap",function(e){
                    e.stopPropagation();
                });
                ul.append(li);
                var a=$("<a></a>");
                a.addClass("mui-control-item");
                a.attr("style","width:20px;height:40px;border-bottom:1px solid;border-left:none;display:inline-block;float:left;font-size:1.3em;");
                //进行数据的展示data.answer.userwords[key]
                if(useranswerspell[index]==undefined){
                    a.text("");
                }else{
                    a.text(useranswerspell[index]);
                    if(useranswerspell[index]!=""){
                        if(index==(data.answer.words.length-1)){
                            wordindex=index;
                        }else{
                            wordindex=(index+1);
                        }
                    }
                }
            }else{
                var li=$("<li></li>");
                //li.addClass("mui-table-view-cell mui-media");
                li.attr("style","display:inline-block;padding:0;");
                li.bind("tap",function(e){
                    e.stopPropagation();
                });
                ul.append(li);
                var a=$("<a></a>");
                a.addClass("mui-control-item");
                a.attr("style","width:20px;height:40px;border-bottom:1px solid;border-left:none;display:inline-block;float:left;font-size:1.3em;");
                
                //进行数据的展示data.answer.userwords[key]
                if(data.answer.userwords[index]==undefined){
                    a.text("");
                }else{
                    a.text(data.answer.userwords[index]);
                    if(data.answer.userwords[index]!=""){
                        if(index==(data.answer.words.length-1)){
                            wordindex=index;
                        }else{
                            wordindex=(index+1);
                        }
                    }
                }
            }
            li.append(a);
        });
        ul.find("a").eq(wordindex).addClass("active");
        //进行选项的展示
        var wordtemp=0;
        var itemdiv=$("<div></div>");
        itemdiv.addClass("mui-content-padded");
        itemdiv.attr("style","background:white;margin-top:30px;margin:30px;");
        parentdiv.append(itemdiv);
        var iteminnerdiv=$("<div></div>");
        iteminnerdiv.addClass("flex-container");
        iteminnerdiv.attr("style","border: none;margin-top: 10px;text-align: center;");
        itemdiv.append(iteminnerdiv);
        var itemul=$("<ul></ul>");
        itemul.attr("style","padding:4px 0;margin:0;");
        iteminnerdiv.append(itemul);
        var items=data.question.option_a.split(",");
        mui.each(items,function(key,value){
            if(issubmit=='1'&&type!='1'){
                if(key<(items.length-1)){
                    wordtemp=wordtemp+1;
                    var ind=data.answer.words.indexOf(value);
                    var itemli=$("<li></li>");
                    itemli.attr("style","float:left;width:20%;");
                    itemli.attr("homeworkid",data.question.homeworkid);
                    itemli.attr("quesid",data.question.id);
                    itemli.attr("wordid",data.question.wordid);
                    itemli.attr("typeid","2");
                    itemul.append(itemli);
                    var itema=$("<a></a>");
                    if(ind>-1&&key<(items.length-1)){
                        data.answer.userwords[ind]="-1";
                        itema.attr("style","font-family: Muiicons;padding-left:0px;margin-left:0px;margin-top:10px;display:inline-block;padding: 0;width: 50px;height: 50px;border-radius: 20px;background-color: #2bc8a0;text-align: center;box-shadow: 0px 3px 8px #aaa, inset 0px 2px 3px #2bc8a0;border: solid 1px transparent;color: white;");
                        itema.attr("inputword",ind);
                    }else{               
                        itema.attr("style","font-family: Muiicons;padding-left:0px;margin-left:0px;margin-top:10px;display:inline-block;padding: 0;width: 50px;height: 50px;border-radius: 20px;  background-color: #f7f7f7;text-align: center;box-shadow: 0px 3px 8px #aaa, inset 0px 2px 3px #fff;border: solid 1px transparent;color: #a7a7a7;");
                    }
                    itemli.append(itema);
                    var itemspan=$("<span></span>");
                    itemspan.addClass("mui-icon");
                    itemspan.attr("style","font-family: inherit;")
                    itema.append(itemspan);
                    var itemfont=$("<font></font>");
                    itemfont.attr("style","font-family: Times New Roman;");
                    itemfont.text(value);
                    itemspan.append(itemfont);
                }
            }else{
                if(key<(items.length-1)){
                    wordtemp=wordtemp+1;
                    var ind=data.answer.userwords.indexOf(value);
                    var itemli=$("<li></li>");
                    itemli.attr("style","float:left;width:20%;");
                    itemli.attr("homeworkid",data.question.homeworkid);
                    itemli.attr("quesid",data.question.id);
                    itemli.attr("wordid",data.question.wordid);
                    itemli.attr("typeid","2");
                    itemli.bind("tap",function(){
                        if(isOverdue!='false'){
                            return false;
                        }
                        if(issubmit=='0'&&studentid!=='0'&&($(this).find("a").attr("inputword")==undefined||$(this).find("a").attr("inputword")=="")){
                            //进行单词的输入
                            var word=$(this).find("font").text();
                            var inputwords=$(this).parents("ul.ques").find(".segmentedControl").find(".active");
                            //计算他是第几个
                            var curindex=$(this).parents("ul.ques").find(".segmentedControl").find("li").index(inputwords.parent("li"));
                            if(curindex<(data.answer.words.length-1)){
                                //将光标移动到下一个
                                inputwords.parent("li").next().find("a").addClass("active");
                                inputwords.removeClass("active");
                            }
                            inputwords.html(word);
                            $(this).find("a").attr("style","").attr("style","font-family: Muiicons;padding-left:0px;margin-left:0px;margin-top:10px;display:inline-block;padding: 0;width: 50px;height: 50px;border-radius: 20px;background-color: #2bc8a0;text-align: center;box-shadow: 0px 3px 8px #aaa, inset 0px 2px 3px #2bc8a0;border: solid 1px transparent;color: white;");
                            $(this).parents(".flex-container").find("a[inputword="+curindex+"]").attr("style","").attr("style","font-family: Muiicons;padding-left:0px;margin-left:0px;margin-top:10px;display:inline-block;padding: 0;width: 50px;height: 50px;border-radius: 20px;  background-color: #f7f7f7;text-align: center;box-shadow: 0px 3px 8px #aaa, inset 0px 2px 3px #fff;border: solid 1px transparent;color: #a7a7a7;").attr("inputword","");
                            $(this).find("a").attr("inputword",curindex);
                            //进行后台数据的交互
                            var questionid=$(this).attr("quesid");
                            var wordid=$(this).attr("wordid");
                            var homeworkid=$(this).attr("homeworkid");
                            var typeid=$(this).attr("typeid");
                            //答案
                            var userans=$(this).parents("ul.ques").find(".segmentedControl").find("a");
                            var ans="";
                            var allword=true;
                            for(var i=0;i<userans.length;i++){
                                var value=userans[i].innerText;
                                if(value==""){
                                    allword=false;
                                }
                                if(i<(userans.length-1)){
                                    ans=ans+value+",";
                                }else{
                                    ans=ans+value;
                                }
                            }
                            var url="../Public/setUserWordtestanswer";
                            mui.ajax(url,
                                {
                                data:{
                                    questionid:questionid,
                                    useranswer:ans,
                                    studentid:studentid,
                                    classid:classid,
                                    homeworkid:homeworkid,
                                    wordid:wordid,
                                    typeid:typeid,
                                    ran:Math.random()
                                },
                                dataType:'json',//服务器返回json格式数据
                                type:'post',//HTTP请求类型
                                timeout:10000,//超时时间设置为10秒；
                                async:true,
                                success:function(data){
                                    if(allword){
                                        //用户回答问题之后直接进行跳转
                                        var next=document.getElementById("next");
                                        mui.trigger(next,'click');
                                    }

                                },
                                error:function(xhr,type,errorThrown){
                                    //异常处理；
                                    return errorThrown;
                                }
                            });
                        }
                    });
                    itemul.append(itemli);
                    var itema=$("<a></a>");
                    if(ind>-1&&key<(items.length-1)){
                        data.answer.userwords[ind]="-1";
                        itema.attr("style","font-family: Muiicons;padding-left:0px;margin-left:0px;margin-top:10px;display:inline-block;padding: 0;width: 50px;height: 50px;border-radius: 20px;background-color: #2bc8a0;text-align: center;box-shadow: 0px 3px 8px #aaa, inset 0px 2px 3px #2bc8a0;border: solid 1px transparent;color: white;");
                        itema.attr("inputword",ind);
                    }else{               
                        itema.attr("style","font-family: Muiicons;padding-left:0px;margin-left:0px;margin-top:10px;display:inline-block;padding: 0;width: 50px;height: 50px;border-radius: 20px;  background-color: #f7f7f7;text-align: center;box-shadow: 0px 3px 8px #aaa, inset 0px 2px 3px #fff;border: solid 1px transparent;color: #a7a7a7;");
                    }
                    //itema.attr("style","font-family: Muiicons;padding-left:0px;margin-left:0px;margin-top:10px;display:inline-block;padding: 0;width: 50px;height: 50px;border-radius: 20px;background-color: #2bc8a0;text-align: center;box-shadow: 0px 3px 8px #aaa, inset 0px 2px 3px #2bc8a0;border: solid 1px transparent;color: white;background-color: rgb(239, 239, 244);");
                    
                    itemli.append(itema);
                    var itemspan=$("<span></span>");
                    itemspan.addClass("mui-icon");
                    itemspan.attr("style","font-family: inherit;")
                    itema.append(itemspan);
                    var itemfont=$("<font></font>");
                    itemfont.attr("style","font-family: Times New Roman;");
                    itemfont.text(value);
                    itemspan.append(itemfont);
                }
            }
            
        });
        var itemli=$("<li></li>");
        itemli.attr("style","float:left;width:20%;");
        itemli.attr("homeworkid",data.question.homeworkid);
        itemli.attr("quesid",data.question.id);
        itemli.attr("wordid",data.question.wordid);
        itemli.attr("typeid","2");
        itemul.append(itemli);
        itemul.append('<div class="clearfix"></div>');
        var itema=$("<a></a>");
        itema.attr("style","font-family: Muiicons;padding-left:0px;margin-left:0px;margin-top:10px;display:inline-block;padding: 0;width: 50px;height: 50px;border-radius: 20px;  background-color: #f7f7f7;text-align: center;box-shadow: 0px 3px 8px #aaa, inset 0px 2px 3px #fff;border: solid 1px transparent;color: #a7a7a7;");
        //itema.addClass("worditems items");
        itemli.append(itema);
        var itemspan=$("<span></span>");
        itemspan.addClass("mui-icon");
        itemspan.attr("style","font-family: inherit;")
        itema.append(itemspan);
        var itemi=$("<i></i>");
        itemi.attr("class","fa fa-arrow-left");
        itemspan.append(itemi);
        itemli.bind("tap",function(){
            if(isOverdue!='false'){
                return false;
            }
            var wordli=$(this).parents("ul.ques").find(".segmentedControl").find("li");
           //单词删除
           var cur=$(this).parents("ul.ques").find(".segmentedControl").find("a.active");
           
           var wordindex=wordli.index(cur.parent("li"));//判断是删除的第几个单词
            if((cur.parent().prev().length>0)&&(cur.text()=="")){
               //alert(cur.text());

                  //不是第一位，且当前值为空，删除前一个，焦点前移就行                
                cur.removeClass("active");
                cur.parent().prev().find("a").addClass("active");
                cur.parent().prev().find("a").html("");
                wordindex=wordindex-1;
                $(this).parents(".flex-container").find("a[inputword="+wordindex+"]").attr("style","").attr("style","font-family: Muiicons;padding-left:0px;margin-left:0px;margin-top:10px;display:inline-block;padding: 0;width: 50px;height: 50px;border-radius: 20px;  background-color: #f7f7f7;text-align: center;box-shadow: 0px 3px 8px #aaa, inset 0px 2px 3px #fff;border: solid 1px transparent;color: #a7a7a7;").attr("inputword","");

           }else{
                cur.html("");
                $(this).parents(".flex-container").find("a[inputword="+wordindex+"]").attr("style","").attr("style","font-family: Muiicons;padding-left:0px;margin-left:0px;margin-top:10px;display:inline-block;padding: 0;width: 50px;height: 50px;border-radius: 20px;  background-color: #f7f7f7;text-align: center;box-shadow: 0px 3px 8px #aaa, inset 0px 2px 3px #fff;border: solid 1px transparent;color: #a7a7a7;").attr("inputword","");
                
           }
           //进行后台数据的交互
            var questionid=$(this).attr("quesid");
            var wordid=$(this).attr("wordid");
            var homeworkid=$(this).attr("homeworkid");
            var typeid=$(this).attr("typeid");
            //答案
            var userans=$(this).parents("ul.ques").find(".segmentedControl").find("a");
            var ans="";
            var allword=true;
            for(var i=0;i<userans.length;i++){
                var value=userans[i].innerText;
                if(value==""){
                    allword=false;
                }
                if(i<(userans.length-1)){
                    ans=ans+value+",";
                }else{
                    ans=ans+value;
                }
            }
            var url="../Public/setUserWordtestanswer";
            mui.ajax(url,
                {
                data:{
                    questionid:questionid,
                    useranswer:ans,
                    studentid:studentid,
                    classid:classid,
                    homeworkid:homeworkid,
                    wordid:wordid,
                    typeid:typeid,
                    ran:Math.random()
                },
                dataType:'json',//服务器返回json格式数据
                type:'post',//HTTP请求类型
                timeout:10000,//超时时间设置为10秒；
                async:true,
                success:function(data){
                    if(allword){
                        //用户回答问题之后直接进行跳转
                        var next=document.getElementById("next");
                        mui.trigger(next,'click');
                    }

                },
                error:function(xhr,type,errorThrown){
                    //异常处理；
                    return errorThrown;
                }
            });
        });
        if(issubmit=='1'){
            if(type=='0'&&studentid!=""){
                //展示个人的数据
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="mui-content-padded";
                attrarr.push(temp);
                temp={};
                temp.id="style";
                temp.val="font-size:100%;color:black;";
                attrarr.push(temp);
                var userdatadiv=initDom("<div></div>",attrarr);
                userdatadiv.appendTo(parentdiv);
                attrarr=[];
                temp={};
                temp.id="style";
                temp.val="color: #8f8f94;";
                attrarr.push(temp);
                var userdatafont=initDom("<font></font>",attrarr);
                var datanum="";
                var userquestionanswer="正确答案是"+data.answer.quesanswer;

                if(data.answer.answer!=undefined&&data.answer.answer!=null){
                    var dataanswer=data.answer.answer.split(",");
                    $.each(dataanswer,function(k,v){
                        datanum=datanum+v;
                    })
                    userquestionanswer=userquestionanswer+"，您的答案"+datanum;
                }else{
                    userquestionanswer=userquestionanswer+"，您未作答";
                }
                userdatafont.text(userquestionanswer);
                userdatafont.appendTo(userdatadiv);
            }

            //展示个人的数据
            attrarr=[];
            temp={};
            temp.id="class";
            temp.val="mui-content-padded";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="font-size:100%;color: #8f8f94;";
            attrarr.push(temp);
            var userdiv=initDom("<div></div>",attrarr);
            userdiv.appendTo(parentdiv);
            attrarr=[];
            temp={};
            temp.id="style";
            temp.val="margin-bottom:10px;";
            attrarr.push(temp);
            var userh5=initDom("<h5></h5>",attrarr);
            userh5.text("班级数据");
            userdiv.append(userh5);
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-table-view mui-grid-view";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="background-color: white;";
            attrarr.push(temp);
            var userul=initDom("<ul></ul>",attrarr);
            userdiv.append(userul);
            
            //展示作答人数的数据

            attrarr=[];
            temp={};
            temp.id="class";
            temp.val="mui-table-view-cell";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="width: 32%;";
            attrarr.push(temp);
            var userli=initDom("<li></li>",attrarr);
            userul.append(userli);
            attrarr=[];
            temp={};
            temp.id="href";
            temp.val="javascript:void(0);";
            attrarr.push(temp);
            var usera=initDom("<a></a>",attrarr);
            userli.append(usera);
            attrarr=[]
            temp={};
            temp.id="style";
            temp.val="font-size:100%;";
            attrarr.push(temp);
            var userspan=initDom("<a></a>",attrarr);
            userspan.text("作答人数");
            usera.append(userspan);
            attrarr=[];
            temp={};
            temp.id="class";
            temp.val="mui-media-body";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="font-size:100%;color: #8f8f94;height:25px;";
            attrarr.push(temp);
            var usernumdiv=initDom("<div></div>",attrarr);
            var datanum=0;
            // console.log(data.answer);
            if(data.answer.summary.answernum!=undefined){
                datanum=data.answer.summary.answernum;
            }
            usernumdiv.text(datanum);
            usera.append(usernumdiv);

            //展示及格人数的数据
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-table-view-cell";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="width: 32%;";
            attrarr.push(temp);
            var userli=initDom("<li></li>",attrarr);
            userul.append(userli);
            attrarr=[]
            temp={};
            temp.id="href";
            temp.val="javascript:void(0);";
            attrarr.push(temp);
            var usera=initDom("<a></a>",attrarr);
            userli.append(usera);
            attrarr=[];
            temp={};
            temp.id="style";
            temp.val="font-size:100%;";
            attrarr.push(temp);
            var userspan=initDom("<a></a>",attrarr);
            userspan.text("正确率");
            usera.append(userspan);
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-media-body";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="font-size:100%;color: #8f8f94;height:25px;";
            attrarr.push(temp);
            var usernumdiv=initDom("<div></div>",attrarr);
            var datanum=0;
            if(data.answer.summary.accrate!=undefined){
                datanum=data.answer.summary.accrate;
            }
            usernumdiv.text(datanum);
            usera.append(usernumdiv);

            //展示最高分的数据
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-table-view-cell";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="width: 32%;";
            attrarr.push(temp);
            var userli=initDom("<li></li>",attrarr);
            userul.append(userli);
            attrarr=[]
            temp={};
            temp.id="href";
            temp.val="javascript:void(0);";
            attrarr.push(temp);
            var usera=initDom("<a></a>",attrarr);
            userli.append(usera);
            attrarr=[]
            temp={};
            temp.id="style";
            temp.val="font-size:100%;";
            attrarr.push(temp);
            var userspan=initDom("<a></a>",attrarr);
            userspan.text("易错项");
            usera.append(userspan);
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-media-body";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="font-size:100%;color: #8f8f94;height:25px;";
            attrarr.push(temp);
            var usernumdiv=initDom("<div></div>",attrarr);
            var datanum="";

            if(data.answer.summary.erroranswer!=undefined){
                var dataanswer=data.answer.summary.erroranswer.split(",");
                $.each(dataanswer,function(k,v){
                    datanum=datanum+v;
                })
            }
            usernumdiv.html(datanum);
            usera.append(usernumdiv);
        }
        return parentdiv;
    }

    //听音选词题
    var wordChooseQuestion=function(data){
        //设置头部信息
        var attrarr=[];
        var temp={};
        temp.id="class";
        temp.val="parent";
        attrarr.push(temp);
        temp.id="id";
        temp.class="parent";
        attrarr.push(temp);
        temp.id="style";
        temp.val="height:100%;margin-left: 0px;";
        attrarr.push(temp);
        var parentdiv =initDom("<div></div>",attrarr);
        //判断是否有音频1表示需要音频 0表示不需要音频
        var voicediv="";
        if(data.question.isvoice=='1'){
            var voicedata={};
            voicedata.loc=word_mp3_url;
            voicedata.mp3=data.question.mp3;
            voicediv=setVoice(voicedata,mp3PlayEvent);
            parentdiv.append(voicediv);
        }
        //选择题展示
        var div=$("<div></div>");
        div.addClass("title tigan");
        div.attr("style","margin-top:30px;margin-left:30px;font-size: 1.0em;font-family: times;color:black;text-align:center;");
        //div.html(data.tcontent);
        parentdiv.append(div);
        var ul=$("<ul></ul>");
        ul.addClass("mui-table-view xuanze");
        ul.attr("style","margin-top: 30px;background-color: while;text-align:center;");
        mui.each(data.items,function(index,item){
            var li=$("<li></li>");
            li.addClass("edi-cell mui-media");
            li.attr("quesid",data.question.id);
            li.attr("itemflag",item.flag);
            li.attr("answerid",data.question.answerid);
            li.attr("homeworkid",data.question.homeworkid);
            li.attr("examsid",data.question.examsid);
            li.attr("quizid",data.question.quizid);
            li.attr("wordid",data.question.wordid);
            li.attr("typeid","0");
            if(issubmit=='0'&&isOverdue=='false'){
                li.bind("tap",wordChooseEvent);
            }
            var a=$("<a></a>");
            a.attr("itemflag",item.flag);
            a.attr("answerid",data.question.answerid);
            a.attr("homeworkid",data.question.homeworkid);
            a.attr("examsid",data.question.examsid);
            a.attr("quizid",data.question.quizid);
            a.attr("wordid",data.question.wordid);
            a.attr("typeid","0");
            li.html(a);
            //a.appendTo(li);
            var div=$("<div></div>");
            div.addClass("mui-media-body");
            div.attr("style","float:left;position:absolute;");
            a.append(div);
            var tdiv=$("<div></div>");
            tdiv.addClass("items");
            //tdiv.attr("style","width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;");
            if(issubmit=='0'){
                //未提交作业的展示
                if(item.flag==data.answer[0].answer){
                    tdiv.attr("style","width:30px;height:30px;background-color:#2bc8a0; border-radius:25px;display: inline-block;");
                }else{
                    tdiv.attr("style","width:30px;height:30px;background-color:white;border-radius:25px;display:inline-block;border:1px solid gray;");
                }
            }else if(issubmit=='1'){
                //已提交作业的展示
                if(item.flag==data.answer[0].answer&&item.flag==data.answer[0].quesanswer){
                    tdiv.attr("style","width:30px;height:30px;background-color:#2bc8a0; border-radius:25px;display: inline-block;");
                }else if(item.flag!=data.answer[0].answer&&item.flag==data.answer[0].quesanswer){
                    tdiv.attr("style","width:30px;height:30px;background-color:#2bc8a0; border-radius:25px;display: inline-block;");
                }else if(item.flag==data.answer[0].answer&&item.flag!=data.answer[0].quesanswer){
                    tdiv.attr("style","width:30px;height:30px;background-color:#FE5A59;border-radius:25px;display:inline-block;border:1px solid gray;");
                }else{
                    tdiv.attr("style","width:30px;height:30px;background-color:white;border-radius:25px;display:inline-block;border:1px solid gray;");
                }

            }
            div.append(tdiv);
            var span=$("<span></span>");
            span.attr("style","height:30px; line-height:30px; display:block; color:#666; text-align:center;");
            span.attr("itemflag",item.flag);
            span.text(item.flag);
            // console.log(span.html());
            tdiv.append(span);
            if(data.itemtype=='1'){
                var imgdiv=$("<img></img>");
                imgdiv.addClass("itemimg");
                imgdiv.attr("style","float:left;margin-left:40px;");
                imgdiv.attr("height","90px");
                imgdiv.attr("width","120px");
                imgdiv.attr("src",item.content);
            }else{
                var imgdiv=$("<div></div>");
                imgdiv.addClass("itemimg");
                imgdiv.attr("style","float:left;margin-left:40px;color:black;white-space: initial;text-overflow: inherit;/* overflow: hidden; */text-align: left;");
                imgdiv.html(item.content);
            }
            a.append(imgdiv);
            li.appendTo(ul);
        });
        parentdiv.append(ul);
        if(issubmit=='1'&&type!='1'){
            if(type=='0'&&studentid!=""){
                //个人作答情况
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="mui-content-padded";
                attrarr.push(temp);
                temp={};
                temp.id="style";
                temp.val="font-size:100%;color:black;";
                attrarr.push(temp);
                var userdatadiv=initDom("<div></div>",attrarr);
                userdatadiv.appendTo(parentdiv);
                attrarr=[];
                temp={};
                temp.id="style";
                temp.val="color: #8f8f94;";
                attrarr.push(temp);
                var userdatafont=initDom("<font></font>",attrarr);
                var userquestionanswer="正确答案是"+data.answer[0].quesanswer;
                if(data.answer[0].answer==''||data.answer[0].answer==undefined){
                    userquestionanswer=userquestionanswer+"，您未作答";
                }else{
                    userquestionanswer=userquestionanswer+"，您的答案"+data.answer[0].answer;
                }
                userdatafont.text(userquestionanswer);
                userdatafont.appendTo(userdatadiv);
            }
            //答案情况
            if(type=="2"){
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="mui-content-padded";
                attrarr.push(temp);
                temp={};
                temp.id="style";
                temp.val="font-size:100%;color: #8f8f94;";
                attrarr.push(temp);
                var userclassdiv=initDom("<div></div>",attrarr);
                userclassdiv.appendTo(parentdiv);
                attrarr=[];
                var userclassh5=initDom("<h5></h5>",attrarr);
                userclassh5.text("作答情况");
                userclassdiv.append(userclassh5);
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="mui-table-view mui-table-view-chevron";
                attrarr.push(temp);
                temp={};
                temp.id="style";
                temp.val="margin-top: 10px;";
                attrarr.push(temp);
                var userclassul=initDom("<ul></ul>",attrarr);
                userclassul.appendTo(userclassdiv);
                var itemsarray=["A","B","C"];
                for(var i=0;i<3;i++){
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="mui-table-view-cell mui-collapse";
                    attrarr.push(temp);
                    temp={};
                    temp.id="itemflag";
                    temp.val=itemsarray[i];
                    attrarr.push(temp);
                    temp={};
                    temp.id="quesid";
                    temp.val=data.question.id;
                    attrarr.push(temp);
                    temp={};
                    temp.id="homeworkid";
                    temp.val=data.question.homeworkid;
                    attrarr.push(temp);
                    var userclassli=initDom("<li></li>",attrarr);
                    userclassli.bind("tap",function(){
                        if(isOverdue!='false'){
                            return false;
                        }
                        var obj=$(this);
                        $(this).find("ul").empty();
                        attrarr=[];
                        temp={};
                        temp.id="class";
                        temp.val="mui-table-view mui-table-view-chevron";
                        attrarr.push(temp);
                        var itemuserul=initDom("<ul></ul>",attrarr);
                        $(this).append(itemuserul);
                        attrarr=[];
                        temp={};
                        temp.id="class";
                        temp.val="mui-table-view-cell";
                        attrarr.push(temp);
                        var itemuserli=initDom("<li></li>",attrarr);
                        itemuserul.append(itemuserli);
                        var answer=$(this).attr("itemflag");
                        var quesid=$(this).attr("quesid");
                        var homeworkid=$(this).attr("homeworkid");
                        //主要进行ajax的数据获取
                        $.getJSON("getAnswerUserList",{classid:classid,answer:answer,quesid:quesid,homeworkid:homeworkid,typeid:"1",ran:Math.random()},function(data){
                            if(data.length>0){
                                $.each(data,function(k,v){
                                    attrarr=[];
                                    temp={};
                                    temp.id="type";
                                    temp.val="button";
                                    attrarr.push(temp);
                                    temp={};
                                    temp.id="style";
                                    temp.val="margin-top: 10px;margin-left: 10px;float:left;";
                                    attrarr.push(temp);
                                    temp={};
                                    temp.id="studentid";
                                    temp.val=v.studentid;
                                    attrarr.push(temp);
                                    var itemuserbutton=initDom("<button></button>",attrarr);
                                    itemuserbutton.text(v.studentname);
                                    itemuserli.append(itemuserbutton);;
                                });
                            }else{
                                $(obj).find("ul").remove();
                                mui.toast("作答人数为0人");
                            }
                            
                        });
                    });
                    userclassli.appendTo(userclassul);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="mui-navigate-right";
                    attrarr.push(temp);
                    temp={};
                    temp.id="href";
                    temp.val="javascript:void(0);";
                    attrarr.push(temp);
                    temp={};
                    temp.id="style";
                    if(itemsarray[i]==data.answer[0].quesanswer){
                        temp.val="color: #2bc8a0;";
                    }else{
                        temp.val="color: #FE5A59;";
                    }
                    attrarr.push(temp);
                    var userclassa=initDom("<a></a>",attrarr);
                    userclassa.html(itemsarray[i]+'.作答人数'+parseInt((parseInt(data.summary[itemsarray[i]])*parseInt(data.answer[0].summary.answernum))/100)+'人，占比'+data.summary[itemsarray[i]]+'%');
                    userclassli.append(userclassa);
                 }
            }
            //展示班级的数据
            attrarr=[];
            temp={};
            temp.id="class";
            temp.val="mui-content-padded";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="font-size:100%;color: #8f8f94;margin-top: 30px;";
            attrarr.push(temp);
            var userdiv=initDom("<div></div>",attrarr);
            userdiv.appendTo(parentdiv);
            attrarr=[];
            temp={};
            temp.id="style";
            temp.val="margin-bottom:10px;";
            attrarr.push(temp);
            var userh5=initDom("<h5></h5>",attrarr);
            userh5.text("班级数据");
            userdiv.append(userh5);
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-table-view mui-grid-view";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="background-color: white;";
            attrarr.push(temp);
            var userul=initDom("<ul></ul>",attrarr);
            userdiv.append(userul);
            
            //展示作答人数的数据
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-table-view-cell";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="width: 32%;";
            attrarr.push(temp);
            var userli=initDom("<li></li>",attrarr);
            userul.append(userli);
            attrarr=[]
            temp={};
            temp.id="href";
            temp.val="javascript:void(0);";
            attrarr.push(temp);
            var usera=initDom("<a></a>",attrarr);
            userli.append(usera);
            attrarr=[]
            temp={};
            temp.id="style";
            temp.val="font-size:100%;";
            attrarr.push(temp);
            var userspan=initDom("<a></a>",attrarr);
            userspan.text("作答人数");
            usera.append(userspan);
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-media-body";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="font-size:100%;color: #8f8f94;";
            attrarr.push(temp);
            var usernumdiv=initDom("<div></div>",attrarr);
            var datanum=0;
            if(data.answer[0].summary.answernum!=undefined){
                datanum=data.answer[0].summary.answernum;
            }
            usernumdiv.text(datanum);
            usera.append(usernumdiv);

            //展示及格人数的数据
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-table-view-cell";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="width: 32%;";
            attrarr.push(temp);
            var userli=initDom("<li></li>",attrarr);
            userul.append(userli);
            attrarr=[]
            temp={};
            temp.id="href";
            temp.val="javascript:void(0);";
            attrarr.push(temp);
            var usera=initDom("<a></a>",attrarr);
            userli.append(usera);
            attrarr=[]
            temp={};
            temp.id="style";
            temp.val="font-size:100%;";
            attrarr.push(temp);
            var userspan=initDom("<a></a>",attrarr);
            userspan.text("正确率");
            usera.append(userspan);
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-media-body";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="font-size:100%;color: #8f8f94;";
            attrarr.push(temp);
            var usernumdiv=initDom("<div></div>",attrarr);
            var datanum=0;
            if(data.answer[0].summary.accrate!=undefined){
                datanum=data.answer[0].summary.accrate;
            }
            usernumdiv.text(datanum);
            usera.append(usernumdiv);

            //展示最高分的数据
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-table-view-cell";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="width: 32%;";
            attrarr.push(temp);
            var userli=initDom("<li></li>",attrarr);
            userul.append(userli);
            attrarr=[]
            temp={};
            temp.id="href";
            temp.val="javascript:void(0);";
            attrarr.push(temp);
            var usera=initDom("<a></a>",attrarr);
            userli.append(usera);
            attrarr=[]
            temp={};
            temp.id="style";
            temp.val="font-size:100%;";
            attrarr.push(temp);
            var userspan=initDom("<a></a>",attrarr);
            userspan.text("易错项");
            usera.append(userspan);
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-media-body";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="font-size:100%;color: #8f8f94;";
            attrarr.push(temp);
            var usernumdiv=initDom("<div></div>",attrarr);
            var datanum=0;
            if(data.answer[0].summary.erroranswer!=undefined){
                datanum=data.answer[0].summary.erroranswer;
            }
            usernumdiv.text(datanum);
            usera.append(usernumdiv);
        }
        return parentdiv;
    }

    //英汉互译题
    var wordTranslateQuestion=function(data){
        //设置头部信息
        var attrarr=[];
        var temp={};
        temp.id="class";
        temp.val="parent";
        attrarr.push(temp);
        temp.id="id";
        temp.class="parent";
        attrarr.push(temp);
        temp.id="style";
        temp.val="height:100%;margin-left: 0px;";
        attrarr.push(temp);
        var parentdiv =initDom("<div></div>",attrarr);
        //判断是否有音频1表示需要音频 0表示不需要音频
        var voicediv="";
        if(data.isvoice=='1'){
            voicediv=setVoice(data);
            parentdiv.append(voicediv);
        }
        //选择题展示
        var div=$("<div></div>");
        div.addClass("title tigan");
        div.attr("style","margin-top:30px;font-size: 1.3em;font-family: times;color:black;text-align:center;");
        if(data.question.typeid=='3'){
            div.html(data.question.explains);
        }else if(data.question.typeid=='1'){
            div.html(data.question.word);
        }
        parentdiv.append(div);
        var ul=$("<ul></ul>");
        ul.addClass("mui-table-view xuanze");
        ul.attr("style","margin-top: 30px;background-color: while;text-align:center;");
        mui.each(data.items,function(index,item){
            var li=$("<li></li>");
            li.addClass("edi-cell mui-media");
            li.attr("quesid",data.question.id);
            li.attr("itemflag",item.flag);
            li.attr("answerid",data.question.answerid);
            li.attr("homeworkid",data.question.homeworkid);
            li.attr("examsid",data.question.examsid);
            li.attr("quizid",data.question.quizid);
            li.attr("wordid",data.question.wordid);
            li.attr("typeid",data.question.typeid);
            if(issubmit=='0'&&isOverdue=='false'){
                li.bind("tap",wordTranslateEvent);
            }
            var a=$("<a></a>");
            a.attr("quesid",data.question.id);
            a.attr("itemflag",item.flag);
            a.attr("answerid",data.question.answerid);
            a.attr("homeworkid",data.question.homeworkid);
            a.attr("examsid",data.question.examsid);
            a.attr("quizid",data.question.quizid);
            a.attr("wordid",data.question.wordid);
            a.attr("typeid",data.question.typeid);
            li.html(a);
            //a.appendTo(li);
            var div=$("<div></div>");
            div.addClass("mui-media-body");
            div.attr("style","float:left;position:absolute;");
            a.append(div);
            var tdiv=$("<div></div>");
            tdiv.addClass("items");
            if(issubmit=='0'){
                //未提交作业的展示
                if(item.flag==data.answer[0].answer){
                    tdiv.attr("style","width:30px;height:30px;background-color:#2bc8a0; border-radius:25px;display: inline-block;");
                }else{
                    tdiv.attr("style","width:30px;height:30px;background-color:white;border-radius:25px;display:inline-block;border:1px solid gray;");
                }
            }else if(issubmit=='1'){
                //已提交作业的展示
                if(item.flag==data.answer[0].answer&&item.flag==data.answer[0].quesanswer){
                    tdiv.attr("style","width:30px;height:30px;background-color:#2bc8a0; border-radius:25px;display: inline-block;");
                }else if(item.flag!=data.answer[0].answer&&item.flag==data.answer[0].quesanswer){
                    tdiv.attr("style","width:30px;height:30px;background-color:#2bc8a0; border-radius:25px;display: inline-block;");
                }else if(item.flag==data.answer[0].answer&&item.flag!=data.answer[0].quesanswer){
                    tdiv.attr("style","width:30px;height:30px;background-color:#FE5A59;border-radius:25px;display:inline-block;border:1px solid gray;");
                }else{
                    tdiv.attr("style","width:30px;height:30px;background-color:white;border-radius:25px;display:inline-block;border:1px solid gray;");
                }
            }
            div.append(tdiv);
            var span=$("<span></span>");
            span.attr("style","height:30px; line-height:30px; display:block; color:#666; text-align:center;");
            span.attr("itemflag",item.flag);
            span.text(item.flag);
            tdiv.append(span);
            var imgdiv=$("<div></div>");
            imgdiv.addClass("itemimg");
            imgdiv.attr("style","float:left;margin-left:40px;color:black;white-space: initial;text-overflow: inherit;/* overflow: hidden; */text-align: left;");
            imgdiv.html(item.content);
            a.append(imgdiv);
            li.appendTo(ul);
        });
        parentdiv.append(ul);
        if(issubmit=='1'&type!='1'){
            if(type=='0'&&studentid!=""){
                //个人作答情况
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="mui-content-padded";
                attrarr.push(temp);
                temp={};
                temp.id="style";
                temp.val="font-size:100%;color:black;";
                attrarr.push(temp);
                var userdatadiv=initDom("<div></div>",attrarr);
                userdatadiv.appendTo(parentdiv);
                attrarr=[];
                temp={};
                temp.id="style";
                temp.val="color: #8f8f94;";
                attrarr.push(temp);
                var userdatafont=initDom("<font></font>",attrarr);
                var userquestionanswer="正确答案是"+data.answer[0].quesanswer;
                if(data.answer[0].answer==''||data.answer[0].answer==undefined){
                    userquestionanswer=userquestionanswer+"，您未作答";
                }else{
                    userquestionanswer=userquestionanswer+"，您的答案"+data.answer[0].answer;
                }
                userdatafont.text(userquestionanswer);
                userdatafont.appendTo(userdatadiv);
            }
        //答案情况
        if(type=="2"){
            attrarr=[];
            temp={};
            temp.id="class";
            temp.val="mui-content-padded";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="font-size:100%;color: #8f8f94;margin-top: 30px;";
            attrarr.push(temp);
            var userclassdiv=initDom("<div></div>",attrarr);
            userclassdiv.appendTo(parentdiv);
            attrarr=[];
            var userclassh5=initDom("<h5></h5>",attrarr);
            userclassh5.text("作答情况");
            userclassdiv.append(userclassh5);
            attrarr=[];
            temp={};
            temp.id="class";
            temp.val="mui-table-view mui-table-view-chevron";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="margin-top: 10px;";
            attrarr.push(temp);
            var userclassul=initDom("<ul></ul>",attrarr);
             userclassul.appendTo(userclassdiv);
             // console.log("start");
             // console.log(data.summary);
             var itemsarray=["A","B","C"];
             for(var i=0;i<3;i++){
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="mui-table-view-cell mui-collapse";
                attrarr.push(temp);
                temp={};
                temp.id="itemflag";
                temp.val=itemsarray[i];
                attrarr.push(temp);
                temp={};
                temp.id="quesid";
                temp.val=data.question.id;
                attrarr.push(temp);
                temp={};
                temp.id="homeworkid";
                temp.val=data.question.homeworkid;
                attrarr.push(temp);
                var userclassli=initDom("<li></li>",attrarr);
                userclassli.bind("tap",function(){
                    if(isOverdue!='false'){
                        return false;
                    }
                    var obj=$(this);
                    $(this).find("ul").remove();
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="mui-table-view mui-table-view-chevron";
                    attrarr.push(temp);
                    var itemuserul=initDom("<ul></ul>",attrarr);
                    $(this).append(itemuserul);
                    attrarr=[];
                    temp={};
                    temp.id="class";
                    temp.val="mui-table-view-cell";
                    attrarr.push(temp);
                    var itemuserli=initDom("<li></li>",attrarr);
                    itemuserul.append(itemuserli);
                    var answer=$(this).attr("itemflag");
                    var quesid=$(this).attr("quesid");
                    var homeworkid=$(this).attr("homeworkid");
                    //主要进行ajax的数据获取
                    mui.ajax(url,
                    {
                        data:{
                            classid:classid,
                            answer:answer,
                            homeworkid:homeworkid,
                            quesid:quesid,
                            typeid:'1',
                            ran:Math.random()
                        },
                        dataType:'json',//服务器返回json格式数据
                        type:'post',//HTTP请求类型
                        timeout:10000,//超时时间设置为10秒；
                        async:false,
                        success:function(data){
                            if(data.length>0){
                                $.each(data,function(k,v){
                                    attrarr=[];
                                    temp={};
                                    temp.id="type";
                                    temp.val="button";
                                    attrarr.push(temp);
                                    temp={};
                                    temp.id="style";
                                    temp.val="margin-top: 10px;margin-left: 10px;float:left;";
                                    attrarr.push(temp);
                                    temp={};
                                    temp.id="studentid";
                                    temp.val=v.studentid;
                                    attrarr.push(temp);
                                    var itemuserbutton=initDom("<button></button>",attrarr);
                                    itemuserbutton.text(v.studentname);
                                    $(itemuserli).append(itemuserbutton);;
                                });
                            }else{
                                $(obj).find("ul").remove();
                                mui.toast("作答人数为0人");
                            }
                        },
                        error:function(xhr,type,errorThrown){
                            //异常处理；
                            return errinfo;
                        }
                    });
                });
                userclassli.appendTo(userclassul);
                attrarr=[];
                temp={};
                temp.id="class";
                temp.val="mui-navigate-right";
                attrarr.push(temp);
                temp={};
                temp.id="href";
                temp.val="javascript:void(0);";
                attrarr.push(temp);
                temp={};
                temp.id="style";
                if(itemsarray[i]==data.answer[0].quesanswer){
                    temp.val="color: #2bc8a0;";
                }else{
                    temp.val="color: #FE5A59;";
                }
                
                attrarr.push(temp);
                var userclassa=initDom("<a></a>",attrarr);
                userclassa.html(itemsarray[i]+'.作答人数'+parseInt((parseInt(data.summary[itemsarray[i]])*parseInt(data.answer[0].summary.answernum))/100)+'人，占比'+data.summary[itemsarray[i]]+'%');
                userclassli.append(userclassa);
            }

        }
        //展示班级的数据
        attrarr=[];
        temp={};
        temp.id="class";
        temp.val="mui-content-padded";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="font-size:100%;color: #8f8f94;margin-top: 30px;";
        attrarr.push(temp);
        var userdiv=initDom("<div></div>",attrarr);
        userdiv.appendTo(parentdiv);
        attrarr=[];
        temp={};
        temp.id="style";
        temp.val="margin-bottom:10px;";
        attrarr.push(temp);
        var userh5=initDom("<h5></h5>",attrarr);
        userh5.text("班级数据");
        userdiv.append(userh5);
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="mui-table-view mui-grid-view";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="background-color: white;";
        attrarr.push(temp);
        var userul=initDom("<ul></ul>",attrarr);
        userdiv.append(userul);
        
        //展示作答人数的数据
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="mui-table-view-cell";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="width: 32%;";
        attrarr.push(temp);
        var userli=initDom("<li></li>",attrarr);
        userul.append(userli);
        attrarr=[]
        temp={};
        temp.id="href";
        temp.val="javascript:void(0);";
        attrarr.push(temp);
        var usera=initDom("<a></a>",attrarr);
        userli.append(usera);
        attrarr=[]
        temp={};
        temp.id="style";
        temp.val="font-size:100%;";
        attrarr.push(temp);
        var userspan=initDom("<a></a>",attrarr);
        userspan.text("作答人数");
        usera.append(userspan);
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="mui-media-body";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="font-size:100%;color: #8f8f94;";
        attrarr.push(temp);
        var usernumdiv=initDom("<div></div>",attrarr);
        var datanum=0;
        if(data.answer[0].summary.answernum!=undefined){
            datanum=data.answer[0].summary.answernum;
        }
        usernumdiv.text(datanum);
        usera.append(usernumdiv);

        //展示及格人数的数据
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="mui-table-view-cell";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="width: 32%;";
        attrarr.push(temp);
        var userli=initDom("<li></li>",attrarr);
        userul.append(userli);
        attrarr=[]
        temp={};
        temp.id="href";
        temp.val="javascript:void(0);";
        attrarr.push(temp);
        var usera=initDom("<a></a>",attrarr);
        userli.append(usera);
        attrarr=[]
        temp={};
        temp.id="style";
        temp.val="font-size:100%;";
        attrarr.push(temp);
        var userspan=initDom("<a></a>",attrarr);
        userspan.text("正确率");
        usera.append(userspan);
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="mui-media-body";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="font-size:100%;color: #8f8f94;";
        attrarr.push(temp);
        var usernumdiv=initDom("<div></div>",attrarr);
        var datanum=0;
        if(data.answer[0].summary.accrate!=undefined){
            datanum=data.answer[0].summary.accrate;
        }
        usernumdiv.text(datanum);
        usera.append(usernumdiv);

        //展示最高分的数据
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="mui-table-view-cell";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="width: 32%;";
        attrarr.push(temp);
        var userli=initDom("<li></li>",attrarr);
        userul.append(userli);
        attrarr=[]
        temp={};
        temp.id="href";
        temp.val="javascript:void(0);";
        attrarr.push(temp);
        var usera=initDom("<a></a>",attrarr);
        userli.append(usera);
        attrarr=[]
        temp={};
        temp.id="style";
        temp.val="font-size:100%;";
        attrarr.push(temp);
        var userspan=initDom("<a></a>",attrarr);
        userspan.text("易错项");
        usera.append(userspan);
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="mui-media-body";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="font-size:100%;color: #8f8f94;";
        attrarr.push(temp);
        var usernumdiv=initDom("<div></div>",attrarr);
        var datanum=0;
        if(data.answer[0].summary.erroranswer!=undefined){
            datanum=data.answer[0].summary.erroranswer;
        }
        usernumdiv.text(datanum);
        usera.append(usernumdiv);
    }
        return parentdiv;
    }


    //单词跟读题
    var wordAloundQuestion=function(data){
        var attrarr=[];
        var temp={};
        temp.id="class";
        temp.val="parent";
        attrarr.push(temp);
        temp.id="id";
        temp.class="parent";
        attrarr.push(temp);
        var parentdiv =initDom("<div></div>",attrarr);
        //单词朗读的展示
        attrarr=[];
        temp={};
        temp.id="class";
        temp.val="title";
        attrarr.push(temp);
        temp={};
        temp.id="onClick";
        temp.val="play();";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="margin-top:10px;font-size:1.2emem;font-family: times;color:black;text-align:center;";
        attrarr.push(temp);
        var div =initDom("<div></div>",attrarr);
        if(data.morphology!=undefined){
            if(data.ukmark!=undefined&&data.ukmark!=""&&data.ukmark!='null'){
                div.html("<h1 style='font-size:2.0em;color:black;'>"+data.tncontent+"</h1>["+data.ukmark+"]<h5 style='font-size:0.5em;color:gray;'>"+data.morphology+data.cncontent+"</h5>");
            }else{
                div.html("<h1 style='font-size:2.0em;color:black;'>"+data.tncontent+"</h1><h5 style='font-size:0.5em;color:gray;'>"+data.morphology+data.cncontent+"</h5>");
            }
        }else{
            div.html(data.tncontent);
        }
        attrarr=[]
        temp={};
        temp.id="style";
        temp.val="margin-top:10px;";
        attrarr.push(temp);
        temp={};
        temp.id="width";
        //temp.val="40px";
        temp.val=document.body.clientWidth-10+"px";
        attrarr.push(temp);
        temp={};
        temp.id="height";
        //temp.val="40px";
        temp.val=(document.body.clientWidth-10)*3/4+"px";
        attrarr.push(temp);
        temp={};
        temp.id="src";
        temp.val=word_pic_url+data.pic;
        attrarr.push(temp);
        var h5img =initDom("<img></img>",attrarr);
        div.append(h5img);
        attrarr=[]
        temp={};
        temp.id="style";
        temp.val="margin-top:10px;font-family: times;text-align:center;margin-left:10px;margin-right:10px;padding-left:10px;padding-right:10px;";
        attrarr.push(temp);
        var h5 =initDom("<h5></h5>",attrarr);
        if(type!=6){
            //单词跟读
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="fen3 edi-dc-left03b wordtest";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="margin-top:-20px;";
        attrarr.push(temp);
        var alounddiv=initDom("<div></div>",attrarr);
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="mp3voice";
        attrarr.push(temp);
        temp={};
        temp.id="contentid";
        temp.val=data.contentid;
        attrarr.push(temp);
        temp={};
        temp.id="tncontent";
        temp.val=data.tncontent;
        attrarr.push(temp);
        temp={};
        temp.id="readid";
        temp.val=data.readid;
        attrarr.push(temp);
        temp={};
        temp.id="type";
        temp.val="0";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        if(issubmit=='0'){
            temp.val="width:24%;";
        }else if(issubmit=='1'){
            temp.val="width:30%;";
        }
        attrarr.push(temp);
        var ymp3p=initDom("<p></p>",attrarr);
        ymp3p.appendTo(alounddiv);
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="btn-bo02-on bo01 edi-yuan";
        attrarr.push(temp);
        temp={};
        temp.id="loc";
        temp.val=word_mp3_url;
        attrarr.push(temp);
        temp={};
        temp.id="mp3";
        temp.val=data.wordmp3;
        attrarr.push(temp);
        var ya=initDom("<a></a>",attrarr);
        ymp3p.append(ya);
        ya.bind("tap",mp3PlayEvent);
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="fa fa-volume-up fa-18";
        attrarr.push(temp);
        var yi=initDom("<i></i>",attrarr);
        ya.append(yi);
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="uservoice";
        attrarr.push(temp);
        temp={};
        temp.id="mp3";
        temp.val=data.usermp3;
        attrarr.push(temp);
        temp={};
        temp.id="style";
        if(issubmit=='0'){
            temp.val="width:24%;";
        }else if(issubmit=='1'){
            temp.val="width:30%;";
        }
        attrarr.push(temp);
        var ump3p=initDom("<p></p>",attrarr);
        ump3p.appendTo(alounddiv);
        attrarr=[];
        if(data.usermp3!=""&&data.usermp3!=null&&data.usermp3!="null"&&data.usermp3!=undefined){
            temp={};
            temp.id="class";
            temp.val="btn-bo02-on bo01 edi-yuan";
            attrarr.push(temp);
        }else{
            temp={};
            temp.id="class";
            temp.val="btn-bo02 bo01 edi-yuan";
            attrarr.push(temp);
        }
        
        temp={};
        temp.id="loc";
        temp.val=user_record_mp3_url;
        attrarr.push(temp);
        temp={};
        temp.id="bid";
        temp.val=data.id;
        attrarr.push(temp);
        temp={};
        temp.id="type";
        temp.val="0";
        attrarr.push(temp);
        temp={};
        temp.id="mp3";
        temp.val=data.usermp3;
        attrarr.push(temp);
        var ua=initDom("<a></a>",attrarr);
        ump3p.append(ua);
        if(studentid!="0"){
            ua.bind("tap",mp3BackPlayEvent);
        }
        attrarr=[]
        temp={};
        temp.id="loc";
        temp.val=user_record_mp3_url;
        attrarr.push(temp);
        temp={};
        temp.id="bid";
        temp.val=data.id;
        attrarr.push(temp);
        temp={};
        temp.id="type";
        temp.val="0";
        attrarr.push(temp);
        temp={};
        temp.id="class";
        temp.val="fa fa-music fa-18";
        attrarr.push(temp);
        var ui=initDom("<i></i>",attrarr);
        ua.append(ui);
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="micro quesvoice";
        attrarr.push(temp);
        temp={};
        temp.id="mp3";
        temp.val=data.usermp3;
        attrarr.push(temp);
        temp={};
        temp.id="style";
        if(issubmit=='0'){
            temp.val="width:24%;";
        }else if(issubmit=='1'){
            temp.val="width:30%;";
        }
        attrarr.push(temp);
        var lmp3p=initDom("<p></p>",attrarr);
        lmp3p.appendTo(alounddiv);
        if(issubmit=='0'){
            attrarr=[]
            if(studentid!="0"){
                temp={};
                temp.id="class";
                temp.val="btn-bo02-on bo01 edi-yuan";
                attrarr.push(temp);
            }else{
                temp={};
                temp.id="class";
                temp.val="btn-bo02 bo01 edi-yuan";
                attrarr.push(temp);
            }
            temp={};
            temp.id="contentid";
            temp.val=data.contentid;
            attrarr.push(temp);
            temp={};
            temp.id="tncontent";
            temp.val=data.tncontent;
            attrarr.push(temp);
            temp={};
            temp.id="readid";
            temp.val=data.readid;
            attrarr.push(temp);
            temp={};
            temp.id="type";
            temp.val="0";
            attrarr.push(temp);
            var la=initDom("<a></a>",attrarr);
            lmp3p.append(la);
            if(issubmit=='0'&&studentid!="0"&&isOverdue=='false'){
                la.bind("tap",function(){
                    //将录音加上样式class
                    $(".microactive").removeClass("microactive");
                    $(this).addClass("microactive");
                    try{
                        if(isExitsFunction(UXinJSInterfaceSpeech.startRecordVoice)){
                            try{
                                var path=UXinJSInterfaceSpeech.startRecordVoice();
                            }catch(e){
                                mui.toast("对不起，有问题请联系客户服务部");
                            }
                        }else{
                            mui.toast("请升级到最新的优教信使");
                        }
                    }catch(e){
                        mui.toast("请升级到最新的优教信使");
                    }
                    
                });
            }
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="fa fa-microphone fa-18";
            attrarr.push(temp);
            var li=initDom("<i></i>",attrarr);
            la.append(li);
        }
        //用户的星级展示
        attrarr=[]
        temp={};
        temp.id="style";
        if(issubmit=='0'){
            temp.val="width:24%;";
        }else if(issubmit=='1'){
            temp.val="width:30%;";
        }
        attrarr.push(temp);
        var xmp3p=initDom("<p></p>",attrarr);
        xmp3p.appendTo(alounddiv);
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="rig-org";
        attrarr.push(temp);
        var xa=initDom("<a></a>",attrarr);
        xmp3p.append(xa);
        var xi="";
        // console.log(Math.round(parseFloat(data.userscore),2));
        if((data.userscore)==null||(data.userscore)==""||(data.userscore)==undefined||(data.userscore)=='null'){
            for(var i=0;i<3;i++){
                attrarr=[]
                temp={};
                temp.id="class";
                temp.val="fa fa-star edi-gg";
                attrarr.push(temp);
                xi=initDom("<i></i>",attrarr);
                xa.append(xi);
            }
            xa.append('<h1 style="margin-right: 30px;text-align: center;font-size:0.9em;"><strong class="score">0</strong>分</h1>');
        }else if(parseFloat(data.userscore)>=0&&parseFloat(data.userscore)<=50){
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="fa fa-star";
            attrarr.push(temp);
            xi=initDom("<i></i>",attrarr);
            xa.append(xi);
            for(var i=0;i<2;i++){
                attrarr=[]
                temp={};
                temp.id="class";
                temp.val="fa fa-star edi-gg";
                attrarr.push(temp);
                xi=initDom("<i></i>",attrarr);
                xa.append(xi);
            }
            xa.append('<h1 style="margin-right: 30px;text-align: center;font-size:0.9em;"><strong class="score">'+parseFloat(data.userscore)+'</strong>分</h1>');
        }else if(parseFloat(data.userscore)>0&&parseFloat(data.userscore)<=80){
            
            for(var i=0;i<2;i++){
                attrarr=[]
                temp={};
                temp.id="class";
                temp.val="fa fa-star";
                attrarr.push(temp);
                xi=initDom("<i></i>",attrarr);
                xa.append(xi);
            }
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="fa fa-star edi-gg";
            attrarr.push(temp);
            xi=initDom("<i></i>",attrarr);
            xa.append(xi);
            xa.append('<h1 style="margin-right: 30px;text-align: center;font-size:0.9em;"><strong class="score">'+parseFloat(data.userscore)+'</strong>分</h1>');

        }else if(parseFloat(data.userscore)>=80){
            for(var i=0;i<3;i++){
                attrarr=[]
                temp={};
                temp.id="class";
                temp.val="fa fa-star";
                attrarr.push(temp);
                xi=initDom("<i></i>",attrarr);
                xa.append(xi);
            }
            xa.append('<h1 style="margin-right: 30px;text-align: center;font-size:0.9em;"><strong class="score">'+parseFloat(data.userscore)+'</strong>分</h1>');
        }
        
       
        parentdiv.append(div);
        alounddiv.appendTo(parentdiv);

        }else{
            parentdiv.append(div);
        }
        
        
        if(issubmit=='1'){
            //展示个人的数据
            attrarr=[];
            temp={};
            temp.id="class";
            temp.val="mui-content-padded";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="font-size:100%;color: #8f8f94;margin-top: 30px;";
            attrarr.push(temp);
            var userdiv=initDom("<div></div>",attrarr);
            userdiv.appendTo(parentdiv);
            attrarr=[];
            temp={};
            temp.id="style";
            temp.val="margin-bottom:10px;";
            attrarr.push(temp);
            var userh5=initDom("<h5></h5>",attrarr);
            userh5.text("班级数据");
            userdiv.append(userh5);
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-table-view mui-grid-view";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="background-color: white;";
            attrarr.push(temp);
            var userul=initDom("<ul></ul>",attrarr);
            userdiv.append(userul);
            
            //展示作答人数的数据

            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-table-view-cell";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="width: 32%;";
            attrarr.push(temp);
            var userli=initDom("<li></li>",attrarr);
            userul.append(userli);
            attrarr=[]
            temp={};
            temp.id="href";
            temp.val="javascript:void(0);";
            attrarr.push(temp);
            var usera=initDom("<a></a>",attrarr);
            userli.append(usera);
            attrarr=[]
            temp={};
            temp.id="style";
            temp.val="font-size:100%;";
            attrarr.push(temp);
            var userspan=initDom("<a></a>",attrarr);
            userspan.text("作答人数");
            usera.append(userspan);
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-media-body";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="font-size:100%;color: #8f8f94;";
            attrarr.push(temp);
            var usernumdiv=initDom("<div></div>",attrarr);
            var datanum=0;
            if(data.classresult[0].num!=undefined){
                datanum=Math.round(data.classresult[0].num);
            }
            usernumdiv.text(datanum);
            usera.append(usernumdiv);

            //展示及格人数的数据
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-table-view-cell";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="width: 32%;";
            attrarr.push(temp);
            var userli=initDom("<li></li>",attrarr);
            userul.append(userli);
            attrarr=[]
            temp={};
            temp.id="href";
            temp.val="javascript:void(0);";
            attrarr.push(temp);
            var usera=initDom("<a></a>",attrarr);
            userli.append(usera);
            attrarr=[]
            temp={};
            temp.id="style";
            temp.val="font-size:100%;";
            attrarr.push(temp);
            var userspan=initDom("<a></a>",attrarr);
            userspan.text("及格率");
            usera.append(userspan);
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-media-body";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="font-size:100%;color: #8f8f94;";
            attrarr.push(temp);
            var usernumdiv=initDom("<div></div>",attrarr);
            var datanum=0;
            try{
                if(data.classresult[0].accnum!=undefined){
                    if(data.classresult[0].num!=undefined&&data.classresult[0].num!=0){
                        datanum=Math.round(data.classresult[0].accnum*100/data.classresult[0].num);
                    }  
                }
            }catch(e){
                datanum=0;
            }
            usernumdiv.text(datanum+"%");
            usera.append(usernumdiv);

            //展示最高分的数据
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-table-view-cell";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="width: 32%;";
            attrarr.push(temp);
            var userli=initDom("<li></li>",attrarr);
            userul.append(userli);
            attrarr=[]
            temp={};
            temp.id="href";
            temp.val="javascript:void(0);";
            attrarr.push(temp);
            var usera=initDom("<a></a>",attrarr);
            userli.append(usera);
            attrarr=[]
            temp={};
            temp.id="style";
            temp.val="font-size:100%;";
            attrarr.push(temp);
            var userspan=initDom("<a></a>",attrarr);
            userspan.text("最高分");
            usera.append(userspan);
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="mui-media-body";
            attrarr.push(temp);
            temp={};
            temp.id="style";
            temp.val="font-size:100%;color: #8f8f94;";
            attrarr.push(temp);
            var usernumdiv=initDom("<div></div>",attrarr);
            var datanum=0;
            if(data.classresult[0].maxscore!=undefined){
                datanum=parseFloat(data.classresult[0].maxscore);
            }
            usernumdiv.text(datanum);
            usera.append(usernumdiv);
        }   
        return parentdiv;
    }

    //课文跟读题
    var textAloundQuestion=function(data){
        var attrarr=[];
        var temp={};
        temp.id="class";
        temp.val="parent";
        attrarr.push(temp);
        temp={};
        temp.id="id";
        temp.val="parent";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="margin-left:10px;margin-right:10px;";
        attrarr.push(temp);
        var parentdiv =initDom("<div></div>",attrarr);
        //单词朗读的展示
        attrarr=[];
        temp={};
        temp.id="class";
        temp.val="title";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="margin-top:30%;font-family: times;color:black;text-align:center;";
        attrarr.push(temp);
        var div =initDom("<div></div>",attrarr);
        div.html("<h3 style='margin-left:10px;width:90%;margin-right:15px;font-size:1.0em;color:black;'>"+data.tncontent+"</h3><h5 style='font-size:1.0em;color:gray;margin-top:20px;margin-left:10px;margin-right:15px;width:90%;'>"+data.cncontent+"</h5>");
        attrarr=[]
        temp={};
        temp.id="style";
        temp.val="margin-top:10px;font-family: times;text-align:center;margin-left:10px;margin-right:10px;padding-left:10px;padding-right:10px;";
        attrarr.push(temp);
        var h5 =initDom("<h5></h5>",attrarr);
        //单词跟读
        if(type!=2){
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="fen3 edi-dc-left03b wordtest";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="margin-top:-20px;";
        attrarr.push(temp);
        var alounddiv=initDom("<div></div>",attrarr);
        attrarr=[];
        temp={};
        temp.id="class";
        temp.val="mp3voice";
        attrarr.push(temp);
        temp={};
        temp.id="contentid";
        temp.val=data.contentid;
        attrarr.push(temp);
        temp={};
        temp.id="tncontent";
        temp.val=data.tncontent;
        attrarr.push(temp);
        temp={};
        temp.id="readid";
        temp.val=data.readid;
        attrarr.push(temp);
        temp={};
        temp.id="type";
        temp.val="1";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        if(issubmit=='0'){
            temp.val="width:24%;";
        }else if(issubmit=='1'){
            temp.val="width:30%;";
        }
        attrarr.push(temp);
        var ymp3p=initDom("<p></p>",attrarr);
        ymp3p.appendTo(alounddiv);
        attrarr=[];
        temp={};
        temp.id="class";
        temp.val="btn-bo02-on bo01 edi-yuan";
        attrarr.push(temp);
        temp={};
        temp.id="loc";
        temp.val=text_mp3_url;
        attrarr.push(temp);
        temp={};
        temp.id="mp3";
		if(data.wordmp3 != null && data.wordmp3 != "" && data.wordmp3 != undefined ){
            temp.val=data.wordmp3.substr(0,2)+"/"+data.wordmp3+".mp3";
        }else{
            temp.val="";
        }
        //temp.val=data.wordmp3.substr(0,2)+"/"+data.wordmp3+".mp3";
        attrarr.push(temp);
        var ya=initDom("<a></a>",attrarr);
        ymp3p.append(ya);
        ya.bind("tap",mp3PlayEvent);
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="fa fa-volume-up fa-18";
        attrarr.push(temp);
        var yi=initDom("<i></i>",attrarr);
        ya.append(yi);
        attrarr=[];
        temp={};
        temp.id="class";
        temp.val="uservoice";
        attrarr.push(temp);
        temp={};
        temp.id="mp3";
        temp.val=data.usermp3;
        attrarr.push(temp);
        temp={};
        temp.id="bid";
        temp.val=data.id;
        attrarr.push(temp);
        temp={};
        temp.id="type";
        temp.val="1";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        if(issubmit=='0'){
            temp.val="width:24%;";
        }else if(issubmit=='1'){
            temp.val="width:30%;";
        }
        attrarr.push(temp);
        var ump3p=initDom("<p></p>",attrarr);
        ump3p.appendTo(alounddiv);
        attrarr=[];
        if(data.usermp3!=""&&data.usermp3!=null&&data.usermp3!="null"&&data.usermp3!=undefined){
            temp={};
            temp.id="class";
            temp.val="btn-bo02-on bo01 edi-yuan";
            attrarr.push(temp);
        }else{
            temp={};
            temp.id="class";
            temp.val="btn-bo02 bo01 edi-yuan";
            attrarr.push(temp);
        }
        // attrarr=[]
        // temp={};
        // temp.id="class";
        // temp.val="btn-bo02-on bo01 edi-yuan";
        // attrarr.push(temp);
        temp={};
        temp.id="loc";
        temp.val=user_record_mp3_url;
        attrarr.push(temp);
        temp={};
        temp.id="bid";
        temp.val=data.id;
        attrarr.push(temp);
        temp={};
        temp.id="type";
        temp.val="1";
        attrarr.push(temp);
        temp={};
        temp.id="mp3";
        temp.val=data.usermp3;
        attrarr.push(temp);
        var ua=initDom("<a></a>",attrarr);
        ump3p.append(ua);
        if(studentid!="0"){
            ua.bind("tap",mp3BackPlayEvent);
        }
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="fa fa-music fa-18";
        attrarr.push(temp);
        var ui=initDom("<i></i>",attrarr);
        ua.append(ui);
        attrarr=[];
        temp={};
        temp.id="class";
        temp.val="micro quesvoice";
        attrarr.push(temp);
        temp={};
        temp.id="mp3";
        temp.val=data.usermp3;
        attrarr.push(temp);
        temp={};
        temp.id="style";
        if(issubmit=='0'){
            temp.val="width:24%;";
        }else if(issubmit=='1'){
            temp.val="width:30%;";
        }
        attrarr.push(temp);
        var lmp3p=initDom("<p></p>",attrarr);
        lmp3p.appendTo(alounddiv);
        if(issubmit=='0'){
            attrarr=[]
            if(studentid!="0"){
                temp={};
                temp.id="class";
                temp.val="btn-bo02-on bo01 edi-yuan";
                attrarr.push(temp);
            }else{
                temp={};
                temp.id="class";
                temp.val="btn-bo02 bo01 edi-yuan";
                attrarr.push(temp);
            }
            temp={};
            temp.id="contentid";
            temp.val=data.contentid;
            attrarr.push(temp);
            temp={};
            temp.id="tncontent";
            temp.val=data.tncontent;
            attrarr.push(temp);
            temp={};
            temp.id="readid";
            temp.val=data.readid;
            attrarr.push(temp);
            temp={};
            temp.id="type";
            temp.val="1";
            attrarr.push(temp);
            var la=initDom("<a></a>",attrarr);
            lmp3p.append(la);
            if(issubmit=='0'&&studentid!="0"&&isOverdue=='false'){
                la.bind("tap",function(){
                    //将录音加上样式class
                    $(".microactive").removeClass("microactive");
                    $(this).addClass("microactive");
                    try{
                        if(isExitsFunction(UXinJSInterfaceSpeech.startRecordVoice)){
                            try{
                                var path=UXinJSInterfaceSpeech.startRecordVoice();
                            }catch(e){
                                mui.toast("对不起，有问题请联系客户服务部");
                            }
                        }else{
                            mui.toast("请升级到最新的优教信使");
                        }
                    }catch(e){
                        mui.toast("请升级到最新的优教信使");
                    }
                    
                });
            }
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="fa fa-microphone fa-18";
            attrarr.push(temp);
            var li=initDom("<i></i>",attrarr);
            la.append(li);
        }
        //用户的星级展示
        attrarr=[]
        temp={};
        temp.id="style";
        if(issubmit=='0'){
            temp.val="width:24%;";
        }else if(issubmit=='1'){
            temp.val="width:30%;";
        }
        attrarr.push(temp);
        var xmp3p=initDom("<p></p>",attrarr);
        xmp3p.appendTo(alounddiv);
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="rig-org";
        attrarr.push(temp);
        var xa=initDom("<a></a>",attrarr);
        xmp3p.append(xa);
        var xi="";
        if((data.userscore)==null||(data.userscore)==""||(data.userscore)==undefined||(data.userscore)=="null"){
            for(var i=0;i<3;i++){
                attrarr=[]
                temp={};
                temp.id="class";
                temp.val="fa fa-star edi-gg";
                attrarr.push(temp);
                xi=initDom("<i></i>",attrarr);
                xa.append(xi);
            }
            xa.append('<h1 style="margin-right: 30px;text-align: center;font-size:0.9em;"><strong class="score">0</strong>分</h1>');
        }else if(parseFloat(data.userscore)>=0&&parseFloat(data.userscore)<=50){
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="fa fa-star";
            attrarr.push(temp);
            xi=initDom("<i></i>",attrarr);
            xa.append(xi);
            for(var i=0;i<2;i++){
                attrarr=[]
                temp={};
                temp.id="class";
                temp.val="fa fa-star edi-gg";
                attrarr.push(temp);
                xi=initDom("<i></i>",attrarr);
                xa.append(xi);
            }
            xa.append('<h1 style="margin-right: 30px;text-align: center;font-size:0.9em;"><strong class="score">'+parseFloat(data.userscore)+'</strong>分</h1>');
        }else if(parseFloat(data.userscore)>50&&parseFloat(data.userscore)<=80){
            
            for(var i=0;i<2;i++){
                attrarr=[]
                temp={};
                temp.id="class";
                temp.val="fa fa-star";
                attrarr.push(temp);
                xi=initDom("<i></i>",attrarr);
                xa.append(xi);
            }
            attrarr=[]
            temp={};
            temp.id="class";
            temp.val="fa fa-star edi-gg";
            attrarr.push(temp);
            xi=initDom("<i></i>",attrarr);
            xa.append(xi);
            xa.append('<h1 style="margin-right: 30px;text-align: center;font-size:0.9em;"><strong class="score">'+parseFloat(data.userscore)+'</strong>分</h1>');
        }else if(Math.round(parseInt(data.userscore),1)>=80){
            for(var i=0;i<3;i++){
                attrarr=[]
                temp={};
                temp.id="class";
                temp.val="fa fa-star";
                attrarr.push(temp);
                xi=initDom("<i></i>",attrarr);
                xa.append(xi);
            }
            xa.append('<h1 style="margin-right: 30px;text-align: center;font-size:0.9em;"><strong class="score">'+parseFloat(data.userscore)+'</strong>分</h1>');
        }
        
        parentdiv.append(div);
        alounddiv.appendTo(parentdiv);

        }else{
            parentdiv.append(div);
        }
        
        if(issubmit=='1'){
        //展示个人的数据
        attrarr=[];
        temp={};
        temp.id="class";
        temp.val="mui-content-padded";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="font-size:100%;color: #8f8f94;margin-top: 30px;";
        attrarr.push(temp);
        var userdiv=initDom("<div></div>",attrarr);
        userdiv.appendTo(parentdiv);
        attrarr=[];
        temp={};
        temp.id="style";
        temp.val="margin-bottom:10px;";
        attrarr.push(temp);
        var userh5=initDom("<h5></h5>",attrarr);
        userh5.text("班级数据");
        userdiv.append(userh5);
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="mui-table-view mui-grid-view";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="background-color: white;";
        attrarr.push(temp);
        var userul=initDom("<ul></ul>",attrarr);
        userdiv.append(userul);
        
        //展示作答人数的数据
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="mui-table-view-cell";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="width: 32%;";
        attrarr.push(temp);
        var userli=initDom("<li></li>",attrarr);
        userul.append(userli);
        attrarr=[]
        temp={};
        temp.id="href";
        temp.val="javascript:void(0);";
        attrarr.push(temp);
        var usera=initDom("<a></a>",attrarr);
        userli.append(usera);
        attrarr=[]
        temp={};
        temp.id="style";
        temp.val="font-size:100%;";
        attrarr.push(temp);
        var userspan=initDom("<a></a>",attrarr);
        userspan.text("作答人数");
        usera.append(userspan);
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="mui-media-body";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="font-size:100%;color: #8f8f94;";
        attrarr.push(temp);
        var usernumdiv=initDom("<div></div>",attrarr);
        var datanum=0;
        if(data.classresult[0].num!=undefined){
                datanum=Math.round(data.classresult[0].num);
        }
        usernumdiv.text(datanum);
        usera.append(usernumdiv);

        //展示及格人数的数据
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="mui-table-view-cell";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="width: 32%;";
        attrarr.push(temp);
        var userli=initDom("<li></li>",attrarr);
        userul.append(userli);
        attrarr=[]
        temp={};
        temp.id="href";
        temp.val="javascript:void(0);";
        attrarr.push(temp);
        var usera=initDom("<a></a>",attrarr);
        userli.append(usera);
        attrarr=[]
        temp={};
        temp.id="style";
        temp.val="font-size:100%;";
        attrarr.push(temp);
        var userspan=initDom("<a></a>",attrarr);
        userspan.text("及格率");
        usera.append(userspan);
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="mui-media-body";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="font-size:100%;color: #8f8f94;";
        attrarr.push(temp);
        var usernumdiv=initDom("<div></div>",attrarr);
        // var datanum=0;
        // if(data.classresult[0].accnum!=undefined){
        //     datanum=Math.round(data.classresult[0].accnum);
        // }
        // usernumdiv.text(datanum);
        var datanum=0;
        try{
            if(data.classresult[0].accnum!=undefined){
                if(data.classresult[0].num!=undefined&&data.classresult[0].num!=0){
                    datanum=Math.round(data.classresult[0].accnum*100/data.classresult[0].num);
                }  
            }
        }catch(e){
            datanum=0;
        }
        usernumdiv.text(datanum+"%");
        usera.append(usernumdiv);

        //展示最高分的数据
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="mui-table-view-cell";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="width: 32%;";
        attrarr.push(temp);
        var userli=initDom("<li></li>",attrarr);
        userul.append(userli);
        attrarr=[]
        temp={};
        temp.id="href";
        temp.val="javascript:void(0);";
        attrarr.push(temp);
        var usera=initDom("<a></a>",attrarr);
        userli.append(usera);
        attrarr=[]
        temp={};
        temp.id="style";
        temp.val="font-size:100%;";
        attrarr.push(temp);
        var userspan=initDom("<a></a>",attrarr);
        userspan.text("最高分");
        usera.append(userspan);
        attrarr=[]
        temp={};
        temp.id="class";
        temp.val="mui-media-body";
        attrarr.push(temp);
        temp={};
        temp.id="style";
        temp.val="font-size:100%;color: #8f8f94;";
        attrarr.push(temp);
        var usernumdiv=initDom("<div></div>",attrarr);
        var datanum=0;
        if(data.classresult[0].maxscore!=undefined){
            datanum=parseFloat(data.classresult[0].maxscore);
        }
        usernumdiv.text(datanum);
        usera.append(usernumdiv);
    }
        return parentdiv; 
    }

    //选择题的监听事件
    function choiceExamsEvent(){
        if(isOverdue!='false'){
            return false;
        }
        //alert("听力训练选择题");
        var ul=$(this).parents("ul");
        ul.find("li").find("div.items").attr("style","width:30px;height:30px;background-color:white;border-radius:25px;display:inline-block;border:1px solid gray;");
        $(this).find("div.items").attr("style","width:30px;height:30px;background-color:#2bc8a0; border-radius:25px;display: inline-block;");
        var questionid=$(this).attr("quesid");
        var homeworkid=$(this).attr("homeworkid");
        var quizid=$(this).attr("quizid");
        var examsid=$(this).attr("examsid");
        var answerid=$(this).attr("answerid");
        var useranswer=$(this).attr("itemflag");
        var typeid=$(this).attr("typeid");
        //进行数据库的插入
        var url="../Public/setUseranswer";
        mui.ajax(url,
            {
            data:{
                questionid:questionid,
                useranswer:useranswer,
                homeworkid:homeworkid,
                quizid:quizid,
                answerid:answerid,
                examsid:examsid,
                typeid:typeid,
                ran:Math.random()
            },
            dataType:'json',//服务器返回json格式数据
            type:'post',//HTTP请求类型
            timeout:10000,//超时时间设置为10秒；
            async:true,
            success:function(data){
                //用户回答问题之后直接进行跳转
                var next=document.getElementById("next");
                mui.trigger(next,'click');
            },
            error:function(xhr,type,errorThrown){
                //异常处理；
                return errinfo;
            }
        });
    }

    //填空题的监听事件
    function blankExamsEvent(){
        alert("听力训练填空题");
    }

    //判断题的监听事件
    function trueOrFalseExamsEvent(){
        var ul=$(this).parents("ul");
        ul.find("li").find("div.items").attr("style","width:30px;height:30px;background-color:white;border-radius:25px;display:inline-block;border:1px solid gray;");
        $(this).find("div.items").attr("style","width:30px;height:30px;background-color:#2bc8a0; border-radius:25px;display: inline-block;");
        var questionid=$(this).attr("quesid");
        var homeworkid=$(this).attr("homeworkid");
        var quizid=$(this).attr("quizid");
        var examsid=$(this).attr("examsid");
        var answerid=$(this).attr("answerid");
        var useranswer=$(this).attr("itemflag");
        var typeid=$(this).attr("typeid");
        //进行数据库的插入
        var url="../Public/setUseranswer";
        mui.ajax(url,
            {
            data:{
                questionid:questionid,
                useranswer:useranswer,
                homeworkid:homeworkid,
                quizid:quizid,
                answerid:answerid,
                examsid:examsid,
                typeid:typeid,
                ran:Math.random()
            },
            dataType:'json',//服务器返回json格式数据
            type:'post',//HTTP请求类型
            timeout:10000,//超时时间设置为10秒；
            async:true,
            success:function(data){
                //用户回答问题之后直接进行跳转
                var next=document.getElementById("next");
                mui.trigger(next,'click');
            },
            error:function(xhr,type,errorThrown){
                //异常处理；
                return errinfo;
            }
        });
    }

    //排序题的监听事件
    function sequenceExamsEvent(){
    	alert("听力训练排序题");
    }


    //英汉互译的监听事件
    function wordTranslateEvent(obj){
        var ul=$(this).parents("ul");
        ul.find("li").find("div.items").attr("style","width:30px;height:30px;background-color:white;border-radius:25px;display:inline-block;border:1px solid gray;");
        $(this).find("div.items").attr("style","width:30px;height:30px;background-color:#2bc8a0; border-radius:25px;display: inline-block;");
        var questionid=$(this).attr("quesid");
        var homeworkid=$(this).attr("homeworkid");
        var wordid=$(this).attr("wordid");
        var useranswer=$(this).attr("itemflag");
        var typeid=$(this).attr("typeid");
        //进行数据库的插入
        var url="../Public/setUserWordtestanswer";
        mui.ajax(url,
            {
            data:{
                questionid:questionid,
                useranswer:useranswer,
                studentid:studentid,
                classid:classid,
                homeworkid:homeworkid,
                wordid:wordid,
                typeid:typeid,
                ran:Math.random()
            },
            dataType:'json',//服务器返回json格式数据
            type:'post',//HTTP请求类型
            timeout:10000,//超时时间设置为10秒；
            async:true,
            success:function(data){
                //用户回答问题之后直接进行跳转
                var next=document.getElementById("next");
                mui.trigger(next,'click');
            },
            error:function(xhr,type,errorThrown){
                //异常处理；
                return errinfo;
            }
        });

    }

    //单词跟读的监听事件
    function wordChooseEvent(){
        var ul=$(this).parents("ul");
        ul.find("li").find("div.items").attr("style","width:30px;height:30px;background-color:white;border-radius:25px;display:inline-block;border:1px solid gray;");
        $(this).find("div.items").attr("style","width:30px;height:30px;background-color:#2bc8a0; border-radius:25px;display: inline-block;");
        var questionid=$(this).attr("quesid");
        var homeworkid=$(this).attr("homeworkid");
        var wordid=$(this).attr("wordid");
        var useranswer=$(this).attr("itemflag");
        var typeid=$(this).attr("typeid");
        //进行数据库的插入
        var url="../Public/setUserWordtestanswer";
        mui.ajax(url,
            {
            data:{
                questionid:questionid,
                useranswer:useranswer,
                studentid:studentid,
                classid:classid,
                homeworkid:homeworkid,
                wordid:wordid,
                typeid:typeid,
                ran:Math.random()
            },
            dataType:'json',//服务器返回json格式数据
            type:'post',//HTTP请求类型
            timeout:10000,//超时时间设置为10秒；
            async:true,
            success:function(data){
                //用户回答问题之后直接进行跳转
                var next=document.getElementById("next");
                mui.trigger(next,'click');
            },
            error:function(xhr,type,errorThrown){
                //异常处理；
                return errinfo;
            }
        });
    }


    //听力训练的监听事件
    function examsPlayEvent(){
        //取出看目前听力的是第几个
        var ttsindex=$(this).parents(".ques").attr("id").substr(4);
        var btn=$(this);
        var classname=$(this).attr("class");
        var quesid=$(this).attr("quesid");
        var type=$(this).attr("type");
        var click=$(this).parent();
        if(classname.indexOf("playing")>0){
            stopaudio();
            $(this).attr("class","");
            $(this).attr("class","btn  pausing");
            $(this).src="../../public/Homework/images/sy.png";
        }
        else 
        {
            var obj=$(this);
            var data=page_tts[ttsindex];
            listendata=[];
            mui.each(data,function(k1,v1){
                var temp={};
                temp.tts_mp3=v1.tts_mp3;
                temp.tts_stoptime=v1.tts_stoptime;
                listendata.push(temp);
            });
            $(this).attr("class","");
            $(this).attr("class","btn playing");
            $(this).attr("src","../../public/Homework/images/sy.gif");
            $(this).attr("id","playing");
            mp.index = 0;
            mp.stemindex = 0;
            mp.queinitindex = 0;
            mp.questionindex = 0;
            mp.childstemindex = 0;
            mp.childinitstemindex = 0;
            mp.url = "";
            mp.repeat = 1; //默认播放次数
            mp.curpeat = 1;//当前播放到第几次
            mp.url = "";
            mp.playtimes = 0;
            question_play(parseInt(pagedata[ttsindex].parent.question.questions_playtimes),listendata,$(click));
         }
    }


    //获取mp3文件路径
    function getmp3url(mp3name){
        //mp3name = mp3name.substr(0,mp3name.length-1);
        var mp3url = '';
        var quespeed = 1;
        //if(examstts_type == 1){           //系统生成
            if(quespeed == 0){
                mp3name = mp3name+'s';
            }
            else if(quespeed == 2){
                mp3name = mp3name+'q';
            }
        //}
		if(mp3name != null && mp3name != undefined && mp3name != ""){
            mp3url = exams_mp3_url+mp3name.substr(0,2)+'/'+mp3name+'.mp3';
        }
       // mp3url = exams_mp3_url+mp3name.substr(0,2)+'/'+mp3name+'.mp3';
        return mp3url;
    }

    //小题播放
    function question_play(playtimes,quettsdata,obj){
        var smallquetts = '';
        clearTimeout();
        //播放次数
        if(mp.playtimes<playtimes){
            if(mp.questionindex < quettsdata.length){
                smallquetts = quettsdata[mp.questionindex];
                playurl = getmp3url(smallquetts.tts_mp3);
                //回调函数
                var options = {};
                options.id = "question";
                options.callback = function(){
                    try{
                        clearTimeout(mp3_progress);
                    }catch(e){
                        // console.log("第一次")
                    }   
                    mp3_progress = setTimeout(function(){
                        mp.questionindex = mp.questionindex +1;
                        // console.log(quettsdata);
                        question_play(playtimes,quettsdata,obj);
                    },parseInt(quettsdata[mp.questionindex].tts_stoptime)*1000);
                }
                mp = mp.change(mp,options);
                mp.play(playurl);
            }else{
                 mp.index = 0;
                 mp.stemindex = 0;
                 mp.queinitindex = 0;
                 mp.questionindex = 0;
                 mp.childstemindex = 0;
                mp.childinitstemindex = 0;
                mp.url = "";
                mp.repeat = 1; //默认播放次数
                mp.curpeat = 1;//当前播放到第几次
                mp.url = "";
                obj.attr("class","");
                obj.attr("class","audio-btn btn");
                try{
                    $("#playing").attr("src","../../public/Homework/images/sy.png");
                    //stopaudio();
                }catch(e){
                    //stopaudio();
                    
                }
                mp.playtimes=mp.playtimes+1;
                try{
                    clearTimeout(mp3_progress_reap);
                }catch(e){
                    //// console.log("第一次")
                }
                mp3_progress_reap=setTimeout(function(){
                    obj.attr("class","");
                    obj.attr("class","btn playing");
                    $("#playing").attr("src","../../public/Homework/images/sy.gif");
                    question_play(playtimes,quettsdata,obj);
                },5000);
                //document.getElementById("audio-btn").style.backgroundImage="url(../../public/Homework/images/pause-to-play-faster.gif)";
            }

        }else{
            mp.index = 0;
            mp.stemindex = 0;
            mp.queinitindex = 0;
            mp.questionindex = 0;
            mp.childstemindex = 0;
             mp.childinitstemindex = 0;
             mp.url = "";
             mp.repeat = 1; //默认播放次数
             mp.curpeat = 1;//当前播放到第几次
             mp.url = "";
            mp.playtimes=0;
             obj.attr("class","");
             obj.attr("class","audio-btn btn");
             try{
                 $("#playing").attr("src","../../public/Homework/images/sy.png");
                 $("#playing").attr("id","");
                 //stopaudio();
             }catch(e){
                 //stopaudio();
                 
             }
        }

    }


    function hwstopaudio(i){
        var perpage=0;
        try{
            if(i==0||pageslider[i-1]==undefined){
                perpage=0;
            }else{
                perpage=pageslider[i-1];
            }
        }catch(e){
           // console.log("error");
        }
        var nxtpage=0;
        try{
            if(pageslider[i+1]==undefined){
                nxtpage=0;
            }else{
                nxtpage=pageslider[i+1];
            }
        }catch(e){

        }
        pageslider[i]=perpage>nxtpage?perpage+1:nxtpage+1;
        if(page_obj[i].type!=1){
            try{
                document.getElementById("playing").setAttribute("src","../../public/Homework/images/sy.png");
                removeClass(document.getElementById("playing").parentNode,"playing");
            }catch(e){

            }
            stopaudio();
            try{
                document.getElementById("playing").removeAttribute("id");
            }catch(e){

            }
            //return 1;
        }else{
            if(page_obj[i].type==0){
                stopaudio();
                //return 1;
            }else if(page_obj[i].type==1){
                //组合试题从哪里过来的
                try{
                    document.getElementById("playing").setAttribute("src","../../public/Homework/images/sy.png");

                }catch(e){
                    
                }
                if(pageslider[i]==(pageslider[i-1]+1)){
                    //表示从左边过来的数据判断左边的情况需要将昨天的那个直接停止了
                    if(page_obj[i].stemid!=page_obj[i-1].stemid){
                        try{
                            removeClass(document.getElementById("playing").parentNode,"playing");
                        }catch(e){

                        }
                        try{
                            document.getElementById("playing").removeAttribute("id");
                            //设置当前的是正在播放的情况
                            document.getElementsByClassName("ques")[i].getElementsByClassName("sy-click")[0].src="../../public/Homework/images/sy.png";
                            document.getElementsByClassName("ques")[i].getElementsByClassName("sy-click")[0].setAttribute("id","playing");
                        }catch(e){
                            document.getElementsByClassName("ques")[i].getElementsByClassName("sy-click")[0].src="../../public/Homework/images/sy.png";
                            document.getElementsByClassName("ques")[i].getElementsByClassName("sy-click")[0].setAttribute("id","playing");
                        }
                        stopaudio();
                        //return 1;
                    }else{
                        try{
                            //设置当前的是正在播放的情况
                            // console.log(document.getElementById("playing"));
                            if(hasClass(document.getElementById("playing").parentNode,"playing")){
                                try{
                                    // console.log("aaaaaa");
                                    removeClass(document.getElementById("playing").parentNode,"playing");
                                }catch(e){
                                    
                                }
                                document.getElementById("playing").removeAttribute("id");
                                document.getElementsByClassName("ques")[i].getElementsByClassName("sy-click")[0].src="../../public/Homework/images/sy.gif";
                                document.getElementsByClassName("ques")[i].getElementsByClassName("sy-click")[0].setAttribute("id","playing");
                                addClass(document.getElementsByClassName("ques")[i].getElementsByClassName("sy-click")[0].parentNode,"playing");
                            }
                        }catch(e){
                             
                        }
                        //return 0;

                    }

                }else if(pageslider[i]==(pageslider[i+1]+1)){
                    //表示从左边过来的数据判断右边的情况
                    if(page_obj[i].stemid!=page_obj[i+1].stemid){
                        try{
                            removeClass(document.getElementById("playing").parentNode,"playing");
                        }catch(e){

                        }
                        try{
                            document.getElementById("playing").removeAttribute("id");
                            //设置当前的是正在播放的情况
                            document.getElementsByClassName("ques")[i].getElementsByClassName("sy-click")[0].src="../../public/Homework/images/sy.png";
                            document.getElementsByClassName("ques")[i].getElementsByClassName("sy-click")[0].setAttribute("id","playing");
                        }catch(e){
                            document.getElementsByClassName("ques")[i].getElementsByClassName("sy-click")[0].src="../../public/Homework/images/sy.png";
                            document.getElementsByClassName("ques")[i].getElementsByClassName("sy-click")[0].setAttribute("id","playing");

                        }
                        stopaudio();
                        //return 1;
                    }else{
                        try{
                            //设置当前的是正在播放的情况判断目前的上一个是不是在播放状态
                            if(hasClass(document.getElementById("playing").parentNode,"playing")){
                                try{
                                    removeClass(document.getElementById("playing").parentNode,"playing");
                                }catch(e){

                                }
                                document.getElementById("playing").removeAttribute("id");
                                document.getElementsByClassName("ques")[i].getElementsByClassName("sy-click")[0].src="../../public/Homework/images/sy.gif";
                                document.getElementsByClassName("ques")[i].getElementsByClassName("sy-click")[0].setAttribute("id","playing");
                                addClass(document.getElementsByClassName("ques")[i].getElementsByClassName("sy-click")[0].parentNode,"playing");
                            }
                        }catch(e){

                        }
                        //return 0;
                    }
                }
            }
        }
    }

    //停止MP3播放
    function stopaudio(){
        try{
            clearTimeout(mp3_progress);
            clearTimeout(mp3_progress_reap);
        }catch(e){
            // console.log(e);
        }
        try{
            mp.pause();
        }catch(e){
            // console.log(e);
        }
        mp3_progress='';
        mp3_progress_reap='';
        mp.index = 0;
        mp.stemindex = 0;
        mp.queinitindex = 0;
        mp.questionindex = 0;
        mp.childstemindex = 0;
        mp.playtimes=0;
        mp.childinitstemindex = 0;
        mp.url = "";
        mp.repeat = 1; //默认播放次数
        mp.curpeat = 1;//当前播放到第几次
        mp.url = "";
        mp.id = "";
        mp.callback = "";
    }


    //停止MP3播放
    function stopaudio(){
        try{
            clearTimeout(mp3_progress);
            clearTimeout(mp3_progress_reap);
        }catch(e){
            // console.log(e);
        }
        try{
            mp.pause();
        }catch(e){
            // console.log(e);
        }
        mp3_progress='';
        mp3_progress_reap='';
        mp.index = 0;
        mp.stemindex = 0;
        mp.queinitindex = 0;
        mp.questionindex = 0;
        mp.childstemindex = 0;
        mp.playtimes=0;
        mp.childinitstemindex = 0;
        mp.url = "";
        mp.repeat = 1; //默认播放次数
        mp.curpeat = 1;//当前播放到第几次
        mp.url = "";
    }

    

    

    //普通MP3的播放的监听事件
    function mp3PlayEvent(){
        //播放时间的MP3的地址
        var location=$(this).attr("loc");
        var mp3=$(this).attr("mp3");
        var obj=this;
        if(mp3==undefined||mp3==""||mp3==null||mp3=='null'){
            mui.toast("音频不存在");
        }else{
            $(this).parent().find(".sy-click").attr("src","../../public/Homework/images/sy.gif");
            player = "single";
            // console.log(player)
            var options = {};
            options.id = "single";
            options.callback = function(){
                $(obj).parent().find(".sy-click:eq(0)").attr("src","../../public/Homework/images/sy.png");
            }
            // console.log(options)
            // console.log(location+"/"+mp3);
            mp = mp.change(mp,options);
            mp.play(location+"/"+mp3);
        }        
    }

    //回放的播放的监听事件
    function mp3BackPlayEvent(){
        //播放时间的MP3的地址
        var id=$(this).attr("bid");
        var type=$(this).attr("type");
        var location=$(this).attr("loc");
        // console.log("start");
        mui.ajax("../Public/playBack",
            {
            data:{
                id:id,
                type:type,
                ran:Math.random()
            },
            dataType:'json',//服务器返回json格式数据
            type:'post',//HTTP请求类型
            timeout:50000,
            async:false,
            success:function(data){
                if(data.filename==""||data.filename==null||data.filename==undefined){
                    mui.toast("请先进行录音");
                }else{
                    // console.log(location+data.filename);
                    var options = {};
                    options.id = "backmp3";
                    options.callback = function(){
                        
                    }
                    mp = mp.change(mp,options);
                    mp.play(location+data.filename);  
                }
                
            },
            error:function(xhr,type,errorThrown){
                //异常处理；
                mui.toast("网络出错,请稍等一会在尝试");
            }
        });
    }


    

    //进行内容的填充
    var setpagecontent=function(data,pageitems){
        //听力训练的展示
        if(data.type=='1'){
            if(data.parent.question.typeid=='1'){
                //调用听力训练的选择试题
                var pagecontent=choiceExamsQuestion(data.parent);
                $(pageitems).html(pagecontent);
            }else if(data.parent.question.typeid=='2'){
                //调用听力训练的填空试题
                var pagecontent=blankExamsQuestion(data.parent);
                $(pageitems).html(pagecontent);
            }else if(data.parent.question.typeid=='3'){
                //表用听力训练的判断试题
                var pagecontent=trueOrFalseExamsQuestion(data.parent);
                $(pageitems).html(pagecontent);
            }else if(data.parent.question.typeid=='4'){
                //表用听力训练的排序试题
                var pagecontent=sequenceExamsQuestion(data.parent);
                $(pageitems).html(pagecontent);
            }
        }

        //单词测试的展示
        if(data.type=='0'){
            if(data.parent.question.typeid=='2'){
                //调用单词测试的单词拼写
                var pagecontent=wordSpellQuestion(data.parent);
                $(pageitems).html(pagecontent);
            }else if(data.parent.question.typeid=='0'){
                //调用单词测试的听音选词
                var pagecontent=wordChooseQuestion(data.parent);
                $(pageitems).html(pagecontent);
            }else if(data.parent.question.typeid=='3'||data.parent.question.typeid=='1'){
                //表用听力训练英汉互译
                var pagecontent=wordTranslateQuestion(data.parent);
                $(pageitems).html(pagecontent);
            }
        }

        //单词跟读的展示
        if(data.type=='2'){
            //调用单词跟读的
            var pagecontent=wordAloundQuestion(data.question);
            $(pageitems).html(pagecontent);
        }

        //单词跟读的展示
        if(data.type=='3'){
            //调用课文跟读
            var pagecontent=textAloundQuestion(data.question);
            $(pageitems).html(pagecontent);
        }
        //var objs=stemdiv.getElementsByClassName("tigan");
        $(pageitems).find(".tigan").find("img").attr("style","width:80%;");
    }

    //进行相应数据
    var getResponse=function(url,data,pageitems,isasync,index,page){
    	var respose="";
		listendata=[];
		mui.ajax(url,
			{
			data:data,
			dataType:'json',//服务器返回json格式数据
			type:'post',//HTTP请求类型
			//timeout:4000,//超时时间设置为10秒；
			async:isasync,
			success:function(data){
                setpagecontent(data,pageitems);
                page[index]=1;
                pagedata[index]=data;
                try{
                    page_tts[index]=data.parent.questts;
                }catch(e){
                    page_tts[index]=[];
                }
                var obj={};
                if(data.children==null||data.children=='null'||data.children==''||data.children==undefined){
                    obj.type=0;
                }else{
                    obj.type=1;
                }
                if(data.type==1){
                    obj.stemid=data.parent.question.stemid;
                }else{
                    obj.stemid='0';
                }
                page_obj[index]=obj;
			},
			error:function(xhr,type,errorThrown){
				//异常处理；
				mui.toast("网络请求出错请等会儿再试试");
				return errorThrown;
			}
	    });
    }

    studentContent.getResponse=getResponse;
    return studentContent;
});
