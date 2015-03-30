$(function() {
	if ($('.slider-block').length) {
		var mySlider = $('.slider-block'),
			itemW = mySlider.find('.slider img').width(),
			itemH = mySlider.find('.slider img').height(),
			sumW = itemW * mySlider.find('.slider img').size();

		var mySliderWrap = $('<div class="slider-inner"></div>').css({
			width: sumW+'px',
			height: itemH+'px',
			position: 'absolute',
			top: 0+'px',
			left: 0+'px',
		});

		$('.slider-block .slider').css({
			width: itemW+'px',
			height: itemH+'px',
			overflow: 'hidden',
			position: 'relative'
		}).wrapInner(mySliderWrap);
	}
});