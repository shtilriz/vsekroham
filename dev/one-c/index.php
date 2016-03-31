<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Синхронизация с 1С");
?>

<h4>Обновить остатки</h4>
<iframe src="/dev/one-c/updrest.php" width="468" height="60" frameborder="0"></iframe>
<hr class="clearfix"/>
<h4>Активировать товары</h4>
<iframe src="/dev/one-c/setactive.php" width="468" height="60" frameborder="0"></iframe>
<hr/>
<h4>Деактивировать товары</h4>
<iframe src="/dev/one-c/setunactive.php" width="468" height="60" frameborder="0"></iframe>
<hr/>
<h4>Добавить новые товары из XML файла</h4>
<iframe src="/dev/one-c/newoffer.php" width="468" height="60" frameborder="0"></iframe>
<hr/>
<h4>обновить коды из 1С у товаров на сайте</h4>
<iframe src="/dev/one-c/setcode.php" width="468" height="60" frameborder="0"></iframe>
<hr/>
<h4>обновить цены из 1С у товаров на сайте</h4>
<iframe src="/dev/one-c/updprice.php" width="468" height="1000" frameborder="0"></iframe>
<hr/>
<h4>добавить заказ</h4>
<iframe src="/dev/one-c/addorder.php" width="468" height="60" frameborder="0"></iframe>
<hr/>
<h4>изменить заказ</h4>
<iframe src="/dev/one-c/updorder.php" width="468" height="1500" frameborder="0"></iframe>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>