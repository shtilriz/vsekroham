<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if(!empty($arResult["CATEGORIES"])):?>
<div class="g-search1-dropdown">
	<table class="title-search-result">
		<?foreach($arResult["CATEGORIES"] as $category_id => $arCategory):?>

			<?foreach($arCategory["ITEMS"] as $i => $arItem):
				if ($arItem["NAME"] == "остальные")
					continue;
				?>
			<tr>
				<?if($category_id === "all"):?>

				<?elseif(isset($arResult["ELEMENTS"][$arItem["ITEM_ID"]])):
					$arElement = $arResult["ELEMENTS"][$arItem["ITEM_ID"]];
				?>
					<td>
						<a href="<?echo $arItem["URL"]?>" class="g-search1-dropdown__img"><img src="<?echo $arElement["PICTURE"]["src"]?>" alt=""/></a>
					</td>
					<td>
						<a href="<?echo $arItem["URL"]?>" class="g-search1-dropdown__title"><?echo $arItem["NAME"]?></a>
					</td>
					<td>
						<div class="g-search1-dropdown__price"><?=$arElement["PRICES"]["BASE"]["PRINT_DISCOUNT_VALUE"]?></div>
					</td>

				<?elseif(isset($arItem["ICON"])):?>
					<td class="title-search-item"><a href="<?echo $arItem["URL"]?>"><?echo $arItem["NAME"]?></td>
				<?else:?>
					<td class="title-search-more"><a href="<?echo $arItem["URL"]?>"><?echo $arItem["NAME"]?></td>
				<?endif;?>
			</tr>
			<?endforeach;?>
		<?endforeach;?>
	</table>
	<div class="g-search1-dropdown__footer"><a href="/search/?q=<?=$arResult["query"]?>" class="more-link">Все результаты</a></div>

</div>
<?endif;
?>
