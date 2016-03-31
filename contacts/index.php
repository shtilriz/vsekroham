<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Контакты");
?>

<h1><?$APPLICATION->ShowTitle();?></h1>

<table class="table-ic">
	<tr>
		<td class="table-ic__item">
			<span class="icon ico_alarm"></span>
			<div class="table-ic__title">Время работы интернет-магазина</div>
			Будни c 9:00 до 19:00 по Московскому времени</br>
			Выходные и праздники 10:00 до 18:00 по Московскому времени
		</td>
		<td class="table-ic__item">
			<span class="icon ico_iphone"></span>
			<div class="table-ic__title">Телефоны</div>
			+7 (499) 372-04-83 - многоканальный по Москве</br>
			8 (800) 775-35-48 - бесплатный для регионов России
		</td>
	</tr>
	<tr>
		<td class="table-ic__item">
			<span class="icon ico_mail"></span>
			<div class="table-ic__title">Электронная почта</div>
			sales@vsekroham.ru - розничный отдел</br>
sklad@vsekroham.ru - отдел по работе с браком</br>
			optom@vsekroham.ru - оптовый отдел
		</td>
		<td class="table-ic__item">
			<span class="icon ico_shop"></span>
			<div class="table-ic__title">Адрес интернет-магазина </div>
			г. Москва, Ул. 6-я Радиальная, д. 17.
		</td>
	</tr>
</table>

<?$APPLICATION->IncludeComponent("bitrix:news.list", "contacts", Array(
	"IBLOCK_TYPE" => "content",	// Тип информационного блока (используется только для проверки)
		"IBLOCK_ID" => "7",	// Код информационного блока
		"NEWS_COUNT" => "20",	// Количество новостей на странице
		"SORT_BY1" => "SORT",	// Поле для первой сортировки новостей
		"SORT_ORDER1" => "ASC",	// Направление для первой сортировки новостей
		"SORT_BY2" => "SORT",	// Поле для второй сортировки новостей
		"SORT_ORDER2" => "ASC",	// Направление для второй сортировки новостей
		"FILTER_NAME" => "",	// Фильтр
		"FIELD_CODE" => array(	// Поля
			0 => "NAME",
			1 => "PREVIEW_PICTURE",
			2 => "",
		),
		"PROPERTY_CODE" => array(	// Свойства
			0 => "ADDRESS",
			1 => "RATE",
			2 => "PHONE",
			3 => "LONGITUDE",
			4 => "LATITUDE",
			5 => "ZOOM",
			6 => "",
		),
		"CHECK_DATES" => "Y",	// Показывать только активные на данный момент элементы
		"DETAIL_URL" => "",	// URL страницы детального просмотра (по умолчанию - из настроек инфоблока)
		"AJAX_MODE" => "N",	// Включить режим AJAX
		"AJAX_OPTION_JUMP" => "N",	// Включить прокрутку к началу компонента
		"AJAX_OPTION_STYLE" => "Y",	// Включить подгрузку стилей
		"AJAX_OPTION_HISTORY" => "N",	// Включить эмуляцию навигации браузера
		"CACHE_TYPE" => "A",	// Тип кеширования
		"CACHE_TIME" => "36000000",	// Время кеширования (сек.)
		"CACHE_FILTER" => "N",	// Кешировать при установленном фильтре
		"CACHE_GROUPS" => "Y",	// Учитывать права доступа
		"PREVIEW_TRUNCATE_LEN" => "",	// Максимальная длина анонса для вывода (только для типа текст)
		"ACTIVE_DATE_FORMAT" => "d.m.Y",	// Формат показа даты
		"SET_TITLE" => "N",	// Устанавливать заголовок страницы
		"SET_BROWSER_TITLE" => "N",	// Устанавливать заголовок окна браузера
		"SET_META_KEYWORDS" => "Y",	// Устанавливать ключевые слова страницы
		"SET_META_DESCRIPTION" => "Y",	// Устанавливать описание страницы
		"SET_STATUS_404" => "N",	// Устанавливать статус 404, если не найдены элемент или раздел
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",	// Включать инфоблок в цепочку навигации
		"ADD_SECTIONS_CHAIN" => "N",	// Включать раздел в цепочку навигации
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",	// Скрывать ссылку, если нет детального описания
		"PARENT_SECTION" => "",	// ID раздела
		"PARENT_SECTION_CODE" => "",	// Код раздела
		"INCLUDE_SUBSECTIONS" => "Y",	// Показывать элементы подразделов раздела
		"DISPLAY_DATE" => "Y",	// Выводить дату элемента
		"DISPLAY_NAME" => "Y",	// Выводить название элемента
		"DISPLAY_PICTURE" => "Y",	// Выводить изображение для анонса
		"DISPLAY_PREVIEW_TEXT" => "Y",	// Выводить текст анонса
		"PAGER_TEMPLATE" => ".default",	// Шаблон постраничной навигации
		"DISPLAY_TOP_PAGER" => "N",	// Выводить над списком
		"DISPLAY_BOTTOM_PAGER" => "Y",	// Выводить под списком
		"PAGER_TITLE" => "Новости",	// Название категорий
		"PAGER_SHOW_ALWAYS" => "N",	// Выводить всегда
		"PAGER_DESC_NUMBERING" => "N",	// Использовать обратную навигацию
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",	// Время кеширования страниц для обратной навигации
		"PAGER_SHOW_ALL" => "N",	// Показывать ссылку "Все"
	),
	false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>