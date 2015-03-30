<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Поиск товаров");
?>

<h1>Результаты поиска</h1>

<?//include($_SERVER["DOCUMENT_ROOT"].'/include/catalog-sort.php');?>

<div class="search-results" id="searchPage">
	<?$APPLICATION->IncludeFile("/include/catalog-filter.php");?>
	<div class="search-results__inner">
		<?include($_SERVER["DOCUMENT_ROOT"].'/include/catalog-section.php');?>
	</div>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>