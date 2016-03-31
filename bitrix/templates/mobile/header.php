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
<html lang="ru" data-offcanvas="" class="off-canvas-wrap"<?($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest'?'':$APPLICATION->ShowViewContent("OpenGraphHTMLtag"));?>>
<head>
	<title><?($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest'?'':$APPLICATION->ShowTitle())?></title>
	<meta id="meta-viewport" name="viewport" content="width=600" />
	<link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/lib-bower/nouislider/distribute/jquery.nouislider.min.css" />
	<link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/lib-bower/nouislider/distribute/jquery.nouislider.pips.min.css" />
	<?($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest'?'':$APPLICATION->ShowHead())?>
	<link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/css/addon_styles.css">
	<script src="<?=SITE_TEMPLATE_PATH?>/lib-bower/jquery/dist/jquery.min.js"></script>
	<script type="text/javascript" src="https://yastatic.net/jquery/cookie/1.0/jquery.cookie.min.js"></script>

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
	<script type="text/javascript">
		var cityobj = {
			YOUR_CITY: '<?=trim($_SESSION["YOUR_CITY"])?>',
			CITY_MOSKOW_REGION: <?=json_encode($arCityMoskowRegion);?>
		};
	</script>
	<script type="text/javascript">
		window.dataLayer = window.dataLayer || [];
	</script>
</head>
<body class="has-b-footer-nav inner-wrap">
<?($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest'?'':$APPLICATION->ShowPanel());?>
	<?$APPLICATION->IncludeComponent("bitrix:menu", ".default", Array(
		"COMPONENT_TEMPLATE" => ".default",
			"ROOT_MENU_TYPE" => "top",	// Тип меню для первого уровня
			"MENU_CACHE_TYPE" => "N",	// Тип кеширования
			"MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
			"MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
			"MENU_CACHE_GET_VARS" => array(	// Значимые переменные запроса
				0 => "",
			),
			"MAX_LEVEL" => "3",	// Уровень вложенности меню
			"CHILD_MENU_TYPE" => "left",	// Тип меню для остальных уровней
			"USE_EXT" => "Y",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
			"DELAY" => "N",	// Откладывать выполнение шаблона меню
			"ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
		),
		false
	);?>
	<div id="main">
		<header class="page-header">
			<ul class="page-header__menu header-menu">
				<?/*<li class="header-menu__item has-dropdown"><a href="#" class="header-menu__link"><span class="header-menu__icon sprite-globe"></span><span class="header-menu__caret"></span></a>
					<div class="header-menu-dropdown js-dropdown-content">
						<div class="b-header-search">
							<header class="b-header-search__header">
								<a href="#" class="b-header-search__button">Отмена</a>
								<div class="b-header-search__input">
									<a href="#" class="icon-close"></a>
									<input type="text" value="" />
								</div>
							</header>
							<ul class="b-header-search__list">
								<li><a href="#">Саки</a></li>
								<li><a href="#">Салават</a></li>
								<li><a href="#">Салехард</a></li>
								<li><a href="#">Сальск</a></li>
								<li><a href="#">Самара</a></li>
								<li><a href="#">Санкт-Петербург</a></li>
								<li><a href="#">Сальск</a></li>
							</ul>
						</div>
					</div>
				</li>*/?>
				<li class="header-menu__item has-dropdown">
					<a href="#" class="header-menu__link"><span class="header-menu__icon sprite-search"></span></a>
					<div class="header-menu-dropdown js-dropdown-content">
						<div class="b-header-search">
							<header class="b-header-search__header">
								<form action="/search/">
									<button type="submit" class="b-header-search__button">Поиск</button>
									<div class="b-header-search__input">
										<a href="#" class="icon-close"></a>
										<input type="text" name="q" value="<?=trim(strip_tags($_GET["q"]))?>" placeholder="Поиск" />
									</div>
								</form>
							</header>
						</div>
					</div>
				</li>
				<li class="header-menu__item" id="basket_small">
					<?$APPLICATION->IncludeComponent(
						"bitrix:main.include",
						"",
						Array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => "/m_include/basket_small.php",
							"EDIT_TEMPLATE" => ""
						),
						false
					);?>
				</li>
			</ul>
			<a href="#" title="Показать меню" class="page-header__side-menu-link side-menu-link js-side-menu-link">МЕНЮ</a>
		</header>
		<div class="page-content">
			<a href="/" class="logo"><img alt="" src="<?=SITE_TEMPLATE_PATH?>/images/logo.png"></a>
			<?global $USER;?>
			<?//if ($USER->IsAdmin()):?>
				<?$APPLICATION->IncludeFile('/m_include/baners.php');?>
			<?/*else:?>
				<?if ($APPLICATION->GetCurDir() == "/"):?>
				<?$APPLICATION->IncludeComponent(
					"dev:banners.main.list",
					".default",
					array(
						"BLOCK_ID" => "5",
						"SORT_BY" => "UF_SORT",
						"SORT_ORDER" => "ASC"
					),
					false
				);?>
				<?endif;?>
			<?endif;*/?>
			<div class="b-content">
