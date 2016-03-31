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
global $MESS;
$PathInstall = str_replace("\\", "/", __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen("/index.php"));
IncludeModuleLangFile(__FILE__);

Class kreattika_shopvk extends CModule
{
        var $MODULE_ID = "kreattika.shopvk";
        var $MODULE_VERSION;
        var $MODULE_VERSION_DATE;
        var $MODULE_NAME;
        var $MODULE_DESCRIPTION;
        var $MODULE_CSS;
//        var $MODULE_GROUP_RIGHTS = "Y";

        function kreattika_shopvk()
        {
                $arModuleVersion = array();

                $path = str_replace("\\", "/", __FILE__);
                $path = substr($path, 0, strlen($path) - strlen("/index.php"));
                include($path."/version.php");

                if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
                {
                        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
                        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
                }
                else
                {
                        $this->MODULE_VERSION = "1.0";
                        $this->MODULE_VERSION_DATE = "2010-11-01 00:00:00";
                }

                $this->MODULE_NAME = GetMessage("KREATTIKA_MODULE_NAME");
                $this->MODULE_DESCRIPTION = GetMessage("KREATTIKA_MODULE_DESCRIPTION");

                $this->PARTNER_NAME = GetMessage("KREATTIKA_PARTNER_NAME");
                $this->PARTNER_URI = GetMessage("KREATTIKA_PARTNER_URI");
        }
        function DoInstall()
        {
                global $DB, $APPLICATION, $step;

                $step = IntVal($step);
                if($step<2)
				{
					$GLOBALS["install_step"] = 1;
	                $APPLICATION->IncludeAdminFile(GetMessage("KREATTIKA_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/kreattika.shopvk/install/step1.php");
				}
				elseif($step==2)
				{
					if($this->InstallDB()){

						$this->InstallFiles();

		          	}

					$GLOBALS["errors"] = $this->errors;
					$GLOBALS["install_step"] = 2;
	                $APPLICATION->IncludeAdminFile(GetMessage("KREATTIKA_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/kreattika.shopvk/install/step1.php");
				}

        }
        function DoUninstall()
        {
                global $DB, $APPLICATION, $step;
                $step = IntVal($step);

                if($step<2)
                {
                        $APPLICATION->IncludeAdminFile(GetMessage("KREATTIKA_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/kreattika.shopvk/install/unstep1.php");
                }
                elseif($step==2)
                {
                        $this->UnInstallDB(array(
                                "savedata" => $_REQUEST["savedata"],
                        ));
                        $this->UnInstallFiles();
                        $APPLICATION->IncludeAdminFile(GetMessage("KREATTIKA_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/kreattika.shopvk/install/unstep2.php");
                }

        }
        function InstallDB()
        {

                global $DB, $DBType, $APPLICATION;
				$this->errors = false;
				// Database tables creation
				if (!$DB->Query("SELECT 'x' FROM b_shopvk_post WHERE 1=0", true) || !$DB->Query("SELECT 'x' FROM b_shopvk_albums WHERE 1=0", true) || !$DB->Query("SELECT 'x' FROM b_shopvk_photos WHERE 1=0", true))
				{
					$this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/kreattika.shopvk/install/db/".strtolower($DB->type)."/install.sql");
				}
				if ($this->errors !== false)
				{
					$APPLICATION->ThrowException(implode("<br>", $this->errors));
					return false;
				}
				else
				{
					RegisterModule($this->MODULE_ID);
					//RegisterModuleDependences("iblock", "OnAfterIblockElementAdd", $this->MODULE_ID, "SVK", "wall_auto_post");
					//RegisterModuleDependences("iblock", "OnAfterIBlockElementUpdate", $this->MODULE_ID, "SVK", "wall_auto_post");
					//RegisterModuleDependences("iblock", "OnAfterIBlockElementDelete", $this->MODULE_ID, "SVK", "delete_auto_post");
					RegisterModuleDependences("iblock", "OnAfterIBlockSectionDelete", $this->MODULE_ID, "SVK", "delete_album");
				}
				return true;

        }
        function UnInstallDB($arParams = array())
        {

                global $DB, $DBType, $APPLICATION;
				$this->errors = false;
				UnRegisterModule($this->MODULE_ID);
				//UnRegisterModuleDependences("iblock", "OnAfterIblockElementAdd", $this->MODULE_ID, "SVK", "wall_auto_post");
				//UnRegisterModuleDependences("iblock", "OnAfterIBlockElementUpdate", $this->MODULE_ID, "SVK", "wall_auto_post");
				//UnRegisterModuleDependences("iblock", "OnAfterIBlockElementDelete", $this->MODULE_ID, "SVK", "delete_auto_post");
				UnRegisterModuleDependences("iblock", "OnAfterIBlockSectionDelete", $this->MODULE_ID, "SVK", "delete_album");
				if (!array_key_exists("savedata", $arParams) || $arParams["savedata"] != "Y")
				{
					$this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/kreattika.shopvk/install/db/".strtolower($DB->type)."/uninstall.sql");
				}
				UnRegisterModule($this->MODULE_ID);
				if ($this->errors !== false)
				{
					$APPLICATION->ThrowException(implode("<br>", $this->errors));
					return false;
				}
				return true;

        }
        function InstallFiles()
        {
       			global $DB;

                CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/kreattika.shopvk/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
                CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/kreattika.shopvk/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true, true);
                CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/kreattika.shopvk/install/themes", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", true, true);

                return true;
        }
        function UnInstallFiles()
        {

                DeleteDirFilesEx("/bitrix/components/kreattika/kreattika.shopvk");
                return true;

        }

}
?>