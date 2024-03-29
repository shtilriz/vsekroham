<?
if (!isset($_SESSION["YOUR_CITY"])) {
	if (isset($_COOKIE["MY_CITY"]) && strlen($_COOKIE["MY_CITY"]))
		$_SESSION["YOUR_CITY"] = $_COOKIE["MY_CITY"];
	else
		$_SESSION["YOUR_CITY"] = getYourCity();
}
require_once($_SERVER["DOCUMENT_ROOT"]."/include/arCity.php");
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if IE 9]>         <html class="no-js ie9"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js"<?($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest'?'':$APPLICATION->ShowViewContent("OpenGraphHTMLtag"));?>> <!--<![endif]-->
<head>
	<title><?($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest'?'':$APPLICATION->ShowTitle())?></title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/css/jquery-ui.css" />
	<link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/jquery.nouislider.min.css">
	<?($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest'?'':$APPLICATION->ShowHead())?>
	<link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/css/addon_styles.css">
	<?if ($APPLICATION->GetCurPage() == "/basket/order.php"):?>
		<link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/js/datepicker/datepicker3.css">
	<?endif?>

	<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/libs/jquery.min.js"></script>
	<script type="text/javascript" src="https://yastatic.net/jquery/cookie/1.0/jquery.cookie.min.js"></script>
	<?/*<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/libs/modernizr.min.js"></script>
	<script type="text/javascript" src="//code.jquery.com/ui/1.11.0/jquery-ui.min.js"></script>*/?>
	<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery-ui.min.js"></script>
	<!--[if lt IE 9]>
		<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/libs/html5shiv.js"></script>
		<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/libs/selectivizr.min.js"></script>
	<![endif]-->

	<link rel="icon" href="/favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />

	<script>
		var rrPartnerId = "53a000601e994424286fc7d9";
		var rrApi = {};
		var rrApiOnReady = rrApiOnReady || [];
		rrApi.addToBasket = rrApi.order = rrApi.categoryView = rrApi.view =
		   rrApi.recomMouseDown = rrApi.recomAddToCart = function() {};
		(function(d) {
			var ref = d.getElementsByTagName('script')[0];
			var apiJs, apiJsId = 'rrApi-jssdk';
			if (d.getElementById(apiJsId)) return;
			apiJs = d.createElement('script');
			apiJs.id = apiJsId;
			apiJs.async = true;
			apiJs.src = "//cdn.retailrocket.ru/content/javascript/api.js";
			ref.parentNode.insertBefore(apiJs, ref);
		}(document));
	</script>

	<?($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest'?'':$APPLICATION->ShowViewContent("OpenGraph"));?>
	<link rel="apple-touch-icon" href="<?=SITE_TEMPLATE_PATH?>/images/ios/icon60.png">
	<link rel="apple-touch-icon" sizes="76x76" href="<?=SITE_TEMPLATE_PATH?>/images/ios/icon76.png">
	<link rel="apple-touch-icon" sizes="120x120" href="<?=SITE_TEMPLATE_PATH?>/images/ios/icon120.png">
	<link rel="apple-touch-icon" sizes="152x152" href="<?=SITE_TEMPLATE_PATH?>/images/ios/icon152.png">
	<?if (isset($_GET["PAGEN_1"]) && (int)($_GET["PAGEN_1"]) > 0):?>
		<link rel="canonical" href="http://<?=$_SERVER["SERVER_NAME"].GetPagePath(false, false);?>"/>
	<?endif;?>
	<?if ($_SERVER["HTTPS"]=="on"):?>
		<link rel="canonical" href="http://<?=$_SERVER["SERVER_NAME"].GetPagePath(false, false);?>"/>
	<?endif;?>
	<?($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest'?'':$APPLICATION->ShowViewContent("Canonical"));?>
	<script type="text/javascript">(window.Image ? (new Image()) : document.createElement('img')).src = location.protocol + '//vk.com/rtrg?r=wZZcqlPPHkj/XsnIQNN7/GvwoDgY0xLynoWQ1Mjr3OeGJAqVgVjrg2MPwWGgvBE7xGu849133st4WtkevWlmQXq*g/MXTkPBYNjjZ075VcPymlED16dYUfurZUHlnXeZCAANHYtzD/dF1QyR9*iJ7p5Do5H4zvnVxrZ7ppb3D/k-';</script>
	<script type="text/javascript">
		var cityobj = {
			YOUR_CITY: '<?=trim($_SESSION["YOUR_CITY"])?>',
			CITY_ALL: <?=json_encode($arCity);?>,
			CITY_MOSKOW_REGION: <?=json_encode($arCityMoskowRegion);?>
		};
	</script>
	<script src="https://cdn.jsdelivr.net/stopsovetnik/latest/ss.min.js"></script>
	<script type="text/javascript">
		window.dataLayer = window.dataLayer || [];
	</script>
</head>
<body>
<?($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest'?'':$APPLICATION->ShowPanel());?>
<?$arUserBrowser = user_browser();?>
<?if ($arUserBrowser["BROWSER"] == "IE" && (int)$arUserBrowser["VERSION"] < 8):?>
<!--[if lte IE 7]>
<div class="chromeframe">
	Вы используете устаревшую версию браузера, поэтому возможна некорректная работа просматриваемого сайта.
	<br />Для быстрой и безопасной работы рекомендуем <a href="http://outdatedbrowser.com/" rel="nofollow">установить другой браузер или обновить ваш браузер до последней версии</a> или установить <a href="http://www.google.com/chromeframe/?redirect=true" rel="nofollow">Google Chrome Frame</a>!
</div>
<![endif]-->
<?endif;?>

<div id="page" class="js-page <?($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest'?'':$APPLICATION->ShowProperty("cssclass", "one-column"));?>">
	<div class="layout">
		<header class="header">
			<div class="header__inner">
				<?/*<div class="search-block search-block_type_header">
					<form class="search__form" action="#">
						<div class="form-field">
							<div class="form-field__inputtext">
								<input class="inputtext inputtext_type_search" type="text" value="" placeholder="" />
							</div>
						</div>
						<div class="form-field">
							<div class="form-field__button">
								<input type="submit" class="button button_type_search" value="" hidden="hidden" />
							</div>
						</div>
					</form>
				</div>*/?>
				<?$APPLICATION->IncludeComponent("bitrix:menu", "top", Array(
					"ROOT_MENU_TYPE" => "top",	// Тип меню для первого уровня
						"MENU_CACHE_TYPE" => "A",	// Тип кеширования
						"MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
						"MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
						"MENU_CACHE_GET_VARS" => array(	// Значимые переменные запроса
							0 => "",
						),
						"MAX_LEVEL" => "1",	// Уровень вложенности меню
						"CHILD_MENU_TYPE" => "",	// Тип меню для остальных уровней
						"USE_EXT" => "N",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
						"DELAY" => "N",	// Откладывать выполнение шаблона меню
						"ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
					),
					false
				);?>

				<div class="header__middle">
					<a class="logo" href="/">
						<?$APPLICATION->IncludeComponent("bitrix:main.include","",
							array(
								"AREA_FILE_SHOW" => "file",
								"PATH" => "/bitrix/templates/main/page_templates/header/logo.php",
								"EDIT_TEMPLATE" => ""
							),
							false
						);?>
					</a>

					<div class="column">
						<div class="phone">
							<?$APPLICATION->IncludeComponent("bitrix:main.include","",
								array(
									"AREA_FILE_SHOW" => "file",
									"PATH" => "/bitrix/templates/main/page_templates/header/phone1.php",
									"EDIT_TEMPLATE" => ""
								),
								false
							);?>
						</div>
					</div>

					<div class="column">
						<div class="phone">
							<?$APPLICATION->IncludeComponent("bitrix:main.include","",
								array(
									"AREA_FILE_SHOW" => "file",
									"PATH" => "/bitrix/templates/main/page_templates/header/phone2.php",
									"EDIT_TEMPLATE" => ""
								),
								false
							);?>
						</div>
					</div>

					<div class="column">
						<div class="basket-preview" id="basket_small">
							<?$APPLICATION->IncludeComponent(
								"bitrix:main.include",
								"",
								Array(
									"AREA_FILE_SHOW" => "file",
									"PATH" => "/include/basket_small.php",
									"EDIT_TEMPLATE" => ""
								),
								false
							);?>
						</div>
					</div>
				</div>

				<?$APPLICATION->IncludeFile('/include/baners.php');?>

			</div>
		</header>

		<?/*<div id="ny-baners">
			<?Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("ny-baners");?>
			<?if (!(strrpos($_SERVER["REQUEST_URI"],'/basket/')===0) && $GLOBALS["B_SHOW_SLIDER"]):?>

			<?else:?>
				<div class="b-ribbon b-ribbon--ny">
					<div class="b-ribbon__inner">
						<a href="/sales/ng2016.php" style="text-decoration: none;">
							<img class="b-ribbon__img" alt="" src="<?=SITE_TEMPLATE_PATH?>/images/temp/ny-ribbon_optimized.png">
							<div class="b-ribbon__title">Время новогодних скидок!<br>Гарантированно лучшая цена с 10 по 20 декабря</div>
						</a>
					</div>
				</div>
			<?endif;?>
			<?Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("ny-baners", "");?>
		</div>

		<div id="sv-ribbon">
			<?Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("sv-ribbon");?>
			<?if (getYourCity() == "Москва"):?>
			<a class="sv-ribbon" href="/our_mags/">
				Только 14 и 15 февраля. Зайдите в любой из наших магазинов
				<br>
				и получите скидку 6% на весь ассортимент!
				<br>
				<small>Посмотреть список магазинов</small>
			</a>
			<?endif;?>
			<?Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("sv-ribbon", "");?>
		</div>
		*/?>
		<div class="before-content">
			<div class="address-search address-search--ver-1">
				<div id="selectYourCity">
					<?Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("selectYourCity");?>
					<a href="javascript:void(0);" class="address-search__title"><?=($_SESSION["YOUR_CITY"]?$_SESSION["YOUR_CITY"]:'Выберите ваш город');?> <span class="address-search__spinner"></span></a>
					<?Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("selectYourCity", "");?>
				</div>
				<?/*
				<div class="address-search__dropdown address-dropdown">
					<div class="address-dropdown__top">
						<form name="selectCity">
							<input type="text" class="address-dropdown__input" placeholder="Введите Ваш Город" name="YOUR_CITY">
						</form>
					</div>
					<div class="address-dropdown__list-wrapper" id="cityBlock">
						<?//сюда будут подгружаться выбираемые города?>
					</div>
				</div>
				*/?>
				<div class="address-search__dropdown address-dropdown js-address-search__dropdown">
					<?if ($_SESSION["YOUR_CITY"] && !in_array($_SESSION["YOUR_CITY"], $arCityMoskowRegion)):?>
					<div class="address-dropdown-s-top">
						<div class="address-dropdown-s-top__buttons">
							<a href="#" class="js-address-dropdown-close address-dropdown-s-top__button c-button c-button--green">Да</a>
							<a href="#" class="js-address-dropdown-link-content address-dropdown-s-top__button c-button c-button--red">Нет</a>
						</div>
						<div class="address-dropdown-s-top__title">Верно ли указан Ваш город?</div>
					</div>
					<?endif;?>

					<div class="address-search__dropdown-content js-address-search-dropdown-content">
						<div class="address-dropdown__top">
							<form name="selectCity">
								<input type="text" class="address-dropdown__input" placeholder="Введите Ваш Город" name="YOUR_CITY">
							</form>
						</div>
						<div class="address-dropdown__list-wrapper scroll-pane js-scroll-pane" id="cityBlock">
							<?//сюда будут подгружаться выбираемые города?>
						</div>
					</div>

				</div>
			</div>
			<?$APPLICATION->IncludeComponent(
				"bitrix:search.title",
				"catalog",
				array(
					"NUM_CATEGORIES" => "1",
					"TOP_COUNT" => "5",
					"ORDER" => "rank",
					"USE_LANGUAGE_GUESS" => "N",
					"CHECK_DATES" => "N",
					"SHOW_OTHERS" => "N",
					"PAGE" => "#SITE_DIR#search/index.php",
					"SHOW_INPUT" => "Y",
					"INPUT_ID" => "title-search-input",
					"CONTAINER_ID" => "title-search",
					"CATEGORY_0_TITLE" => "",
					"CATEGORY_0" => array(
						0 => "iblock_catalogs",
					),
					"CATEGORY_0_iblock_catalogs" => array(
						0 => "1",
					)
				),
				false
			);?>
		</div>

		<div id="content">
			<?global $USER;?>
			<?if ($APPLICATION->GetCurPage() == "/" || strrpos($_SERVER["REQUEST_URI"],'/catalog/')===0):?>
				<?$APPLICATION->IncludeFile("/include/filter/catalog-filter.php");?>
			<?elseif (!(strrpos($_SERVER["REQUEST_URI"],'/product/')===0)):?>
				<?$APPLICATION->IncludeComponent("bitrix:breadcrumb", ".default", Array(
					"START_FROM" => "0",
						"PATH" => "",
						"SITE_ID" => "-",
					),
					false
				);?>
			<?endif;?>

			<?if ($APPLICATION->GetCurPage() != "/404.php"):?>
			<section class="page-wrapper">
				<div class="main-column">
			<?endif;?>