<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);?>

<?foreach ($arResult['rows'] as $k => $row):?>
<div class="b-stock <?=($k%2==0?'b-stock--blue-rainbow':'b-stock--green-rainbow')?>">
	<div class="b-stock__img">
		<?$y=CFile::ResizeImageGet(
			$row["UF_IMAGE2"],
			array("width" => 669, "height" => 300),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true
		);?>
		<img src="<?=$y["src"]?>" alt="" />
	</div>
	<?if ($row["UF_DISCOUNT"]):?>
	<div class="b-stock-discount b-stock__discount b-stock-discount--blue">
		<div class="b-stock-discount__inner"><?=$row["UF_DISCOUNT"]?>%</div>
	</div>
	<?endif;?>
	<div class="b-stock__content">
		<div class="b-stock__title"><?=$row["UF_NAME"]?></div>
		<div class="b-stock__text">
			<?if ($row["UF_DESCRIPTION"]):?>
			<p>
				<?=htmlspecialcharsBack($row["UF_DESCRIPTION"])?>
			</p>
			<?endif;?>
		</div>
	</div>
</div>
<?endforeach;?>