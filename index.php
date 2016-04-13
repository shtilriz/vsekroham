<?php
require($_SERVER["DOCUMENT_ROOT"] . '/bitrix/header.php');
$APPLICATION->SetPageProperty('ecomm_pagetype', 'home');
$APPLICATION->SetPageProperty('title', 'Магазин "Всё для Крохи" - товары для новорожденных с доставкой по Москве и России');
$APPLICATION->SetPageProperty('textdescription', '<h1>Магазин «Все для Крохи» - товары для новорожденных с доставкой по Москве и России: большой выбор, доступные цены, высокое качество, безупречный сервис!</h1><h2>Большой выбор:</h2><p>Интернет-магазин «Все для Крохи» является официальным дистрибьютором ряда европейских и отечественных брендов, поставляя на российский рынок продукцию ведущих производителей.</p><p>Магазин ориентирован на покупателей с различными потребностями и предпочтениями, непрерывно расширяем ассортимент.</p><h2>Доступные цены:</h2><p>Мы придерживаемся демократичной ценовой политики! Интернет-магазин «Все для Крохи» презентует товары по доступным ценам, радуем клиентов праздничными акциями, скидками, устраиваем распродажи. </p><p>Каталог магазина состоит из бюджетных и элитных предложений: каждый желающий может подобрать что-то особенное и полезное, подходящее для себя!</p><h2>Высокое качество:</h2><p>Мы работаем только с официальными представительствами брендов или самими производителями напрямую, что исключает риск поставок неоригинальной продукции!</p> <p>Наши клиенты гарантировано получают товары с заявленными характеристиками, достойного качества.</p> <h2>Безупречный сервис:</h2><p>«Все для Крохи» работает в рамках законодательной базы РФ!</p><p>Наши клиенты могут рассчитывать на компетентную консультацию менеджеров магазина, помощь в подборе товаров.</p><p>Гарантируя индивидуальный подход, мы в каждом случае разрабатываем наиболее удобный для клиента способ доставки покупки, предлагаем различные способы оплаты.</p><p>В случае возникновения форс-мажора, покупатель интернет-магазина «Все для Крохи» всегда может рассчитывать на нашу поддержку и содействие в решении вопросов.</p>');
$APPLICATION->SetPageProperty('cssclass', 'two-column left-aside');
$APPLICATION->SetTitle('Интернет-магазин детских товаров "Все для Крохи"');
$APPLICATION->IncludeFile('/include/personalRecommendation.php');
?>
<h2>Спецпредложения</h2>
<?
$APPLICATION->AddViewContent('OpenGraphHTMLtag', ' prefix="og: http://ogp.me/ns#"');
$APPLICATION->AddViewContent(
	'OpenGraph',
	sprintf('<meta property="og:title" content="%s"/>', str_replace('"', "'", strip_tags($APPLICATION->GetTitle()))),
	10
);
$APPLICATION->AddViewContent(
	'OpenGraph',
	sprintf('<meta property="og:description" content="%s"/>', strip_tags($APPLICATION->GetPageProperty('textdescription'))),
	20
);
$APPLICATION->AddViewContent(
	'OpenGraph',
	sprintf('<meta property="og:image" content="http://%s/bitrix/templates/main/images/logo.png"/>', $_SERVER["SERVER_NAME"]),
	30
);
$APPLICATION->AddViewContent(
	'OpenGraph',
	sprintf('<meta property="og:url" content="http://%s"/>', $_SERVER["SERVER_NAME"]),
	40
);

$arProducts = [];
$obCache = new CPHPCache();
$cacheLifetime = 3600;
$cacheID = 'main.products';
$cachePath = '/' . $cacheID;
if ($obCache->InitCache($cacheLifetime, $cacheID, $cachePath)) {
	$vars = $obCache->GetVars();
	$arProducts = $vars["arProducts"];
}
elseif ($obCache->StartDataCache() && CModule::IncludeModule('iblock')) {
	$rsProduct = CIBlockElement::GetList(
		['SORT' => 'ASC'],
		['IBLOCK_ID' => 5, 'ACTIVE' => 'Y'],
		false,
		['nTopCount' => 6],
		['PROPERTY_PRODUCT_ID']
	);
	while ($arProduct = $rsProduct->Fetch()) {
		$arProducts[] = $arProduct['PROPERTY_PRODUCT_ID_VALUE'];
	}
	$obCache->EndDataCache(['arProducts' => $arProducts]);
}
$GLOBALS['arrFilter'] = ['ID' => $arProducts];

include($_SERVER["DOCUMENT_ROOT"].'/include/catalog-section.php');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
