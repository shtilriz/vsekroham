$(function() {
	$('form[name=addReview]').on('submit',function(e) {
		var form = $(this);
		var sID = form.find('select[name=section] option:selected').val();
		var eID = form.find('select[name=element] option:selected').val();
		if (sID == 0 || eID == 0) {
			e.preventDefault();
			alert('Выберите раздел и товар, к которому нужно привязать отзыв');
		}
	});

	$('form[name=addReview] select[name=section]').on('change',function() {
		var sID = $(this).find('option:selected').val();
		if (sID > 0) {
			$.get('',{sID:sID},function(data) {
				$('#element').html(data);
			});
		}
	});

	var sID = $('form[name=parserReviews] input[name=sID]').val();
	var elID = $('form[name=parserReviews] input[name=elID]').val();
	if (sID > 0) {
		$.get('',{sID:sID,elID:elID},function(data) {
			$('#element').html(data);
		});
	}
});