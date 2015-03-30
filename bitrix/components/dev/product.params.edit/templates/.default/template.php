<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<div id="messageBox">
	<?if (!empty($arResult["MESSAGE"])):?>
	<p class="<?=($arResult["MESSAGE"]["SUCCESS"]=="Y"?'bg-success':'bg-warning')?>"><?=$arResult["MESSAGE"]["REPLY"]?></p>
	<?endif?>
</div>
<form class="form-horizontal" role="form" name="formParams" method="POST">
	<input type="hidden" name="formParams" value="Y">
	<div class="form-group">
		<label class="col-sm-3 control-label">Выберите раздел</label>
		<div class="col-sm-9">
			<select class="form-control" name="section">
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
					echo '<option'.($_REQUEST["section"]==$arSection["ID"]?' selected':'').' value="'.$arSection["ID"].'">'.$s.$arSection["NAME"].'</option>';
				}?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Выберите производителя</label>
		<div class="col-sm-9">
			<select class="form-control" name="maker">
				<option value=""></option>
				<?foreach ($arResult["MAKERS"] as $arMaker) {
					echo '<option'.($_REQUEST["maker"]==$arMaker["ID"]?' selected':'').' value="'.$arMaker["ID"].'">'.$arMaker["NAME"].'</option>';
				}?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">Название товара</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" name="q" value="<?=$_REQUEST["q"]?>">
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button type="submit" class="btn btn-primary">Отправить</button>
		</div>
	</div>
</form>

<?if (!empty($arResult["PRODUCTS"])):?>
<form class="form-horizontal" role="form" name="formProducts" method="POST">
	<input type="hidden" name="formProducts" value="Y">
	<input type="hidden" name="section" value="<?=$_REQUEST["section"]?>">
	<input type="hidden" name="maker" value="<?=$_REQUEST["maker"]?>">
	<table class="table table-bordered">
		<tr><th>#</th><th>Картинка</th><th>Название / Дата создания</th><th>Вес, грамм</th><th>Объем, м<sup>3</sup></th><th>Длина, мм</th><th>Ширина, мм</th><th>Высота, мм</th></tr>
		<?php
		foreach ($arResult["PRODUCTS"] as $key => $arItem) {
			echo '<tr>';
				echo '<td>'.($key+1).'</td>';
				$y=CFile::ResizeImageGet(
					$arItem["PREVIEW_PICTURE"],
					array("width" => 75, "height" => 75),
					BX_RESIZE_IMAGE_PROPORTIONAL,
					true
				);
				echo "<td><a href='{$arItem[DETAIL_PAGE_URL]}' target='_blank'><img src='{$y[src]}' alt='{$arItem[NAME]}'></a></td>";
				echo "<td><p><a href='{$arItem[DETAIL_PAGE_URL]}' target='_blank'>{$arItem[NAME]}</a></p><p>{$arItem[DATE_CREATE]}</p></td>";
				echo "<td><input class='form-control' type='text' name='WEIGHT[{$arItem[ID]}]' value='{$arItem[WEIGHT]}'></td>";
				echo "<td><input class='form-control VOLUME' type='text' value=''></td>";
				echo "<td><input class='form-control LENGTH' type='text' name='LENGTH[{$arItem[ID]}]' value='{$arItem[LENGTH]}'></td>";
				echo "<td><input class='form-control WIDTH' type='text' name='WIDTH[{$arItem[ID]}]' value='{$arItem[WIDTH]}'></td>";
				echo "<td><input class='form-control HEIGHT' type='text' name='HEIGHT[{$arItem[ID]}]' value='{$arItem[HEIGHT]}'></td>";
			echo '</tr>';
		}
		?>
	</table>
	<div class="form-group">
		<div class="col-sm-12">
			<button type="submit" class="btn btn-primary btn-lg pull-right">Сохранить изменения</button>
		</div>
	</div>
</form>

<div class="modal fade" id="OfferModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Закрыть</span></button>
				<h4 class="modal-title" id="myModalLabel">Modal title</h4>
			</div>
			<div class="modal-body">
				<img src="" alt="" class="img-responsive">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
			</div>
		</div>
	</div>
</div>
<?endif;?>