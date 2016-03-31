<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);?>

<?foreach ($arResult['rows'] as $k => $row):?>
<div class="b-share <?=$row["UF_CSS_CLASS"]?> <?=($k%2==0?'b-share--media-left':'b-share--media-right')?>">
	<?if ($row["UF_IMAGE2"]):?>
		<?$y=CFile::ResizeImageGet(
			$row["UF_IMAGE2"],
			array("width" => 940, "height" => 232),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true
		);?>
		<div class="b-share__media"><img src="<?=$y["src"]?>" alt="" width="<?=$y["width"]?>" height="<?=$y["height"]?>" class="b-share__img"/></div>
	<?endif;?>
	<?if ($row["UF_DISCOUNT"]):?>
	<div class="b-share__number"><?=$row["UF_DISCOUNT"]?>%</div>
	<?endif;?>
	<div class="b-share__content">
		<div class="b-share__title"><?=$row["UF_NAME"]?></div>
		<?if ($row["UF_DESCRIPTION"]):?>
		<div class="b-share__text"><?=htmlspecialcharsBack($row["UF_DESCRIPTION"])?></div>
		<?endif;?>
	</div>
</div>
<?endforeach;?>