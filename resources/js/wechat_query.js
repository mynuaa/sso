function getWechatLoginStatus(){
	if(document.hidden)return;
	if(document.getElementById("group3").className.indexOf("group-current")<0)return;
	ajax({
		url:"?action=login",
		method:"POST",
		content:JSON.stringify({
			type:"wechat",
			queryCode:queryCode,
			action:"get"
		}),
		success:function(d){
			if(d=="")return;
			d=JSON.parse(d);
			if(d.uid>0)window.location.href=redirect_uri;
		}
	});
}
setInterval(getWechatLoginStatus,2000);