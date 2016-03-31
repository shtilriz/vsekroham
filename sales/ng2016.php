<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Акция к Новому году");
$arProducts = array();
$obCache = new CPHPCache();
$cacheLifetime = 3600; $cacheID = "new-year.products"; $cachePath = "/".$cacheID;
CModule::IncludeModule("iblock");
if ($obCache->InitCache($cacheLifetime, $cacheID, $cachePath)) {
	$vars = $obCache->GetVars();
	$arProducts = $vars["arProducts"];
	$arSections = $vars["arSections"];
}
elseif ($obCache->StartDataCache()) {
	$rsProductDiscounts = CCatalogDiscount::GetList(
		array (),
		array (
			"ACTIVE" => "Y",
			"COUPON" => "",
			"ID" => array(1, 2, 3)
		),
		false,
		false,
		array ("ID", "PRODUCT_ID")
	);
	while ($arProductDiscount = $rsProductDiscounts->GetNext()) {
		$arProducts[] = $arProductDiscount["PRODUCT_ID"];
	}
	//узнать родительские разделы данных товаров
	$arSectionIDs = array();
	if (!empty($arProducts)) {
		$rsProducts = CIBlockElement::GetList(
			array("SORT" => "ASC"),
			array(
				"IBLOCK_ID" => IBLOCK_PRODUCT_ID,
				"ACTIVE" => "Y",
				"ID" => $arProducts
			),
			false,
			false,
			array("IBLOCK_SECTION_ID")
		);
	}
	while ($arRes = $rsProducts->GetNext()) {
		if (!in_array($arRes["IBLOCK_SECTION_ID"], $arSectionIDs))
			$arSectionIDs[] = $arRes["IBLOCK_SECTION_ID"];
	}
	//категории первого уровня
	$arSectionMainIDs = array();
	foreach ($arSectionIDs as $sID) {
		$nav = CIBlockSection::GetNavChain(false, $sID);
		if ($arSectionPath = $nav->GetNext()) {
			if (!in_array($arSectionPath, $arSectionMainIDs))
				$arSectionMainIDs[] = $arSectionPath["ID"];
		}
	}

	$arSections = array();
	if (!empty($arSectionMainIDs)) {
		$rsSections = CIBlockSection::GetList(
			array("LEFT_MARGIN" => "ASC"),
			array(
				"IBLOCK_ID" => IBLOCK_PRODUCT_ID,
				"ACTIVE" => "Y",
				"ID" => $arSectionMainIDs
			),
			false,
			array("IBLOCK_ID", "ID", "NAME")
		);
		while ($arSection = $rsSections->GetNext()) {
			$arSections[$arSection["ID"]] = $arSection["NAME"];
		}
	}

	$obCache->EndDataCache(array("arProducts" => $arProducts, "arSections" => $arSections));
}
$GLOBALS["arrFilter"] = array("ID"=>$arProducts);
?><style type="text/css">
	#new-year .stuff-list .stuff-list__item {
		width: 223px;
	}
</style>
<div class="b-stock__info">
	 До 20 декабря, на все товары с ярлыком<span class="icon"></span> &nbsp; будет действовать специальная цена.
</div>
<div id="new-year">
	 <?if (!empty($arSections)):?>
	<div class="brands brands--col-3">
		<ul class="brands__list">
			 <?foreach ($arSections as $id => $name) {
				echo '<li class="brands__item"><a href="'.$APPLICATION->GetCurPage().'?SECTION_ID='.$id.'#new-year"'.($id==$_GET["SECTION_ID"]?' class="active"':'').'>'.$name.'</a></li>';
			}?>
		</ul>
	</div>
	 <?endif;?> <?include($_SERVER["DOCUMENT_ROOT"].'/include/catalog-section.php');?>
</div>
 <br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>