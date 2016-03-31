<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div id="top-baners">
<script type="text/javascript" src="//vk.com/js/api/openapi.js?120"></script>
<?Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("top-baners");?>
	<?
	$arPathHide = array(
		"/buy/",
		"/mebel/",
		"/warranty/",
		"/delivery/",
		"/wholesalers/",
		"/sales/",
		"/our_mags/"
	);
	global $APPLICATION;
	$arReturn = array();
	$obCache = new CPHPCache();
	$cacheLifetime = 3600;
	$cacheID = "slider/".$APPLICATION->GetCurDir();
	$cachePath = "/".$cacheID;
	CModule::IncludeModule("iblock");
	if ($obCache->InitCache($cacheLifetime, $cacheID, $cachePath)) {
		$vars = $obCache->GetVars();
		$arReturn = $vars["arReturn"];
	}
	elseif ($obCache->StartDataCache()) {
		CModule::IncludeModule("iblock");
		if ($APPLICATION->GetCurDir() != "/") {
			$rsBaners = CIBlockElement::GetList(
				array("RAND" => "ASC"),
				array(
					"IBLOCK_ID" => 9,
					"ACTIVE" => "Y",
					"SECTION_ID" => 237,
					"PROPERTY_SECTIONS" => "%".$APPLICATION->GetCurDir()."%",
				),
				false,
				array("nTopCount" => 1),
				array("IBLOCK_ID", "ID", "NAME", "PREVIEW_PICTURE", "PROPERTY_LINK", "PROPERTY_OPEN")
			);
			if ($arBaner = $rsBaners->GetNext()) {
				$arReturn["BANER"] = $arBaner;
			}
		}
		$bShowSlider = true;
		//проверить, выводить ли слайдер в списке товаров данного раздела каталога
		if (isset($_GET["SECTION_CODE"]) && strlen($_GET["SECTION_CODE"]) > 0 && strrpos($_SERVER["REQUEST_URI"],'/catalog/')===0) {
			$rsSections = CIBlockSection::GetList(array(),array("IBLOCK_ID" => IBLOCK_PRODUCT_ID,"ACTIVE" => "Y","CODE" => $_GET["SECTION_CODE"]),false,array("IBLOCK_ID", "ID", "IBLOCK_SECTION_ID"));
			if ($arSection = $rsSections->GetNext()) {
				$bShowSlider = bShowSlider($arSection["ID"]);
				if ($arSection["IBLOCK_SECTION_ID"] && $bShowSlider) {
					$rsParentSections = CIBlockSection::GetList(array(),array("IBLOCK_ID" => IBLOCK_PRODUCT_ID,"ACTIVE" => "Y","ID" => $arSection["IBLOCK_SECTION_ID"]),false,array("IBLOCK_ID", "UF_SLIDER"));
					if ($arParentSection = $rsParentSections->GetNext()) {
						if ($arParentSection["UF_SLIDER"])
							$bShowSlider = false;
					}
				}
			}
		}
		//проверить, выводить ли слайдер в карточке товара
		if (isset($_GET["ELEMENT_CODE"]) && strlen($_GET["ELEMENT_CODE"]) > 0 && strrpos($_SERVER["REQUEST_URI"],'/product/')===0) {
			$arSections = array();
			$rsElements = CIBlockElement::GetList(array(),array("IBLOCK_ID"=>IBLOCK_PRODUCT_ID,"ACTIVE"=>"Y","CODE"=>$_GET["ELEMENT_CODE"]),false,false,array("IBLOCK_ID","IBLOCK_SECTION_ID","PROPERTY_SLIDER"));
			if ($arElement = $rsElements->GetNext()) {
				if ($arElement["PROPERTY_SLIDER_VALUE"] == "Y") {
					$bShowSlider = false;
				}
				elseif ($arElement["IBLOCK_SECTION_ID"]) {
					$nav = CIBlockSection::GetNavChain(IBLOCK_PRODUCT_ID,$arElement["IBLOCK_SECTION_ID"]);
					while($arSectionPath = $nav->GetNext()) {
						$arSections[] = $arSectionPath["ID"];
					}
					foreach ($arSections as $key => $sID) {
						if (!bShowSlider($sID)) {
							$bShowSlider = bShowSlider($sID);
							break;
						}
					}
				}
			}
		}

		//проверить, выводить ли слайдер в разделах каталога по производителям
		if (isset($_GET["ELEMENT_CODE"]) && strlen($_GET["ELEMENT_CODE"]) > 0 && strrpos($_SERVER["REQUEST_URI"],'/makers/')===0) {
			$arSections = array();
			$rsElements = CIBlockElement::GetList(array(),array("IBLOCK_ID"=>4,"ACTIVE"=>"Y","CODE"=>$_GET["ELEMENT_CODE"]),false,false,array("IBLOCK_ID","PROPERTY_SECTION_ID"));
			if ($arElement = $rsElements->GetNext()) {
				if ($arElement["PROPERTY_SECTION_ID_VALUE"]) {
					$nav = CIBlockSection::GetNavChain(IBLOCK_PRODUCT_ID,$arElement["PROPERTY_SECTION_ID_VALUE"]);
					while($arSectionPath = $nav->GetNext()) {
						$arSections[] = $arSectionPath["ID"];
					}
					foreach ($arSections as $key => $sID) {
						if (!bShowSlider($sID)) {
							$bShowSlider = bShowSlider($sID);
							break;
						}
					}
				}
			}
		}

		//проверить, выводить ли слайдер в разделе бренда
		if (isset($_GET["ELEMENT_CODE"]) && strlen($_GET["ELEMENT_CODE"]) > 0 && strrpos($_SERVER["REQUEST_URI"],'/brands/')===0) {
			$rsElements = CIBlockElement::GetList(array(),array("IBLOCK_ID"=>3,"ACTIVE"=>"Y","CODE"=>$_GET["ELEMENT_CODE"]),false,false,array("IBLOCK_ID","PROPERTY_SLIDER"));
			if ($arElement = $rsElements->GetNext()) {
				if ($arElement["PROPERTY_SLIDER_VALUE"] == "Y")
					$bShowSlider = false;
			}
		}

		$arReturn["B_SHOW_SLIDER"] = $bShowSlider;

		$obCache->EndDataCache(array("arReturn" => $arReturn));
	}
	if (in_array($APPLICATION->GetCurDir(), $arPathHide))
		$arReturn["B_SHOW_SLIDER"] = false;

	if (!empty($arReturn["BANER"]) || !$arReturn["B_SHOW_SLIDER"])
		$GLOBALS["VK_LEFT"] = true;
	else
		$GLOBALS["VK_LEFT"] = false;
	$GLOBALS["B_SHOW_SLIDER"] = $arReturn["B_SHOW_SLIDER"];
	?>

	<?if (!empty($arReturn["BANER"])):?>
		<div class="top_baners">
			<a href="<?=$arReturn["BANER"]["PROPERTY_LINK_VALUE"]?>"<?=$arReturn["BANER"]["PROPERTY_OPEN_ENUM_ID"]==117?' target="_blank"':''?>><img src="<?=CFile::GetPath($arReturn["BANER"]["PREVIEW_PICTURE"]);?>" alt="<?=$arReturn["BANER"]["NAME"]?>"></a>
		</div>
	<?elseif (!(strrpos($_SERVER["REQUEST_URI"],'/basket/')===0) && $arReturn["B_SHOW_SLIDER"]):?>
		<div class="header__bottom">
			<?$APPLICATION->IncludeComponent(
				"dev:banners.main.list",
				".default",
				array(
					"BLOCK_ID" => "5",
					"SORT_BY" => "UF_SORT",
					"SORT_ORDER" => "ASC"
				),
				false
			);?>
			<noindex>
				<div class="vk-widget">
					<div class="vk-widget__inner">
						<div id="vk_groups"></div>
						<script type="text/javascript">
							VK.Widgets.Group("vk_groups", {mode: 0, width: "220", height: "255", color1: 'FFFFFF', color2: '2B587A', color3: '5B7FA6'}, 62994219);
						</script>
					</div>
				</div>
			</noindex>
		</div>
	<?endif;?>
<?Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("top-baners", "");?>
</div>