<?php
$_SERVER["DOCUMENT_ROOT"] = "/home/vsekroham/public_html";
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

//скрипт заходит на сайт http://api.retailrocket.ru, получает список товаров в разделе и проставляет индексы сортировки у товаров в порядке сортировки ретейлрокета
AddMessage2Log("/include/setSortIndex.php - Запуск скрипта");

if (CModule::IncludeModule("iblock")) {
	set_time_limit(0);
	//получить массив ID разделов верхнего уровня
	$arSect = array();
	$db_list = CIBlockSection::GetList(
		array(),
		array(
			"IBLOCK_ID" => IBLOCK_PRODUCT_ID,
			"ACTIVE" => "Y",
			"DEPTH_LEVEL" => 1
		),
		false,
		array("IBLOCK_ID", "ID")
	);
	while ($ar_result = $db_list->GetNext()) {
		$arSect[] = $ar_result["ID"];
	}

	foreach ($arSect as $sID) {
		//сделать запрос на retailrocket и получить массив товаров раздела
		$query = 'http://api.retailrocket.ru/api/1.0/Recomendation/SortItemsInCategory/53a000601e994424286fc7d9/'.$sID;
		$xml_string = file_get_contents($query);
		$arData = json_decode($xml_string,true);
		//проставить индексы сортировки у товаров
		$i = 1;
		if (!empty($arData)) {
			$res = CIBlockElement::GetList(
				array("ID" => "ASC"),
				array(
					"IBLOCK_ID" => IBLOCK_PRODUCT_ID,
					"SECTION_ID" => $sID,
					"INCLUDE_SUBSECTIONS" => "Y"
				),
				false,
				false,
				array("IBLOCK_ID", "ID")
			);
			while ($arRes = $res->GetNext()) {
				if (in_array($arRes["ID"], $arData)) {
					$el = new CIBlockElement;
					$el->Update(
						$arRes["ID"],
						array(
							"SORT" => array_search($arRes["ID"], $arData),
							//"MODIFIED_BY" => 1
						)
					);
					$i++;
				}
				else {
					$el = new CIBlockElement;
					$el->Update(
						$arRes["ID"],
						array(
							"SORT" => 999,
							//"MODIFIED_BY" => 1
						)
					);
				}
			}
		}
	}
}
?>