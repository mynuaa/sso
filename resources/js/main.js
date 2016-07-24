function ajax(a){
	if(!a||!a.url)return false;
	var xhr=new XMLHttpRequest();
	xhr.open(a.method,a.url,true);
	xhr.withCredentials=true;
	xhr.setRequestHeader("Content-Type",a.type?a.type:"application/json");
	xhr.onreadystatechange=function(){
		if(xhr.readyState==4){
			switch(xhr.status){
				case 200:(a.success?a.success(xhr.responseText):null);break;
				default:(a.failed?a.failed(xhr.status,a.url):null);break;
			}
		}
	}
	xhr.timeout=a.wait?a.wait:20000;
	xhr.ontimeout=function(){return false;}
	xhr.send(a.content?a.content:"");
}
// 将fid中的标签页切换到gid
function switchTo(fid,gid){
	var frame=document.getElementById("frame"+fid);
	if(!frame){
		console.error("Error: frame with id = fid not found.");
		return;
	}
	var tabs=document.querySelectorAll(".tabs>.tab",frame);
	var groups=document.querySelectorAll(".groups>.group",frame);
	if(gid<=0||gid>tabs.length){
		console.error("Error: gid overflow.");
		return;
	}
	var count=tabs.length;
	for(var i=0;i<count;i++){
		var k=i+1;
		if(k==gid){
			tabs[i].classList.add("tab-current");
			groups[i].classList.add("group-current");
		}
		else{
			tabs[i].classList.remove("tab-current");
			groups[i].classList.remove("group-current");
		}
	}
}
function checkValid(dom) {
	var param=dom.getAttribute("check-valid"),
		value=dom.value;
	if(value=="")return;
	ajax({
		url:"/sso/?action=check&param="+param+"&value="+value,
		method:"GET",
		success:function(d){
			d=JSON.parse(d);
			if(d.msg==""){
				dom.parentNode.className=dom.parentNode.className.replace(" failed","");
				dom.parentNode.removeAttribute("data-msg");
			}
			else{
				dom.parentNode.setAttribute("data-msg",d.msg);
				dom.parentNode.className+=" failed";
			}
		}
	});
}
(function(window){
	var frames=document.querySelectorAll(".frame");
	for(var i=0;i<frames.length;i++){
		var fid=frames[i].id.replace("frame","");
		var tabs=document.querySelectorAll(".tabs>.tab",frames[i]);
		for(var j=0;j<tabs.length;j++){
			var gid=tabs[j].id.replace("tab","");
			tabs[j].addEventListener("click",(function(a,b){
				return function(){
					switchTo(a,b);
				};
			})(fid,gid));
		}
	}
	var checklist=document.querySelectorAll("[check-valid]");
	for(var i=0;i<checklist.length;i++){
		checklist[i].onfocus=function(){this.parentNode.className=this.parentNode.className.replace(" failed","")};
		checklist[i].onblur=function(){checkValid(this)};
	}
})(window);
