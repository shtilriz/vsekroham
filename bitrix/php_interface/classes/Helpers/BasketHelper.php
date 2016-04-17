<?php
namespace Vsekroham\Helpers;
/**
 * Class BasketHelper
 *
 * Класс содержит свойства и методы, помогающие в работе с корзиной
 *
 * @package Vsekroham\Helpers
 *
 * @author Artem Luchnikov <artem@luchnikov.ru>
 */

class BasketHelper
{
	/**
	 * возвращает id товара по id записи в корзине текущего пользователя
	 *
	 * @param int $id - идентификатор записи в корзине
	 *
	 * @return int|false
	 */
	public static function getProductIdByBasketId($id)
	{
		if (!$id)
			return false;

		$rsList = \CSaleBasket::GetList(
			array(),
			array(
				'FUSER_ID' => \CSaleBasket::GetBasketUserID(),
				'LID' => SITE_ID,
				'ORDER_ID' => 'NULL',
				'ID' => $id
			),
			false,
			false,
			array('PRODUCT_ID')
		);
		if ($arList = $rsList->Fetch()) {
			return $arList['PRODUCT_ID'];
		}

		return false;
	}

	/**
	 * возвращает id записи в корзине по id товара
	 *
	 * @param int $id - идентификатор товара в корзине
	 *
	 * @return int|false
	 */
	public static function getBasketIdByProductId($id)
	{
		if (!$id)
			return false;

		$rsList = \CSaleBasket::GetList(
			array(),
			array(
				'FUSER_ID' => \CSaleBasket::GetBasketUserID(),
				'LID' => SITE_ID,
				'ORDER_ID' => 'NULL',
				'PRODUCT_ID' => $id
			),
			false,
			false,
			array('ID')
		);
		if ($arList = $rsList->Fetch()) {
			return $arList['ID'];
		}

		return false;
	}
}
