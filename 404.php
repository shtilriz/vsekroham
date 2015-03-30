<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("cssclass", "one-column page-404");
$APPLICATION->SetTitle("Ошибка доступа к странице 404");
CHTTP::SetStatus("404 Not Found");
@define("ERROR_404","Y");
?>

<div class="page-404__inner">
	<strong>404</strong>
	<h1>Ошибка</h1>
	<p>Страница, которую вы ищете не существует, либо устарела.</p>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>