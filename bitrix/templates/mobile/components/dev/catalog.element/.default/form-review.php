<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<form name="send-reviews">
	<input type="hidden" name="UF_PRODUCT" value="<?=$arResult["ID"]?>">
	<input type="hidden">
	<div id="messageBox"></div>
	<div class="my-review">
		<div class="review__title">Мой отзыв</div>
		<div class="review__item review__item__gray">
			<div class="review__item__title">Оценка</div>
			<div class="stars">
				<a href="#" class="star-gray"></a>
				<a href="#" class="star-gray"></a>
				<a href="#" class="star-gray"></a>
				<a href="#" class="star-gray"></a>
				<a href="#" class="star-gray"></a>
			</div>
			<input type="hidden" name="UF_RATE" value="">
		</div>
		<div class="review__item">
			<table class="like-inline noboder">
				<tbody>
					<tr>
						<td><label class="control-label block-label">Достоинства:</label></td>
						<td><textarea type="text" name="UF_WORTH" rows="1" class="form-control autosize"></textarea></td>
					</tr>
					<tr>
						<td><label class="control-label block-label">Недостатки:</label></td>
						<td><textarea type="text" name="UF_LACK" rows="1" class="form-control autosize"></textarea></td>
					</tr>
					<tr>
						<td><label class="control-label block-label">Комментарий:</label></td>
						<td><textarea type="text" name="UF_COMMENT" rows="4" class="form-control autosize"></textarea></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="review__item__bottom">
			<div class="review__item__inner">
				<table class="table2">
					<tr>
						<td>
							<div class="form-field">
								<div class="form-field__inputtext">
									<label>Имя</label>
									<input type="text" name="UF_NAME" class="inputtext" value="">
								</div>
							</div>
							<div class="form-field">
								<div class="form-field__inputtext">
									<label>E-mail</label>
									<input type="email" name="UF_EMAIL" class="inputtext">
								</div>
							</div>
							<p id="auth2vk">Или авторизуйтесь через
								<script src="//ulogin.ru/js/ulogin.js"></script>
								<noindex>
								<span id="uLogin" data-ulogin="display=small;fields=first_name,last_name,photo;providers=vkontakte,facebook,odnoklassniki,mailru,twitter,google,yandex;hidden=;redirect_uri=http://www.vsekroham.ru<?=$arResult["DETAIL_PAGE_URL"]?>#add-review"></span>
								</noindex>
							</p>
						</td>
						<td class="bdr">
							<div class="form-field__button">
								<?/*<a href="#" data-target="info-success" class="form-button button_font_big">Отправить отзыв</a>*/?>
								<button type="submit" class="form-button button_font_big">Отправить отзыв</button>
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</form>