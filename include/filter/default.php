<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="content__top">
	<div class="product-filter">
		<form class="product-filter__form" action="<?=($APPLICATION->GetCurDir()=="/search/"?'/search/':$formAction)?>" name="catalog-filter">
			<input type="hidden" name="send-filter" value="<?=$_GET["send-filter"]?>">
			<?if ($APPLICATION->GetCurDir() == "/search/"):?>
			<div class="form-field">
				<div class="form-field__inputtext">
					<input class="inputtext" type="text" name="q" value="<?=(isset($_GET["q"])?$_GET["q"]:'')?>" placeholder="Введите товар для поиска">
				</div>
			</div>
			<?endif;?>

			<?if (!empty($arReturn["SECTIONS"])):?>
			<div class="form-field">
				<div class="form-field__select">
					<select class="form-select select_type_category" data-placeholder="Категория" id="catalog-filter" name="category">
						<?if ($APPLICATION->GetCurDir() == "/search/"):?>
						<option value=""></option>
						<?endif;?>
						<?foreach ($arReturn["SECTIONS"] as $arSection) {
							echo '<option value="'.($APPLICATION->GetCurDir()=="/search/"?$arSection["ID"]:$arSection["SECTION_PAGE_URL"]).'"'.($arSection["SECTION_PAGE_URL"]==$APPLICATION->GetCurDir()?' selected':'').($arSection["ID"]==$_GET["category"]?' selected':'').'>'.$arSection["NAME"].'</option>';
						}?>
					</select>
				</div>
			</div>
			<?endif;?>
			<?if (!empty($arReturn["BRANDS"])):?>
			<div class="form-field">
				<div class="form-field__select">
					<select class="form-select select_type_multiselect select_type_brand" multiple="multiple" size="1" name="brand[]">
						<?foreach ($arReturn["BRANDS"] as $key => $name) {
							echo '<option value="'.$key.'"'.(in_array($key, $_GET["brand"])?' selected':'').'>'.$name.'</option>';
						}?>
					</select>
				</div>
			</div>
			<?endif;?>

			<?if ($APPLICATION->GetCurDir() != "/search/"):?>
			<div class="form-field form-field_type_row">
				<div class="form-field__inputtext">
					<span class="like-label">Цены от</span>
					<input class="inputtext" type="text" value="<?=($_GET["price_from"]?intval($_GET["price_from"]):"")?>" name="price_from">
				</div>
				<div class="form-field__inputtext">
					<span class="like-label">до</span>
					<input class="inputtext" type="text" value="<?=($_GET["price_to"]?intval($_GET["price_to"]):"")?>" name="price_to">
					<span class="like-label">руб.</span>
				</div>
			</div>
			<?endif;?>

			<div class="form-field">
				<div class="form-field__button">
					<input type="hidden" name="send-filter" value="Y">
					<input type="submit" class="form-button button_font_big" value="Найти товар" />
				</div>
			</div>
		</form>
	</div>
</div>