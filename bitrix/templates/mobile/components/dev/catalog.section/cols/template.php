<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);?>


<?if (!empty($arResult["ITEMS"])):?>
	<div id="catalog-section" class="b-content__goods b-goods" data-navrecordcount="<?=$arResult["NAV_RESULT"]->NavRecordCount?>" data-navpagesize="<?=$arResult["NAV_RESULT"]->NavPageSize?>" data-code="<?=$arParams["SECTION_CODE"]?>" itemscope itemtype="http://schema.org/ItemList">

		<link itemprop="url" href="<?=$arResult["SECTION_PAGE_URL"]?>"/>
		<meta itemprop="numberOfElements" content="<?=$arResult["NAV_RESULT"]->NavRecordCount?>">

		<?
		if($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest') {
			$APPLICATION->RestartBuffer();
			$arReturn = array();
		}
		foreach ($arResult["ITEMS"] as $key => $arItem) {
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
			$strMainID = $this->GetEditAreaId($arItem['ID']);
			echo getProductHTML2SectionMobileCols($arItem, $strMainID);
		}
		if($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest') {
			$arReturn["PRODUCTS"] = ob_get_contents();
		}
		//if($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest') die();
		?>

	</div>
	<?//if ($arParams["TEMPLATE_THEME"] == ".default" && $arResult["NAV_RESULT"]->NavPageCount > 1):?>
		<div id="preloader" class="spin-loader"></div>
		<a id="catalogMoreLink" class="show-more-button" href="#" data-pagenum="<?=$arResult["NAV_RESULT"]->PAGEN?>" data-navpagecount="<?=$arResult["NAV_RESULT"]->NavPageCount?>" data-navrecordcount="<?=$arResult["NAV_RESULT"]->NavRecordCount?>" data-navnum="<?=$arResult["NAV_RESULT"]->NavNum?>"<?=($arResult["NAV_RESULT"]->PAGEN==$arResult["NAV_RESULT"]->NavPageCount?' style="display: none"':'')?>>показать еще товары</a>
	<?//endif;?>
	<div id="paginatorBlock">
		<?
		if ($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')
			$APPLICATION->RestartBuffer();
		/*if ($arParams["DISPLAY_BOTTOM_PAGER"])
			echo $arResult["NAV_STRING"];*/
		if ($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest') {
			$arReturn["NAV"] = ob_get_contents();
			ob_end_clean();
			echo json_encode($arReturn);
			die();
		}
		?>
	</div>

<?else:?>
	<div class="search-results__inner">
		<div class="nothing">К сожалению у нас нет товаров, соответствующих вашему запросу.Попробуйте изменить поисковый запрос</div>
	</div>
<?endif;?>


<?$this->SetViewTarget("heading");?>
	<?$arEndPath = end($arResult["PATH"]);?>
	<?if ($arEndPath["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"]):?>
		<h1><?=$arEndPath["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"]?></h1>
	<?else:?>
		<h1><?=$arResult["NAME"]?></h1>
	<?endif;?>
<?$this->EndViewTarget();?>

<?if (($arResult["NAV_RESULT"]->PAGEN == 1) && $arResult["DESCRIPTION"]):?>
<?$this->SetViewTarget("textdescription");?>
	<?=$arResult["DESCRIPTION"]?>
<?$this->EndViewTarget();?>
<?endif?>

<?if ($arResult["ID"] > 0):?>
<script type="text/javascript">
    rrApiOnReady.push(function() {
		try { rrApi.categoryView(<?=$arResult["ID"]?>); } catch(e) {}
	})
</script>
<?endif;?>

<?if ($arResult["NAME"] && $arResult["SECTION_PAGE_URL"] && $arResult["PICTURE"] && $arResult["DESCRIPTION"]):?>
	<?$this->SetViewTarget("OpenGraphHTMLtag");?> prefix="og: http://ogp.me/ns#"<?$this->EndViewTarget();?>
	<?$this->SetViewTarget("OpenGraph");?>
	<meta property="og:title" content="<?=strip_tags($arResult["NAME"])?>" />
	<?
	$obParser = new CTextParser;
	$TEXT = $obParser->html_cut($arResult["DESCRIPTION"], 300);
	?>
	<meta property="og:description" content="<?=trim(strip_tags($TEXT))?>" />
	<meta property="og:image" content="https://<?=$_SERVER["SERVER_NAME"].$arResult["PICTURE"]["SRC"]?>" />
	<meta property="og:type" content="website"/>
	<meta property="og:url" content="https://<?=$_SERVER["SERVER_NAME"].$arResult["SECTION_PAGE_URL"]?>" />
	<?$this->EndViewTarget();?>
<?endif;?>