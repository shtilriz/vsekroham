<?php
//добавляет новые бренды по категориям в инфоблок "Каталог по производителям"
$_SERVER["DOCUMENT_ROOT"] = "/home/vsekroham/public_html";
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
AddMessage2Log("/include/brands2category.php - Запуск скрипта");

$IBLOCK_ID_PRODUCTS = 1; //ИБ товаров
$IBLOCK_ID_BRANDS = 4; //ИБ каталога по производителям

if (CModule::IncludeModule("iblock")){
	$arResult = array();
	$arFilter = array("IBLOCK_ID"=>$IBLOCK_ID_PRODUCTS, "GLOBAL_ACTIVE"=>"Y");
	$arSelect = array("IBLOCK_ID","ID","NAME");
	$db_list = CIBlockSection::GetList(array("SORT"=>"ASC"), $arFilter, false, $arSelect);
	while ($ar_result = $db_list->GetNext()) {
		$arSelect = Array("IBLOCK_ID", "ID", "NAME", "PROPERTY_MAKER");
		$arFilter = Array("IBLOCK_ID"=>$IBLOCK_ID_PRODUCTS,"SECTION_ID"=>$ar_result["ID"],"INCLUDE_SUBSECTIONS"=>"Y","ACTIVE"=>"Y");
		$arSort = Array("SORT"=>"ASC");
		$res = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
		$makersId = array();
		while($arRes = $res->GetNext()) {
			if (!in_array($arRes["PROPERTY_MAKER_VALUE"], $makersId) && !empty($arRes["PROPERTY_MAKER_VALUE"])) {
				$resM = CIBlockElement::GetByID($arRes["PROPERTY_MAKER_VALUE"]);
				$ar_resM = $resM->Fetch();
				$makersId[] = array(
					"ID" => $arRes["PROPERTY_MAKER_VALUE"],
					"NAME" => $ar_resM["NAME"],
				); 
			}
		}
		$arResult[] = array(
			"ID" => $ar_result["ID"],
			"NAME" => $ar_result["NAME"],
			"MAKERS" => $makersId
		);
	}

	foreach ($arResult as $arSection) {
		foreach ($arSection["MAKERS"] as $key => $maker) {
			$sort = array("SORT"=>"ASC");
			$filter = array("IBLOCK_ID"=>$IBLOCK_ID_BRANDS,"PROPERTY_SECTION_ID"=>$arSection["ID"],"PROPERTY_MAKER"=>$maker["ID"]);
			$select = array("IBLOCK_ID", "ID");
			$res = CIBlockElement::GetList($sort, $filter, false, false, $select);
			//Если элемента с таким разделом и производителем ещё не существует, то добавляем его
			if ($res->SelectedRowsCount() == 0) {
				$el = new CIBlockElement;
				$PROP = array(
					"SECTION_ID"=>$arSection["ID"],
					"MAKER"=>$maker["ID"]
				);
				$arParamsT = array("replace_space"=>"_","replace_other"=>"_", 'change_case' => 'L', 'max_len' => 100);
				$tempCode = Cutil::translit($arSection["NAME"].' '.$maker["NAME"],"ru",$arParamsT);
				$arElArray = array(
					"IBLOCK_SECTION_ID" => false,
					"IBLOCK_ID"      => $IBLOCK_ID_BRANDS,
					"PROPERTY_VALUES"=> $PROP,
					"NAME"           => $arSection["NAME"].' '.$maker["NAME"],
					"ACTIVE"         => "Y",
					"CODE"			 => $tempCode
				);
				if($PRODUCT_ID = $el->Add($arElArray)) {
					echo "New ID: ".$PRODUCT_ID."<br/>";
					AddMessage2Log("/include/brands2category.php - Добавлен новый элемент ID=".$PRODUCT_ID." - ".$arSection["NAME"].' '.$maker["NAME"]);
				}
				else {
					echo "Error: ".$el->LAST_ERROR."<br/>";
					AddMessage2Log("/include/brands2category.php - Возникла ошибка ".$el->LAST_ERROR);
				}
			}
		}
	}

	//деактивировать элементы, в которых бренд не активен, либо удален
	$res = CIBlockElement::GetList(
		array("SORT"=>"ASC"),
		array(
			"IBLOCK_ID" => $IBLOCK_ID_BRANDS,
			"ACTIVE" => "Y"
		),
		false,
		false,
		array("IBLOCK_ID", "ID", "PROPERTY_SECTION_ID", "PROPERTY_MAKER")
	);
	while ($arRes = $res->GetNext()) {
		$db_list = CIBlockSection::GetList(
			array(),
			array(
				"IBLOCK_ID" => $IBLOCK_ID_PRODUCTS,
				"ID" => $arRes["PROPERTY_SECTION_ID_VALUE"],
				"ACTIVE" => "Y"
			),
			false,
			array("IBLOCK_ID", "ID")
		);
		$resMakers = CIBlockElement::GetList(
			array("SORT"=>"ASC"),
			array(
				"IBLOCK_ID" => 3,
				"ID" => $arRes["PROPERTY_MAKER_VALUE"],
				"ACTIVE" => "Y"
			),
			false,
			false,
			array("IBLOCK_ID", "ID")
		);
		//если не существует данного активного раздела или активноно производителя
		if ($db_list->SelectedRowsCount() == 0 || $resMakers->SelectedRowsCount() == 0) {
			//деактивировать данный раздел по производителям
			$el = new CIBlockElement;
			if ($resEl = $el->Update($arRes["ID"], array("ACTIVE"=>"N"))) {
				AddMessage2Log("/include/brands2category.php - Деактивирован элемент ID=".$arRes["ID"]);
			}
			else {
				AddMessage2Log("/include/brands2category.php - Возникла ошибка ".$el->LAST_ERROR);
			}
		}
	}
}
?>