<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
<ul class="nav" id="side-menu">

<?
foreach($arResult as $arItem) {
	if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1) 
		continue;
	echo '<li><a'.($arItem["SELECTED"]?' class="active"':'').' href="'.$arItem["LINK"].'">'.($arItem["PARAMS"]["ICON"]?'<i class="fa '.$arItem["PARAMS"]["ICON"].' fa-fw"></i> ':'').$arItem["TEXT"].'</a></li>';
}
?>

</ul>
<?endif?>