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
$this->setFrameMode(true);

if(!$arResult["NavShowAlways"])
{
	if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
		return;
}

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");
?>

<div class="paginator paginator_type_light">
	<ul class="paginator__list">

	<?if ($arResult["NavPageNomer"] > 1):?>

		<?if($arResult["bSavePage"]):?>
			<li class="paginator__item"><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>" class="paginator__page paginator__page_type_prev" data-page="<?=$arResult["NavPageNomer"]-1?>">&nbsp;</a></li>
		<?else:?>
			<?if ($arResult["NavPageNomer"] > 2):?>
				<li class="paginator__item"><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>" class="paginator__page paginator__page_type_prev" data-page="<?=$arResult["NavPageNomer"]-1?>">&nbsp;</a></li>
			<?else:?>
				<li class="paginator__item"><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>" class="paginator__page paginator__page_type_prev" data-page="<?=$arResult["NavPageNomer"]-1?>">&nbsp;</a></li>
			<?endif?>
		<?endif?>

	<?else:?>
		<li class="paginator__item"><span class="paginator__page paginator__page_type_prev">&nbsp;</span></li>
	<?endif?>

	<?while($arResult["nStartPage"] <= $arResult["nEndPage"]):?>

		<?if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):?>
			<li class="paginator__item"><span><?=$arResult["nStartPage"]?></span>
		<?elseif($arResult["nStartPage"] == 1 && $arResult["bSavePage"] == false):?>
			<li class="paginator__item"><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>" class="paginator__page" data-page="<?=$arResult["nStartPage"]?>"><?=$arResult["nStartPage"]?></a></li>
		<?else:?>
			<li class="paginator__item"><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>" class="paginator__page" data-page="<?=$arResult["nStartPage"]?>"><?=$arResult["nStartPage"]?></a></li>
		<?endif?>
		<?$arResult["nStartPage"]++?>
	<?endwhile?>

	<?if($arResult["NavPageNomer"] < $arResult["NavPageCount"]):?>
		<li class="paginator__item"><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>" class="paginator__page paginator__page_type_next" data-page="<?=$arResult["NavPageNomer"]+1?>">&nbsp;</a></li>
	<?else:?>
		<li class="paginator__item"><span class="paginator__page paginator__page_type_next">&nbsp;</span></li>
	<?endif?>

	</ul>
</div>