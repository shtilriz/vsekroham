<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Экспорт данных о покупателях по бренду");
CModule::IncludeModule("iblock");
CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");
?>

<form role="form" method="post" action="">
	<input type="hidden" name="action" value="loadCSV">
	<legend>Cкрипт позволяет выгрузить информацию о покупателях по определенному бренду</legend>
	<div class="row">
		<div class="col-md-4">
			<label>Выберите бренд</label>
			<select name="maker" class="form-control">
				<?$res = CIBlockElement::GetList(
					array("NAME" => "ASC"),
					array(
						"IBLOCK_ID" => 3,
						"ACTIVE" => "Y"
					),
					false,
					false,
					array("IBLOCK_ID", "ID", "NAME")
				);
				while ($arRes = $res->GetNext()) {
					echo '<option value="'.$arRes["ID"].'" '.($arRes["ID"]==$_POST["maker"]?'selected':'').'>'.$arRes["NAME"].'</option>';
				}?>
			</select>
		</div>
		<div class="col-md-4">
			<label>Период с</label>
			<?
			if (isset($_POST["start"])) {
				$date_start = $_POST["start"];
			}
			else {
				$date_start = date("d.m.Y", time() - 3600 * 24 * 30 * 3);
			}?>
			<?$APPLICATION->IncludeComponent("bitrix:main.calendar", ".default", array(
				"SHOW_INPUT" => "Y",
				"FORM_NAME" => "",
				"INPUT_NAME" => "start",
				"INPUT_NAME_FINISH" => "",
				"INPUT_VALUE" => $date_start,
				"INPUT_VALUE_FINISH" => "",
				"SHOW_TIME" => "Y",
				"HIDE_TIMEBAR" => "Y"
				),
				false
			);?>
		</div>
		<div class="col-md-4">
			<label>Период по</label>
			<?
			if (isset($_POST["end"])) {
				$date_end = $_POST["end"];
			}
			else {
				$date_end = date("d.m.Y");
			}?>
			<?$APPLICATION->IncludeComponent("bitrix:main.calendar", ".default", array(
				"SHOW_INPUT" => "Y",
				"FORM_NAME" => "",
				"INPUT_NAME" => "end",
				"INPUT_NAME_FINISH" => "",
				"INPUT_VALUE" => $date_end,
				"INPUT_VALUE_FINISH" => "",
				"SHOW_TIME" => "Y",
				"HIDE_TIMEBAR" => "Y"
				),
				false
			);?>
		</div>
	</div>
	<br/>
	<button type="submit" class="btn btn-primary">Выгрузить</button>
</form>

<?
if (isset($_POST["action"]) && $_POST["action"] == "loadCSV" && intval($_POST["maker"]) > 0) {
	$arReturn = array();
	$arTable = array();
	$arFilter = array();
	if (strlen($_POST["start"]) > 0) {
		$arFilter[">=DATE_INSERT"] = ConvertDateTime($_POST["start"],"DD.MM.YYYY HH:MI:SS");
	}
	if (strlen($_POST["end"]) > 0) {
		$arFilter["<=DATE_INSERT"] = ConvertDateTime($_POST["end"],"DD.MM.YYYY HH:MI:SS");
	}

	$db_sales = CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), $arFilter);
	while ($arOrder = $db_sales->Fetch()) {
		$dbBasketItems = CSaleBasket::GetList(
			array("ID" => "ASC"),
			array(
				"LID" => SITE_ID,
				"ORDER_ID" => $arOrder["ID"]
			),
			false,
			false,
			array("ID", "PRODUCT_ID", "PRICE")
		);
		while ($arItems = $dbBasketItems->Fetch()) {
			$arProducts = array();
			$PRODUCT_ID = $arItems["PRODUCT_ID"];
			$mxResult = CCatalogSku::GetProductInfo($PRODUCT_ID);
			if (is_array($mxResult)) {
				$PRODUCT_ID = $mxResult['ID'];
			}
			$res = CIBlockElement::GetList(array(),	array("ACTIVE"=>"Y","ID"=>$PRODUCT_ID),false,false,array("IBLOCK_ID", "ID", "NAME", "PROPERTY_MAKER"));
			while ($arRes = $res->GetNext()) {
				if ($arRes["PROPERTY_MAKER_VALUE"] == $_POST["maker"] && !array_key_exists($arOrder["ID"], $arReturn)) {
					$db_vals = CSaleOrderPropsValue::GetList(array("SORT" => "ASC"),array("ORDER_ID" => $arOrder["ID"], "ORDER_PROPS_ID" => 3));
					if ($arVals = $db_vals->Fetch())
						$PHONE = $arVals["VALUE"];
					$db_vals = CSaleOrderPropsValue::GetList(array("SORT" => "ASC"),array("ORDER_ID" => $arOrder["ID"], "ORDER_PROPS_ID" => 1));
					if ($arVals = $db_vals->Fetch())
						$FIO = $arVals["VALUE"];
					$db_vals = CSaleOrderPropsValue::GetList(array("SORT" => "ASC"),array("ORDER_ID" => $arOrder["ID"], "ORDER_PROPS_ID" => 2));
					if ($arVals = $db_vals->Fetch())
						$EMAIL = $arVals["VALUE"];

					$dbBsk = CSaleBasket::GetList(
						array("ID" => "ASC"),
						array(
							"LID" => SITE_ID,
							"ORDER_ID" => $arOrder["ID"]
						),
						false,
						false,
						array("ID", "PRODUCT_ID")
					);
					while ($arBsk = $dbBsk->Fetch()) {
						/*$PR_ID = $arBsk["PRODUCT_ID"];
						$mxPr = CCatalogSku::GetProductInfo($PR_ID);
						if (is_array($mxResult)) {
							$PR_ID = $mxPr['ID'];
						}*/
						$rsPr = CIBlockElement::GetList(array(), array("ACTIVE"=>"Y","=ID"=>$arBsk["PRODUCT_ID"]),false,false,array("IBLOCK_ID", "ID", "NAME"));
						while ($arPr = $rsPr->GetNext()) {
							$arProducts[] = $arPr["NAME"];
						}
					}

					$arReturn[$arOrder["ID"]] = array(
						"ORDER_ID" => iconv('utf8', 'cp1251', $arOrder["ID"]),
						"FIO" => iconv('utf8', 'cp1251', $FIO),
						"EMAIL" => iconv('utf8', 'cp1251', $EMAIL),
						"PHONE" => iconv('utf8', 'cp1251', $PHONE),
						"PRODUCT_NAME" => iconv('utf8', 'cp1251', implode(", ", $arProducts)),
						"DATE_INSERT" => iconv('utf8', 'cp1251', $arOrder["DATE_INSERT"])
					);
					$arTable[$arOrder["ID"]] = array(
						"ORDER_ID" => $arOrder["ID"],
						"FIO" => $FIO,
						"EMAIL" => $EMAIL,
						"PHONE" => $PHONE,
						"PRODUCT_NAME" => implode(",<br/>", $arProducts),
						"DATE_INSERT" => $arOrder["DATE_INSERT"]
					);
				}
			}
		}
	}
	if (!empty($arReturn)) {
		//Записать масив в csv файл
		$file = '/upload/order2brand.csv';
		$fp = fopen($_SERVER["DOCUMENT_ROOT"].$file, 'w');

		foreach ($arReturn as $value) {
			fputcsv($fp, $value, ";");
		}

		fclose($fp);

		//Вывести ссылку на файл
		echo '<div class="bg-success" style="padding: 15px;margin:10px 0;">Экспорт успешно завершен. Выгружено '.count($arReturn).' заказов. <a href="/upload/order2brand.csv" target="_blank">Скачать файл выгрузки</a>.</div>';

		echo '<table class="table table-bordered">
			<tr><th>№ заказа</th><th>Имя покупателя</th><th>Email</th><th>Телефон</th><th>Название товара</th><th>Дата покупки</th></tr>';
		foreach ($arTable as $key => $arItem) {
			echo '<tr>
				<td>'.$arItem["ORDER_ID"].'</td>
				<td>'.$arItem["FIO"].'</td>
				<td>'.$arItem["EMAIL"].'</td>
				<td>'.$arItem["PHONE"].'</td>
				<td>'.$arItem["PRODUCT_NAME"].'</td>
				<td>'.$arItem["DATE_INSERT"].'</td>
			</tr>';
		}
		echo '</table>';
	}
	else {
		echo '<div class="bg-warning" style="padding: 15px;margin:10px 0;">По данным параметрам ничего не найдено. Попробуйте изменить параметры фильтра.</div>';
	}
}
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>