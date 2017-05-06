/*author shimin*/
//快商通
document.writeln("<script type=\"text/javascript\" src=\"http://vip5-kf9.kuaishang.cn/bs/ks.j?cI=363296&fI=63690\" charset=\"utf-8\"></script>");
var onKST = openZoosUrl = function(text){
	ksChatLink = 'http://vip5-kf9.kuaishang.cn/bs/im.htm?cas=55208___363296&fi=63690';
	
	//验证参数是否存在
	function checkQueryString(params,name){
		if(!params)return false;
		return new RegExp("(^|&)"+ name +"=([^&]*)(&|$)", "i").test(params);
	}
	//获取URL参数值
	function getQueryString(url,name) {
		var index = url.indexOf('?');
		if(index==-1)return '';
		url=url.substr(index+1,url.length);
		var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
		var r = url.match(reg);
		if (r != null) return unescape(r[2]);
		return '';
	}
	var openNewChatWin;
	var localArr = ksChatLink.split("?");
	localArr.push("");
	if(typeof ksUserDefinedOpenNewChatWin!='undefined' && ksUserDefinedOpenNewChatWin==true){
		openNewChatWin = true;
	}else if(checkQueryString(localArr[1],'ism')){
		openNewChatWin = false;
	}else{
		openNewChatWin = true;
	}
	//打开快商通聊天窗口链接
	function ksOpenLink(){
		var appendTailUrl='';
		try{
			var cas = getQueryString(ksChatLink,'cas');
			if(cas){
				var vi='';
				var dc = document.cookie.match(new RegExp('(^| )' + cas+'_KS_'+cas + '=([^;]*)(;|$)'));
				if (dc != null){
					vi = unescape(dc[2]);
				}
				if(vi){
					appendTailUrl += '&vi='+vi;
				}
			}
		}catch(e){}
		var ref="";
		try{if(opener.document.referrer.length>0){ref=opener.document.referrer;}}catch(e){ref=document.referrer;}
		if(!ref || ref.length==0){ref=document.referrer;}
		//对话网址
		appendTailUrl += '&dp='+encodeURIComponent(window.location.href);
		//访客来源
		if(ref)appendTailUrl+='&ref='+encodeURIComponent(ref);
		//对话标识
		if(text)appendTailUrl+='&sText='+encodeURIComponent(text);
		if(ksChatLink.indexOf('?')==-1){appendTailUrl=appendTailUrl.substring(1)+'?';}
		ksChatLink+=appendTailUrl;
		//根据openNewChatWin设置打开聊天窗口
		if(!openNewChatWin){
			window.location.href=ksChatLink;
		}else{
			var ksWin = window.open(ksChatLink,'_blank');
			if(ksWin){
				try{ksWin.focus();}catch(e){} //将焦点定位到聊天窗口
			}
		}
	}
	//如果快商通代码有加载完成,则使用快商通默认的打开聊天窗口事件,否则使用自定义的打开事件
	if(typeof KS!='undefined'){
		var p = {};
		if(text)p['sText']=text;
		if(openNewChatWin)p['oTarget']='_blank';
		try{
			if(typeof KS.openChatWin=='function'){
				KS.openChatWin(p);
			}else if(typeof KS.openChatLink=='function'){
				KS.openChatLink(p);
			}else{
				ksOpenLink();
			}
		}catch(e){
			ksOpenLink();
		}
	}else{
		ksOpenLink();
	}
};
document.writeln("<link rel=\"stylesheet\" href=\"/swtimg/css/swtStyle.css\" />");
document.writeln("<script src=\"/js/jquery.easing.1.3.js\"></script>");

document.writeln("<div class=\"njCenter\" id=\"njCenter\">");
document.writeln("<a id=\"cenClose\" class=\"cenClose swtIcon absolute iBlock\" title=\"关闭\" target=\"_self\" href=\"javascript:void(0)\"></a>");
document.writeln("	<div class=\"njswtBg relative\">");
document.writeln("    	<div class=\"models absolute\" id=\"models\">");
document.writeln("        	<div class=\"model\" id=\"model3\"></div>");
document.writeln("          <div class=\"model\" id=\"model2\"></div>");
document.writeln("          <div class=\"model fadein\" id=\"model1\"></div>");
document.writeln("        </div>");
document.writeln("    <div class=\"njswtTxt\">");
document.writeln("    	<div class=\"tellFormcen\">");
document.writeln("        <input id=\"tell_numcen\" class=\"tell_numcen\" type=\"text\" value=\"输入您的电话号码\" onfocus=\"value=\'\'\" onblur=\"if(!value)value=defaultValue\"></input>");
document.writeln("        <a id=\"subCen_btn\" class=\"subCen_btn swtAni\" href=\"javascript:void(0)\">免费回电</a>");
document.writeln("    	</div>");
document.writeln("    </div>");
document.writeln("    </div>");
/*document.writeln("    <div class=\"njswtTxt\">");
document.writeln("    	<div class=\"tellFormcen\">");
document.writeln("        <input id=\"tell_numcen\" class=\"tell_numcen\" type=\"text\" value=\"输入您的电话号码\" onfocus=\"value=\'\'\" onblur=\"if(!value)value=defaultValue\"></input>");
document.writeln("        <a id=\"subCen_btn\" class=\"subCen_btn swtAni\" href=\"javascript:void(0)\"><em class=\"iBlock swtIcon\"></em>免费回电</a>");
document.writeln("    	</div>");
document.writeln("        <p>我们将立即回电，该通话对您免费，并严格保密，请放心接听！手机请直接输入，座机前加区号。</p>");
document.writeln("    </div>");*/
document.writeln("    <div class=\"njTextarea\">");
document.writeln("    	<div class=\"njChatBox clearfix\">");
document.writeln("        <textarea class=\"textarea\">在此输入可直接对话,快速咨询...</textarea>");
document.writeln("        <a href=\"javascript:void(0)\" class=\"KSTsendTxtBtn swtAni\"><p>发送<br/><small>Enter</small></p></a>");
document.writeln("        </div>");
document.writeln("    </div>");
document.writeln("</div>");

document.writeln("<div class=\"swtBottom\">");
document.writeln("	<div class=\"title\">");
document.writeln("    	<span>快速咨询</span>");
document.writeln("        <a href=\"javascript:void(0)\" class=\"closeBtn\"></a>");
document.writeln("    </div>");
document.writeln("    <div class=\"chatBox\">");
document.writeln("    	<div class=\"welcomeWord\">您好，请问有什么可以帮助您？");
document.writeln("</div>");
document.writeln("    </div>");
document.writeln("    <div class=\"chatTxt clearfix\">");
document.writeln("    	<div class=\"txt fl\"><textarea  placeholder=\"在此输入可直接对话\"></textarea></div>");
document.writeln("        <a href=\"javascript:void(0)\"  class=\"txtSubmit fl\" target=\"_self\"><span>发送</span><span>Enter</span></a>");
document.writeln("    </div>");
document.writeln("</div>");

function tabModels(){
	function fadeIn(e) {e.className = "model fadein"	};
	function fadeOut(e){e.className = "model"};
	cur_img = $(".models .model").length-1;
	function turnImgs(imgs) {
		var imgs = $("#models").children(".model");
		if (cur_img == 0) 
		{
		  fadeOut(imgs[cur_img]);
		  cur_img = imgs.length - 1;
		  fadeIn(imgs[cur_img]);
		} 
		else 
		{
		  fadeOut(imgs[cur_img]);
		  fadeIn(imgs[cur_img - 1]);
		  cur_img--;
		}
	  }
	setInterval(turnImgs, 5000);
	}

function openMdivM(){
	$("#njCenter").fadeIn(1000);
	tabModels();
	
}
setTimeout("openMdivM()",10000);//10000

$(document).ready(function(){
	$("#cenClose").click(function(){
		$(".njCenter").addClass("off");		
		setTimeout(function(){
					$(".swtBottom").animate({width:185+"px",height:158+"px"},600)
				},1300);
	});	

	$(".swtBottom .txtSubmit").on("click",function(){
			var swt="http://vip5-kf9.kuaishang.cn/bs/im.htm?cas=55208___363296&fi=63690&sText=dibuxuanfu";
			window.open(swt);
	})
	
	/*$(".swtBottom .txtSubmit").on("click",function(){
			getSendTxt($(".swtBottom"));
		})*/
	//右下角
	$(".swtBottom").find(".closeBtn").on("click",function(){
			$(".swtBottom").animate({width:0,height:0},800,function(){
					setTimeout(function(){
							$(".njCenter").removeClass("off");
						},30000)
				})
		});
	$(".swtBottom").find("textarea").keyup(function (e) {  
	   if (e.which == 13){  
			$(".swtBottom .txtSubmit").click();  
	   }  
	});	
	//去除字符串前后空格
	function trim(str){
	return (str||"").replace(/^\s+|\s+$/g, "");
	};
	//获取用户输入框输入的文字，然后发送到快商通主对话窗口
	var defaultTxt ="在此输入可直接对话,快速咨询...";
	function getSendTxt(_this){
			var text = _this.find(" textarea").val();
			var sendTxt =trim( _this.find(" textarea").val());
			if(sendTxt==defaultTxt){
					sendTxt="";
					_this.find(" textarea").val(defaultTxt);
				}else{
						_this.find(" textarea").val(sendTxt);	
					}
			console.log(sendTxt);
			console.log(sendTxt.length);
			KS.openChatWin({cv:sendTxt,sText:'新版弹窗'});			
		};
	
	$(".KSTsendTxtBtn").on("click",function(){
			getSendTxt($(".njChatBox"));

		});
	$(".njChatBox").find("textarea").keyup(function (e) {  
               if (e.which == 13){  
                 getSendTxt($(".njChatBox"));
               }  
            });	
	$(".textarea").on("focus",function(){
			var txt = $(this).val();
			if(txt==defaultTxt){
				$(this).val("");
			}
		});
	$(".textarea").on("blur",function(){
			var txt = $(this).val();
			if(txt==""){
					$(this).val(defaultTxt);
				}
		});
	
	})
document.writeln("<script src=\"/swtimg/right.js\"></script>");
function GetRandomNum(Min,Max)
{   
var Range = Max - Min;   
var Rand = Math.random();   
return(Min + Math.round(Rand * Range));   
}   
var num = GetRandomNum(5,15);
$(document).ready(function() { 
	document.getElementById("njzfRnum").innerHTML=num;
});
/* 判断用户使用的是否是平板ipad或者iopd*/
//移动设备访问跳转
function is_mobile() {
 	var regex_match = /(nokia|iphone|android|motorola|^mot-|softbank|foma|docomo|kddi|up.browser|up.link|htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte-|longcos|pantech|gionee|^sie-|portalmmm|jigs browser|hiptop|^benq|haier|^lct|operas*mobi|opera*mini|320x320|240x320|176x220)/i;
 	var u = navigator.userAgent; 
	var result = regex_match.exec(u);
	if (null == result) {
		return false
	} else {
		return true
	}
}
//alert(is_mobile());
if (is_mobile()) {
	document.location.href= 'http://wap.025lx.com/'+"?TZ";
}

(function(){
		var localUrl = window.location.pathname;
		console.log(localUrl);
		if(localUrl != "/zt/2017nldqh/"){
       	document.writeln("<script src=\"/\huodongJs/huodong.js\"><\/script>");
	   }
	})();