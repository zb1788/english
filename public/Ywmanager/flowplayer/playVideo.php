<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>网页视频播放器</title>
<meta name="keywords" content="网页播放器" />
<meta name="description" content="网页播放器" />
<link rel="shortcut icon" href="/favicon.ico" >
<style type="text/css">
</style>
</head>

<body>
<a href="javascript:void(0);" style="display: block;width:600px;height:410px" id="player"></a>
<!-- 我爱播放器(52player.com)/代码开始 -->
<script type="text/javascript">
//alert('uploads/'+GetQueryString("filepath"));
function GetQueryString(name)
{
     var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
     var r = window.location.search.substr(1).match(reg);
     if(r!=null)return  unescape(r[2]); return null;
}

</script>
<script type="text/javascript" src="flowplayer-3.2.11.min.js"></script>
<script type="text/javascript">
loadmp4(decodeURI(GetQueryString("filepath")));
function loadmp4(url){
    flowplayer("player", "flowplayer.commercial-3.2.14-0.swf", {
        clip: {
            autoPlay: true,
            provider: 'pseudostreaming',
            urlResolvers: 'brselect',
            bitrates: [{
                url: encodeURIComponent(url),
                bitrate: 300,
                isDefault: true,
                label: "300 k"
            }]
        },
        screen: {
            height: 518
        },
        plugins: {
            menu: {

                url: "flowplayer.menu-3.2.11.swf",
                items: [{
                    label: "选择码率:",
                    enabled: true
                }]
            },
            brselect: {
                url: "flowplayer.bitrateselect-3.2.13.swf",

                menu: true,

                onStreamSwitchBegin: function(newItem, currentItem){
                    $f().getPlugin('content').setHtml("Will switch to: " + newItem.streamName +
                    " from " +
                    currentItem.streamName);
                },
                onStreamSwitch: function(newItem){
                    $f().getPlugin('content').setHtml("Switched to: " + newItem.streamName);
                }
            },
            pseudostreaming: {
                url: "flowplayer.pseudostreaming-3.2.11.swf"
            },
            content: {
                /*url: "http://releases.flowplayer.org/swf/flowplayer.content-3.2.8.swf",*/
                url: "flowplayer.content-3.2.8.swf",
                top: 0,
                left: 0,
                width: 600,
                height: 410,
                backgroundColor: 'transparent',
                backgroundGradient: 'none',
                border: 0,
                textDecoration: 'outline',
                style: {
                    body: {
                        fontSize: 14,
                        fontFamily: 'Arial',
                        textAlign: 'center',
                        color: '#ffffff'
                    //color: '#000000'
                    }
                }
            },
            controls: {
                tooltips: {
                    buttons: true,
                    fullscreen: '\u5168\u5C4F',
                    fullscreenExit: '\u9000\u51FA\u5168\u5C4F',
                    mute: '\u9759\u97F3',
                    unmute: '\u53D6\u6D88\u9759\u97F3',
                    stop: '\u505C\u6B62',
                    play: '\u5F00\u59CB',
                    volume: '\u97F3\u91CF',
                    next: '\u4E0B\u4E00\u4E2A',
                    previous: '\u4E0A\u4E00\u4E2A',
                    pause: '\u6682\u505C'
                }
            }
        }
    });
}
</script>
<!-- 我爱播放器(52player.com)/代码结束 -->



</body>
</html>




