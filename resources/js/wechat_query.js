function getWechatLoginStatus(){
	if(document.hidden)return;
	if(!document.getElementById("group3"))return;
	if(document.getElementById("group3").className.indexOf("group-current")<0)return;
	ajax({
		url:"?action=login"+(oauth?"&inoauth":""),
		method:"POST",
		content:JSON.stringify({
			type:"wechat",
			code:code,
			action:"get"
		}),
		success:function(d){
			if(d=="")return;
			d=JSON.parse(d);
			if(!d)return;
			if(d.uid==-1)return;
			else if(d.uid.length>=0){
				var uid=d.uid.join(":");
				window.location.href="?page=choose&uid="+uid+"&code="+code+(oauth?("&inoauth&origin="+origin):("&redirect_uri="+bredirect_uri));
			}
		}
	});
}
function getWechatBindStatus(){
	if(document.hidden)return;
	if(!document.getElementById("group3"))return;
	if(document.getElementById("group3").className.indexOf("group-current")<0)return;
	ajax({
		url:"?action=login&inoauth",
		method:"POST",
		content:JSON.stringify({
			type:"wechat",
			action:"querybind",
			uid:uid
		}),
		success:function(d){
			if(d=="")return;
			if(d=="1"){
				document.getElementById("bind-successful").style.display="block";
				setTimeout(function(){window.close()},3000);
			}
		}
	});
}
var isLogin=!document.getElementById("bind-successful");
setInterval(function(){
	if(isLogin)getWechatLoginStatus();
	else getWechatBindStatus();
},2000);