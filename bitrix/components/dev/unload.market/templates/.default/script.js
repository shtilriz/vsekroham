$(function () {
	$('form[name=formParams] button').on('click', function (e) {
		e.preventDefault();
		$('#messageBox').empty();
		var form = $(this).closest('form'),
			section = form.find('select[name=section] option:selected').val(),
			maker = form.find('select[name=maker] option:selected').val();
		if (parseInt(section, 10) > 0 || parseInt(maker, 10) > 0) {
			form.submit();
		}
		else {
			$('#messageBox').html('<p class="bg-warning">Выберите раздел и(или) производителя.</p>');
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
});