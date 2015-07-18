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
			else if(d.uid.length>=0){
				var uid=d.uid.join(":");
				window.location.href="?page=choose&uid="+uid+"&code="+queryCode+(oauth?("&inoauth&origin="+origin):("&redirect_uri="+bredirect_uri));
			}
		}
	});
}
setInterval(getWechatLoginStatus,2000);