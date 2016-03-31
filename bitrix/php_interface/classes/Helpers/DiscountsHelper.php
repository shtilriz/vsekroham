<?php
/**
 * Class DiscountsHelper
 *
 * Класс содержит свойства и методы необходимые для работы с модулем 'Информационные блоки'.
 *
 * @author Artem Luchnikov <artem@luchnikov.ru>
 */
use Bitrix\Sale\Internals\DiscountTable as DT;

class DiscountsHelper
{
	/**
	 * метод определяет, есть ли у товара подарок на основе правил работы с корзиной
	 * если у правила работы с корзиной во внешнем коде XML_ID записан ID товара, то метод возвратит ID товара-подарка
	 *
	 * @param int $productId
	 *
	 * @return int|bool
	 */
	public static function getGiftByProduct($productId)
	{
		if ($productId) {
			$rsList = DT::getList(
				array(
					'filter' => array('ACTIVE' => 'Y', 'XML_ID' => $productId),
					'select' => array('ACTIONS_LIST'),
				)
			);
			if ($arList = $rsList->Fetch()) {
				$arAction = $arList['ACTIONS_LIST']['CHILDREN'][0];
				if ($arAction['DATA']['Unit'] == 'Perc'
					&& $arAction['DATA']['Value'] == 100
					&& isset($arAction['CHILDREN'][0]['DATA']['value'])
				) {
					return
						$arAction['CHILDREN'][0]['DATA']['value'];
				}
			}
		}
		return false;
	}
}
