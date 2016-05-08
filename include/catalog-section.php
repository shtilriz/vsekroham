<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
	require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
}

if (!isset($_SESSION['CAT_ORDER']) || empty($_SESSION['CAT_ORDER'])) {
	$_SESSION['CAT_ORDER'] = 'ASC';
}
if (!isset($_SESSION['CAT_SORT']) || empty($_SESSION['CAT_ORDER'])) {
	$_SESSION['CAT_SORT'] = 'SORT';
}
if (isset($_REQUEST['order'])) {
	$_SESSION['CAT_ORDER'] = $_REQUEST['order'];
}

//если меняется поле сортировки, то принудительно ставим направление сортировки по возрастанию
if (isset($_REQUEST['sort']) && $_REQUEST['sort'] != $_SESSION['CAT_SORT']) {
	$_SESSION['CAT_ORDER'] = 'ASC';
}
if (isset($_REQUEST['sort'])) {
	$_SESSION['CAT_SORT'] = $_REQUEST['sort'];
}

require_once('setFilter.php');

$TEMPLATE_THEME = '.default';
$PAGER_TEMPLATE = 'fixed';
if (strrpos($_SERVER['REQUEST_URI'], '/brands/') === 0) {
	$TEMPLATE_THEME = 'ajax';
	$PAGER_TEMPLATE = '.default';
}

$SECT_ID = $APPLICATION->IncludeComponent('dev:catalog.section', '.default', Array(
	'IBLOCK_TYPE'                         => 'catalogs',	// Тип инфоблока
		'IBLOCK_ID'                       => '1',	// Инфоблок
		'SECTION_ID'                      => ($SECTION_ID > 0 ? $SECTION_ID : ''),	// ID раздела
		'SECTION_CODE'                    => $_REQUEST['SECTION_CODE'],	// Код раздела
		'SECTION_USER_FIELDS'             => array(	// Свойства раздела
			0 => '',
			1 => '',
		),
		'ELEMENT_SORT_FIELD'              => 'PROPERTY_AVAILABLE',	// По какому полю сортируем элементы
		'ELEMENT_SORT_ORDER'              => 'ASC,nulls',	// Порядок сортировки элементов
		'ELEMENT_SORT_FIELD2'             => $_SESSION['CAT_SORT'],	// Поле для второй сортировки элементов
		'ELEMENT_SORT_ORDER2'             => $_SESSION['CAT_ORDER'],	// Порядок второй сортировки элементов
		'FILTER_NAME'                     => 'arrFilter',	// Имя массива со значениями фильтра для фильтрации элементов
		'INCLUDE_SUBSECTIONS'             => 'Y',	// Показывать элементы подразделов раздела
		'SHOW_ALL_WO_SECTION'             => 'Y',	// Показывать все элементы, если не указан раздел
		'HIDE_NOT_AVAILABLE'              => 'N',	// Не отображать товары, которых нет на складах
		'PAGE_ELEMENT_COUNT'              => '24',	// Количество элементов на странице
		'LINE_ELEMENT_COUNT'              => '3',	// Количество элементов выводимых в одной строке таблицы
		'PROPERTY_CODE' => array(	// Свойства
			0 => 'AVAILABLE',
			1 => '',
		),
		'OFFERS_LIMIT'                    => '5',	// Максимальное количество предложений для показа (0 - все)
		'TEMPLATE_THEME'                  => $TEMPLATE_THEME,	// Цветовая тема
		'PRODUCT_SUBSCRIPTION'            => 'N',	// Разрешить оповещения для отсутствующих товаров
		'SHOW_DISCOUNT_PERCENT'           => 'N',	// Показывать процент скидки
		'SHOW_OLD_PRICE'                  => 'N',	// Показывать старую цену
		'MESS_BTN_BUY'                    => 'Купить',	// Текст кнопки 'Купить'
		'MESS_BTN_ADD_TO_BASKET'          => 'В корзину',	// Текст кнопки 'Добавить в корзину'
		'MESS_BTN_SUBSCRIBE'              => 'Подписаться',	// Текст кнопки 'Уведомить о поступлении'
		'MESS_BTN_DETAIL'                 => 'Подробнее',	// Текст кнопки 'Подробнее'
		'MESS_NOT_AVAILABLE'              => 'Нет в наличии',	// Сообщение об отсутствии товара
		'SECTION_URL'                     => '',	// URL, ведущий на страницу с содержимым раздела
		'DETAIL_URL'                      => '',	// URL, ведущий на страницу с содержимым элемента раздела
		'SECTION_ID_VARIABLE'             => 'SECTION_ID',	// Название переменной, в которой передается код группы
		'AJAX_MODE'                       => 'N',	// Включить режим AJAX
		'AJAX_OPTION_JUMP'                => 'N',	// Включить прокрутку к началу компонента
		'AJAX_OPTION_STYLE'               => 'Y',	// Включить подгрузку стилей
		'AJAX_OPTION_HISTORY'             => 'N',	// Включить эмуляцию навигации браузера
		'CACHE_TYPE'                      => 'A',	// Тип кеширования
		'CACHE_TIME'                      => '36000000',	// Время кеширования (сек.)
		'CACHE_GROUPS'                    => 'Y',	// Учитывать права доступа
		'SET_TITLE'                       => 'Y',	// Устанавливать заголовок страницы
		'SET_BROWSER_TITLE'               => 'Y',	// Устанавливать заголовок окна браузера
		'BROWSER_TITLE'                   => '-',	// Установить заголовок окна браузера из свойства
		'SET_META_KEYWORDS'               => 'Y',	// Устанавливать ключевые слова страницы
		'META_KEYWORDS'                   => '',	// Установить ключевые слова страницы из свойства
		'SET_META_DESCRIPTION'            => 'Y',	// Устанавливать описание страницы
		'META_DESCRIPTION'                => '',	// Установить описание страницы из свойства
		'ADD_SECTIONS_CHAIN'              => 'Y',	// Включать раздел в цепочку навигации
		'DISPLAY_COMPARE'                 => 'Y',	// Разрешить сравнение товаров
		'SET_STATUS_404'                  => 'Y',	// Устанавливать статус 404, если не найдены элемент или раздел
		'CACHE_FILTER'                    => 'N',	// Кешировать при установленном фильтре
		'PRICE_CODE' => array(	// Тип цены
			0 => 'BASE',
			1 => 'MARGIN',
		),
		'USE_PRICE_COUNT'                 => 'N',	// Использовать вывод цен с диапазонами
		'SHOW_PRICE_COUNT'                => '1',	// Выводить цены для количества
		'PRICE_VAT_INCLUDE'               => 'Y',	// Включать НДС в цену
		'CONVERT_CURRENCY'                => 'N',	// Показывать цены в одной валюте
		'BASKET_URL'                      => '/bsket/',	// URL, ведущий на страницу с корзиной покупателя
		'ACTION_VARIABLE'                 => 'action',	// Название переменной, в которой передается действие
		'PRODUCT_ID_VARIABLE'             => 'id',	// Название переменной, в которой передается код товара для покупки
		'USE_PRODUCT_QUANTITY'            => 'N',	// Разрешить указание количества товара
		'ADD_PROPERTIES_TO_BASKET'        => 'Y',	// Добавлять в корзину свойства товаров и предложений
		'PRODUCT_PROPS_VARIABLE'          => 'prop',	// Название переменной, в которой передаются характеристики товара
		'PARTIAL_PRODUCT_PROPERTIES'      => 'N',	// Разрешить добавлять в корзину товары, у которых заполнены не все характеристики
		'PRODUCT_PROPERTIES'              => '',	// Характеристики товара
		'PAGER_TEMPLATE'                  => $PAGER_TEMPLATE, // Шаблон постраничной навигации
		'DISPLAY_TOP_PAGER'               => 'N',	// Выводить над списком
		'DISPLAY_BOTTOM_PAGER'            => 'Y',	// Выводить под списком
		'PAGER_TITLE'                     => 'Товары',	// Название категорий
		'PAGER_SHOW_ALWAYS'               => 'N',	// Выводить всегда
		'PAGER_DESC_NUMBERING'            => 'N',	// Использовать обратную навигацию
		'PAGER_DESC_NUMBERING_CACHE_TIME' => '36000',	// Время кеширования страниц для обратной навигации
		'PAGER_SHOW_ALL'                  => 'N',	// Показывать ссылку 'Все'
		'OFFERS_FIELD_CODE' => array(	// Поля предложений
			0 => 'ID',
			1 => 'NAME',
			2 => 'PREVIEW_TEXT',
			3 => 'PREVIEW_PICTURE',
			4 => '',
		),
		'OFFERS_PROPERTY_CODE' => array(	// Свойства предложений
			0 => 'SIZE',
			1 => 'COLOR',
			2 => '',
		),
		'OFFERS_SORT_FIELD'               => 'sort',	// По какому полю сортируем предложения товара
		'OFFERS_SORT_ORDER'               => 'asc',	// Порядок сортировки предложений товара
		'OFFERS_SORT_FIELD2'              => 'id',	// Поле для второй сортировки предложений товара
		'OFFERS_SORT_ORDER2'              => 'asc',	// Порядок второй сортировки предложений товара
		'PRODUCT_DISPLAY_MODE'            => 'N',	// Схема отображения
		'ADD_PICT_PROP'                   => '-',	// Дополнительная картинка основного товара
		'LABEL_PROP'                      => '-',	// Свойство меток товара
		'OFFERS_CART_PROPERTIES' => array(	// Свойства предложений, добавляемые в корзину
			0 => 'SIZE',
			1 => 'COLOR',
		),
		'AJAX_OPTION_ADDITIONAL'          => '',	// Дополнительный идентификатор
		'PRODUCT_QUANTITY_VARIABLE'       => 'quantity',	// Название переменной, в которой передается количество товара
		'COMPARE_PATH'                    => '',	// Путь к странице сравнения
	),
	false
);

if (!$SECT_ID && strrpos($_SERVER['REQUEST_URI'], '/catalog/') === 0) {
	LocalRedirect('/404.php');
}
