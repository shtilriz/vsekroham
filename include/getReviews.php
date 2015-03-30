<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
//отзывы
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Bitrix\Main\Type as FieldType;

$iNumPage = ($_REQUEST['iNumPage']?intval($_REQUEST['iNumPage']):1);
if (isset($arParams['iNumPage'])) {
	$iNumPage = $arParams['iNumPage'];
}

$id = abs((int)$_REQUEST["id"]);
if (isset($arParams["id"])) {
	$id = $arParams["id"];
}

$limit = 10; //отзывов на странице
$offset = ($iNumPage-1)*$limit;

$arResult = array();

if (CModule::IncludeModule("highloadblock") && $id > 0) {
	$hlblock = HL\HighloadBlockTable::getById(6)->fetch();
	$entity = HL\HighloadBlockTable::compileEntity($hlblock);
	$main_query = new Entity\Query($entity);
	$main_query->setSelect(array('*'));
	$main_query->setOrder(array("UF_LIKE" => "DESC", "UF_DIZLIKE" => "ASC"));
	$main_query->setFilter(
		array(
			"UF_ACTIVE" => true,
			"UF_PRODUCT" => $id
		)
	);
	//$main_query->setLimit($limit);
	//$main_query->setOffset($offset);
	$result = $main_query->exec();
	$result = new CDBResult($result);
	//$entity_data_class = $entity->getDataClass();
	$result->NavStart($limit,false,$iNumPage);
	//echo $result->NavPrint("Баннеры");
	while ($arReview = $result->Fetch()) {
		$arResult["ITEMS"][] = $arReview;
	}
	$arResult["NAV_STRING"] = $result->GetPageNavString('', 'reviews');
	$arResult["NAV_PARAMS"] = $result->GetNavParams();
	$arResult["NAV_NUM"] = $result->NavNum;
}
?>

<?if (!empty($arResult["ITEMS"])):?>
	<?foreach ($arResult["ITEMS"] as $key => $arItem):?>
		<div class="review__item" data-review_id="<?=$arItem["ID"]?>">
			<div class="review__top">
				<span class="date"><?=$arItem["UF_DATE"];?></span>
				<span class="avatar">
					<?if ($arItem["UF_AVATAR"]):
						$y=CFile::ResizeImageGet(
							$arItem["UF_AVATAR"],
							array("width" => 50, "height" => 50),
							BX_RESIZE_IMAGE_EXACT,
							true
						);?>
						<a href="<?=$arItem["UF_PROFILE_LINK"]?>" target="_blank" rel="nofollow"><img src="<?=$y["src"]?>" alt="" /></a>
					<?else:?>
						<img src="<?=SITE_TEMPLATE_PATH.'/images/avatar_default.png'?>" alt="" />
					<?endif;?>
				</span>
				<span class="name"><?=$arItem["UF_NAME"]?></span>
			</div>
			<div class="review__content">
				<div class="stars">
					<?$rating = intval($arItem["UF_RATE"]);
					for ($i = 0; $i < 5 ; $i++) {
						$star = 'star-gray';
						if ($i < $rating)
							$star = 'star-blue';
						echo '<span class="'.$star.'"></span>';
					}?>
				</div>
				<?if ($arItem["UF_WORTH"]):?>
					<p><span class="text-green bold">Достоинства:</span> <?=$arItem["UF_WORTH"];?></p>
				<?endif;?>
				<?if ($arItem["UF_LACK"]):?>
					<p><span class="text-red bold">Недостатки:</span> <?=$arItem["UF_LACK"];?></p>
				<?endif;?>
				<?if ($arItem["UF_COMMENT"]):?>
				<p><span class="bold">Комментарий:</span> <?=$arItem["UF_COMMENT"];?></p>
				<?endif;?>
			</div>
			<div class="review__bottom">
				Отзыв полезен? <a href="#" class="review-plus">Да</a> <span class="review-plus-count"><?=intval($arItem["UF_LIKE"])?></span> / <a href="#" class="review-minus">Нет</a> <span class="review-minus-count"><?=intval($arItem["UF_DIZLIKE"])?></span>
			</div>
		</div>

	<?endforeach;?>
	<?php echo $arResult["NAV_STRING"]?>
	<?/*<div class="review__info">
		Отзывы взяты из Яндекс Маркет и mail.ru <a href="#"><img src="<?=SITE_TEMPLATE_PATH?>/images/link_img_01.gif" alt=""/></a> <a href="#"><img src="<?=SITE_TEMPLATE_PATH?>/images/link_img_02.gif" alt=""/></a>
	</div>*/?>
<?endif;?>
