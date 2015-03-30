$(function () {
	$('form[name=formParams] button').on('click', function (e) {
		e.preventDefault();
		$('#messageBox').empty();
		var form = $(this).closest('form'),
			section = form.find('select[name=section] option:selected').val(),
			maker = form.find('select[name=maker] option:selected').val(),
			q = form.find('input[name=q]').val();
		if (parseInt(section, 10) > 0 || parseInt(maker, 10) > 0 || q.length > 0) {
			form.submit();
		}
		else {
			$('#messageBox').html('<p class="bg-warning">Выберите раздел и(или) производителя и(или) введите название товара.</p>');
		}
	});

	$('.offer-image').on('click', function (e) {
		e.preventDefault();
		var el = $(this),
			offer_name = el.data('offer_name'),
			img_src = el.attr('href');
		$('#OfferModal h4.modal-title').text(offer_name);
		$('#OfferModal .modal-body img').attr('src', img_src);
		$('#OfferModal').modal('show');
	});

	$('form[name=formProducts] input[type=checkbox]').on('change', function () {
		var el = $(this);
		if (el.prop('checked')) {
			if (el.hasClass('sku_market')) {
				el.next('input').attr('name', 'MARKET_Y[]');
			}
			else if (el.hasClass('product_market')) {
				el.next('input').attr('name', 'MARKET_PRODUCT_Y[]');
			}
		}
		else {
			if (el.hasClass('sku_market')) {
				el.next('input').attr('name', 'MARKET_N[]');
			}
			else if (el.hasClass('product_market')) {
				el.next('input').attr('name', 'MARKET_PRODUCT_N[]');
			}
		}
	});

	$('form[name=formProducts] table tr').each(function () {
		var tr = $(this),
			LENGTH = parseInt(tr.find('input.LENGTH').val(), 10),
			WIDTH = parseInt(tr.find('input.WIDTH').val(), 10),
			HEIGHT = parseInt(tr.find('input.HEIGHT').val(), 10),
			VOLUME = (LENGTH * WIDTH * HEIGHT / 1000000000).toFixed(2);
		tr.find('input.VOLUME').val(VOLUME);
	});

	$('form[name=formProducts] input.VOLUME').on('keyup', function () {
		var input = $(this),
			val = input.val(),
			param = Math.pow(val,1/3) * 1000;
		input.closest('tr').find('input.LENGTH,input.WIDTH,input.HEIGHT').val(param.toFixed(0));
	});
});