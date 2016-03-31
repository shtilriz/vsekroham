<?php
/**
 * Class CatalogHandler
 *  Содержит обработчики событий модуля торгового каталога
 */

class CatalogHandler
{
	/**
	 * обработчик события, вызываемого в случае успешного изменения ценового предложения
	 */
	public static function OnPriceUpdateHandler($id, $arFields)
	{
		$path = '/home/vsekroham/public_html/upload/priceLog.csv';
		//узнать изменилось ли значение цены
		if (($handle = fopen($path, 'r+')) !== FALSE) {
			$priceFile = 0;
			while (($arData = fgetcsv($handle, 100, ';')) !== FALSE) {
				if ($arData[2] == $id) {
					$priceFile = $arData[3];
				}
			}

			if ($priceFile != $arFields['PRICE']) {
				$productId = $arFields['PRODUCT_ID'];
				$rsPrice = CPrice::GetList(
					array(),
					array('ID' => $id),
					false,
					false,
					array('PRODUCT_ID')
				);
				if ($arPrice = $rsPrice->GetNext()) {
					$productId = $arPrice['PRODUCT_ID'];
				}

				fputcsv(
					$handle,
					array(
						date('Y-m-d H:i'),
						$productId,
						$id,
						$arFields['PRICE']
					),
					';'
				);
			}
			fclose($handle);
		}
	}
}