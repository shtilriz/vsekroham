<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Магазин \"Всё для Крохи\" - товары для новорожденных с доставкой по Москве и России");
$APPLICATION->SetPageProperty("textdescription", "<h1>Магазин &laquo;Все для Крохи&raquo; - товары для новорожденных с доставкой по Москве и России: большой выбор, доступные цены, высокое качество, безупречный сервис!</h1><h2>Большой выбор:</h2><div>Интернет-магазин &laquo;Все для Крохи&raquo; является официальным дистрибьютором ряда европейских и отечественных брендов, поставляя на российский рынок продукцию ведущих производителей.</div><div>Магазин ориентирован на покупателей с различными потребностями и предпочтениями, непрерывно расширяем ассортимент.</div><h2>Доступные цены:</h2><div>Мы придерживаемся демократичной ценовой политики! Интернет-магазин &laquo;Все для Крохи&raquo; презентует товары по доступным ценам, радуем клиентов праздничными акциями, скидками, устраиваем распродажи.&nbsp;</div><div>Каталог магазина состоит из бюджетных и элитных предложений: каждый желающий может подобрать что-то особенное и полезное, подходящее для себя!</div><h2>Высокое качество:</h2><div>Мы работаем только с официальными представительствами брендов или самими производителями напрямую, что исключает риск поставок неоригинальной продукции!</div> <div>Наши клиенты гарантировано получают товары с заявленными характеристиками, достойного качества.</div> <h2>Безупречный сервис:</h2><div>&laquo;Все для Крохи&raquo; работает в рамках законодательной базы РФ!</div><div>Наши клиенты могут рассчитывать на компетентную консультацию менеджеров магазина, помощь в подборе товаров.</div><div>Гарантируя индивидуальный подход, мы в каждом случае разрабатываем наиболее удобный для клиента способ доставки покупки, предлагаем различные способы оплаты.</div><div>В случае возникновения форс-мажора, покупатель интернет-магазина &laquo;Все для Крохи&raquo; всегда может рассчитывать на нашу поддержку и содействие в решении вопросов.</div>");
$APPLICATION->SetPageProperty("cssclass", "two-column left-aside");
$APPLICATION->SetTitle("Интернет-магазин детских товаров \"Все для Крохи\"");
?><h2>Спецпредложения</h2>
<?
$APPLICATION->AddViewContent("OpenGraphHTMLtag", " prefix=\"og: http://ogp.me/ns#\"");
$APPLICATION->AddViewContent("OpenGraph", "<meta property='og:title' content='".strip_tags($APPLICATION->GetTitle())."'/>", 10);
$APPLICATION->AddViewContent("OpenGraph", "<meta property='og:description' content='".strip_tags($APPLICATION->GetPageProperty('textdescription'))."'/>", 20);
$APPLICATION->AddViewContent("OpenGraph", "<meta property='og:image' content='http://".$_SERVER["SERVER_NAME"]."/bitrix/templates/main/images/logo.png'/>", 30);
$APPLICATION->AddViewContent("OpenGraph", "<meta property='og:url' content='http://".$_SERVER["SERVER_NAME"]."'/>", 40);

$arProducts = array();
$obCache = new CPHPCache();
$cacheLifetime = 3600; $cacheID = "main.products"; $cachePath = "/".$cacheID;
CModule::IncludeModule("iblock");
if ($obCache->InitCache($cacheLifetime, $cacheID, $cachePath)) {
	$vars = $obCache->GetVars();
	$arProducts = $vars["arProducts"];
}
elseif ($obCache->StartDataCache()) {
	$rsProduct = CIBlockElement::GetList(
		array("SORT" => "ASC"),
		array("IBLOCK_ID" => 5,"ACTIVE" => "Y"),
		false,
		array("nTopCount" => 12),
		array("IBLOCK_ID", "ID", "PROPERTY_PRODUCT_ID")
	);
	while ($arProduct = $rsProduct->GetNext()) {
		$arProducts[] = $arProduct["PROPERTY_PRODUCT_ID_VALUE"];
	}
	$obCache->EndDataCache(array("arProducts" => $arProducts));
}
$GLOBALS["arrFilter"] = array("ID"=>$arProducts);
?>
<?include($_SERVER["DOCUMENT_ROOT"].'/include/catalog-section.php');?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>