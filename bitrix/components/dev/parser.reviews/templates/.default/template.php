<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<section id="parser">

<form class="form-horizontal" role="form" method="post" name="parserReviews">
	<input type="hidden" name="getParser" value="Y" />
	<div class="row">
		<div class="col-sm-10">
			<input type="text" class="form-control" placeholder="Введите адрес страницы" name="<?=(strlen($arParams["LINK_INPUT_NAME"])>0?$arParams["LINK_INPUT_NAME"]:'link')?>" value="<?=$_POST[(strlen($arParams["LINK_INPUT_NAME"])>0?$arParams["LINK_INPUT_NAME"]:'link')]?>">
		</div>
		<div class="col-sm-2">
			<button type="submit" class="btn btn-primary">Парсить</button>
		</div>
	</div>
</form>

<?if (count($arResult["SUCCESS"]) > 0):?>
	<div class="info-messages">
		<p class="bg-success">
			<?=implode("<br/>", $arResult["SUCCESS"])?>
		</p>
	</div>
<?endif;?>

<?if (count($arResult["ERRORS"]) > 0):?>
	<div class="info-messages">
		<p class="bg-danger">
			<?=implode("<br/>", $arResult["ERRORS"])?>
		</p>
	</div>
<?elseif(count($arResult["PARSING"]) > 0):?>
	<form name="addReview" enctype="multipart/form-data" method="post" class="form-horizontal">
		<input type="hidden" name="saveForm" value="Y" />
		<input type="hidden" name="service" value="<?=$arResult["SERVICE"]?>">
		<h2>Получены следующие данные:</h2>

		<div id="features">
			<table class="table table-bordered">
				<tr><th>#</th><th>Отзыв</th><th>Добавить</th></tr>
				<?foreach ($arResult["PARSING"] as $key => $arReview) {?>
					<input type="hidden" name="review[<?=$key?>][DATE_REVIEW]" value="<?=$arReview["date"]?>">
					<tr<?=(($key+1)%2==0?' class="warning"':'')?>>
						<td style="vertical-align: middle; text-align: center"><?=$key+1?></td>
						<td>
							<div class="form-group">
								<label class="col-sm-2 control-label">Имя</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="review[<?=$key?>][NAME]" value="<?=$arReview["name"]?trim($arReview["name"]):"Пользователь скрыл свои данные"?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Оценка</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="review[<?=$key?>][<?=$arParams["RATING"]?>]" value="<?=$arReview["rating"]?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Достоинства</label>
								<div class="col-sm-10">
									<textarea class="form-control" name="review[<?=$key?>][<?=$arParams["WORTH"]?>]"><?=trim($arReview["worth"])?></textarea>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Недостатки</label>
								<div class="col-sm-10">
									<textarea class="form-control" name="review[<?=$key?>][<?=$arParams["LIMITATIONS"]?>]"><?=trim($arReview["limitations"])?></textarea>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Комментарий</label>
								<div class="col-sm-10">
									<textarea class="form-control" name="review[<?=$key?>][<?=$arParams["COMMENT"]?>]"><?=trim($arReview["comment"])?></textarea>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Кол-во лайков</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="review[<?=$key?>][<?=$arParams["LIKE"]?>]" value="<?=$arReview["like"]?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Кол-во дизлайков</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="review[<?=$key?>][<?=$arParams["DIZLIKE"]?>]" value="<?=$arReview["dizlike"]?>">
								</div>
							</div>
						</td>
						<td style="vertical-align: middle">
							<div class="form-group">
								<div class="col-sm-offset-2 col-sm-10">
									<div class="checkbox">
										<label>
											<input type="checkbox" name="review[<?=$key?>][ADD]" value="Y" checked> добавить
										</label>
									</div>
								</div>
							</div>
						</td>
					</tr>
				<?}?>
			</table>
		</div>
		<h3>Добавить к товару:</h3>
		<div class="form-group">
			<label class="col-sm-2 control-label">Выберите раздел:</label>
			<div class="col-sm-4">
				<select name="section" class="form-control">
					<option value=""></option>
					<?foreach ($arResult["SECTIONS"] as $arSection) {
						if ($arSection["DEPTH_LEVEL"] == 1)
						{
							$s = '';
						}
						elseif ($arSection["DEPTH_LEVEL"] == 2) {
							$s = '&nbsp;.&nbsp;&nbsp;.&nbsp;';
						}
						elseif ($arSection["DEPTH_LEVEL"] == 3) {
							$s = '&nbsp;.&nbsp;&nbsp;.&nbsp;&nbsp;.&nbsp;';
						}
						echo '<option'.($arSection["SELECTED"]?' selected':'').' value="'.$arSection["ID"].'">'.$s.$arSection["NAME"].'</option>';
					}?>
				</select>
			</div>
		</div>

		<div id="element" class="form-group"></div>
		<div class="form-group">
			<div class="col-sm-4 col-sm-offset-2">
				<button type="submit" class="btn btn-primary btn-lg">Сохранить отзывы</button>
			</div>
		</div>
	</form>
<?endif;?>
</section>