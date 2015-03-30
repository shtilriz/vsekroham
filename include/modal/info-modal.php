<div class="info-success popup" id="info-modal">
	<div class="popup__top">
		<a href="#" class="popup__close">X</a>
		<strong class="popup__title"><?=($_POST["title"]?$_POST["title"]:'Сообщение')?></strong>
	</div>
	<div class="popup__content">
		<form action="#" class="">
			<div class="form-field">
				<div class="form-field__info">
					<?=($_POST["msg"])?>
				</div>
			</div>
			<div class="form-field">
				<div class="form-field__button">
					<input type="submit" value="Закрыть" class="form-button button_type_submit popup__button__close" />
				</div>
			</div>
		</form>
	</div>
	<div class="popup__footer"></div>
</div>