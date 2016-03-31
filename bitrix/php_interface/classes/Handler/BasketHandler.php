<?php
/**
 * Class BasketHandler
 *
 * Класс содержит обработчики событий, связанных с изменением записей в корзине
 *
 * @author Artem Luchnikov <artem@luchnikov.ru>
 */

class BasketHandler
{
	/**
	 * После добавления товара в корзину проверить, имеет ли этот товар подарок.
	 * Если у товара имеется подарок, то добавить его в корзину.
	 * Вызывается после добавления записи в корзину.
	 *
	 * @param int $id - идентификатор добавленной записи корзины
	 * @param array $arFields - Массив полей записи корзины
	 */
	public static function addGift($id, array $arFields)
	{
		$productId = $arFields['PRODUCT_ID'];
		if ($productId) {
			$productId = CatalogHelper::getProductId($productId);
			if ($giftId = DiscountsHelper::getGiftByProduct($productId)) {
				Add2BasketByProductID(
					$giftId,
					$arFields['QUANTITY'],
					array(),
					array(
						array(
							'NAME' => 'Подарок',
							'CODE' => 'GIFT',
							'VALUE' => sprintf('к товару %s', $arFields['NAME']),
							'SORT' => 100
						)
					)
				);
			}
		}
	}

	/**
	 * После обновления товара в корзине проверить, имеет ли этот товар подарок.
	 * Если у товара имеется подарок, то добавить к этому товару столько подарков, сколько имеется товаров
	 * Вызывается после обновления записи в корзине
	 *
	 * @param int $id - идентификатор изменяемой записи корзины
	 * @param array $arFields - Массив полей записи корзины
	 */
	public static function updateGift($id, array $arFields)
	{
		$productId = BasketHelper::getProductIdByBasketId($id);

		if ($productId) {
			$productId = CatalogHelper::getProductId($productId);
			if ($giftId = DiscountsHelper::getGiftByProduct($productId)) {
				if ($basketId = BasketHelper::getBasketIdByProductId($giftId)) {
					$arGiftFields = array(
						'QUANTITY' => $arFields['QUANTITY']
					);
					CSaleBasket::Update($basketId, $arGiftFields);
				}
			}
		}
	}
	/**
	 * После удаления товара из корзины проверить, имеет ли этот товар подарок.
	 * Если у товара имеется подарок, то удалить этот подарок из корзины
	 * Вызывается после удаления записи в корзине
	 *
	 * @param int $id - идентификатор удаляемой записи корзины
	 */
	public static function deleteGift($id)
	{
		$productId = BasketHelper::getProductIdByBasketId($id);

		if ($productId) {
			$productId = CatalogHelper::getProductId($productId);
			if ($giftId = DiscountsHelper::getGiftByProduct($productId)) {
				if ($basketId = BasketHelper::getBasketIdByProductId($giftId)) {
					CSaleBasket::Delete($basketId);
				}
			}
		}
	}
}
