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


<?foreach($arResult["ITEMS"] as $arItem):
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));?>
	<div class="b-block-2" id="<?=$this->GetEditAreaId($arItem['ID']);?>" itemscope itemtype="http://schema.org/Organization">
		<div class="b-block-2__title" itemprop="name"><?=$arItem["NAME"]?></div>
		<div class="b-block-2__img">
			<?if (!empty($arItem["PREVIEW_PICTURE"])):?>
				<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>">
			<?endif;?>
			<?if ($arItem["DISPLAY_PROPERTIES"]["PHONE"]["DISPLAY_VALUE"]):?>
				<div class="b-block-2__tel" itemprop="telephone"><?=$arItem["DISPLAY_PROPERTIES"]["PHONE"]["DISPLAY_VALUE"]?></div>
			<?endif;?>
		</div>
		<ul class="b-block-2__list">
			<?if ($arItem["DISPLAY_PROPERTIES"]["ADDRESS"]["DISPLAY_VALUE"]):?>
			<li itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
				<meta itemprop="addressLocality" content="<?=$arItem["PROPERTIES"]["CITY"]["VALUE"]?>">
				<meta itemprop="streetAddress" content="<?=$arItem["PROPERTIES"]["STREET"]["VALUE"]?>">
				<span class="icon sprite-pin-1-xs"></span><?=$arItem["DISPLAY_PROPERTIES"]["ADDRESS"]["DISPLAY_VALUE"];?>
			</li>
			<?endif;?>
			<?if ($arItem["DISPLAY_PROPERTIES"]["RATE"]["DISPLAY_VALUE"]):?>
			<li><span class="icon sprite-alarm-xs"></span><?=$arItem["DISPLAY_PROPERTIES"]["RATE"]["DISPLAY_VALUE"]?></li>
			<?endif;?>
		</ul>
	</div>
<?endforeach;?>