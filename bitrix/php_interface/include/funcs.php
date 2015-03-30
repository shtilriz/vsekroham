<?
//возвращает html товара в списке товаров
function getProductHTML2Section(&$arItem, &$strMainID = '') {
	$y=CFile::ResizeImageGet(
		$arItem["PREVIEW_PICTURE"]["ID"],
		array("width" => 225, "height" => 180),
		BX_RESIZE_IMAGE_PROPORTIONAL,
		true
	);
	$arPrices = array();
	if (!empty($arItem["OFFERS"])) {
		$arFirstOffer = reset($arItem["OFFERS"]);
		$arPrices = $arFirstOffer["PRICES"];
	}
	else
		$arPrices = $arItem["PRICES"];

	$oldPrice = "";
	if ($arPrices["BASE"]["DISCOUNT_DIFF"] > 0) {
		$oldPrice = "<i>{$arPrices["BASE"]["PRINT_VALUE"]}</i>";
	}
	elseif ($arPrices["MARGIN"]["VALUE"] > 0) {
		$oldPrice = "<i>{$arPrices["MARGIN"]["PRINT_VALUE"]}</i>";
	}
	$arRate = getRatingProduct($arItem["ID"]);
	$stars = '';
	for ($i=0; $i < 5; $i++)
		$stars .= '<span class="'.($i<$arRate["RATE"]?'star-blue':'star-gray').'"></span>';

	$html = '<li class="stuff-list__item"'.($strMainID?' id="'.$strMainID.'"':'').' itemprop="itemListElement" itemscope itemtype="http://schema.org/Product">
		<meta itemprop="description" content="'.($arItem["PREVIEW_TEXT"]?strip_tags($arItem["PREVIEW_TEXT"]):TruncateText(strip_tags($arItem["DETAIL_TEXT"]), 300).'...').'">
		<a class="stuff-list__link" href="'.$arItem["DETAIL_PAGE_URL"].'" itemprop="url">
			<img src="'.$y["src"].'" alt="'.$arItem["NAME"].'" itemprop="image">
			<span itemprop="name">'.$arItem["NAME"].'</span>
		</a>
		<table class="stars-wrapper">
			<tr>
				<td><div class="stars">'.$stars.'</div></td>
				<td><a class="more-link" href="'.$arItem["DETAIL_PAGE_URL"].'#reviews">'.($arRate["COUNT"]>0?$arRate["COUNT"].' '.rating_txt($arRate["COUNT"]):'Оцените первым').'</a></td>
			</tr>
		</table>
		<div class="price-block" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
			'.($arPrices["BASE"]["DISCOUNT_VALUE"]>0?'<span class="stuff-list__price" itemprop="price">'.$oldPrice.$arPrices["BASE"]["PRINT_DISCOUNT_VALUE"].'</span>':'').'
			<meta itemprop="priceCurrency" content="RUB"/>
		</div>
		<div class="stuff-list__bottom">
			'.($arItem["PROPERTIES"]["AVAILABLE"]["VALUE"]=="Y"?'<a class="add-to-basket form-button '.(!empty($arItem["OFFERS"])?'showSelectOffers':'add2basket').'" href="'.$arItem["DETAIL_PAGE_URL"].'" data-id="'.$arItem["ID"].'">Купить</a>':'<span class="not-available">Нет в наличии</span>').'
			<div class="badge-wrapper">
				'.($arItem["PRICES"]["BASE"]["DISCOUNT_DIFF_PERCENT"]>0?'<div class="badge badge_type_discount">Скидка '.$arItem["PRICES"]["BASE"]["DISCOUNT_DIFF_PERCENT"].'%</div>':'').'
				'.(!empty($arItem["PROPERTIES"]["GIFT"]["VALUE"])?'<div class="badge badge_type_gift">+ Подарок</div>':'').'
				'.($arItem["PROPERTIES"]["NEW"]["VALUE"]=="Y"?'<div class="badge badge_type_gift">Новинка</div>':'').'
			</div>
		</div>
	</li>';
	return $html;
}

//возвращает название города текущего пользователя по IP
function getYourCity() {
	require($_SERVER["DOCUMENT_ROOT"]."/include/arCity.php");
	if ($_SERVER['HTTP_X_FORWARDED_FOR']) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	else {
		$ip = $_SERVER["REMOTE_ADDR"];
	}
	$query = new GeobazaQuery();
	$geobaza = $query->get_path($ip);
	//$city = $objGeoBase->translations[0]->ru;
	$arPath = array();
	foreach ($geobaza as $obj) {
		$arPath[$obj->type] = $obj->translations[0]->ru;
	}
	$thisCity = '';
	if (isset($arPath["locality"]) && strlen($arPath["locality"]) > 0) {
		//проверить, есть ли в базе такой город и сколько раз повторяется
		$arThisCity = array();
		foreach ($arCity as $key => $city) {
			$pos = stripos($city, $arPath["locality"]);
			if ($pos !== false && $pos == 0) {
				$arThisCity[] = $city;
			}
		}
		if (count($arThisCity) == 1) {
			reset($arThisCity);
			$thisCity = current($arThisCity);
		}
		elseif (count($arThisCity) > 1 && isset($arPath["region"]) && strlen($arPath["region"]) > 0) {
			foreach ($arThisCity as $key => $city) {
				$pos = stripos($city, $arPath["region"]);
				if ($pos !== false) {
					$thisCity = $city;
					break;
				}
			}
		}
	}
	return $thisCity;
}

//возвращает сумму заказа в корзине текущего пользователя
function getSummBasket () {
	global $USER;
	$allSum = 0.0;
	$allWeight = 0.0;
	$arBasketItems = array();
	if (CModule::IncludeModule("sale")) {
		$dbBasketItems = CSaleBasket::GetList(
			array("ID" => "ASC"),
			array(
				"FUSER_ID" => CSaleBasket::GetBasketUserID(),
				"LID" => SITE_ID,
				"ORDER_ID" => "NULL"
			),
			false,
			false,
			array(
				"ID", "NAME", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY",
				"PRICE", "WEIGHT", "DETAIL_PAGE_URL", "NOTES", "CURRENCY", "VAT_RATE", "CATALOG_XML_ID",
				"PRODUCT_XML_ID", "SUBSCRIBE", "DISCOUNT_PRICE", "PRODUCT_PROVIDER_CLASS", "TYPE", "SET_PARENT_ID"
			)
		);
		while ($arItem = $dbBasketItems->Fetch()) {
			$allSum += $arItem["PRICE"] * $arItem["QUANTITY"];
			$allWeight += ($arItem["WEIGHT"] * $arItem["QUANTITY"]);
			$arBasketItems[] = $arItem;
		}
		if (!empty($arBasketItems)) {
			$arOrder = array(
				'SITE_ID' => SITE_ID,
				'USER_ID' => $USER->GetID(),
				'ORDER_PRICE' => $allSum,
				'ORDER_WEIGHT' => $allWeight,
				'BASKET_ITEMS' => $arBasketItems
			);
			$arOptions = array();
			$arErrors = array();
			CSaleDiscount::DoProcessOrder($arOrder, $arOptions, $arErrors);
			$allSum = $arOrder["ORDER_PRICE"];
		}
	}
	return $allSum;
}

//Проверяет, имеются ли у раздела активные подразделы
function isSubsection (&$arFields) {
	$isSubsection = false;
	if ($arFields["ID"] > 0 && CModule::IncludeModule("iblock")) {
		$arFilter = array(
			"IBLOCK_ID" => $arFields["IBLOCK_ID"],
			"SECTION_ID" => $arFields["ID"],
			"ACTIVE" => "Y"
		);
		if (CIBlockSection::GetCount($arFilter) > 0) {
			$isSubsection = true;
		}
	}
	return $isSubsection;
}

/*
проверяет, в наличии товар или нет
товар считается в статусе "в наличии" в слудующих случаях:
1) если у товара есть торговые предложения, то хотя бы одно из них должно быть в наличии (доступное количество > 0)
2) если товар без торговых предложений, то у него должно быть доступное количество > 0
*/
function isProductAvailable (&$arResult) {
	$bAvailable = false;
	if (!empty($arResult) && is_array($arResult) && $arResult["IBLOCK_ID"] == IBLOCK_PRODUCT_ID) {
		//если товар с торговыми предложениями
		if (!empty($arResult["OFFERS"])) {
			foreach ($arResult["OFFERS"] as $keyOffer => $arOffer) {
				if (0 < $arOffer["CATALOG_QUANTITY"]) {
					$bAvailable = true;
					break;
				}
			}
		}
		//иначе простой товар
		elseif (0 < $arResult["CATALOG_QUANTITY"]) {
				$bAvailable = true;
		}
	}
	return $bAvailable;
}

//очищает переменную типа integer
function clearInt($data) {
	return abs((int)$data);
}
//очищает переменную типа string
function clearStr($data) {
	global $link;
	return trim(strip_tags($data));
}

//возвращает массив с рейтингом товара $id по оценкам в отзывах и количеством отзывов
use Bitrix\Highloadblock as HL_REVIEWS;
use Bitrix\Main\Entity;
function getRatingProduct($id) {
	$arReturn = array("RATE" => 0, "COUNT" => 0);
	if ($id > 0 && CModule::IncludeModule("highloadblock")) {
		$hlblock = HL_REVIEWS\HighloadBlockTable::getById(6)->fetch();
		$entity = HL_REVIEWS\HighloadBlockTable::compileEntity($hlblock);
		$main_query = new Entity\Query($entity);
		$main_query->setSelect(array('UF_RATE'));
		$main_query->setFilter(
			array(
				"UF_ACTIVE" => true,
				"UF_PRODUCT" => $id
			)
		);
		$result = $main_query->exec();
		$result = new CDBResult($result);
		$rate = 0;
		$arReturn["COUNT"] = $result->SelectedRowsCount();
		if ($arReturn["COUNT"]) {
			while ($arReview = $result->Fetch()) {
				$rate += (int)$arReview["UF_RATE"];
			}
			if ($rate)
				$arReturn["RATE"] = round($rate/$arReturn["COUNT"]);
		}
	}
	return $arReturn;
}

//выводит слово "отзыв" в разных падежах в зависимости от их количества
if (!function_exists('rating_txt')) {
	function rating_txt($col) {
		$str = array('отзыв', 'отзыва', 'отзывов');
		$num = intval($col);
		$s='';
		if($num>19)
			$num = $num - floor($num/10)*10;
		switch ($num) {
			case 1:
				$s = $str[0]; break;
			case $num < 5:
				$s = $str[1]; break;
			default:
				$s = $str[2];
		}
		return $s;
	}
}

//возвращает название и версию браузера
function user_browser() {
	$agent = $_SERVER["HTTP_USER_AGENT"];
	preg_match("/(MSIE|Opera|Firefox|Chrome|Version|Opera Mini|Netscape|Konqueror|SeaMonkey|Camino|Minefield|Iceweasel|K-Meleon|Maxthon)(?:\/| )([0-9.]+)/", $agent, $browser_info);
	list(,$browser,$version) = $browser_info;
	$arReturn = array();
	if (preg_match("/Opera ([0-9.]+)/i", $agent, $opera))
		return array("BROWSER" => 'Opera', "VERSION" => $opera[1]);
	if ($browser == 'MSIE') {
		preg_match("/(Maxthon|Avant Browser|MyIE2)/i", $agent, $ie);
		if ($ie)
			return array("BROWSER" => $ie[1].' based on IE', "VERSION" => $version);
		return array("BROWSER" => 'IE', "VERSION" => $version);
	}
	if ($browser == 'Firefox') {
		preg_match("/(Flock|Navigator|Epiphany)\/([0-9.]+)/", $agent, $ff);
		if ($ff) return array("BROWSER" => $ff[1], "VERSION" => $ff[2]);
	}
	if ($browser == 'Opera' && $version == '9.80') return array("BROWSER" => 'Opera', "VERSION" => substr($agent,-5));
	if ($browser == 'Version') return array("BROWSER" => 'Safari', "VERSION" => $version);
	if (!$browser && strpos($agent, 'Gecko')) return array("BROWSER" => 'Browser based on Gecko', "VERSION" => '');
	return array("BROWSER" => $browser, $version);
}

function _substr($text, $length) {
	$length = strripos(substr($text, 0, $length), '.');
	return substr($text, 0, $length);
}
?>