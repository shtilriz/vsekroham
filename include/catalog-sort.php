<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="sortby" id="catalog-sort">
	<?Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("catalog-sort");?>
	<span class="sortby__label">Сортировать по:</span>
	<a class="form-button form-button_bg_gray sortby__btn<?if($_SESSION["CAT_SORT"]=="CATALOG_PRICE_1")echo' active'?>" href="" data-sort="CATALOG_PRICE_1" data-order="<?=$_SESSION["CAT_ORDER"]?>">По цене</a>
	<a class="form-button form-button_bg_gray sortby__btn<?if($_SESSION["CAT_SORT"]=="SORT")echo' active'?>" href="" data-sort="SORT" data-order="<?=$_SESSION["CAT_ORDER"]?>">По популярности</a>
	<?/*<a class="form-button form-button_bg_gray sortby__btn<?if($_SESSION["CAT_SORT"]=="NAME")echo' active'?>" href="" data-sort="NAME" data-order="<?=$_SESSION["CAT_ORDER"]?>">По названию</a>*/?>
	<?if (strrpos($_SERVER["REQUEST_URI"],'/brands/')===0) {
		if (!isset($_SESSION["SHOW_ALL"]))
			$_SESSION["SHOW_ALL"] = "Y";
		if (isset($_POST["SHOW_ALL"]))
			$_SESSION["SHOW_ALL"] = $_POST["SHOW_ALL"];
		//echo '<div class="pull-right"><a class="show-hide-not-aval" href="?SHOW_ALL='.($_SESSION["SHOW_ALL"]=="Y"?"N":'Y').'">'.($_SESSION["SHOW_ALL"]=="Y"?'Скрыть со статусом нет в наличии':'Показать все товары').'</a></div>';
		echo '<div class="pull-right"><form action="" method="POST"><input type="hidden" name="SHOW_ALL" value="'.($_SESSION["SHOW_ALL"]=="Y"?"N":'Y').'"><button class="show-hide-not-aval">'.($_SESSION["SHOW_ALL"]=="Y"?'Скрыть со статусом нет в наличии':'Показать все товары').'</button></form></div>';
	}?>
	<?Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("catalog-sort", "");?>
</div>