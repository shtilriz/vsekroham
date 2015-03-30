<?php
/**
*   interface ILogsDB
*       содердит основные методы для логирования действий с инфоблоками
*/
interface ILogsDB {
	/**
	*	Добавление новой записи в лог
	*
	*	@param string $event - событие, по которому произошло добавление записи в лог
	*	@param string $ip - IP пользователя, добавившего запись
	*	@param integer $user - ID пользователя в CMS Bitrix
	*	@param array $arProduct - массив, описывающий поля товара
	*
	*	@return boolean - результат успех/ошибка
	*/
	public function add($event, $ip, $user, $arProduct);
	/**
	*	Выборка записей из лога
	*
	*	@param array $arSort - массив для сортировки выбираемых записей
	*	@param array $arFilter - массив для фильтрации выбираемых записей
	*	@param array $arSelect - массив возвращаемых полей записи
	*	@param array $arNav - параметры для постраничной навигации и ограничения количества выводимых записей
	*		@param integer $arNav["iNumPage"] - номер страницы при постраничной навигации
	*		@param integer $arNav["nPageSize"] - количество элементов на странице при постраничной навигации
	*
	*	@return array - массив, содержащий информацию о записях из лога
	*/
	public function getList($arSort, $arFilter, $arSelect, $arNav);
	/**
	*	Удаление записи из лога
	*
	*	@param integer $id - идентификатор удаляемой записи
	*
	*	@return bolean - результат успех/ошибка
	*/
	public function delete($id);
}
?>