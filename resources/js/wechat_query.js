function getWechatLoginStatus(){
	if(document.hidden)return;
	if(!document.getElementById("group3"))return;
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
			if(!d)return;
			if(d.uid==-1)return;
			else if(d.uid==0)window.location.href="?page=complete&code="+queryCode+"&redirect_uri="+redirect_uri;
			else if(d.uid.length==1)window.location.href=redirect_uri;
			else if(d.uid.length==2){
				var uid=d.uid.join(":");
				window.location.href="?page=choose&uid="+uid+"&code="+queryCode+"&redirect_uri="+bredirect_uri;
			}
		}
	});
}
setInterval(getWechatLoginStatus,2000);