<?php
/**
 * Скрипт запрашивает с сервиса RetailRocket персональные рекомендации и показывает их пользователю
 *
 * @link Task: http://p.natix.ru/public/index.php?path_info=projects/2-vsekrohamru/tasks/801
 * @author Artem Luchnikov <artem@luchnikov.ru>
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

if (!strlen($_COOKIE['rrpusid'])) {
	die();
}

$recomendation = file_get_contents(
	sprintf(
		'http://api.retailrocket.ru/api/1.0/Recomendation/PersonalRecommendation/%s/?rrUserId=%s',
		'53a000601e994424286fc7d9',
		$_COOKIE['rrpusid']
	)
);

$arProductIDs = json_decode($recomendation, true);

if (!empty($arProductIDs)) {
	?>
	<h2>Наши рекомендации</h2>
	<?

	$GLOBALS['arrFilter'] = ['ID' => array_slice($arProductIDs, 0, 6)];

	include($_SERVER["DOCUMENT_ROOT"].'/include/catalog-section.php');
}