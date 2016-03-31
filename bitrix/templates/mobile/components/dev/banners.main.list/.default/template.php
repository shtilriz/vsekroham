<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);?>

<?if (!empty($arResult["rows"])):?>
<div class="slider-default">
	<?foreach ($arResult["rows"] as $key => $arItem) {
		$y=CFile::ResizeImageGet(
			$arItem["UF_IMAGE"],
			array("width" => 669, "height" => 300),
			BX_RESIZE_IMAGE_EXACT,
			true
		);
		echo '<div><a href="/sales/"><img src="'.$y["src"].'" alt="'.$arItem["UF_NAME"].'"></a></div>';
	}?>
</div>
<?endif;?>