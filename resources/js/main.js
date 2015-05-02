// 将fid中的标签页切换到gid
function switchTo(fid,gid){
	var frame=document.querySelectorAll("#frame"+fid);
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
})(window);