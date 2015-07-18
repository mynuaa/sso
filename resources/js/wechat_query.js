function getWechatLoginStatus(){
	if(document.hidden)return;
	if(!document.getElementById("group3"))return;
	if(document.getElementById("group3").className.indexOf("group-current")<0)return;
	ajax({
		url:"?action=login"+(oauth?"&inoauth":""),
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
			else if(d.uid.length==1){
				if(oauth){
					document.cookie="myauth_oauth_querycode="+queryCode;
					window.location.reload();
				}
				else window.location.href=redirect_uri;
			}
			else if(d.uid.length==2){
				var uid=d.uid.join(":");
				window.location.href="?page=choose&uid="+uid+"&code="+queryCode+"&redirect_uri="+bredirect_uri+(oauth?"&inoauth":"");
			}
		}
	});
}
setInterval(getWechatLoginStatus,2000);