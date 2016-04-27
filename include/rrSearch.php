<?php
/**
 * Скрипт запрашивает с сервиса RetailRocket поисковые рекомендации показывает их пользователю
 *
 * @link Task: http://p.natix.ru/public/index.php?path_info=projects/2-vsekrohamru/tasks/801
 * @author Artem Luchnikov <artem@luchnikov.ru>
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true
	|| !isset($_GET['q']) || empty($_GET['q'])
) {
	die();
}

$query = trim(strip_tags($_GET['q']));

$rr = new Vsekroham\Service\RetailRocket();
$arResult = $rr->search($query);

foreach ($arResult as $key => $arItem) {
	$arProductIDs[] = $arItem['ItemId'];
}

if (!empty($arProductIDs)) {
	?>
	<h2>Те кто искал "<?echo $query;?>", интересовались этими товарами...</h2>
	<?

	$GLOBALS['arrFilter'] = ['ID' => $arProductIDs];

	include($_SERVER["DOCUMENT_ROOT"].'/include/catalog-section.php');
}
