<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
$SECTION_ID_KOLYASKI = 141;
//ID разделов с колясками
$arSectKolyaski = array(141, 157, 165, 170, 174, 180, 184, 190, 195);
//массив символьных кодов свойств типа 'список', которые необходимо выводить в фильтре по коляскам
$arPropertyCodeKolyaski = array('NUMBER_WHEELS', 'MEHANIZM_VRACH', 'DIFFERENT_AXLES', 'CUSHIONING_SYSTEM', 'PARENT_HANDLE', 'MATERIAL_WHEELS', 'CARRYING_HANDLE', 'SEATBELTS2', 'MAT_BASKET4BUY', 'BACK_ANGLE', 'PLATING_STROLLERS');

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Bitrix\Main\Type as FieldType;

$obCache = new CPHPCache();
$cacheLifetime = 3600; $cacheID = "catalog.filter.new/".$_REQUEST["SECTION_CODE"]; $cachePath = "/".$cacheID;
CModule::IncludeModule("iblock");
CModule::IncludeModule("highloadblock");
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
	$arReturn["SECTION_ID"] = $SECTION_ID;
	$rsSections = CIblockSection::GetList(
		array("SORT" => "ASC", "NAME" => "ASC"),
		array(
			"IBLOCK_ID" => IBLOCK_PRODUCT_ID,
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
	if (in_array($SECTION_ID, $arSectKolyaski)) {
		//список подразделов раздела Коляски
		if ($SECTION_ID == $SECTION_ID_KOLYASKI) {
			$rsSubSectKolyaski = CIblockSection::GetList(
				array("SORT" => "ASC", "NAME" => "ASC"),
				array(
					"IBLOCK_ID" => IBLOCK_PRODUCT_ID,
					"GLOBAL_ACTIVE" => "Y",
					"DEPTH_LEVEL" => 2,
					"SECTION_ID" => $SECTION_ID_KOLYASKI
				),
				false,
				array("IBLOCK_ID", "ID", "NAME", "SECTION_PAGE_URL")
			);
			while ($arSect = $rsSubSectKolyaski->GetNext()) {
				$arReturn["SUBSECTIONS_KOLYASKI"][] = array(
					"ID" => $arSect["ID"],
					"NAME" => $arSect["NAME"],
					"SECTION_PAGE_URL" => $arSect["SECTION_PAGE_URL"]
				);
			}
		}

		foreach ($arPropertyCodeKolyaski as $code) {
			$arReturn['PROPS'][$code] = IBlockHelper::getPropertyEnumByCode(IBLOCK_PRODUCT_ID, $code);

			$rsProps = CIBlockProperty::GetList(
				array('SORT' => 'ASC'),
				array('IBLOCK_ID' => IBLOCK_PRODUCT_ID, 'CODE' => $code)
			);
			while ($arProp = $rsProps->Fetch()) {
				$arReturn['PROPS_NAME'][$arProp['CODE']] = $arProp['NAME'];
			}
		}
	}
	//список брендов
	$arMakers = array();
	$arFilterMakers = array(
		'IBLOCK_ID' => 4,
		'ACTIVE' => 'Y'
	);
	if ($SECTION_ID > 0) {
		$arFilterMakers['PROPERTY_SECTION_ID'] = $SECTION_ID;
	}
	$rsMaker = CIBlockElement::GetList(
		array('NAME' => 'ASC', 'SORT' => 'ASC'),
		$arFilterMakers,
		false,
		false,
		array('IBLOCK_ID', 'ID', 'PROPERTY_MAKER')
	);
	while ($arMaker = $rsMaker->GetNext()) {
		$arMakers[] = $arMaker['PROPERTY_MAKER_VALUE'];
	}
	if (!empty($arMakers)) {
		$rsMaker = CIBlockElement::GetList(
			array('NAME' => 'ASC', 'SORT' => 'ASC'),
			array(
				'IBLOCK_ID' => 3,
				'ACTIVE' => 'Y',
				'ID' => $arMakers
			),
			false,
			false,
			array('ID', 'NAME')
		);
		while ($arMaker = $rsMaker->GetNext()) {
			$arReturn['PROPS']['MAKER'][$arMaker['ID']] = $arMaker['NAME'];
		}
	}

	//комплектация
	$hlblock = HL\HighloadBlockTable::getById(4)->fetch();
	$entity = HL\HighloadBlockTable::compileEntity($hlblock);
	$entity_data_class = $entity->getDataClass();
	$rsData = $entity_data_class::getList(array(
		'select' => array('*'),
		'order' => array('UF_SORT' => 'ASC')
	));
	while ($arComplect = $rsData->Fetch()) {
		$arReturn['PROPS']['OPTIONS'][] = $arComplect;
	}

	$obCache->EndDataCache(array('arReturn' => $arReturn));
}
$formAction = $arReturn['SECTIONS'][0]['SECTION_PAGE_URL'];
foreach ($arReturn['SECTIONS'] as $key => $arSection) {
	if (strrpos($_SERVER['REQUEST_URI'],$arSection['SECTION_PAGE_URL'])===0) {
		$formAction = $arSection['SECTION_PAGE_URL'];
		break;
	}
}
?>

<?if (in_array($arReturn["SECTION_ID"], $arSectKolyaski)):?>
	<div class="content__top" id="catalog-filter">
		<?Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("#catalog-filter");?>
		<div class="product-filter-wrapper">
			<div class="product-filter-2">
				<form action="<?=($APPLICATION->GetCurDir()=="/search/"?'/search/':'')?>" name="catalog-filter" data-section_id="<?=$arReturn["SECTION_ID"]?>" data-section_code="<?=$_REQUEST["SECTION_CODE"]?>">
					<input type="hidden" name="send-filter" value="Y">
					<?include('kolyaski.php');?>
				</form>
			</div>
			<a href="javascript:void(0)" class="more-link more-link-dotted pull-right link-advanced-filter"<?=($_GET["send-filter"]=="Y" ? ' style="display: none"' : '')?>>Показать расширенный фильтр</a>
		</div>
		<?Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("catalog-filter", "");?>
	</div>
<?else:?>
	<?include('default.php');?>
<?endif;?>