<div id="returnPolicy" class="p-warranty popup">
	<div class="popup__top">
		<a class="popup__close" href="#">X</a>
		<strong class="popup__title">Форма возврата товара</strong>
	</div>
	<form action="/warranty/ok.php" enctype="multipart/form-data" method="post" name="returnPolicy">
		<div class="popup__content">
			<table class="like-inline noboder">
				<tbody>
					<tr>
						<td class="first"><label class="control-label text-right block-label">Наименование организации (ФИО)</label></td>
						<td class="second"><input class="form-control" type="text" name="FIO" value=""></td>
						<td></td>
					</tr>
					<tr>
						<td class="first"><label class="control-label text-right block-label">Город</label></td>
						<td class="second"><input class="form-control" type="text" name="prop[CITY]" value=""></td>
						<td></td>
					</tr>
					<tr>
						<td class="first"><label class="control-label text-right block-label">Телефон</label></td>
						<td><input class="form-control" type="text" name="prop[PHONE]" value=""></td>
						<td></td>
					</tr>
					<tr>
						<td class="first"><label class="control-label text-right block-label">E-mail</label></td>
						<td class="second"><input class="form-control" type="text" name="prop[EMAIL]" value=""></td>
						<td></td>
					</tr>
					<tr>
						<td class="first"><label class="control-label text-right block-label">Наименование товара</label></td>
						<td class="second"><input class="form-control" type="text" name="prop[PRODUCT]" value=""></td>
						<td></td>
					</tr>
					<tr>
						<td class="first"><label class="control-label text-right block-label">Номер заказа</label></td>
						<td class="second"><input class="form-control" type="text" name="prop[ORDER_ID]" value=""></td>
						<td></td>
					</tr>
					<tr>
						<td class="first v-top"><label class="control-label text-right block-label">Прикрепить изображение</label></td>
						<td class="second">
							<input class="upload" type="file" name="file">
						</td>
						<td></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="popup__footer">
			<div class="form-field">
				<div class="form-field__button">
					<input type="submit" class="form-button button_type_submit" value="Отправить заявку">
				</div>
			</div>
		</div>
	</form>
</div>