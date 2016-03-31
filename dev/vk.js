var page = require('webpage').create(),
	system = require('system'),
	url = system.args[1];

page.open(url, function (status) {
	page.injectJs('/jquery.min.js');
	page.injectJs('/json2.min.js');

	var returnObject = page.evaluate(function() {
		var posts = {};
		$('#public_wall #page_wall_posts .post').each(function (i) {
			if (i > 5)
				return false;
			var post = $(this);
			posts[i] = {
				wall_post_text: post.find('.wall_post_text').text(),
				page_post_queue_wide: post.find('.page_post_queue_wide img').attr('src'),
				time: post.find('.rel_date').attr('time'),
				like: post.find('.post_like .post_like_count').text(),
				repost: post.find('.post_share .post_share_count').text()
			}
		});

		return JSON.stringify(posts);
	});

	console.log(returnObject);

	phantom.exit();
});