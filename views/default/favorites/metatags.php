<?php
	global $CONFIG;
?>
	function setFlagImage(uid, name) {
		document['img_flag_'+name].src = '<?php echo $CONFIG->wwwroot.'mod/flagged/graphics/star_'.$name.".png"; ?>';
	}
	function contentFlagged(uid, url, text) {
		var div = document.getElementById('flag_'+uid);
		div.innerHTML = text.replace("action/flagged/flag", "action/flagged/unflag")
		div.innerHTML = div.innerHTML.replace("star_to_on", "star_on")
		div.innerHTML = div.innerHTML.replace("flagContent", "unflagContent")
	}
	function contentUnflagged(uid, url, text) {
		var div = document.getElementById('flag_'+uid);
		div.innerHTML = text.replace("action/flagged/unflag", "action/flagged/flag")
		div.innerHTML = div.innerHTML.replace("star_to_on", "star_off")
		div.innerHTML = div.innerHTML.replace("unflagContent", "flagContent")
	}
	function flagContent(url, uid) {
		var div = document.getElementById('flag_'+uid);
		div.innerHTML = div.innerHTML.replace("star_off", "star_to_on")
		$.ajax({url:url, success: function() {
			var text = div.innerHTML;
			contentFlagged(uid, url, text)
		} })
	}
	function unflagContent(url, uid) {
		var div = document.getElementById('flag_'+uid);
		div.innerHTML = div.innerHTML.replace("star_on", "star_to_on")
		$.ajax({url:url, success: function() {
			var text = div.innerHTML;
			contentUnflagged(uid, url, text)
		} })
	}
