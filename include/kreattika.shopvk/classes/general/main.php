<?
######################################################
# Name: kreattika.shopvk                             #
# (c) 2011-2014 Kreattika, Sedov S.Y.                #
# Dual licensed under the MIT and GPL                #
# http://kreattika.ru/                               #
# mailto:info@kreattika.ru                           #
######################################################
?>
<?
class SVK {

	//автопостинг на стену группы
	public function wall_auto_post(&$arFields) {
		if ($arFields["IBLOCK_ID"] == IBLOCK_PRODUCT_ID) {
			//узнать, в наличии товар или нет
			$bAvailable = false;
			$db_props = CIBlockElement::GetProperty($arFields["IBLOCK_ID"], $arFields["ID"], array(), array("CODE"=>"AVAILABLE"));
			if ($ar_props = $db_props->Fetch()) {
				if ($ar_props["VALUE"])
					$bAvailable = true;
			}
			//если свойство "В наличии" не указано или товар неактивен - удалить фото из группы вконтакте
			if (!$bAvailable || $arFields["ACTIVE"] == "N") {
				SVK::delete_auto_post($arFields);
				//так же если у товара есть ТП, то их тоже нужно грохнуть
				$rsSKU = CIBlockElement::GetList(
					array(),
					array(
						"IBLOCK_ID" => IBLOCK_SKU_ID,
						"PROPERTY_CML2_LINK" => $arFields["ID"]
					),
					false,
					false,
					array("IBLOCK_ID", "ID", "NAME")
				);
				while ($obSku = $rsSKU->GetNextElement()) {
					$arSku = $obSku->GetFields();
					if (is_array($arSku) && !empty($arSku)) {
						$arSku["IBLOCK_ID"] = IBLOCK_PRODUCT_ID;
						$arSku["~IBLOCK_ID"] = IBLOCK_PRODUCT_ID;
						SVK::delete_auto_post($arSku);
					}
					sleep(3);
				}
				return;
			}
			else {
				//иначе выгружаем все активные ТП и удаляем все неактивные
				$rsSKU = CIBlockElement::GetList(
					array("SORT" => "ASC"),
					array(
						"IBLOCK_ID" => IBLOCK_SKU_ID,
						"PROPERTY_CML2_LINK" => $arFields["ID"],
						//"ACTIVE" => "Y"
					),
					false,
					false,
					array("IBLOCK_ID", "ID", "NAME", "ACTIVE", "PREVIEW_PICTURE", "DETAIL_PICTURE", "PREVIEW_TEXT", "DETAIL_TEXT")
				);
				while ($obSku = $rsSKU->GetNextElement()) {
					$arSku = $obSku->GetFields();
					if (is_array($arSku) && !empty($arSku)) {
						if ($arSku["ACTIVE"] == "Y") {
							SVK::wall_auto_post($arSku);
						}
						elseif ($arSku["ACTIVE"] == "N") {
							$arSku["IBLOCK_ID"] = IBLOCK_PRODUCT_ID;
							$arSku["~IBLOCK_ID"] = IBLOCK_PRODUCT_ID;
							SVK::delete_auto_post($arSku);
						}
					}
					sleep(3);
				}
			}
		}
		elseif ($arFields["IBLOCK_ID"] == IBLOCK_SKU_ID) {
			//проверить, в наличии ли товар, к которому принадлежит это ТП
			CModule::IncludeModule("catalog");
			$mxResult = CCatalogSku::GetProductInfo($arFields["ID"]);
			if (is_array($mxResult)) {
				$bAvailable = false;
				$db_props = CIBlockElement::GetProperty($mxResult["IBLOCK_ID"], $mxResult["ID"], array(), array("CODE"=>"AVAILABLE"));
				if ($ar_props = $db_props->Fetch()) {
					if ($ar_props["VALUE"])
						$bAvailable = true;
				}
				//если основной товар не в наличии или ТП неактивно, то удалить его из контакта
				if (!$bAvailable || $arFields["ACTIVE"] == "N") {
					$arFields["IBLOCK_ID"] = IBLOCK_PRODUCT_ID;
					SVK::delete_auto_post($arFields);
					return;
				}
			}
		}

		if ($arFields["IBLOCK_ID"] == IBLOCK_SKU_ID) {
			$arFields["IBLOCK_ID"] = IBLOCK_PRODUCT_ID;
		}

		global $APPLICATION;
		$MODULE_ID = "kreattika.shopvk";
		$TableNamePost = "b_shopvk_post";
		$TableNameAlbums = "b_shopvk_albums";
		$TableNamePhotos = "b_shopvk_photos";

		$flAutotest = ShopVKTEST::_AllLibInstalled();
		$flPost = COption::GetOptionString($MODULE_ID, "svk_on", "N"); //Активировать модуль магазин ВКонтакте
		$flPostVK = COption::GetOptionString($MODULE_ID, "shop_vk_on", "N"); //Включить автопостинг ВКонтакте
		$flAlbumVK = COption::GetOptionString($MODULE_ID, "shop_vk_album_on", "N"); //Включить синхронизацию альбомов ВКонтакте
		$flNewPostVK = true;
		$flImgExistVK = false;

		//если модуль активен
		if ($flPost=="Y") {

			$arPostVKIBList = explode(',', COption::GetOptionString($MODULE_ID, "shop_vk_ib", "N")); //выгружаемые ИБ
			$arAlbumVKIBList = explode(',', COption::GetOptionString($MODULE_ID, "shop_vk_album_ib", "N")); //выгружаемые свойства
			$PostVKActiveElementPost = COption::GetOptionString($MODULE_ID, "shop_vk_active_auto_post", "Y"); //Публиковать только активные элементы (посты)
			$PostVKActiveElementAlbum = COption::GetOptionString($MODULE_ID, "shop_vk_album_active_auto_post", "Y"); //Публиковать только активные элементы (альбомы)

			$arPostVKPropsIBListValues = array();
			$arPostVKPricesListValues = array();
			$arAlbumVKPropsIBListValues = array();
			$arAlbumVKPricesListValues = array();
			$PostPicture = '';

			$flPostVKIBExist = false;
			foreach($arPostVKIBList as $IBItemID) {
				if($IBItemID == $arFields['IBLOCK_ID'])
					$flPostVKIBExist = true;
			}

			$flAlbumVKIBExist = false;
			foreach($arAlbumVKIBList as $IBItemID) {
				if($IBItemID == $arFields['IBLOCK_ID'])
					$flAlbumVKIBExist = true;
			}

			//если выбраны инфоблоки для выгрузки и выбраны выгружаемые свойства
			if ($flPostVKIBExist || $flAlbumVKIBExist) {

				$VKOwnerID = COption::GetOptionString($MODULE_ID, "shop_vk_owner_id", "N");
				$arPostVKFieldsIBList = explode(',', COption::GetOptionString($MODULE_ID, "shop_vk_ib_fields", "N")); //Поля инфолока для автопостинга
				$arAlbumVKFieldsIBList = explode(',', COption::GetOptionString($MODULE_ID, "shop_vk_album_ib_fields", "N")); //Поля инфолока для альбомов
				$arPostVKPropsIBList = explode(',', COption::GetOptionString($MODULE_ID, "shop_vk_ib_properties", "N")); //Свойства инфоблока для автопостинга
				$arAlbumVKPropsIBList = explode(',', COption::GetOptionString($MODULE_ID, "shop_vk_album_ib_properties", "N")); //Свойства инфоблока для альбомов
				$arPostVKPricesList = explode(',', COption::GetOptionString($MODULE_ID, "shop_vk_ib_prices", "N")); //Типы цен для автопостинга
				$arAlbumVKPricesList = explode(',', COption::GetOptionString($MODULE_ID, "shop_vk_album_ib_prices", "N")); //Типы цен для альбомов
				$PostVKTPL = COption::GetOptionString($MODULE_ID, "shop_vk_tpl", "N"); //Шаблон поста
				$AlbumVKTPL = COption::GetOptionString($MODULE_ID, "shop_vk_album_tpl", "N"); //Шаблон подписи
				$PostVKEventLog = COption::GetOptionString($MODULE_ID, "shop_vk_event_log", "N"); //Создавать запись в журнале событий при автопостинге
				$AlbumVKEventLog = COption::GetOptionString($MODULE_ID, "shop_vk_album_event_log", "N"); //Создавать запись в журнале событий

				$fl_is_group = COption::GetOptionString($MODULE_ID, "shop_vk_is_group", "Y"); //Это группа

				//если модуль корректно установлен
				if ($flAutotest) {
					if (in_array('LINK', $arPostVKFieldsIBList) || count($arPostVKPropsIBList) > 0 || in_array('LINK', $arAlbumVKFieldsIBList) || count($arAlbumVKPropsIBList) > 0) {
						if (CModule::IncludeModule("iblock")) {
							$resEl = CIBlockElement::GetByID($arFields['ID']);
							if ($obEl = $resEl->GetNextElement()) {
								$arElFields = $obEl->GetFields();
								$arElProps = $obEl->GetProperties();
								foreach ($arElProps as $ElPropValue) {
									if ( in_array($ElPropValue['ID'], $arPostVKPropsIBList) || in_array($ElPropValue['ID'], $arAlbumVKPropsIBList) ) {
										$ElPropID = $ElPropValue['ID'];
										$arPValues = array();
										$arPValues['ID'] = $ElPropValue['ID'];
										$arPValues['NAME'] = $ElPropValue['NAME'];
										$arPValues['CODE'] = $ElPropValue['CODE'];
										$arPValues['VALUE'] = $ElPropValue['VALUE'];
										$arPostVKPropsIBListValues[$ElPropID] = $arPValues;
										$arAlbumVKPropsIBListValues[$ElPropID] = $arPValues;
									}
								}
							}
						}
					}

					if (count($arPostVKPricesList) > 0 || count($arAlbumVKPricesList) > 0) {
						if (CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog")) {
							$obElPrice = CPrice::GetList(array(), array("PRODUCT_ID" =>$arFields['ID']));
							while ($ElPriceValue = $obElPrice->GetNext()) {
								if (in_array($ElPriceValue['CATALOG_GROUP_ID'], $arPostVKPricesList) || in_array($ElPriceValue['CATALOG_GROUP_ID'], $arAlbumVKPricesList)) {
									$ElPriceID = $ElPriceValue['CATALOG_GROUP_ID'];
									$arPrValues = array();
									$arPrValues['ID'] = $ElPriceValue['CATALOG_GROUP_ID'];
									$arPrValues['NAME'] = $ElPriceValue['CATALOG_GROUP_NAME'];
									$arPrValues['VALUE'] = ($ElPriceValue['CATALOG_GROUP_ID']==1?($_POST["CAT_BASE_PRICE"]?$_POST["CAT_BASE_PRICE"]:$ElPriceValue['PRICE']):$ElPriceValue['PRICE']);
									$arPrValues['CURRENCY'] = $ElPriceValue['CURRENCY'];
									$arPostVKPricesListValues[$ElPriceID] = $arPrValues;
									$arAlbumVKPricesListValues[$ElPriceID] = $arPrValues;
								}
							}
						}
					}

					if (empty($arAlbumVKPricesListValues) && isset($_POST["CAT_BASE_PRICE"])) {
						$arAlbumVKPricesListValues[1] = array(
							'ID' => 1,
							'NAME' => 'Розничная цена',
							'VALUE' => $_POST["CAT_BASE_PRICE"],
							'CURRENCY' => $_POST["CAT_BASE_CURRENCY"]
						);
					}

					foreach ($arAlbumVKPricesListValues as $k => $ElPriceValue) {
						if ($ElPriceValue["VALUE"])
							$arAlbumVKPricesListValues[$k]["VALUE"] = $ElPriceValue["VALUE"].' руб.';
					}

					$PostName = $arFields['NAME'];
					if ($arFields['PREVIEW_TEXT_TYPE']=='text') {
						$PostPText = $arFields['PREVIEW_TEXT'];
					} else {
						$PostPText = HTMLToTxt($arFields['PREVIEW_TEXT']);
					}
					if($arFields['DETAIL_TEXT_TYPE']=='text') {
						$PostDText = $arFields['DETAIL_TEXT'];
					} else {
						$PostDText = HTMLToTxt($arFields['DETAIL_TEXT']);
					}

					$PostText = $PostVKTPL;
					$AlbumText = $AlbumVKTPL;

					$arDBPostFilter = array("ACTIVE"=>'\'Y\'', "VK_OWNER_ID"=>intval($VKOwnerID), "IBLOCK_ID"=>intval($arFields["IBLOCK_ID"]), "ELEMENT_ID"=>intval($arFields["ID"]));
					$obDBPostResult = CSVKDataBlock::GetList($TableNamePost, $arDBPostFilter);
					if ($arDBPostResult = $obDBPostResult->Fetch()) {
						$flNewPostVK = false;
					}

					/*if ($flNewPostVK) {
						if ( isset($arFields['PREVIEW_PICTURE_ID']) && !empty($arFields['PREVIEW_PICTURE_ID']) ) {
							$Post_PREVIEW_PICTURE_SRC = $_SERVER["DOCUMENT_ROOT"].CFile::GetPath($arFields['PREVIEW_PICTURE_ID']);
							$Post_PREVIEW_PICTURE_ID = $arFields['PREVIEW_PICTURE_ID'];
						} elseif (isset($arFields["PREVIEW_PICTURE"])) {
							$Post_PREVIEW_PICTURE_SRC = $_SERVER["DOCUMENT_ROOT"].CFile::GetPath($arFields["PREVIEW_PICTURE"]);
							$Post_PREVIEW_PICTURE_ID = $arFields["PREVIEW_PICTURE"];
						} elseif (isset($arFields["PREVIEW_PICTURE"]["old_file"]) && (int)$arFields["PREVIEW_PICTURE"]["old_file"] > 0) {
							$Post_PREVIEW_PICTURE_SRC = $_SERVER["DOCUMENT_ROOT"].CFile::GetPath($arFields["PREVIEW_PICTURE"]["old_file"]);
							$Post_PREVIEW_PICTURE_ID = $arFields["PREVIEW_PICTURE"]["old_file"];
						}
						$flImgExistVK = true;
					}
					else {
						if( isset($arElFields['PREVIEW_PICTURE']) && !empty($arElFields['PREVIEW_PICTURE']) ) {
							$Post_PREVIEW_PICTURE_SRC = $_SERVER["DOCUMENT_ROOT"].CFile::GetPath($arElFields['PREVIEW_PICTURE']);
							$Post_PREVIEW_PICTURE_ID = $arElFields['PREVIEW_PICTURE'];
							$flImgExistVK = true;
						}
					}*/
					if( isset($arElFields['PREVIEW_PICTURE']) && !empty($arElFields['PREVIEW_PICTURE']) ) {
						$Post_PREVIEW_PICTURE_SRC = $_SERVER["DOCUMENT_ROOT"].CFile::GetPath($arElFields['PREVIEW_PICTURE']);
						$Post_PREVIEW_PICTURE_ID = $arElFields['PREVIEW_PICTURE'];
						$flImgExistVK = true;
					}

					if ($flNewPostVK) {
						if ( isset($arFields['DETAIL_PICTURE_ID']) && !empty($arFields['DETAIL_PICTURE_ID']) ) {
							$Post_DETAIL_PICTURE_SRC = $_SERVER["DOCUMENT_ROOT"].CFile::GetPath($arFields['DETAIL_PICTURE_ID']);
							$Post_DETAIL_PICTURE_ID = $arFields['DETAIL_PICTURE_ID'];
							$flImgExistVK = true;
						}
					}
					else {
						if(isset($arElFields['DETAIL_PICTURE']) && !empty($arElFields['DETAIL_PICTURE'])) {
							$Post_DETAIL_PICTURE_SRC = $_SERVER["DOCUMENT_ROOT"].CFile::GetPath($arElFields['DETAIL_PICTURE']);
							$Post_DETAIL_PICTURE_ID = $arElFields['DETAIL_PICTURE'];
							$flImgExistVK = true;
						}
					}

					if ($flAlbumVKIBExist && $flImgExistVK && $flAlbumVK=="Y") {
						if($PostVKActiveElementAlbum=='Y' && $arFields['ACTIVE']=='N') {
							#return false;
						}
						else {
							$arAlbumVKID = array();
							$IBLOCK_SECTION_ID = $arFields["IBLOCK_SECTION_ID"];
							$mxResult = CCatalogSku::GetProductInfo($arFields["ID"]);
							if (is_array($mxResult)) {
								$rsProduct = CIBlockElement::GetList(
									array(),
									array(
										"IBLOCK_ID" => $mxResult["IBLOCK_ID"],
										"ACTIVE" => "Y",
										"ID" => $mxResult["ID"]
									),
									false,
									false,
									array("IBLOCK_SECTION_ID")
								);
								if ($arProduct = $rsProduct->GetNext())
									$IBLOCK_SECTION_ID = $arProduct["IBLOCK_SECTION_ID"];
							}
							if (isset($IBLOCK_SECTION_ID) && !empty($IBLOCK_SECTION_ID)) {
								if ($tmpAlbumVKID = SVK::find_creat_album(intval($IBLOCK_SECTION_ID), $arFields, $arElFields)) {
									$arAlbumVKID[] = $tmpAlbumVKID;
								}
							}
							elseif (is_array($arFields["IBLOCK_SECTION"]) && count($arFields["IBLOCK_SECTION"]) > 0) {
								foreach($arFields["IBLOCK_SECTION"] as $elSection) {
									if ($tmpAlbumVKID = SVK::find_creat_album(intval($elSection), $arFields, $arElFields)) {
										$arAlbumVKID[] = $tmpAlbumVKID;
									}
								}
							}
						}
					}

					if (in_array('NAME', $arPostVKFieldsIBList)) {
						$PostText = str_replace('#NAME#', $PostName, $PostText);
					}
					if (in_array('LINK', $arPostVKFieldsIBList)) {
						$PostText = str_replace('#LINK#', $_SERVER['HTTP_HOST'].$arElFields['DETAIL_PAGE_URL'], $PostText);
					}
					if (in_array('PREVIEW_OR_DETAIL_TEXT', $arPostVKFieldsIBList)) {
						if(!empty($PostDText)) {
							$PostText = str_replace('#PREVIEW_OR_DETAIL_TEXT#', $PostDText, $PostText);
						}
						elseif (!empty($PostPText)) {
							$PostText = str_replace('#PREVIEW_OR_DETAIL_TEXT#', $PostPText, $PostText);
						}
						else {
							$PostText = str_replace('#PREVIEW_OR_DETAIL_TEXT#', '', $PostText);
						}
					}
					if (in_array('PREVIEW_OR_DETAIL_PICTURE', $arPostVKFieldsIBList)) {
						if (!empty($Post_DETAIL_PICTURE_ID)) {
							$PostPicture = $Post_DETAIL_PICTURE_SRC;
							$PostPictureID = $Post_DETAIL_PICTURE_ID;
						}
						elseif (!empty($Post_PREVIEW_PICTURE_ID)) {
							$PostPicture = $Post_PREVIEW_PICTURE_SRC;
							$PostPictureID = $Post_PREVIEW_PICTURE_ID;
						}
						$PostText = str_replace('#PREVIEW_OR_DETAIL_PICTURE#', '', $PostText);
					}
					if (in_array('PREVIEW_PICTURE', $arPostVKFieldsIBList)) {
						$PostPicture = $Post_PREVIEW_PICTURE_SRC;
						$PostPictureID = $Post_PREVIEW_PICTURE_ID;
						$PostText = str_replace('#PREVIEW_PICTURE#', '', $PostText);
					}
					if (in_array('PREVIEW_TEXT', $arPostVKFieldsIBList)) {
						$PostText = str_replace('#PREVIEW_TEXT#', $PostPText, $PostText);
					}

					if (in_array('DETAIL_PICTURE', $arPostVKFieldsIBList)) {
						$PostPicture = $Post_DETAIL_PICTURE_SRC;
						$PostPictureID = $Post_DETAIL_PICTURE_ID;
						$PostText = str_replace('#DETAIL_PICTURE#', '', $PostText);
					}
					if (in_array('DETAIL_TEXT', $arPostVKFieldsIBList)) {
						$PostText = str_replace('#DETAIL_TEXT#', $PostDText, $PostText);
					}

					foreach($arPostVKPropsIBListValues as $ElPropValue) {
						if ( in_array($ElPropValue['ID'], $arPostVKPropsIBList) ) {
							$tmpElPropName = '#PROPERTY_'.$ElPropValue['ID'].'_NAME#';
							$tmpElPropValue = '#PROPERTY_'.$ElPropValue['ID'].'_VALUE#';
							$PostText = str_replace($tmpElPropName, $ElPropValue['NAME'], $PostText);
							$PostText = str_replace($tmpElPropValue, $ElPropValue['VALUE'], $PostText);
						}
					}

					if( is_array($arPostVKPricesListValues) && count($arPostVKPricesListValues) <= 0) {
						foreach($arPostVKPricesList as $ElPriceValue):
								$tmpElPriceName = '#PRICE_'.$ElPriceValue.'_NAME#';
								$tmpElPriceValue = '#PRICE_'.$ElPriceValue.'_VALUE#';
								$PostText = str_replace($tmpElPriceName, '', $PostText);
								$PostText = str_replace($tmpElPriceValue, '', $PostText);
						endforeach;
					}
					else {
						foreach($arPostVKPricesListValues as $ElPriceValue) {
							if ( in_array($ElPriceValue['ID'], $arPostVKPricesList) ) {
								$tmpElPriceName = '#PRICE_'.$ElPriceValue['ID'].'_NAME#';
								$tmpElPriceValue = '#PRICE_'.$ElPriceValue['ID'].'_VALUE#';
								$PostText = str_replace($tmpElPriceName, $ElPriceValue['NAME'], $PostText);
								$PostText = str_replace($tmpElPriceValue, $ElPriceValue['VALUE'], $PostText);
							}
						}
					}

					if (in_array('NAME', $arAlbumVKFieldsIBList)) {
						$AlbumText = str_replace('#NAME#', $PostName, $AlbumText);
					}
					if (in_array('LINK', $arAlbumVKFieldsIBList)) {
						$AlbumText = str_replace('#LINK#', $_SERVER['HTTP_HOST'].$arElFields['DETAIL_PAGE_URL'], $AlbumText);
					}
					if (in_array('PREVIEW_OR_DETAIL_TEXT', $arAlbumVKFieldsIBList)) {
						if(!empty($PostDText))
							$AlbumText = str_replace('#PREVIEW_OR_DETAIL_TEXT#', $PostDText, $AlbumText);
						elseif(!empty($PostPText))
							$AlbumText = str_replace('#PREVIEW_OR_DETAIL_TEXT#', $PostPText, $AlbumText);
						else
							$AlbumText = str_replace('#PREVIEW_OR_DETAIL_TEXT#', '', $AlbumText);
					}
					if (in_array('PREVIEW_OR_DETAIL_PICTURE', $arAlbumVKFieldsIBList)) {
						if(!empty($Post_DETAIL_PICTURE_ID)) {
							$PostPicture = $Post_DETAIL_PICTURE_SRC;
							$PostPictureID = $Post_DETAIL_PICTURE_ID;
						}
						elseif (!empty($Post_PREVIEW_PICTURE_ID)) {
							$PostPicture = $Post_PREVIEW_PICTURE_SRC;
							$PostPictureID = $Post_PREVIEW_PICTURE_ID;
						}
						$AlbumText = str_replace('#PREVIEW_OR_DETAIL_PICTURE#', '', $AlbumText);
					}
					if (in_array('PREVIEW_PICTURE', $arAlbumVKFieldsIBList)) {
						$PostPicture = $Post_PREVIEW_PICTURE_SRC;
						$PostPictureID = $Post_PREVIEW_PICTURE_ID;
						$AlbumText = str_replace('#PREVIEW_PICTURE#', '', $AlbumText);
					}
					if (in_array('PREVIEW_TEXT', $arAlbumVKFieldsIBList)) {
						$AlbumText = str_replace('#PREVIEW_TEXT#', $PostPText, $AlbumText);
					}

					if (in_array('DETAIL_PICTURE', $arAlbumVKFieldsIBList)) {
						$PostPicture = $Post_DETAIL_PICTURE_SRC;
						$PostPictureID = $Post_DETAIL_PICTURE_ID;
						$AlbumText = str_replace('#DETAIL_PICTURE#', '', $AlbumText);
					}
					if (in_array('DETAIL_TEXT', $arAlbumVKFieldsIBList)) {
						$AlbumText = str_replace('#DETAIL_TEXT#', $PostDText, $AlbumText);
					}

					foreach($arAlbumVKPropsIBListValues as $ElPropValue) {
						if (in_array($ElPropValue['ID'], $arAlbumVKPropsIBList)) {
							$tmpElPropName = '#PROPERTY_'.$ElPropValue['ID'].'_NAME#';
							$tmpElPropValue = '#PROPERTY_'.$ElPropValue['ID'].'_VALUE#';
							$AlbumText = str_replace($tmpElPropName, $ElPropValue['NAME'], $AlbumText);
							$AlbumText = str_replace($tmpElPropValue, $ElPropValue['VALUE'], $AlbumText);
						}
					}

					if (is_array($arAlbumVKPricesListValues) && count($arAlbumVKPricesListValues) <= 0) {
						foreach ($arAlbumVKPricesList as $ElPriceValue) {
								$tmpElPriceName = '#PRICE_'.$ElPriceValue.'_NAME#';
								$tmpElPriceValue = '#PRICE_'.$ElPriceValue.'_VALUE#';
								$AlbumText = str_replace($tmpElPriceName, '', $AlbumText);
								$AlbumText = str_replace($tmpElPriceValue, '', $AlbumText);
						}
					}
					else {
						foreach($arAlbumVKPricesListValues as $ElPriceValue) {
							if ( in_array($ElPriceValue['ID'], $arPostVKPricesList) ) {
								$tmpElPriceName = '#PRICE_'.$ElPriceValue['ID'].'_NAME#';
								$tmpElPriceValue = '#PRICE_'.$ElPriceValue['ID'].'_VALUE#';
								$AlbumText = str_replace($tmpElPriceName, $ElPriceValue['NAME'], $AlbumText);
								$AlbumText = str_replace($tmpElPriceValue, $ElPriceValue['VALUE'], $AlbumText);
							}
						}
					}


					if ($flPostVKIBExist && $flPostVK=="Y") {
						if($PostVKActiveElementPost=='Y' && $arFields['ACTIVE']=='N') {
							#return false;
						}
						else {
							$arDBPostFilter=array("ACTIVE"=>'\'Y\'', "VK_OWNER_ID"=>intval($VKOwnerID), "IBLOCK_ID"=>intval($arFields["IBLOCK_ID"]), "ELEMENT_ID"=>intval($arFields["ID"]));
							$obDBPostResult = CSVKDataBlock::GetList($TableNamePost, $arDBPostFilter);
							if ($arDBPostResult = $obDBPostResult->Fetch()) {
								$flNewPostVK = false;
								$PostVKID = $arDBPostResult["VK_POST_ID"];
								$DBPostItemID = $arDBPostResult["ID"];
								$AutoPostResult = $PostVKID;
								$AutoEditPostResult = ShopVK::wall_edit_post($PostVKID, $PostText, $PostPicture, '', $fl_is_group, 'N');
								if($AutoPostResult) {
									$arDBPostFields = array(
										"LAST_MODIFIED"=>ConvertTimeStamp(time(), "FULL", $arElFields['LID']),
									);
									CSVKDataBlock::Update($TableNamePost, $arDBPostFields, $DBPostItemID);
								}
								$EventLogDesc = 'Edit VKPostID: '.$AutoPostResult.', ElementID: '.$arFields['ID'].', '.$arFields['NAME'];
							}
							else {
								$AutoPostResult = ShopVK::wall_post($PostText, $PostPicture, '', $fl_is_group, 'N');
								if($AutoPostResult) {
									$arDBPostFields = array(
										"ACTIVE"=>"Y",
										"CREATED"=>ConvertTimeStamp(time(), "FULL", $arElFields['LID']),
										"SITE_ID"=>$arElFields['LID'],
										"IBLOCK_ID"=>intval($arFields['IBLOCK_ID']),
										"ELEMENT_ID"=>intval($arFields['ID']),
										"VK_OWNER_ID"=>intval($VKOwnerID),
										"VK_POST_ID"=>intval($AutoPostResult),
									);
									CSVKDataBlock::Add($TableNamePost, $arDBPostFields);
								}
								$EventLogDesc = 'New VKPostID: '.$AutoPostResult.', ElementID: '.$arFields['ID'].', '.$arFields['NAME'];
							}

							if ($PostVKEventLog=='Y') {
										CEventLog::Add(array(
										 "SEVERITY" => "SECURITY",
										 "AUDIT_TYPE_ID" => "SHOP_VK_AUTO_POST",
										 "MODULE_ID" => $MODULE_ID,
										 "ITEM_ID" => $arFields['ID'],
										 "DESCRIPTION" => $EventLogDesc,
									  ));
							}
						}
					}

					//если выбраны выгружаемые свойства и включена синхронизация альбомов вконтакте
					if ($flAlbumVKIBExist && $flImgExistVK && $flAlbumVK=="Y") {
						//если разрешено публиковать только активные элементы и элемент не активен, то ничего не делаем
						if($PostVKActiveElementAlbum=='Y' && $arFields['ACTIVE']=='N') {
							#return false;
						}
						else {
							foreach ($arAlbumVKID as $AlbumVKID) {
								$arDBPhotoFilter=array("ACTIVE"=>'\'Y\'', "VK_OWNER_ID"=>intval($VKOwnerID), "IBLOCK_ID"=>intval($arFields["IBLOCK_ID"]), "ELEMENT_ID"=>intval($arFields["ID"]), "VK_ALBUM_ID"=>intval($AlbumVKID));
								$obDBPhotoResult = CSVKDataBlock::GetList($TableNamePhotos, $arDBPhotoFilter);
								$lfEventExist = false;
								if ($arDBPhotoResult = $obDBPhotoResult->Fetch()) {
									//if($arDBPhotoResult["FILE_ID"] != $PostPictureID):
										$PhotoVKID = intval(ShopVK::upload_photo($PostPicture, $AlbumVKID, $AlbumText, 'N'));
										if( isset($PhotoVKID) && !empty($PhotoVKID) ) {
											ShopVK::delete_photo($arDBPhotoResult["VK_PHOTO_ID"], 'N');
											$arDBPhotoFields = array(
												"LAST_MODIFIED"=>ConvertTimeStamp(time(), "FULL", $arElFields['LID']),
												"FILE_ID"=>intval($PostPictureID),
												"VK_ALBUM_ID"=>intval($AlbumVKID),
												"VK_PHOTO_ID"=>intval($PhotoVKID),
												"VK_PHOTO_CAPTION"=>$PostText,
											);
											CSVKDataBlock::Update($TableNamePhotos, $arDBPhotoFields, $arDBPhotoResult["ID"]);
											$EventLogDesc = 'Update VKPhotoID: '.$PhotoVKID.', for ElementID: '.$arFields['ID'].', '.$arFields['NAME'];
											$lfEventExist = true;
										}
									//endif;
								}
								else {
									$PhotoVKID = intval(ShopVK::upload_photo($PostPicture, $AlbumVKID, $AlbumText, 'N'));
									if( isset($PhotoVKID) && !empty($PhotoVKID) ) {
										$arDBPhotoFields = array(
											"ACTIVE"=>"Y",
											"CREATED"=>ConvertTimeStamp(time(), "FULL", $arElFields['LID']),
											"SITE_ID"=>$arElFields['LID'],
											"IBLOCK_ID"=>intval($arFields['IBLOCK_ID']),
											"ELEMENT_ID"=>intval($arFields['ID']),
											"FILE_ID"=>intval($PostPictureID),
											"VK_OWNER_ID"=>intval($VKOwnerID),
											"VK_ALBUM_ID"=>intval($AlbumVKID),
											"VK_PHOTO_ID"=>intval($PhotoVKID),
											"VK_PHOTO_CAPTION"=>$PostText,
										);
										CSVKDataBlock::Add($TableNamePhotos, $arDBPhotoFields);
										$EventLogDesc = 'New VKPhotoID: '.$PhotoVKID.', for ElementID: '.$arFields['ID'].', '.$arFields['NAME'];
										$lfEventExist = true;
									}
								}

								if($AlbumVKEventLog=='Y' && $lfEventExist) {
									CEventLog::Add(array(
											 "SEVERITY" => "SECURITY",
											 "AUDIT_TYPE_ID" => "SHOP_VK_AUTOEX_PHOTO",
											 "MODULE_ID" => $MODULE_ID,
											 "ITEM_ID" => $arFields['ID'],
											 "DESCRIPTION" => $EventLogDesc,
										  ));
								}
							}
						}
					}
				} else {
					$EventLogDesc = 'Error ShopVK autotest';
					CEventLog::Add(array(
							 "SEVERITY" => "SECURITY",
							 "AUDIT_TYPE_ID" => "SHOP_VK_AUTOTEST",
							 "MODULE_ID" => $MODULE_ID,
							 "ITEM_ID" => $arFields['ID'],
							 "DESCRIPTION" => $EventLogDesc,
						  ));
				}
			}
		}
	}

	//создание альбома
	public function find_creat_album($SectionID, $arFields, $arELFields=array()) {
		if( !isset($SectionID) || empty($SectionID) || !is_array($arFields) ):
			return false;
		endif;

		global $APPLICATION;
		$MODULE_ID = "kreattika.shopvk";

		$VKOwnerID = COption::GetOptionString($MODULE_ID, "shop_vk_owner_id", "N");
		$AlbumVKEventLog = COption::GetOptionString($MODULE_ID, "shop_vk_album_event_log", "N");
		$TableNameAlbums = "b_shopvk_albums";
		$lfEventExist = false;

		$arDBAlbumFilter=array("ACTIVE"=>'\'Y\'', "VK_OWNER_ID"=>intval($VKOwnerID), "IBLOCK_ID"=>intval($arFields["IBLOCK_ID"]), "SECTION_ID"=>intval($SectionID));
		$obDBAlbumResult = CSVKDataBlock::GetList($TableNameAlbums, $arDBAlbumFilter);
		if ($arDBAlbumResult = $obDBAlbumResult->Fetch()) :
			$AlbumVKID = $arDBAlbumResult["VK_ALBUM_ID"];
			$DBAlbumItemID = $arDBAlbumResult["ID"];
			return intval($AlbumVKID);
		else:

			if(CModule::IncludeModule("iblock")):
				$obSection = CIBlockSection::GetByID($SectionID);
				if($arSection = $obSection->Fetch()):
					$AlbumVKID = ShopVK::create_album($arSection['NAME'], HTMLToTxt($arSection['DESCRIPTION']), 'N');
					$AlbumVKID = intval($AlbumVKID);
					$arDBAlbumFields = array(
						"ACTIVE"=>"Y",
						"CREATED"=>ConvertTimeStamp(time(), "FULL", $arElFields['LID']),
						"SITE_ID"=>$arElFields['LID'],
						"IBLOCK_ID"=>intval($arFields['IBLOCK_ID']),
						"SECTION_ID"=>intval($arSection['ID']),
						"SECTION_NAME"=>trim($arSection['NAME']),
						"VK_OWNER_ID"=>intval($VKOwnerID),
						"VK_ALBUM_ID"=>intval($AlbumVKID),
						"VK_ALBUM_NAME"=>trim($arSection['NAME']),
						"VK_ALBUM_CAPTION"=>HTMLToTxt($arSection['DESCRIPTION']),
					);
					CSVKDataBlock::Add($TableNameAlbums, $arDBAlbumFields);
					$EventLogDesc = 'New VKAlbumID: '.$AlbumVKID.', for SectionID: '.$arSection['ID'].', '.$arSection['NAME'];
					$lfEventExist = true;
					return intval($AlbumVKID);
				else:
					return false;
				endif;
			else:
				return false;
			endif;
		endif;

		if($AlbumVKEventLog=='Y' && $lfEventExist):
			CEventLog::Add(array(
					 "SEVERITY" => "SECURITY",
					 "AUDIT_TYPE_ID" => "SHOP_VK_AUTOEX_ALBUM",
					 "MODULE_ID" => $MODULE_ID,
					 "ITEM_ID" => $arFields['ID'],
					 "DESCRIPTION" => $EventLogDesc,
				  ));
		endif;

	}

	//удаление товара
	public function delete_auto_post($arFields) {

		global $APPLICATION;

		$MODULE_ID = "kreattika.shopvk";
		$TableNamePost = "b_shopvk_post";
		$TableNameAlbums = "b_shopvk_albums";
		$TableNamePhotos = "b_shopvk_photos";

		$VKOwnerID = COption::GetOptionString($MODULE_ID, "shop_vk_owner_id", "N");
		$flDeletePostVK = COption::GetOptionString($MODULE_ID, "shop_vk_delete_post", "N");
		$flDeletePhotoVK = COption::GetOptionString($MODULE_ID, "shop_vk_album_delete_photo", "N");
		$PostVKEventLog = COption::GetOptionString($MODULE_ID, "shop_vk_event_log", "N");
		$AlbumVKEventLog = COption::GetOptionString($MODULE_ID, "shop_vk_album_event_log", "N");

		if($flDeletePostVK == "Y"):
			$arDBPostFilter=array("ACTIVE"=>'\'Y\'', "VK_OWNER_ID"=>intval($VKOwnerID), "IBLOCK_ID"=>intval($arFields["IBLOCK_ID"]), "ELEMENT_ID"=>intval($arFields["ID"]));
			$obDBPostResult = CSVKDataBlock::GetList($TableNamePost, $arDBPostFilter);
			while ($arDBPostResult = $obDBPostResult->GetNext()) :
				$PostVKID = $arDBPostResult["VK_POST_ID"];
				ShopVK::wall_delete_post($PostVKID, 'N');
				$arDBPostFields = array(
					"ACTIVE"=>"N",
					"LAST_MODIFIED"=>ConvertTimeStamp(time(), "FULL", $arElFields['LID']),
				);
				CSVKDataBlock::Update($TableNamePost, $arDBPostFields, $arDBPostResult["ID"]);

				$EventLogDesc = 'Delete VKPostID: '.$PostVKID.', for ElementID: '.$arFields['ID'];
				if($PostVKEventLog=='Y'):
					CEventLog::Add(array(
							 "SEVERITY" => "SECURITY",
							 "AUDIT_TYPE_ID" => "SHOP_VK_AUTO_POST",
							 "MODULE_ID" => $MODULE_ID,
							 "ITEM_ID" => $arFields['ID'],
							 "DESCRIPTION" => $EventLogDesc,
						  ));
				endif;
			endwhile;
		endif;

		if($flDeletePhotoVK == "Y"):
			$arDBPhotoFilter=array(
				//"ACTIVE"=>'\'Y\'',
				"VK_OWNER_ID"=>intval($VKOwnerID),
				"IBLOCK_ID"=>intval($arFields["IBLOCK_ID"]),
				"ELEMENT_ID"=>intval($arFields["ID"])
			);
			$obDBPhotoResult = CSVKDataBlock::GetList($TableNamePhotos, $arDBPhotoFilter);
			while ($arDBPhotoResult = $obDBPhotoResult->GetNext()) :
				$PhotoVKID = $arDBPhotoResult["VK_PHOTO_ID"];
				ShopVK::delete_photo($PhotoVKID, 'N');
				$arDBPhotoFields = array(
					"ACTIVE"=>"N",
					"LAST_MODIFIED"=>ConvertTimeStamp(time(), "FULL", $arElFields['LID']),
				);
				CSVKDataBlock::Update($TableNamePhotos, $arDBPhotoFields, $arDBPhotoResult["ID"]);

				$EventLogDesc = 'Delete VKPhotoID: '.$PhotoVKID.', for ElementID: '.$arFields['ID'];
				if($AlbumVKEventLog=='Y'):
					CEventLog::Add(array(
							 "SEVERITY" => "SECURITY",
							 "AUDIT_TYPE_ID" => "SHOP_VK_AUTOEX_PHOTO",
							 "MODULE_ID" => $MODULE_ID,
							 "ITEM_ID" => $arFields['ID'],
							 "DESCRIPTION" => $EventLogDesc,
						  ));
				endif;
			endwhile;
		endif;
	}

	//удаление группы
	public function delete_album($arFields) {

		global $APPLICATION;
		$MODULE_ID = "kreattika.shopvk";
		$TableNamePost = "b_shopvk_post";
		$TableNameAlbums = "b_shopvk_albums";
		$TableNamePhotos = "b_shopvk_photos";
		$lfEventExist = false;

		$VKOwnerID = COption::GetOptionString($MODULE_ID, "shop_vk_owner_id", "N");
		$flDeleteAlbumVK = COption::GetOptionString($MODULE_ID, "shop_vk_album_delete_album", "N");
		$AlbumVKEventLog = COption::GetOptionString($MODULE_ID, "shop_vk_album_event_log", "N");

		if($flDeleteAlbumVK == "Y"):
			$SectionID = $arFields["ID"];

			$arDBAlbumFilter=array("ACTIVE"=>'\'Y\'', "VK_OWNER_ID"=>intval($VKOwnerID), "IBLOCK_ID"=>intval($arFields["IBLOCK_ID"]), "SECTION_ID"=>intval($SectionID));
			$obDBAlbumResult = CSVKDataBlock::GetList($TableNameAlbums, $arDBAlbumFilter);
			while ($arDBAlbumResult = $obDBAlbumResult->GetNext()) :
				$AlbumVKID = $arDBAlbumResult["VK_ALBUM_ID"];
				$DBAlbumItemID = $arDBAlbumResult["ID"];

				ShopVK::delete_album($AlbumVKID, 'N');

				$arDBAlbumFields = array(
					"ACTIVE"=>"N",
					"LAST_MODIFIED"=>ConvertTimeStamp(time(), "FULL", $arElFields['LID']),
				);
				CSVKDataBlock::Update($TableNameAlbums, $arDBAlbumFields, $arDBAlbumResult["ID"]);

				$EventLogDesc = 'Delete VKAlbumID: '.$AlbumVKID.', for SectionID: '.$SectionID;
				if($AlbumVKEventLog=='Y'):
					CEventLog::Add(array(
							 "SEVERITY" => "SECURITY",
							 "AUDIT_TYPE_ID" => "SHOP_VK_AUTOEX_ALBUM",
							 "MODULE_ID" => $MODULE_ID,
							 "ITEM_ID" => $arFields['ID'],
							 "DESCRIPTION" => $EventLogDesc,
						  ));
				endif;
			endwhile;
		endif;
	}

}
?>