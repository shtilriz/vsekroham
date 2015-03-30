<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Производители");
?>

<?if (!isset($_REQUEST["ELEMENT_CODE"]) && empty($_REQUEST["ELEMENT_CODE"])):?>
	<h1><?=$APPLICATION->ShowTitle();?></h1>
	<?$APPLICATION->IncludeComponent("bitrix:menu", "brands-list", Array(
		"ROOT_MENU_TYPE" => "brands",	// Тип меню для первого уровня
			"MENU_CACHE_TYPE" => "A",	// Тип кеширования
			"MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
			"MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
			"MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
			"MAX_LEVEL" => "1",	// Уровень вложенности меню
			"CHILD_MENU_TYPE" => "",	// Тип меню для остальных уровней
			"USE_EXT" => "N",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
			"DELAY" => "N",	// Откладывать выполнение шаблона меню
			"ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
		),
		false
	);?>
<?else:
	$obCache = new CPHPCache();
	$cacheLifetime = 3600; $cacheID = $_REQUEST["ELEMENT_CODE"]; $cachePath = "/brands/".$cacheID;
	if ($obCache->InitCache($cacheLifetime, $cacheID, $cachePath)) {
		$vars = $obCache->GetVars();
		$arReturn = $vars['arReturn'];
	}
	elseif ($obCache->StartDataCache()) {
		$arReturn = array();
		if (CModule::IncludeModule("iblock") && strlen($_REQUEST["ELEMENT_CODE"]) > 0) {
			$res = CIBlockElement::GetList(
				array(),
				array(
					"IBLOCK_ID" => 3,
					"ACTIVE" => "Y",
					"=CODE" => $_REQUEST["ELEMENT_CODE"]
				),
				false,
				false,
				array("IBLOCK_ID", "ID", "NAME", "DETAIL_TEXT", "DETAIL_PAGE_URL")
			);
			if ($arRes = $res->GetNext()) {
				$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($arRes["IBLOCK_ID"], $arRes["ID"]);
				$arPropValues = $ipropValues->getValues();

				$arReturn = array(
					"ID" => $arRes["ID"],
					"NAME" => $arRes["NAME"],
					"DETAIL_PAGE_URL" => $arRes["DETAIL_PAGE_URL"],
					"DETAIL_TEXT" => $arRes["DETAIL_TEXT"],
					"IPROPERTY_VALUES" => $arPropValues
				);
			}
		}
		$obCache->EndDataCache(array("arReturn" => $arReturn));
	}
	$MAKER = $arReturn["ID"];
	?>

	<?if ($MAKER > 0):?>
		<h1>
		<?if ($arReturn["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]) {
			echo $arReturn["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"];
		}
		else {
			echo $arReturn["NAME"];
		}?>
		</h1>

		<?/*$APPLICATION->IncludeComponent(
			"dev:maker.links",
			".default",
			array(
				"IBLOCK_TYPE" => "catalogs",
				"IBLOCK_ID" => "4",
				"IBLOCK_ID_CATALOG" => "1",
				"SECTION_ID" => $SECTION_ID,
				"SECTION_CODE" => "",
				"FOLDER" => "/makers/",
				"CACHE_TYPE" => "A",
				"CACHE_TIME" => "3600"
			),
			$component
		);*/?>

		<?include($_SERVER["DOCUMENT_ROOT"].'/include/catalog-sort.php');?>

		<?$GLOBALS["arrFilter"]["PROPERTY_MAKER"] = $MAKER;?>
		<form name="makersForm">
			<input type="hidden" name="MAKER" value="<?=$MAKER?>">
		</form>

		<?$APPLICATION->IncludeFile(
			'/include/catalog-section.php',
			array(
				"TEMPLATE" => "pagination"
			)
		);?>
		<?//include($_SERVER["DOCUMENT_ROOT"].'/include/catalog-section.php');?>

		<?
		$APPLICATION->AddChainItem($arReturn["NAME"], $arReturn["DETAIL_PAGE_URL"]);

		$pageCnt = ((int)$_GET["PAGEN_1"]>1?'Страница №'.(int)$_GET["PAGEN_1"].'. ':'');
		if ($arReturn["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"]) {
			//$APPLICATION->SetTitle($arReturn["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"]);
			$APPLICATION->SetPageProperty('title', $pageCnt.$arReturn["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"]);
		}
		else {
			$APPLICATION->SetTitle($pageCnt.$arReturn["NAME"]);
		}
		if ($arReturn["IPROPERTY_VALUES"]["ELEMENT_META_KEYWORDS"]) {
			$APPLICATION->SetPageProperty('keywords', $arReturn["IPROPERTY_VALUES"]["ELEMENT_META_KEYWORDS"]);
		}
		if ($arReturn["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"]) {
			$APPLICATION->SetPageProperty('description', $arReturn["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"]);
		}
		if ($arReturn["DETAIL_TEXT"]) {
			$APPLICATION->SetPageProperty("textdescription",$arReturn["DETAIL_TEXT"]);
			//$APPLICATION->AddBufferContent(Array(&$APPLICATION, "GetProperty"), 'block-text', $arReturn["DETAIL_TEXT"]);
		}
		else {
			$APPLICATION->SetPageProperty("textdescription", "&nbsp;");
		}
		?>
	<?endif;?>

	<script type="text/javascript">
	$(function() {
		var _GET = window.location.search.substring(1).split("&");
		if (_GET) {
			for (var i=0; i<_GET.length; i++) {
				var getVar = _GET[i].split("=");
				if (getVar[0] == "PAGEN_1") {
					$('html, body').scrollTop($('#content').offset().top);
					break;
				}
			}
		}
	});
	</script>
<?endif;?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>