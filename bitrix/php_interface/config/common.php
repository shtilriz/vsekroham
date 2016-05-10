<?php
use Vsekroham\Helpers\EnvironmentHelper;
/**
 * Общий инициализатор конфигурации
 */

EnvironmentHelper::setConfiguration([
	'catalogIblockId'      => 1,  //Каталог товаров
	'catalogSkuIblockId'   => 2,  //SKU
	'foundCheaperIblockId' => 12, //Нашли дешевле? Сделаем скидку.

	//RetailRocket
	'rrPartnerId' => '53a000601e994424286fc7d9',
]);
