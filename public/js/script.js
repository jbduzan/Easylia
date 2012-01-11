$(document).ready(function() {
	// slideshow with play/pause
		var slideShow = $('.playPauseExample').slideShow({
			interval: 3
		});
		// now add logic to play/pause button
		$('.playPauseExample a.togglePlayback').click(function() {
			if (slideShow.isPlaying()) {
				$(this).html('<img src="images/play.png" alt="" />');
			} else {
				$(this).html('<img src="images/stop.png" alt="" />');
			}
			slideShow.togglePlayback();
			return false;
		});
});