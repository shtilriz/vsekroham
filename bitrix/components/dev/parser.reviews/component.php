<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	
	if(!(CModule::IncludeModule("iblock"))) {
		ShowError("IBLOCK_MODULE_NOT_INSTALLED");
		return false;
	}

	CModule::IncludeModule('highloadblock');
	use Bitrix\Highloadblock as HL;
	use Bitrix\Main\Entity;
	//use Bitrix\Main\Type\DateTime;
	use Bitrix\Main\Type as FieldType;

	$arResult = array();
	/*global $USER;
	if ($USER->IsAdmin())
	{*/
		if (strlen(shell_exec('/usr/local/bin/phantomjs -v')) > 0) {
			$url_name = strlen($arParams["LINK_INPUT_NAME"])>0?$arParams["LINK_INPUT_NAME"]:'link';
			//Если прищёл POST запрос со ссылкой
			$url = $_POST[$url_name];
			$getParams = strstr($url, "?");
			$url = str_replace($getParams, "", $url);

			if (strlen($url)) {
				//определить, на какой сервис слать запрос
				$bParsing = false;
				$arResult["SERVICE"] = '';
				if (!(strrpos($url,'market.yandex')===false)) {
					$arResult["SERVICE"] = 'yandex';
					$bParsing = true;
				}
				if (!(strrpos($url,'torg.mail')===false)) {
					$arResult["SERVICE"] = 'mailru';
					$bParsing = true;
				}
				//Если ссылка относится к сервису Яндекс Маркет или Товары Мейлру
				if ($bParsing) {
					if ($arResult["SERVICE"] == "yandex") {
						//распарсить страницу
						//может быть несколько страниц, поэтому применяем рекурсию для прохода по всем страницам
						function queryYandex($url = false, $arReturn) {
							if (!$url) {
								return $arReturn;
							}
							else {
								$html = shell_exec('/usr/local/bin/phantomjs '.$_SERVER["DOCUMENT_ROOT"].'/bitrix/components/dev/parser.reviews/yandex.js '.$url);
								$html = strstr($html, '{'); //отсекаем всю лишнюю фигню
								$arTemp = json_decode($html,true);
								return queryYandex($arTemp["NEXT_PAGE"],array_merge($arReturn,$arTemp["items"]));
							}								
						}
						//записать в массив распарсенные данные
						$arResult["PARSING"] = queryYandex($url,array());
						foreach ($arResult["PARSING"] as $key => $arItem) {
							$date = substr($arItem["date"],0,10);
							$mkTime = strtotime($date);
							$arResult["PARSING"][$key]["date"] = date('d.m.Y', $mkTime);
						}
					}
					elseif ($arResult["SERVICE"] == "mailru") {
						$html = shell_exec('/usr/local/bin/phantomjs '.$_SERVER["DOCUMENT_ROOT"].$this->GetPath().'/mailru.js '.$url);
						$arResult["PARSING"] = json_decode($html,true);
						foreach ($arResult["PARSING"] as $key => $arItem) {
							$mkTime = time();
							$mkTimeOld = $mkTime - 86000*30*34;
							$mkRand = rand($mkTimeOld, $mkTime);
							$arResult["PARSING"][$key]["date"] = date('d.m.Y', $mkRand);
						}
					}
					else {
						$arResult["ERRORS"][] = 'Ошибка: могу парсить только с Яндекс.Маркета или Товары@Мейл.ru';
					}
					//выводим ошибку, если нет отзывов
					if (count($arResult["PARSING"]) <= 0) {
						$arResult["ERRORS"][] = 'Ошибка: что то пошло не так при парсинге';
					}
				}
				else {
					$arResult["ERRORS"][] = 'Ошибка: могу парсить только с Яндекс.Маркета или Товары@Мейл.ru';//Иначе сообщить об ошибке
				}				
			}

			if (empty($arResult["ERRORS"]) && !empty($arResult["PARSING"]) && $arParams["IBLOCK_ID_CATALOG"]) {
				$arResult["SECTIONS"] = array();
				//список разделов каталога
				$arFilter = array(
					"IBLOCK_ID" => $arParams["IBLOCK_ID_CATALOG"],
					"ACTIVE" => "Y"
				);
				$resS = CIBlockSection::GetList(array("left_margin"=>"asc"), $arFilter);
				while ($arSection = $resS->GetNext()) {
					$arResult["SECTIONS"][] = array(
						"ID" => $arSection["ID"],
						"NAME" => $arSection["NAME"],
						"DEPTH_LEVEL" => $arSection["DEPTH_LEVEL"],
						"SELECTED" => ($arSection["ID"]==$_GET["section_id"]?true:false)
					);
				}
			}
		}
		else {
			$arResult["ERRORS"][] = 'Ошибка: на сервере не установлен модуль phantomjs';
		}
	/*}
	else {
		$arResult["ERRORS"][] = 'Ошибка: доступ запрещён!';
	}*/


	function getElements($sID, $IBLOCK_ID) {
		$arReturn = array();
		$arSort = array("NAME"=>"ASC");
		$arFilter = array(
			"IBLOCK_ID" => $IBLOCK_ID,
			"ACTIVE" => "Y",
			"SECTION_ID" => $sID,
			"INCLUDE_SUBSECTIONS" => "Y"
		);
		$arSelect = array("IBLOCK_ID", "ID", "NAME");
		$res = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
		while ($arRes = $res->GetNext()) {
			$arReturn[$arRes["ID"]] = $arRes["NAME"];
		}
		return $arReturn;
	}
	//запрос товаров по ID раздела через ajax
	if ($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest' && $_GET["sID"] > 0) {
		$APPLICATION->RestartBuffer();
		$arRes = getElements($_GET["sID"], $arParams["IBLOCK_ID_CATALOG"]);
		$s = '';
		foreach ($arRes as $key => $value)
		{
			$s .= '<option'.($_GET["elID"]==$key?' selected':'').' value="'.$key.'">'.$value.'</option>';
		}
		echo '<label class="col-sm-2 control-label">Выберите товар:</label><div class="col-sm-4"><select name="element" class="form-control"><option value=""></option>'.$s.'</select></div>';
		die();
	}

	//сохранение отзыва
	if ($_POST["saveForm"] == "Y") {
		$hlblock = HL\HighloadBlockTable::getById($arParams["BLOCK_ID"])->fetch();
		$entity = HL\HighloadBlockTable::compileEntity($hlblock);
		$entity_data_class = $entity->getDataClass();

		$i = 0; //кол-во успешно-добавленных отзывов
		$j = 0; //кол-во отзывов, которые не удалось добавить
		foreach ($_POST["review"] as $key => $arReview) {
			if ($arReview["ADD"] == "Y") {

				$result = $entity_data_class::add(array(
					$arParams["PRODUCT"] => $_POST["element"],
					"UF_NAME" => $arReview["NAME"],
					"UF_DATE" => $arReview["DATE_REVIEW"],
					$arParams["RATING"] => $arReview[$arParams["RATING"]],
					$arParams["WORTH"] => $arReview[$arParams["WORTH"]],
					$arParams["LIMITATIONS"] => $arReview[$arParams["LIMITATIONS"]],
					$arParams["COMMENT"] => $arReview[$arParams["COMMENT"]],
					$arParams["LIKE"] => $arReview[$arParams["LIKE"]],
					$arParams["DIZLIKE"] => $arReview[$arParams["DIZLIKE"]],
					"UF_SERVICE" => $_POST["service"]
				));

				if ($result->isSuccess()) {
					$i++;
				}
				else {
					$j++;
				}
			}
		}

		if ($i > 0) {
			$res = CIBlockElement::GetByID($_POST["element"]);
			if($ar_res = $res->GetNext())
				$arResult["SUCCESS"][] = 'Добавлено '.$i.' отзывов к товару <a target="_blank" href="'.$ar_res["DETAIL_PAGE_URL"].'">'.$ar_res["NAME"].'</a>! С ошибкой '.$j.' отзывов';
		}
		else
			$arResult["ERRORS"][] = 'Возникла ошибка при добавлении отзывов в базу.';
	}

	$this->IncludeComponentTemplate();
?>