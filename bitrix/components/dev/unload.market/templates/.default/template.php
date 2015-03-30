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
		<tr><th>#</th><th>товар</th><th>Торговые предложения</th></tr>
		<?foreach ($arResult["PRODUCTS"] as $key => $arProduct):?>
		<tr<?=($key%2==0?' class="warning"':'')?>>
			<td><?=($key+1)?></td>
			<td width="250">
				<div class="row">
					<div class="col-sm-12">
						<div class="thumbnail">
							<?$y=CFile::ResizeImageGet(
								$arProduct["PREVIEW_PICTURE"],
								array("width" => 250, "height" => 250),
								BX_RESIZE_IMAGE_PROPORTIONAL,
								true
							);?>
							<a href="<?=$arProduct["DETAIL_PAGE_URL"]?>" target="_blank"><img src="<?=$y["src"]?>" alt="<?=$arProduct["NAME"]?>"></a>
							<div class="caption">
								<h4><?=$arProduct["NAME"]?></h4>
							</div>
						</div>
					</div>
				</div>
			</td>
			<td>
				<?if (!empty($arProduct["OFFERS"])):?>
					<table class="table" style="background: transparent; width:100%">
						<tr><th>#</th><th>Картинка</th><th>Торговое предложение</th><th>Цвет</th><th>Размер</th><th>Маркет</th></tr>
						<?foreach ($arProduct["OFFERS"] as $cell => $arOffer):?>
						<tr>
							<td width="3%"><?=$cell+1;?></td>
							<td width="7%">
								<?$y=CFile::ResizeImageGet(
									$arOffer["PREVIEW_PICTURE"],
									array("width" => 50, "height" => 50),
									BX_RESIZE_IMAGE_EXACT,
									true
								);?>
								<a href="<?=CFile::GetPath($arOffer["PREVIEW_PICTURE"]);?>" data-offer_name="<?=$arOffer["NAME"]?>" class="offer-image"><img src="<?=$y["src"]?>" alt="<?=$arOffer["NAME"]?>"></a>
							</td>
							<td width="50%"><?=$arOffer["NAME"]?></td>
							<td width="15%"><?=$arOffer["COLOR"]?></td>
							<td width="15%"><?=$arOffer["SIZE"]?></td>
							<td width="10%">
								<input type="checkbox" class="sku_market" <?=($arOffer["MARKET"]=="Y"?' checked':'')?>>
								<input type="hidden" name="<?=($arOffer["MARKET"]=="Y"?'MARKET_Y[]':'MARKET_N[]')?>" value="<?=$arOffer["ID"]?>">
							</td>
						</tr>
						<?endforeach;?>
					</table>
				<?else:?>
					<table class="table" style="background: transparent;width: 100%">
						<tr><th colspan="5" width="90%">&nbsp;</th><th width="10%">Маркет</th></tr>
						<tr>
							<td colspan="5" width="90%">&nbsp;</td>
							<td width="10%">
								<input type="checkbox" class="product_market" <?=($arProduct["MARKET"]=="Y"?' checked':'')?>>
								<input type="hidden" name="<?=($arProduct["MARKET"]=="Y"?'MARKET_PRODUCT_Y[]':'MARKET_PRODUCT_N[]')?>" value="<?=$arProduct["ID"]?>">
							</td>
						</tr>
					</table>
				<?endif;?>
			</td>
		</tr>
		<?endforeach;?>
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