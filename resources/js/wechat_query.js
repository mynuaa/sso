function getWechatLoginStatus(){
	if(document.hidden)return;
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
			if(d.uid>0)window.location.reload();
		}
	});
}
setInterval(getWechatLoginStatus,3000);