
var dataForWeixin={
	appId:	"",
	img:	"http://"+document.domain+"/diy/share.png",
	url:	"http://"+document.domain+"/",
	title:	"阳光城花满墅微楼书",
	desc:	"阳光城花满墅是……",
	fakeid:	"",
};
(function(){
	var onBridgeReady=function(){
		// 发送给好友; 
		WeixinJSBridge.on('menu:share:appmessage', function(argv){
			WeixinJSBridge.invoke('sendAppMessage',{
				"appid":dataForWeixin.appId,
				"img_url":dataForWeixin.img,
				"img_width":"120",
				"img_height":"120",
				"link":dataForWeixin.url,
				"desc":dataForWeixin.desc,
				"title":dataForWeixin.title
			}, function(res){});
		});
		// 分享到朋友圈;
		WeixinJSBridge.on('menu:share:timeline', function(argv){
			WeixinJSBridge.invoke('shareTimeline',{
			"img_url":dataForWeixin.img,
			"img_width":"120",
			"img_height":"120",
			"link":dataForWeixin.url,
			"desc":dataForWeixin.desc,
			"title":dataForWeixin.title
			}, function(res){});
		});
	};
    //document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {WeixinJSBridge.call('hideToolbar');});
})();