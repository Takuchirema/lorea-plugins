<?php
/**
 * Update each minute all friendly times
 *
 * @package ElggFriendlyTime
 */
?>

elgg.provide('elgg.friendly_time');

elgg.friendly_time = function(time) {
	
	//TODO friendly:time hook

	diff = new Date().getTime()/1000 - parseInt(time);

	minute = 60;
	hour = minute * 60;
	day = hour * 24;

	if (diff < minute) {
			return elgg.echo("friendlytime:justnow");
	} else if (diff < hour) {
		diff = Math.round(diff / minute);
		if (diff == 0) {
			diff = 1;
		}

		if (diff > 1) {
			return elgg.echo("friendlytime:minutes", [diff]);
		} else {
			return elgg.echo("friendlytime:minutes:singular", [diff]);
		}
	} else if (diff < day) {
		diff = Math.round(diff / hour);
		if (diff == 0) {
			diff = 1;
		}

		if (diff > 1) {
			return elgg.echo("friendlytime:hours", [diff]);
		} else {
			return elgg.echo("friendlytime:hours:singular", [diff]);
		}
	} else {
		diff = Math.round(diff / day);
		if (diff == 0) {
			diff = 1;
		}

		if (diff > 1) {
			return elgg.echo("friendlytime:days", [diff]);
		} else {
			return elgg.echo("friendlytime:days:singular", [diff]);
		}
	}
}

elgg.friendly_time.update = function() {
	$('.elgg-friendlytime').each(function(){
		friendlytime = elgg.friendly_time($(this).find('span').text())
		$(this).find('acronym').text(friendlytime);
	});
}

elgg.friendly_time.init = function() {
	elgg.friendly_time.update();
	setInterval(elgg.friendly_time.update, 1000*60); // each 60 sec
};

elgg.register_hook_handler('init', 'system', elgg.friendly_time.init);
