<?
//<title>Aport</title>
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/catalog/export_setup_templ.php');

global $APPLICATION, $USER;

$arSetupErrors = array();

$strAllowExportPath = COption::GetOptionString("catalog", "export_default_path", "/bitrix/catalog_export/");

if (($ACTION == 'EXPORT_EDIT' || $ACTION == 'EXPORT_COPY') && $STEP == 1)
{
	if (array_key_exists('IBLOCK_ID', $arOldSetupVars))
		$IBLOCK_ID = $arOldSetupVars['IBLOCK_ID'];
	if (array_key_exists('SETUP_FILE_NAME', $arOldSetupVars))
		$SETUP_FILE_NAME = str_replace($strAllowExportPath,'',$arOldSetupVars['SETUP_FILE_NAME']);
	if (array_key_exists('SETUP_PROFILE_NAME', $arOldSetupVars))
		$SETUP_PROFILE_NAME = $arOldSetupVars['SETUP_PROFILE_NAME'];
	if (array_key_exists('GET_PARAMS', $arOldSetupVars))
		$GET_PARAMS = $arOldSetupVars['GET_PARAMS'];
	if (array_key_exists('SHOW_UTM', $arOldSetupVars))
		$SHOW_UTM = $arOldSetupVars['SHOW_UTM'];
	if (array_key_exists('SETUP_SERVER_NAME', $arOldSetupVars))
		$SETUP_SERVER_NAME = $arOldSetupVars['SETUP_SERVER_NAME'];
}

if ($STEP>1)
{
	$IBLOCK_ID = intval($IBLOCK_ID);
	$rsIBlocks = CIBlock::GetByID($IBLOCK_ID);
	if ($IBLOCK_ID<=0 || !($arIBlock = $rsIBlocks->Fetch()))
	{
		$arSetupErrors[] = GetMessage("CET_ERROR_NO_IBLOCK1")." #".$IBLOCK_ID." ".GetMessage("CET_ERROR_NO_IBLOCK2");
	}
	else
	{
		$bRightBlock = !CIBlockRights::UserHasRightTo($IBLOCK_ID, $IBLOCK_ID, "iblock_admin_display");
		if ($bRightBlock)
		{
			$arSetupErrors[] = str_replace('#IBLOCK_ID#',$IBLOCK_ID,GetMessage("CET_ERROR_IBLOCK_PERM"));
		}
	}

	if (strlen($SETUP_FILE_NAME)<=0)
	{
		$arSetupErrors[] = GetMessage("CET_ERROR_NO_FILENAME");
	}
	elseif (preg_match(BX_CATALOG_FILENAME_REG, $strAllowExportPath.$SETUP_FILE_NAME))
	{
		$arSetupErrors[] = GetMessage("CES_ERROR_BAD_EXPORT_FILENAME");
	}
	elseif ($APPLICATION->GetFileAccessPermission($strAllowExportPath.$SETUP_FILE_NAME) < "W")
	{
		$arSetupErrors[] = str_replace("#FILE#", $strAllowExportPath.$SETUP_FILE_NAME, GetMessage('CET_YAND_RUN_ERR_SETUP_FILE_ACCESS_DENIED'));
	}

	$SETUP_SERVER_NAME = trim($SETUP_SERVER_NAME);

	if (($ACTION=="EXPORT_SETUP" || $ACTION=="EXPORT_EDIT" || $ACTION=="EXPORT_COPY") && strlen($SETUP_PROFILE_NAME)<=0)
		$arSetupErrors[] = GetMessage("CET_ERROR_NO_PROFILE_NAME");

	if (!empty($arSetupErrors))
	{
		$STEP = 1;
	}
}

$aMenu = array(
	array(
		"TEXT"=>GetMessage("CATI_ADM_RETURN_TO_LIST"),
		"TITLE"=>GetMessage("CATI_ADM_RETURN_TO_LIST_TITLE"),
		"LINK"=>"/bitrix/admin/cat_export_setup.php?lang=".LANGUAGE_ID,
		"ICON"=>"btn_list",
	)
);

$context = new CAdminContextMenu($aMenu);

$context->Show();

if (!empty($arSetupErrors))
	ShowError(implode('<br />', $arSetupErrors));
?>
<form method="post" action="<?echo $APPLICATION->GetCurPage() ?>" name="yandex_setup_form" id="yandex_setup_form">
<?
$aTabs = array(
	array("DIV" => "yand_edit1", "TAB" => GetMessage("CAT_ADM_MISC_EXP_TAB1"), "ICON" => "store", "TITLE" => GetMessage("CAT_ADM_MISC_EXP_TAB1_TITLE")),
	array("DIV" => "yand_edit2", "TAB" => GetMessage("CAT_ADM_MISC_EXP_TAB2"), "ICON" => "store", "TITLE" => GetMessage("CAT_ADM_MISC_EXP_TAB2_TITLE")),
);

$tabControl = new CAdminTabControl("tabYandex", $aTabs, false, true);
$tabControl->Begin();

$tabControl->BeginNextTab();

if ($STEP==1)
{
?><tr>
	<td width="40%"><? echo GetMessage('CET_SELECT_IBLOCK_EXT'); ?></td>
	<td width="60%"><?
	$arIBlockIDs = array();
	$rsCatalogs = CCatalog::GetList(
		array(),
		array('!PRODUCT_IBLOCK_ID' => 0),
		false,
		false,
		array('PRODUCT_IBLOCK_ID')
	);
	while ($arCatalog = $rsCatalogs->Fetch())
	{
		$arCatalog['PRODUCT_IBLOCK_ID'] = intval($arCatalog['PRODUCT_IBLOCK_ID']);
		if (0 < $arCatalog['PRODUCT_IBLOCK_ID'])
			$arIBlockIDs[$arCatalog['PRODUCT_IBLOCK_ID']] = true;
	}
	$rsCatalogs = CCatalog::GetList(
		array(),
		array('PRODUCT_IBLOCK_ID' => 0),
		false,
		false,
		array('IBLOCK_ID')
	);
	while ($arCatalog = $rsCatalogs->Fetch())
	{
		$arCatalog['IBLOCK_ID'] = intval($arCatalog['IBLOCK_ID']);
		if (0 < $arCatalog['IBLOCK_ID'])
			$arIBlockIDs[$arCatalog['IBLOCK_ID']] = true;
	}
	if (empty($arIBlockIDs))
		$arIBlockIDs[-1] = true;
	echo GetIBlockDropDownListEx(
		$IBLOCK_ID, 'IBLOCK_TYPE_ID', 'IBLOCK_ID',
		array(
			'ID' => array_keys($arIBlockIDs), 'ACTIVE' => 'Y',
			'CHECK_PERMISSIONS' => 'Y','MIN_PERMISSION' => 'W'
		),
		"ClearSelected(); BX('id_ifr').src='/bitrix/tools/catalog_export/yandex_util.php?IBLOCK_ID=0&'+'".bitrix_sessid_get()."';",
		"ClearSelected(); BX('id_ifr').src='/bitrix/tools/catalog_export/yandex_util.php?IBLOCK_ID='+this[this.selectedIndex].value+'&'+'".bitrix_sessid_get()."';",
		'class="adm-detail-iblock-types"',
		'class="adm-detail-iblock-list"'
	);
	?>
		<script type="text/javascript">
		var TreeSelected = new Array();
		<?
		$intCountSelected = 0;
		if (isset($V) && !empty($V) && is_array($V))
		{
			foreach ($V as $oneKey)
			{
				?>TreeSelected[<? echo $intCountSelected ?>] = <? echo intval($oneKey); ?>;
			<?
			$intCountSelected++;
			}
		}
		?>
		function ClearSelected()
		{
			BX.showWait();
			TreeSelected = new Array();
		}
		</script>
	</td>
</tr>

<tr>
	<td width="40%"><?echo GetMessage("CET_SERVER_NAME");?></td>
	<td width="60%">
		<input type="text" name="SETUP_SERVER_NAME" value="<?echo (strlen($SETUP_SERVER_NAME)>0) ? htmlspecialcharsbx($SETUP_SERVER_NAME) : '' ?>" size="50" /> <input type="button" onclick="this.form['SETUP_SERVER_NAME'].value = window.location.host;" value="<?echo htmlspecialcharsbx(GetMessage('CET_SERVER_NAME_SET_CURRENT'))?>" />
	</td>
</tr>
<tr>
	<td width="40%"><?echo GetMessage("CET_SAVE_FILENAME");?></td>
	<td width="60%">
		<b><? echo htmlspecialcharsbx(COption::GetOptionString("catalog", "export_default_path", "/bitrix/catalog_export/"));?></b><input type="text" name="SETUP_FILE_NAME" value="<?echo (strlen($SETUP_FILE_NAME)>0) ? htmlspecialcharsbx($SETUP_FILE_NAME) : "yandex_".mt_rand(0, 999999).".php" ?>" size="50" />
	</td>
</tr>
<?
	if ($ACTION=="EXPORT_SETUP" || $ACTION == 'EXPORT_EDIT' || $ACTION == 'EXPORT_COPY')
	{
?><tr>
	<td width="40%"><?echo GetMessage("CET_PROFILE_NAME");?></td>
	<td width="60%">
		<input type="text" name="SETUP_PROFILE_NAME" value="<?echo htmlspecialcharsbx($SETUP_PROFILE_NAME) ?>" size="30">
	</td>
</tr><?
	}
?>
<tr>
	<td width="40%">GET-параметры ссылки</td>
	<td width="60%">
		<input type="text" name="GET_PARAMS" value="<?echo htmlspecialcharsbx($GET_PARAMS) ?>" size="70">
	</td>
</tr>
<tr>
	<td width="40%">Выводить в ссылке параметры utm_campaign и utm_term</td>
	<td width="60%">
		<input type="checkbox" name="SHOW_UTM" value="Y"<?=($SHOW_UTM=='Y'?' checked':'')?>>
	</td>
</tr>
<?
}

$tabControl->EndTab();

$tabControl->BeginNextTab();

if ($STEP==2)
{
	$SETUP_FILE_NAME = $strAllowExportPath.$SETUP_FILE_NAME;
	if (strlen($XML_DATA) > 0)
	{
		$XML_DATA = base64_decode($XML_DATA);
	}
	$SETUP_SERVER_NAME = htmlspecialcharsbx($SETUP_SERVER_NAME);
	$_POST['SETUP_SERVER_NAME'] = htmlspecialcharsbx($_POST['SETUP_SERVER_NAME']);
	$_REQUEST['SETUP_SERVER_NAME'] = htmlspecialcharsbx($_REQUEST['SETUP_SERVER_NAME']);

	$FINITE = true;
}
$tabControl->EndTab();

$tabControl->Buttons();

?><? echo bitrix_sessid_post();?><?
if ($ACTION == 'EXPORT_EDIT' || $ACTION == 'EXPORT_COPY')
{
	?><input type="hidden" name="PROFILE_ID" value="<? echo intval($PROFILE_ID); ?>"><?
}

if (2 > $STEP)
{
	?><input type="hidden" name="lang" value="<?echo LANGUAGE_ID ?>">
	<input type="hidden" name="ACT_FILE" value="<?echo htmlspecialcharsbx($_REQUEST["ACT_FILE"]) ?>">
	<input type="hidden" name="ACTION" value="<?echo htmlspecialcharsbx($ACTION) ?>">
	<input type="hidden" name="STEP" value="<?echo intval($STEP) + 1 ?>">
	<input type="hidden" name="SETUP_FIELDS_LIST" value="IBLOCK_ID,SETUP_SERVER_NAME,SETUP_FILE_NAME,GET_PARAMS,SHOW_UTM">
	<input type="submit" value="<?echo ($ACTION=="EXPORT")?GetMessage("CET_EXPORT"):GetMessage("CET_SAVE")?>"><?
}

$tabControl->End();
?></form>
<script type="text/javascript">
<?if ($STEP < 2):?>
tabYandex.SelectTab("yand_edit1");
tabYandex.DisableTab("yand_edit2");
<?elseif ($STEP == 2):?>
tabYandex.SelectTab("yand_edit2");
tabYandex.DisableTab("yand_edit1");
<?endif;?>
</script>