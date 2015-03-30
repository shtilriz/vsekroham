var page = require('webpage').create(),
	system = require('system'),
	url = system.args[1];

page.open(url, function (status) {
	page.injectJs('/jquery.js');
	page.injectJs('/json2.js');

	var title = page.evaluate(function() {
		var reviews = [];
		$('.card__responses .card__responses__response').each(function(i) {
			var el = $(this);
			var nameblock = el.find('.card__responses__response__information .card__responses__response__information__author__wrapper');
			nameblock.find('span').remove();
			var name = nameblock.text();
			reviews[i] = {
				name: name,
				//date: el.find('.b-aura-review__title .b-aura-usergeo meta[itemprop=datePublished]').attr('content'),
				rating: el.find('.card__responses__response__information__rating .rating_good b').text(),
			}

			if (el.find('b:contains("Достоинства")').next('p').find('a').hasClass('more')) {
				reviews[i].worth = el.find('b:contains("Достоинства")').next('p').find('a').attr('full-text');
			}
			else {
				reviews[i].worth = el.find('b:contains("Достоинства")').next('p').text();
			}

			if (el.find('b:contains("Недостатки")').next('p').find('a').hasClass('more')) {
				reviews[i].limitations = el.find('b:contains("Недостатки")').next('p').find('a').attr('full-text');
			}
			else {
				reviews[i].limitations = el.find('b:contains("Недостатки")').next('p').text();
			}

			if (el.find('.card__responses__response__information').next('p').find('a').hasClass('more')) {
				reviews[i].comment = el.find('.card__responses__response__information').next('p').find('a').attr('full-text');
			}
			else {
				reviews[i].comment = el.find('.card__responses__response__information').next('p').text();
			}
		});
		return JSON.stringify(reviews);
	});

	console.log(title);

	phantom.exit();
});