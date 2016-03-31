<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
require($_SERVER["DOCUMENT_ROOT"]."/include/arCity.php");

$yourCity = ($_SESSION["YOUR_CITY"]?$_SESSION["YOUR_CITY"]:getYourCity());

$weight = (int)$arResult["TOTAL_WEIGHT"];
$width = $length = $height = round(pow($arResult["VOLUME"], 1/3));

$cache_id = md5(serialize(array($yourCity, $weight, $width, $length, $height)));
$cache_dir = "/edost";
$obCache = new CPHPCache;
if($obCache->InitCache(3600*24, $cache_id, $cache_dir)) {
	$arReturn = $obCache->GetVars();
}
elseif ($obCache->StartDataCache()) {
	if ($yourCity && $weight > 0) {
		if ($curl = curl_init()) {
			$arPost = array(
				"id" => 4590,
				"p" => "CC5jGjKi9guSHltNUiNmZG7XQ3vRf2js",
				"to_city" => $yourCity,
				"weight" => (round($weight/1000, 2)),
				"strah" => 0
			);
			if ($length > 0 && $width > 0 && $height > 0) {
				$arPost["ln"] = $length;
				$arPost["wd"] = $width;
				$arPost["hg"] = $height;
			}
			curl_setopt($curl, CURLOPT_URL, 'http://www.edost.ru/edost_calc_kln.php');
			curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $arPost);
			$out = curl_exec($curl);
			curl_close($curl);

			$arReturn = array();
			$xml = new CDataXML();
			$xml->LoadString($out);

			$arData = $xml->GetArray();
			foreach ($arData["rsp"]["#"]["tarif"] as $key => $arItem) {
				$id = (int)$arItem["#"]["id"][0]["#"];
				$company = trim($arItem["#"]["company"][0]["#"]);
				$price = (int)$arItem["#"]["price"][0]["#"];
				$day = trim($arItem["#"]["day"][0]["#"]);
				$name = trim($arItem["#"]["name"][0]["#"]);
				$arReturn[] = array(
					"id" => $id,
					"company" => $company,
					"price" => $price,
					"day" => $day,
					"name" => $name
				);
			}
		}
	}
	$obCache->EndDataCache($arReturn);
}
foreach ($arReturn as $key => $arItem) {
	//если Курьер (тариф 1)
	if ($arItem["id"] == 31) {
		if ((int)$_REQUEST["price"] >= 2000) {
			$arReturn[$key]["name"] = "За каждый км. от МКАД";
			$arReturn[$key]["price"] = 25;
		}
		else {
			$arReturn[$key]["name"] = "350 р. + 25р. за каждый км от МКАД";
			$arReturn[$key]["price"] = 350;
		}
	}
	if ($arItem["id"] == 32) {
		if ((int)$_REQUEST["price"] < 2000) {
			$arReturn[$key]["name"] = "если сумма заказа ниже 2000 рублей";
			$arReturn[$key]["price"] = 350;
		}
	}

}
//ищем самый привлекательный вариант по цене
if (count($arReturn) > 0) {
	$price = 999999;
	$arCurrent = array();
	foreach ($arReturn as $key => $arItem) {
		if ((int)$arItem["price"] < $price) {
			$arCurrent = $arItem;
			$price = $arItem["price"];
		}
		if ($price == 0)
			break;
	}
	$arResult["EDOST_CURRENT"] = $arCurrent;
}
$arResult["EDOST"] = $arReturn;
?>