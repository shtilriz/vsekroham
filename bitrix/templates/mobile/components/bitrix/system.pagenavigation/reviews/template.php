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

<nav>
	<ul class="pagination product__pagination pagination--full pagination--bordered">

	<?if ($arResult["NavPageNomer"] > 1):?>

		<?if($arResult["bSavePage"]):?>
			<li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>" aria-label="Previous" data-page="<?=$arResult["NavPageNomer"]-1?>"><span aria-hidden="true">«</span></a></li>
		<?else:?>
			<?if ($arResult["NavPageNomer"] > 2):?>
				<li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>" aria-label="Previous" data-page="<?=$arResult["NavPageNomer"]-1?>"><span aria-hidden="true">«</span></a></li>
			<?else:?>
				<li><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>" aria-label="Previous" data-page="<?=$arResult["NavPageNomer"]-1?>"><span aria-hidden="true">«</span></a></li>
			<?endif?>
		<?endif?>

	<?/*else:?>
		<li><span class="paginator__page paginator__page_type_prev">&nbsp;</span></li>*/?>
	<?endif?>

	<?while($arResult["nStartPage"] <= $arResult["nEndPage"]):?>

		<?if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):?>
			<li class="active"><a href="#"><?=$arResult["nStartPage"]?></a></li>
		<?elseif($arResult["nStartPage"] == 1 && $arResult["bSavePage"] == false):?>
			<li><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>" data-page="<?=$arResult["nStartPage"]?>"><?=$arResult["nStartPage"]?></a></li>
		<?else:?>
			<li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>" data-page="<?=$arResult["nStartPage"]?>"><?=$arResult["nStartPage"]?></a></li>
		<?endif?>
		<?$arResult["nStartPage"]++?>
	<?endwhile?>

	<?if($arResult["NavPageNomer"] < $arResult["NavPageCount"]):?>
		<li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>" aria-label="Next" data-page="<?=$arResult["NavPageNomer"]+1?>"><span aria-hidden="true">»</span></a></li>
	<?/*else:?>
		<li><span class="paginator__page paginator__page_type_next">&nbsp;</span></li>*/?>
	<?endif?>

	</ul>
</nav>