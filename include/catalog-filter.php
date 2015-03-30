<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
$obCache = new CPHPCache();
$cacheLifetime = 3600; $cacheID = "catalog.filter/".$_REQUEST["SECTION_CODE"]; $cachePath = "/".$cacheID;
CModule::IncludeModule("iblock");
if ($obCache->InitCache($cacheLifetime, $cacheID, $cachePath)) {
	$vars = $obCache->GetVars();
	$arReturn = $vars["arReturn"];
}
elseif ($obCache->StartDataCache()) {
	$SECTION_ID = CIBlockFindTools::GetSectionID(
		"",
		$_REQUEST["SECTION_CODE"],
		false,
		false,
		array(
			"GLOBAL_ACTIVE" => "Y",
			"IBLOCK_ID" => IBLOCK_PRODUCT_ID
		)
	);
	//список разделов 1-ого уровня
	$rsSections = CIblockSection::GetList(
		array("SORT" => "ASC", "NAME" => "ASC"),
		array(
			"IBLOCK_ID" => 1,
			"GLOBAL_ACTIVE" => "Y",
			"DEPTH_LEVEL" => 1
		),
		false,
		array("IBLOCK_ID", "ID", "NAME", "SECTION_PAGE_URL")
	);
	while ($arSect = $rsSections->GetNext()) {
		$arReturn["SECTIONS"][] = array(
			"ID" => $arSect["ID"],
			"NAME" => $arSect["NAME"],
			"SECTION_PAGE_URL" => $arSect["SECTION_PAGE_URL"]
		);
	}
	//список брендов
	$arMakers = array();
	$arFilterMakers = array(
		"IBLOCK_ID" => 4,
		"ACTIVE" => "Y"
	);
	if ($SECTION_ID > 0) {
		$arFilterMakers["PROPERTY_SECTION_ID"] = $SECTION_ID;
	}
	$rsMaker = CIBlockElement::GetList(
		array("NAME" => "ASC", "SORT" => "ASC"),
		$arFilterMakers,
		false,
		false,
		array("IBLOCK_ID", "ID", "PROPERTY_MAKER")
	);
	while ($arMaker = $rsMaker->GetNext()) {
		$arMakers[] = $arMaker["PROPERTY_MAKER_VALUE"];
		//$arReturn["BRANDS"][$arMaker["ID"]] = $arMaker["NAME"];
	}
	if (!empty($arMakers)) {
		$rsMaker = CIBlockElement::GetList(
			array("NAME" => "ASC", "SORT" => "ASC"),
			array(
				"IBLOCK_ID" => 3,
				"ACTIVE" => "Y",
				"ID" => $arMakers
			),
			false,
			false,
			array("IBLOCK_ID", "ID", "NAME")
		);
		while ($arMaker = $rsMaker->GetNext()) {
			$arReturn["BRANDS"][$arMaker["ID"]] = $arMaker["NAME"];
		}
	}
	$obCache->EndDataCache(array("arReturn" => $arReturn));
}
$formAction = $arReturn["SECTIONS"][0]["SECTION_PAGE_URL"];
foreach ($arReturn["SECTIONS"] as $key => $arSection) {
	if (strrpos($_SERVER["REQUEST_URI"],$arSection["SECTION_PAGE_URL"])===0) {
		$formAction = $arSection["SECTION_PAGE_URL"];
		break;
	}
}
?>

<div class="content__top">
	<div class="product-filter">
		<form class="product-filter__form" action="<?=($APPLICATION->GetCurDir()=="/search/"?'/search/':$formAction)?>" name="catalog-filter">
			<input type="hidden" id="send-filter" value="<?=$_GET["send-filter"]?>">
			<?if ($APPLICATION->GetCurDir() == "/search/"):?>
			<div class="form-field">
				<div class="form-field__inputtext">
					<input class="inputtext" type="text" name="q" value="<?=(isset($_GET["q"])?$_GET["q"]:'')?>" placeholder="Введите товар для поиска">
				</div>
			</div>
			<?endif;?>

			<?if (!empty($arReturn["SECTIONS"])):?>
			<div class="form-field">
				<div class="form-field__select">
					<select class="form-select select_type_category" data-placeholder="Категория" id="catalog-filter" name="category">
						<?if ($APPLICATION->GetCurDir() == "/search/"):?>
						<option value=""></option>
						<?endif;?>
						<?foreach ($arReturn["SECTIONS"] as $arSection) {
							echo '<option value="'.($APPLICATION->GetCurDir()=="/search/"?$arSection["ID"]:$arSection["SECTION_PAGE_URL"]).'"'.($arSection["SECTION_PAGE_URL"]==$APPLICATION->GetCurDir()?' selected':'').($arSection["ID"]==$_GET["category"]?' selected':'').'>'.$arSection["NAME"].'</option>';
						}?>
					</select>
				</div>
			</div>
			<?endif;?>
			<?if (!empty($arReturn["BRANDS"])):?>
			<div class="form-field">
				<div class="form-field__select">
					<select class="form-select select_type_multiselect select_type_brand" multiple="multiple" size="1" name="brand[]">
						<?foreach ($arReturn["BRANDS"] as $key => $name) {
							echo '<option value="'.$key.'"'.(in_array($key, $_GET["brand"])?' selected':'').'>'.$name.'</option>';
						}?>
					</select>
				</div>
			</div>
			<?endif;?>

			<?if ($APPLICATION->GetCurDir() != "/search/"):?>
			<div class="form-field form-field_type_row">
				<div class="form-field__inputtext">
					<span class="like-label">Цены от</span>
					<input class="inputtext" type="text" value="<?=($_GET["price_from"]?intval($_GET["price_from"]):"")?>" name="price_from">
				</div>
				<div class="form-field__inputtext">
					<span class="like-label">до</span>
					<input class="inputtext" type="text" value="<?=($_GET["price_to"]?intval($_GET["price_to"]):"")?>" name="price_to">
					<span class="like-label">руб.</span>
				</div>
			</div>
			<?endif;?>

			<div class="form-field">
				<div class="form-field__button">
					<input type="hidden" name="send-filter" value="Y">
					<input type="submit" class="form-button button_font_big" value="Найти товар" />
				</div>
			</div>
		</form>
	</div>
</div>