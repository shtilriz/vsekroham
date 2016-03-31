<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?//выводит всплывающее окно с расцветками?>

<?
$bShowOffers = false;
foreach ($arResult["OFFERS"] as $key => $arOffer){
	//if (!empty($arOffer["PROPERTIES"]["COLOR"]["VALUE"]) && $arOffer["CATALOG_QUANTITY"] > 0) {
	if (!empty($arOffer["PROPERTIES"]["COLOR"]["VALUE"])) {
		$bShowOffers = true;
		break;
	}
}
?>

<div id="modal-pr" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" class="modal fade modal-pr">
	<?$frame = $this->createFrame("modal-pr", false)->begin('');?>
	<div role="document" class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" data-dismiss="modal" aria-label="Close" class="modal__close"><span aria-hidden="true">×</span>
				</button>
				<h3 id="myModalLabel" class="modal-title"><?=$arResult["NAME"]?></h3>
			</div>
			<div class="modal-body">
				<div class="slider-big">
					<?//$i = 0;?>
					<?
					if (empty($arResult["OFFERS"]) || !$bShowOffers) {
						$y=CFile::ResizeImageGet(
							$arResult["PREVIEW_PICTURE"]["ID"],
							array("width" => 800, "height" => 600),
							BX_RESIZE_IMAGE_PROPORTIONAL,
							true
						);
						echo '<div data-id="'.$arResult["ID"].'" data-title="'.$arResult["NAME"].'" data-color="'.$arResult["PROPERTIES"]["COLOR"]["VALUE"].'" data-size="'.$arResult["PROPERTIES"]["SIZE"]["VALUE"].'" data-price="'.$arResult["PRICES"]["BASE"]["PRINT_DISCOUNT_VALUE"].'"><img src="'.$y["src"].'" alt="" /></div>';
					}
					else {
						$arTemp = array();
						foreach ($arResult["OFFERS"] as $key => $arOffer) {
							if (empty($arOffer["PROPERTIES"]["COLOR"]["VALUE"]) || !$arOffer["PREVIEW_PICTURE"])
								continue;
							if (!in_array($arOffer["PROPERTIES"]["COLOR"]["VALUE"], $arTemp)) {
								$arTemp[] = $arOffer["PROPERTIES"]["COLOR"]["VALUE"];
								$offerIMG = 0;
								$y = array();
								if ($arOffer["PREVIEW_PICTURE"])
									$offerIMG = $arOffer["PREVIEW_PICTURE"];
								elseif ($arResult["PREVIEW_PICTURE"]["ID"])
									$offerIMG = $arResult["PREVIEW_PICTURE"]["ID"];
								if ($offerIMG) {
									$y=CFile::ResizeImageGet(
										$offerIMG,
										array("width" => 800, "height" => 600),
										BX_RESIZE_IMAGE_PROPORTIONAL,
										true
									);
								}
								echo '<div data-id="'.$arOffer["ID"].'" data-title="'.$arOffer["NAME"].'" data-color="'.$arOffer["PROPERTIES"]["COLOR"]["VALUE"].'" data-size="'.$arOffer["PROPERTIES"]["SIZE"]["VALUE"].'" data-price="'.$arOffer["PRICES"]["BASE"]["PRINT_DISCOUNT_VALUE"].'"><img src="'.$y["src"].'" alt="" /></div>';
							}
						}
					}
					foreach ($arResult["PROPERTIES"]["IMAGES"]["VALUE"] as $key => $value) {
						$y=CFile::ResizeImageGet(
							$value,
							array("width" => 800, "height" => 600),
							BX_RESIZE_IMAGE_PROPORTIONAL,
							true
						);
						echo '<div data-title="'.($arResult["PROPERTIES"]["IMAGES"]["DESCRIPTION"][$key]?$arResult["PROPERTIES"]["IMAGES"]["DESCRIPTION"][$key]:$arResult["NAME"]).'"><img src="'.$y["src"].'" alt="" /></div>';
					}
					?>
				</div>
				<div class="modal-pr__opts">
					<div class="modal-pr__opts-inner">
						<div class="modal-pr__price"></div>
						<div class="pr-properties">
							<?//здесь будут подставляться свойства (цвета, размеры и т.д.)?>
						</div>
					</div>
				</div>
			</div>
			<?if ($arResult["PROPERTIES"]["AVAILABLE"]["VALUE"] == "Y"):?>
			<div class="modal-footer">
				<button type="submit" class="modal__submit" id="add2bsk_popup" data-id="">В корзину</button>
			</div>
			<?endif;?>
		</div>
	</div>
	<?$frame->end();?>
</div>