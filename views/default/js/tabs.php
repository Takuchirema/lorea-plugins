function tabs(tabsId, divId) {


function setTab(a, url, div) {
	var liL = a.parentNode.parentNode.getElementsByTagName('li');
	for(var i=0; i<liL.length; i++) {
		$(liL[i]).removeClass('selected');
	}
	$(div).html('<div align=\"center\" class=\"ajax_loader\"></div>');
	$(div).load(url);
	$(a).parent().addClass('selected');
}

function bind(a, url, div) {
	a.addEventListener('click',function (e) { setTab(a, url, div) },false);
}



	var oUl = document.getElementById(tabsId);
	var oDiv = document.getElementById(divId);
	var aL = oUl.getElementsByTagName('a');

	for(var i=0; i<aL.length; i++) 
	{
		var a = aL[i];
		var url = a.href;
		bind(a, url, oDiv);
		a.href="##";
		if(i==0) setTab(a, url, oDiv);
	}					
     
	         
}


