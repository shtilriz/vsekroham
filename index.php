<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Магазин \"Всё для Крохи\" - товары для новорожденных с доставкой по Москве и России");
$APPLICATION->SetPageProperty("textdescription", "<h1>Интернет-магазин детских товаров «Все для крохи» обеспечивает доставку заказанных товаров в любой регион Российской Федерации.</h1><p>В интернет-магазине товаров для детей «Все для крохи» вы найдете все, что необходимо вашему ребенку. У нас вы купите:</p><ul><li>детские кроватки, коляски, комоды;</li><li>электромобили и велосипеды для детей;</li><li>детские автомобильные кресла;</li></ul><p>Маленьким детям нужна не только забота, но и наше родительское внимание, очень много внимания. Взамен мы получаем радость в глазах ребенка и его чистую бескорыстную любовь. Чтобы правильно воспитать ребенка, родителям придется затратить очень много сил и времени – такой уж родительский долг. Интернет-магазин для новорожденных «Все для крохи» взял на себя миссию значительно облегчить задачу каждого родителя. Сколько всего нужно купить родителям для своего чада. Очень важно ничего не забыть, но как не запутаться в огромном многообразии товаров для новорожденных.</p><p>Все товары в нашем интернет-магазине детских товаров удобно упорядочены и разбиты на группы. Наше меню построено так, чтобы взглянув на него, вы ничего не забыли. У нас вы сможете купить все необходимое для вашего малыша, не покидая уютный дом и не ища нужные товары по всему городу, простаивая не один час в пробках.</p>");
$APPLICATION->SetPageProperty("cssclass", "two-column left-aside");
$APPLICATION->SetTitle("Интернет-магазин детских товаров \"Все для Крохи\"");
?>

<h2>Спецпредложения</h2>

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

<?include($_SERVER["DOCUMENT_ROOT"].'/include/catalog-section.php');?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
