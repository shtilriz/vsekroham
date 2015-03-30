var page = require('webpage').create(),
	system = require('system'),
	url = system.args[1];

page.open(url, function (status) {
	page.includeJs('http://yastatic.net/jquery/2.1.1/jquery.min.js');
	page.includeJs('http://yastatic.net/json2/2011-10-19/json2.min.js');

	var returnObject = page.evaluate(function() {
		var reviews = new Object,
			items = new Object;
		$('.b-aura-reviews .b-aura-review').each(function(i) {
			var el = $(this);
			items[i] = {
				name: el.find('.b-aura-review__title .b-aura-username').text(),
				date: el.find('.b-aura-review__title .b-aura-usergeo meta[itemprop=datePublished]').attr('content'),
				rating: el.find('.b-aura-review__rate .b-aura-rating').data('rate'),
				worth: el.find('.b-aura-review__verdict div[itemprop=pro]').html(),
				limitations: el.find('.b-aura-review__verdict div[itemprop=contra]').html(),
				comment: el.find('.b-aura-review__verdict div[itemprop=description]').html(),
				like: el.find('.b-aura-usergrade__votes .b-aura-usergrade__pro-num').text(),
				dizlike: el.find('.b-aura-usergrade__votes .b-aura-usergrade__contra-num').text()
			}
		});
		reviews.items = items;
		if ($('.b-pager a.b-pager__next').length > 0) {
			reviews.NEXT_PAGE = 'http://market.yandex.ru'+$('.b-pager a.b-pager__next').attr('href');
		}
		return JSON.stringify(reviews);
	});

	console.log(returnObject);

	phantom.exit();
});