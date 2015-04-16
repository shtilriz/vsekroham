<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Каталог по производителям");
?>

<?if (!isset($_REQUEST["ELEMENT_CODE"]) && empty($_REQUEST["ELEMENT_CODE"])):?>
	<div id="catalog2makers">
	<?$APPLICATION->IncludeComponent("bitrix:news.list", "lists", Array(
		"IBLOCK_TYPE" => "catalogs",	// Тип информационного блока (используется только для проверки)
			"IBLOCK_ID" => "4",	// Код информационного блока
			"NEWS_COUNT" => "1000",	// Количество новостей на странице
			"SORT_BY1" => "NAME",	// Поле для первой сортировки новостей
			"SORT_ORDER1" => "ASC",	// Направление для первой сортировки новостей
			"SORT_BY2" => "SORT",	// Поле для второй сортировки новостей
			"SORT_ORDER2" => "ASC",	// Направление для второй сортировки новостей
			"FILTER_NAME" => "",	// Фильтр
			"FIELD_CODE" => array(	// Поля
				0 => "",
				1 => "undefined",
				2 => "",
			),
			"PROPERTY_CODE" => array(	// Свойства
				0 => "",
				1 => "undefined",
				2 => "",
			),
			"CHECK_DATES" => "Y",	// Показывать только активные на данный момент элементы
			"DETAIL_URL" => "",	// URL страницы детального просмотра (по умолчанию - из настроек инфоблока)
			"AJAX_MODE" => "N",	// Включить режим AJAX
			"AJAX_OPTION_JUMP" => "N",	// Включить прокрутку к началу компонента
			"AJAX_OPTION_STYLE" => "Y",	// Включить подгрузку стилей
			"AJAX_OPTION_HISTORY" => "N",	// Включить эмуляцию навигации браузера
			"CACHE_TYPE" => "A",	// Тип кеширования
			"CACHE_TIME" => "36000000",	// Время кеширования (сек.)
			"CACHE_FILTER" => "N",	// Кешировать при установленном фильтре
			"CACHE_GROUPS" => "Y",	// Учитывать права доступа
			"PREVIEW_TRUNCATE_LEN" => "",	// Максимальная длина анонса для вывода (только для типа текст)
			"ACTIVE_DATE_FORMAT" => "d.m.Y",	// Формат показа даты
			"SET_TITLE" => "N",	// Устанавливать заголовок страницы
			"SET_BROWSER_TITLE" => "N",	// Устанавливать заголовок окна браузера
			"SET_META_KEYWORDS" => "N",	// Устанавливать ключевые слова страницы
			"SET_META_DESCRIPTION" => "N",	// Устанавливать описание страницы
			"SET_STATUS_404" => "N",	// Устанавливать статус 404, если не найдены элемент или раздел
			"INCLUDE_IBLOCK_INTO_CHAIN" => "N",	// Включать инфоблок в цепочку навигации
			"ADD_SECTIONS_CHAIN" => "N",	// Включать раздел в цепочку навигации
			"HIDE_LINK_WHEN_NO_DETAIL" => "N",	// Скрывать ссылку, если нет детального описания
			"PARENT_SECTION" => "",	// ID раздела
			"PARENT_SECTION_CODE" => "",	// Код раздела
			"INCLUDE_SUBSECTIONS" => "Y",	// Показывать элементы подразделов раздела
			"DISPLAY_DATE" => "Y",	// Выводить дату элемента
			"DISPLAY_NAME" => "Y",	// Выводить название элемента
			"DISPLAY_PICTURE" => "Y",	// Выводить изображение для анонса
			"DISPLAY_PREVIEW_TEXT" => "Y",	// Выводить текст анонса
			"PAGER_TEMPLATE" => ".default",	// Шаблон постраничной навигации
			"DISPLAY_TOP_PAGER" => "N",	// Выводить над списком
			"DISPLAY_BOTTOM_PAGER" => "Y",	// Выводить под списком
			"PAGER_TITLE" => "Новости",	// Название категорий
			"PAGER_SHOW_ALWAYS" => "N",	// Выводить всегда
			"PAGER_DESC_NUMBERING" => "N",	// Использовать обратную навигацию
			"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",	// Время кеширования страниц для обратной навигации
			"PAGER_SHOW_ALL" => "N",	// Показывать ссылку "Все"
		),
		false
	);?>
	</div>
<?else:
	$obCache = new CPHPCache();
	$cacheLifetime = 3600; $cacheID = $_REQUEST["ELEMENT_CODE"]; $cachePath = "/makers/".$cacheID;
	$arReturn = array();
	if ($obCache->InitCache($cacheLifetime, $cacheID, $cachePath)) {
		$vars = $obCache->GetVars();
		$arReturn = $vars['arReturn'];
	}
	elseif ($obCache->StartDataCache()) {
		if (CModule::IncludeModule("iblock") && strlen($_REQUEST["ELEMENT_CODE"]) > 0) {
			$res = CIBlockElement::GetList(
				array(),
				array(
					"IBLOCK_ID" => 4,
					"ACTIVE" => "Y",
					"=CODE" => $_REQUEST["ELEMENT_CODE"]
				),
				false,
				false,
				array("IBLOCK_ID", "ID", "NAME", "DETAIL_TEXT", "DETAIL_PAGE_URL", "PROPERTY_SECTION_ID", "PROPERTY_MAKER")
			);
			if ($arRes = $res->GetNext()) {
				$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($arRes["IBLOCK_ID"], $arRes["ID"]);
				$arPropValues = $ipropValues->getValues();

				$arReturn = array(
					"NAME" => $arRes["NAME"],
					"DETAIL_PAGE_URL" => $arRes["DETAIL_PAGE_URL"],
					"DETAIL_TEXT" => $arRes["DETAIL_TEXT"],
					"SECTION_ID" => $arRes["PROPERTY_SECTION_ID_VALUE"],
					"MAKER" => $arRes["PROPERTY_MAKER_VALUE"],
					"IPROPERTY_VALUES" => $arPropValues
				);
				//В случае, если у производителя только d одном подразделе есть товары, добавлять каноническую ссылку на вышележащий раздел
				$arReturn["CANONICAL"] = false;
				if ($arReturn["SECTION_ID"] && $arReturn["MAKER"]) {
					$rsChain = CIblockSection::GetNavChain(false, $arReturn["SECTION_ID"], array("ID"));
					$arSectIDs = array();
					while ($arSectPath = $rsChain->GetNext()) {
						if ($arSectPath["ID"] == $arReturn["SECTION_ID"])
							continue;
						$rsTemp = CIBlockElement::GetList(
							array(),
							array(
								"IBLOCK_ID" => IBLOCK_PRODUCT_ID,
								"ACTIVE" => "Y",
								"SECTION_ID" => $arSectPath["ID"],
								"INCLUDE_SUBSECTIONS" => "Y",
								"PROPERTY_MAKER" => $arReturn["MAKER"]
							),
							false,
							false,
							array("IBLOCK_SECTION_ID")
						);
						while ($arTemp = $rsTemp->GetNext()) {
							if (!in_array($arTemp["IBLOCK_SECTION_ID"], $arSectIDs))
								$arSectIDs[] = $arTemp["IBLOCK_SECTION_ID"];
						}
						$arReturn["CANONICAL"] = (count($arSectIDs)==1);
						//если нужно выводить каноническу ссылку, то получаем её
						if ($arReturn["CANONICAL"]) {
							$rsParent = CIBlockElement::GetList(
								array(),
								array(
									"IBLOCK_ID" => $arRes["IBLOCK_ID"],
									"ACTIVE" => "Y",
									"PROPERTY_SECTION_ID" => $arSectPath["ID"],
									"PROPERTY_MAKER" => $arReturn["MAKER"]
								),
								false,
								false,
								array("IBLOCK_ID", "DETAIL_PAGE_URL")
							);
							if ($arParent = $rsParent->GetNext())
								$arReturn["CANONICAL_LINK"] = $arParent["DETAIL_PAGE_URL"];
						}
					}
				}
			}
		}
		$obCache->EndDataCache(array("arReturn" => $arReturn));
	}
	$SECTION_ID = $arReturn["SECTION_ID"];
	$MAKER = $arReturn["MAKER"];
	?>

	<?if ($SECTION_ID > 0 && $MAKER > 0):?>
		<h1>
		<?if ($arReturn["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]) {
			echo $arReturn["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"];
		}
		else {
			echo $arReturn["NAME"];
		}?>
		</h1>

		<?$APPLICATION->IncludeComponent(
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
		);?>

		<?include($_SERVER["DOCUMENT_ROOT"].'/include/catalog-sort.php');?>

		<?$GLOBALS["arrFilter"]["PROPERTY_MAKER"] = $MAKER;
		$GLOBALS["arrFilter"]["!PROPERTY_AVAILABLE"] = false;
		$GLOBALS["arrFilter"]["!CATALOG_PRICE_1"] = false;?>
		<form name="makersForm">
			<input type="hidden" name="SECTION_ID" value="<?=$SECTION_ID?>">
			<input type="hidden" name="MAKER" value="<?=$MAKER?>">
		</form>

		<?include($_SERVER["DOCUMENT_ROOT"].'/include/catalog-section.php');?>

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

		$APPLICATION->SetPageProperty('description', $arReturn["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"]);

		if ($arReturn["DETAIL_TEXT"]) {
			$APPLICATION->SetPageProperty("textdescription",$arReturn["DETAIL_TEXT"]);
			//$APPLICATION->AddBufferContent(Array(&$APPLICATION, "GetProperty"), 'block-text', $arReturn["DETAIL_TEXT"]);
		}
		else {
			$APPLICATION->SetPageProperty("textdescription", "&nbsp;");
		}
		if ($arReturn["CANONICAL"] && strlen($arReturn["CANONICAL_LINK"]) > 0) {
			$APPLICATION->AddViewContent("Canonical", '<link rel="canonical" href="http://'.$_SERVER["SERVER_NAME"].$arReturn["CANONICAL_LINK"].'"/>');
		}
		?>
		<script type="text/javascript">
		$(function() {
			var _GET = window.location.search.substring(1).split("&");
			if (_GET) {
				for (var i=0; i<_GET.length; i++) {
					var getVar = _GET[i].split("=");
					if (getVar[0] == "PAGEN_1") {
						$('html, body').scrollTop($('#content .page-wrapper').offset().top);
						break;
					}
				}
			}
		});
		</script>
	<?endif;?>
<?endif;?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>