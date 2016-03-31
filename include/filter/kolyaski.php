<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<table class="table-top">
	<tr>
		<?if (!empty($arReturn["PROPS"]["MAKER"])):?>
		<td width="236">
			<div class="form-field">
				<div class="form-field__select">
					<label style="display: block;">Производитель</label>
					<select multiple="multiple" class="form-select select_type_brand" name="prop[maker][]" data-select_text="Выберите производителя">
						<?foreach ($arReturn["PROPS"]["MAKER"] as $key => $name) {
							echo '<option value="'.$key.'"'.(in_array($key, $_GET['prop']['maker'])?' selected':'').'>'.$name.'</option>';
						}?>
					</select>
				</div>
			</div>
		</td>
		<?endif;?>
		<?if (!empty($arReturn["SUBSECTIONS_KOLYASKI"])):?>
		<td width="236">
			<div class="form-field">
				<div class="form-field__select">
					<label>Тип коляски</label>
					<select class="form-select select_type_category select-chosen" name="category" data-placeholder="Категория">
						<option value="/catalog/kolyaski/">Тип коляски</option>
						<?foreach ($arReturn["SUBSECTIONS_KOLYASKI"] as $arSection) {
							echo '<option value="'.$arSection["SECTION_PAGE_URL"].'"'.($arSection["SECTION_PAGE_URL"]==$APPLICATION->GetCurDir()?' selected':'').' data-id="'.$arSection["ID"].'">'.$arSection["NAME"].'</option>';
						}?>
					</select>
				</div>
			</div>
		</td>
		<?endif;?>
		<td width="<?=(!empty($arReturn["SUBSECTIONS_KOLYASKI"]) ? '470' : '706')?>">
			<div class="form-field">
				<div class="like-label">Цена</div>
				<?
				$maxPrice = 150000;
				if ($arReturn["SECTION_ID"]) {
					if ($mPrice = CatalogHelper::getMaxPriceSect($arReturn["SECTION_ID"]))
						$maxPrice = (int)$mPrice;
				}
				$startMinPrice = round($maxPrice * 0.1);
				if (isset($_GET["price_from"])) {
					$startMinPrice = (int)$_GET["price_from"];
				}
				$startMaxPrice = round($maxPrice * 0.9);
				if ($_GET["price_to"]) {
					$startMaxPrice = (int)$_GET["price_to"];
				}
				?>
				<div class="range-slider-wrapper">
					<div data-min="0" data-max="<?=$maxPrice?>" data-start-min="<?=$startMinPrice?>" data-start-max="<?=$startMaxPrice?>" data-measuring="руб." class="range-slider"></div>
					<div class="row">
						<div class="col-xs-6">
							<div class="range-value-lower"></div>
						</div>
						<div class="col-xs-6 text-right">
							<div class="range-value-upper"></div>
						</div>
						<input type="hidden" name="price_from" value="<?=$startMinPrice?>" class="range-slider-low" />
						<input type="hidden" name="price_to" value="<?=$startMaxPrice?>" class="range-slider-high" />
					</div>
				</div>
			</div>
		</td>
		<td width="188">
			<div class="form-field">
				<div class="form-field__button">
					<input type="submit" value="Найти товар" class="form-button button_font_big" />
				</div>
			</div>
		</td>
	</tr>
</table>
<div class="product-filter-2__body"<?=($_GET["send-filter"]=="Y" ? ' style="display: block"' : '')?>>
	<table class="table-middle">
		<tr>
			<td width="236">
				<?if (!empty($arReturn["PROPS"]["OPTIONS"])):?>
				<div class="form-field">
					<div class="like-label">Комплектация</div>
					<?foreach ($arReturn["PROPS"]["OPTIONS"] as $key => $arItem):?>
					<div class="form-field__checkbox">
						<input type="checkbox" name="prop[OPTIONS][]" value="<?=$arItem["UF_XML_ID"]?>"<?=(in_array($arItem["UF_XML_ID"], $_GET["prop"]["OPTIONS"]) ? ' checked' : '')?> id="checkbox<?=$arItem["ID"]?>" class="checkbox-styled" />
						<label for="checkbox<?=$arItem["ID"]?>"><?=$arItem["UF_NAME"]?></label>
					</div>
					<?endforeach?>
				</div>
				<?endif;?>
			</td>
			<td width="236">
				<?if (!empty($arReturn["PROPS"]["PLATING_STROLLERS"])):?>
				<div class="form-field">
					<div class="like-label"><?echo $arReturn["PROPS_NAME"]["PLATING_STROLLERS"]?></div>
					<?foreach ($arReturn["PROPS"]["PLATING_STROLLERS"] as $key => $value):?>
					<div class="form-field__checkbox">
						<input type="checkbox" name="prop[PLATING_STROLLERS][]" value="<?=$key?>"<?=(in_array($key, $_GET["prop"]["PLATING_STROLLERS"]) ? ' checked' : '')?> id="PLATING_STROLLERS<?=$key?>" class="checkbox-styled" />
						<label for="PLATING_STROLLERS<?=$key?>"><?=$value?></label>
					</div>
					<?endforeach;?>
				</div>
				<?endif;?>
				<?/*if (!empty($arReturn["PROPS"]["HANDLE_BEFORE"])):?>
				<div class="form-field">
					<div class="like-label"><?echo $arReturn["PROPS_NAME"]["HANDLE_BEFORE"]?></div>
					<?foreach ($arReturn["PROPS"]["HANDLE_BEFORE"] as $key => $value):?>
					<div class="form-field__checkbox">
						<input type="checkbox" name="prop[HANDLE_BEFORE][]" value="<?=$key?>"<?=(in_array($key, $_GET["prop"]["HANDLE_BEFORE"]) ? ' checked' : '')?> id="HANDLE_BEFORE<?=$key?>" class="checkbox-styled" />
						<label for="HANDLE_BEFORE<?=$key?>"><?=($value=="Y" ? 'да' : $value)?></label>
					</div>
					<?endforeach;?>
				</div>
				<?endif;?>
				<?if (!empty($arReturn["CARRYING_HANDLE"])):?>
				<div class="form-field">
					<div class="like-label">Ручка для переноски люльки</div>
					<?foreach ($arReturn["CARRYING_HANDLE"] as $key => $value):?>
					<div class="form-field__checkbox">
						<input type="checkbox" name="carrying_handle[]" value="<?=$key?>"<?=(in_array($key, $_GET["carrying_handle"]) ? ' checked' : '')?> id="CARRYING_HANDLE<?=$key?>" class="checkbox-styled" />
						<label for="CARRYING_HANDLE<?=$key?>"><?=($value=="Y" ? 'да' : $value)?></label>
					</div>
					<?endforeach;?>
				</div>
				<?endif;*/?>
				<?if (!empty($arReturn["PROPS"]["PARENT_HANDLE"])):?>
				<div class="form-field">
					<div class="like-label"><?echo $arReturn["PROPS_NAME"]["PARENT_HANDLE"]?></div>
					<?foreach ($arReturn["PROPS"]["PARENT_HANDLE"] as $key => $value):?>
					<div class="form-field__checkbox">
						<input type="checkbox" name="prop[PARENT_HANDLE][]" value="<?=$key?>"<?=(in_array($key, $_GET["prop"]["PARENT_HANDLE"]) ? ' checked' : '')?> id="PARENT_HANDLE<?=$key?>" class="checkbox-styled" />
						<label for="PARENT_HANDLE<?=$key?>"><?=($value=="Y" ? 'да' : $value)?></label>
					</div>
					<?endforeach;?>
				</div>
				<?endif;?>
				<?if (!empty($arReturn["PROPS"]["MAT_BASKET4BUY"])):?>
				<div class="form-field">
					<div class="like-label"><?echo $arReturn["PROPS_NAME"]["MAT_BASKET4BUY"]?></div>
					<?foreach ($arReturn["PROPS"]["MAT_BASKET4BUY"] as $key => $value):?>
					<div class="form-field__checkbox">
						<input type="checkbox" name="prop[MAT_BASKET4BUY][]" value="<?=$key?>"<?=(in_array($key, $_GET["prop"]["MAT_BASKET4BUY"]) ? ' checked' : '')?> id="MAT_BASKET4BUY<?=$key?>" class="checkbox-styled" />
						<label for="MAT_BASKET4BUY<?=$key?>"><?=($value=="Y" ? 'да' : $value)?></label>
					</div>
					<?endforeach;?>
				</div>
				<?endif;?>
			</td>
			<td width="470">
				<div class="form-field">
					<?
					$maxWeight = 20;
					if ($arReturn["SECTION_ID"]) {
						if ($mWeight = CatalogHelper::getMaxWeightSect($arReturn["SECTION_ID"]))
							$maxWeight = round($mWeight/1000);
					}
					$startMinWeight = round($maxWeight * 0.1);
					if (isset($_GET["weight_from"])) {
						$startMinWeight = (int)$_GET["weight_from"];
					}
					$startMaxWeight = round($maxWeight * 0.9);
					if ($_GET["weight_to"]) {
						$startMaxWeight = (int)$_GET["weight_to"];
					}
					?>
					<div class="range-slider-wrapper">
						<div class="like-label">Вес коляски (кг)</div>
						<div data-min="0" data-max="<?=$maxWeight?>" data-start-min="<?=$startMinWeight?>" data-start-max="<?=$startMaxWeight?>" class="range-slider"></div>
						<div class="row">
							<div class="col-xs-6">
								<div class="range-value-lower"></div>
							</div>
							<div class="col-xs-6 text-right">
								<div class="range-value-upper"></div>
							</div>
						</div>
						<input type="hidden" value="<?=$startMinWeight?>" class="range-slider-low" name="weight_from" />
						<input type="hidden" value="<?=$startMaxWeight?>" class="range-slider-high" name="weight_to" />
					</div>
				</div>
				<div class="row">
					<div class="col-xs-6">
						<?if (!empty($arReturn["PROPS"]["MATERIAL_WHEELS"])):?>
						<div class="form-field">
							<div class="like-label"><?echo $arReturn["PROPS_NAME"]["MATERIAL_WHEELS"]?></div>
							<?foreach ($arReturn["PROPS"]["MATERIAL_WHEELS"] as $key => $value):?>
							<div class="form-field__checkbox">
								<input type="checkbox" name="prop[MATERIAL_WHEELS][]" value="<?=$key?>"<?=(in_array($key, $_GET["prop"]["MATERIAL_WHEELS"]) ? ' checked' : '')?> id="MATERIAL_WHEELS<?=$key?>" class="checkbox-styled" />
								<label for="MATERIAL_WHEELS<?=$key?>"><?=($value=="Y" ? 'да' : $value)?></label>
							</div>
							<?endforeach;?>
						</div>
						<?endif;?>
						<?if (!empty($arReturn["PROPS"]["NUMBER_WHEELS"])):?>
						<div class="form-field">
							<div class="like-label"><?echo $arReturn["PROPS_NAME"]["NUMBER_WHEELS"]?></div>
							<?foreach ($arReturn["PROPS"]["NUMBER_WHEELS"] as $key => $value):?>
								<div class="form-field__checkbox">
									<input type="checkbox" name="prop[NUMBER_WHEELS][]" value="<?=$key?>"<?=(in_array($key, $_GET["prop"]["NUMBER_WHEELS"]) ? ' checked' : '')?> id="checkbox<?=$key?>" class="checkbox-styled" />
									<label for="checkbox<?=$key?>"><?=$value?></label>
								</div>
							<?endforeach;?>
						</div>
						<?endif;?>
						<?/*if (!empty($arReturn["PROPS"]["BACK_ANGLE"])):?>
						<div class="form-field">
							<div class="like-label"><?echo $arReturn["PROPS_NAME"]["BACK_ANGLE"]?></div>
							<div class="scroll-pane js-scroll-pane" data-max-els="4">
								<?foreach ($arReturn["PROPS"]["BACK_ANGLE"] as $key => $value):?>
								<div class="form-field__checkbox">
									<input type="checkbox" name="prop[BACK_ANGLE][]" value="<?=$key?>"<?=(in_array($key, $_GET["prop"]["BACK_ANGLE"]) ? ' checked' : '')?> id="BACK_ANGLE<?=$key?>" class="checkbox-styled" />
									<label for="BACK_ANGLE<?=$key?>"><?=($value=="Y" ? 'да' : $value)?></label>
								</div>
								<?endforeach;?>
							</div>
						</div>
						<?endif;*/?>
					</div>
					<div class="col-xs-6">
						<div class="form-field">
							<div class="like-label">Ширина рамы (см)</div>
							<div class="form-horizontal">
								<div class="form-group clearfix">
									<label class="col-xs-1">от</label>
									<div class="col-xs-5">
										<input type="text" name="WIDTH_FRAME_from" value="<?=$_GET["WIDTH_FRAME_from"]?>" class="form-control">
									</div>
									<label class="col-xs-1">до</label>
									<div class="col-xs-5">
										<input type="text" name="WIDTH_FRAME_to" value="<?=$_GET["WIDTH_FRAME_to"]?>" class="form-control">
									</div>
								</div>
							</div>
						</div>
						<?if (!empty($arReturn["PROPS"]["MEHANIZM_VRACH"])):?>
						<div class="form-field">
							<div class="like-label"><?echo $arReturn["PROPS_NAME"]["MEHANIZM_VRACH"]?></div>
							<?foreach ($arReturn["PROPS"]["MEHANIZM_VRACH"] as $key => $value):?>
							<div class="form-field__checkbox">
								<input type="checkbox" name="prop[MEHANIZM_VRACH][]" value="<?=$key?>"<?=(in_array($key, $_GET["prop"]["MEHANIZM_VRACH"]) ? ' checked' : '')?> id="MEHANIZM_VRACH<?=$key?>" class="checkbox-styled" />
								<label for="MEHANIZM_VRACH<?=$key?>"><?=($value=="Y" ? 'да' : $value)?></label>
							</div>
							<?endforeach;?>
						</div>
						<?endif;?>
						<div class="form-field">
							<div class="like-label">Другие функции</div>
							<?foreach ($arReturn['PROPS'] as $propCode => $arValues):
								if (count($arValues) == 1):
									foreach ($arValues as $key => $value):?>
									<div class="form-field__checkbox">
										<input type="checkbox" name="prop[<?echo $propCode?>][]" value="<?echo $key?>" id="<?echo $propCode?>" class="checkbox-styled" <?echo (in_array($key, $_GET["prop"][$propCode]) ? ' checked' : '')?> />
										<label for="<?=$propCode?>"><?echo $arReturn['PROPS_NAME'][$propCode]?></label>
									</div>
									<?endforeach;
								endif;
							endforeach;?>
						</div>
						<?/*if (!empty($arReturn["PROPS"]["CUSHIONING_SYSTEM"])):?>
						<div class="form-field">
							<div class="like-label"><?echo $arReturn["PROPS_NAME"]["CUSHIONING_SYSTEM"]?></div>
							<?foreach ($arReturn["PROPS"]["CUSHIONING_SYSTEM"] as $key => $value):?>
							<div class="form-field__checkbox">
								<input type="checkbox" name="prop[CUSHIONING_SYSTEM][]" value="<?=$key?>"<?=(in_array($key, $_GET["prop"]["CUSHIONING_SYSTEM"]) ? ' checked' : '')?> id="CUSHIONING_SYSTEM<?=$key?>" class="checkbox-styled" />
								<label for="CUSHIONING_SYSTEM<?=$key?>"><?=($value=="Y" ? 'да' : $value)?></label>
							</div>
							<?endforeach;?>
						</div>
						<?endif;*/?>
					</div>
				</div>
			</td>
		</tr>
	</table>
	<div class="product-filter__footer">
		<div class="row">
			<div class="col-xs-4" id="findProducts">&nbsp;</div>
			<div class="col-xs-4"></div>
			<div class="col-xs-4 text-right"><a href="javascript:void(0)" class="more-link more-link-dotted link-advanced-filter link-hide">Скрыть расширенный фильтр</a></div>
		</div>
	</div>
</div>