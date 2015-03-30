<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
//переносит структуру разделов с сайта все крохам
/*CModule::IncludeModule("iblock");
$sectContent = file_get_contents("http://vsekroham.ru/export/getSections.php");
$arSectionsOld = json_decode($sectContent, true);

$arSectionsNew = array();
$db_list = CIBlockSection::GetList(
	array("SORT"=>"ASC"),
	array(
		"IBLOCK_ID" => 1,
		"ACTIVE" => "Y",
		"GLOBAL_ACTIVE" => "Y",
	),
	false,
	array("IBLOCK_ID", "ID", "NAME", "XML_ID")
);
while ($ar_result = $db_list->GetNext()) {
	$arSectionsNew[] = $ar_result;	
}


//пройтись по всем разделам на старом сайте
foreach ($arSectionsOld as $key => $arSectOld) {
	//если текущий раздел старого сайта ещё не добавлен и его IBLOCK_SECTION_ID равен XML_ID какого либо раздела на новом сайте, то добавить данный раздел на новый сайт в группу с этим XML_ID
	$bAdded = false;
	$SECTION_ID = 0; //группа на новом сайте, в которую добавлять текущий раздел

	foreach ($arSectionsNew as $cell => $arSectNew) {
		if ($arSectNew["XML_ID"] == $arSectOld["ID"]) {
			$bAdded = true;
		}
		if ($arSectOld["IBLOCK_SECTION_ID"] == $arSectNew["XML_ID"]) {
			$SECTION_ID = $arSectNew["ID"];
		}
	}
	if (!$bAdded && $SECTION_ID > 0) {
		$bs = new CIBlockSection;
		$arFields = Array(
			"ACTIVE" => "Y",
			"IBLOCK_SECTION_ID" => $SECTION_ID,
			"IBLOCK_ID" => 1,
			"NAME" => $arSectOld["NAME"],
			"DESCRIPTION" => $arSectOld["DESCRIPTION"],
			"DESCRIPTION_TYPE" => $arSectOld["DESCRIPTION_TYPE"],
			"PICTURE" => $arSectOld["PICTURE"]?CFile::MakeFileArray('http://vsekroham.ru'.$arSectOld["PICTURE"]):'',
			"SORT" => $arSectOld["SORT"],
			"XML_ID" => $arSectOld["ID"],
			"CODE" =>  $arSectOld["CODE"],
			"UF_SHOW" => $arSectOld["UF_SHOW"]?1:0,
			"UF_SHOWRECOMMEND" => $arSectionsOld["UF_SHOWRECOMMEND"]
		);
		if ($ID = $bs->Add($arFields)) {
			echo 'new ID ='.$ID.'<br/>';
		}
		else {
			echo $bs->LAST_ERROR;
		}
	}
}*/
?>