<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
}
?>
<div class="assembling__form">
	<form action="/mebel/ajax.php" name="collector">
		<h4>Вызвать сборщика</h4>
		<div id="messageBox"></div>
		<div class="form-field form-field-row">
			<div class="form-field__inputtext">
				<label>Имя:</label>
				<input class="inputtext" type="text" name="FIO" value="">
			</div>

			<div class="form-field__inputtext">
				<label>Телефон:</label>
				<input class="inputtext" type="text" name="PHONE" value="">
			</div>
		</div>
		<div class="form-field">
			<div class="form-field__button">
				<input type="submit" class="form-button button_font_big" value="отправить" />
			</div>
		</div>
	</form>
</div>