<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Гарантия и возврат");
?> 
<h1><?$APPLICATION->ShowTitle();?></h1>

<?
$arReturn = array();
if (CModule::IncludeModule("iblock") && !empty($_POST)) {
  $arProps = $_REQUEST["prop"];
  $el = new CIBlockElement;


  $arResult = array(
    "IBLOCK_ID"      => 6,
    "IBLOCK_SECTION" => false,
    "NAME"           => $_REQUEST["FIO"],
    "DATE_ACTIVE_FROM" => date('d.m.Y H:i'),
    "ACTIVE"         => "N",
    "PREVIEW_PICTURE" => (is_array($_FILES['file'])?$_FILES['file']:''),
    "PROPERTY_VALUES"=> $arProps
  );

  if($DEAL_ID = $el->Add($arResult))
  {
    $arReturn = array(
      "SUCCESS" => "Y",
      "MESSAGE" => "Ваша заявка принята, вскоре наш менеджер свяжется с вами."
    );

    $res = CIBlockElement::GetList(
      array(),
      array(
        "IBLOCK_ID"=>6,
        "ID" => $DEAL_ID
      ),
      false,
      false,
      array("IBLOCK_ID","PREVIEW_PICTURE")
    );
    $arEventFields["IMAGE"] = '';
    if ($arRes = $res->GetNext()) {
      if ($arRes["PREVIEW_PICTURE"] > 0) {
        $arEventFields["IMAGE"] = "http://".$_SERVER["SERVER_NAME"].CFile::GetPath($arRes["PREVIEW_PICTURE"]);
      }
    }
    $arEventFields["FIO"] = $arResult["NAME"];
    CEvent::SendImmediate("RETURN_POLICY","s1",array_merge($arEventFields,$arProps));
  }
  else {
    $arReturn = array(
      "SUCCESS" => "N",
      "MESSAGE" => "Возникла ошибка при отправке заявки. Повторите попытку позже."
    );
  }
}
else {
  $arReturn = array(
    "SUCCESS" => "N",
    "MESSAGE" => "Возникла ошибка при отправке заявки. Повторите попытку позже."
  );
}

echo '<p>'.$arReturn["MESSAGE"].'</p>';
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>