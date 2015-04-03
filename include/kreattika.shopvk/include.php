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
global $DBType;
IncludeModuleLangFile(__FILE__);

$arClassesList = array(
        // main classes
        "SVK"             => "classes/general/main.php",
        // main autotest classes
        "ShopVKTEST"         => "classes/general/autotest.php",
        // API classes
        "ShopVK"           => "classes/general/vk-api-classes.php",
        // DataBlock classes
        "CSVKDataBlock"           => "classes/general/data_block_classes.php",
);

// fix strange update bug
if (method_exists(CModule, "AddAutoloadClasses"))
{

        CModule::AddAutoloadClasses(
                "kreattika.shopvk",
                $arClassesList
        );
}
else
{
        foreach ($arClassesList as $sClassName => $sClassFile)
        {
                require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/kreattika.shopvk/".$sClassFile);
        }
}

?>
