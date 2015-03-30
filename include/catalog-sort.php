<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="sortby" id="catalog-sort">
	<?Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("catalog-sort");?>
	<span class="sortby__label">Сортировать по:</span>
	<a class="form-button form-button_bg_gray sortby__btn<?if($_SESSION["CAT_SORT"]=="CATALOG_PRICE_1")echo' active'?>" href="" data-sort="CATALOG_PRICE_1" data-order="<?=$_SESSION["CAT_ORDER"]?>">По цене</a>
	<a class="form-button form-button_bg_gray sortby__btn<?if($_SESSION["CAT_SORT"]=="SORT")echo' active'?>" href="" data-sort="SORT" data-order="<?=$_SESSION["CAT_ORDER"]?>">По популярности</a>
	<?/*<a class="form-button form-button_bg_gray sortby__btn<?if($_SESSION["CAT_SORT"]=="NAME")echo' active'?>" href="" data-sort="NAME" data-order="<?=$_SESSION["CAT_ORDER"]?>">По названию</a>*/?>
	<?Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("catalog-sort", "");?>
</div>