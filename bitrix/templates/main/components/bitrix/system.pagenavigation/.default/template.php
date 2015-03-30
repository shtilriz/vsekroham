<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(!$arResult["NavShowAlways"])
{
	if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
		return;
}

//echo "<pre>"; print_r($arResult);echo "</pre>";

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");

?>
<div class="paginator">
    <ul class="paginator__list">
	<?if ($arResult["NavPageNomer"] > 1):?>

		<?if($arResult["bSavePage"]):?>
			<li class="paginator__item"><a class="paginator__page paginator__page_type_prev" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>">Предыдущая</a></li>
		<?else:?>
			<?if ($arResult["NavPageNomer"] > 2):?>
				<li class="paginator__item"><a class="paginator__page paginator__page_type_prev" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>">Предыдущая</a></li>
			<?else:?>
				<li class="paginator__item"><a class="paginator__page paginator__page_type_prev" href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>">Предыдущая</a></li>
			<?endif?>
		<?endif?>
	<?endif?>

	<?if ($arResult["NavPageNomer"] > 3 && $arResult["NavPageCount"] > 5):?>
		<li class="paginator__item"><a class="paginator__page" href="<?=$arResult["sUrlPath"]?>">1</a></li>
		<li class="paginator__item"><span>...</span></li>
	<?endif?>

	<?while($arResult["nStartPage"] <= $arResult["nEndPage"]):?>

		<?if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):?>
			<li class="paginator__item"><span><?=$arResult["nStartPage"]?></span></li>
		<?elseif($arResult["nStartPage"] == 1 && $arResult["bSavePage"] == false):?>
			<li class="paginator__item"><a class="paginator__page" href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=$arResult["nStartPage"]?></a></li>
		<?else:?>
			<li class="paginator__item"><a class="paginator__page" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>"><?=$arResult["nStartPage"]?></a></li>
		<?endif?>
		<?$arResult["nStartPage"]++?>
	<?endwhile?>

	<?if ($arResult["nEndPage"] < $arResult["NavPageCount"]):?>
		<li class="paginator__item"><span>...</span></li>
		<li class="paginator__item"><a class="paginator__page" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageCount"])?>"><?=$arResult["NavPageCount"]?></a></li>
	<?endif?>

	<?if($arResult["NavPageNomer"] < $arResult["NavPageCount"]):?>
		<li class="paginator__item"><a class="paginator__page paginator__page_type_next" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>">Следующая</a></li>
	<?endif?>
</ul>
</div>