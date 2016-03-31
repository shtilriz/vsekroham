<?php
/**
 * Class Edost
 * Содержит свойства и методы для работы с API сервиса Edost
 *
 * @author Artem Luchnikov <artem@luchnikov.ru>
 * @version 1.0
 */

class Edost
{
	/**
	 * URL-адрес для запросов
	 */
	const API_URL = 'http://www.edost.ru/edost_calc_kln.php';

	/**
	 * Идентификатор магазина в Edost
	 */
	const ID = 4590;

	/**
	 * Пароль для доступа к серверу расчетов
	 */
	const PASSWORD = 'CC5jGjKi9guSHltNUiNmZG7XQ3vRf2js';

	/**
	 * Расчет доставки
	 *
	 * @param string|int $to_city - название города, региона или страны или их код, куда неоходимо отправить посылку
	 * @param float|int  $weight  - вес в кг (минимально допустимый для расчета вес: 0,001)
	 * @param float|int  $strah   - сумма для страховки в рублях - при расчете доставки "со страховкой" должна быть больше нуля!
	 * @param float|int  $ln      - длина посылки
	 * @param float|int  $wd      - ширина посылки
	 * @param float|int  $hg      - высота посылки
	 */
	public function calculate($to_city, $weight, $strah = 0, $ln, $wd, $hg)
	{
		$params = [
			'to_city' => $to_city,
			'weight'  => $weight,
			'strah'   => $strah,
			'ln'      => $ln,
			'wd'      => $wd,
			'hg'      => $hg,
		];

		return $this->_execute($params);
	}

	/**
	 * Отправка запроса на сервер Edost
	 *
	 * @param array $params - массив данных со структурой запроса
	 *
	 * @return array - ответ от сервера Edost
	 */
	protected function _execute($params)
	{
		$params['id'] = self::ID;
		$params['p'] = self::PASSWORD;
		$ch = curl_init(self::API_URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		$out = curl_exec($ch);
		curl_close($ch);
		return $this->_parseResponseXML($out);
	}

	/**
	 * Парсит xml-ответ от сервера DHL
	 *
	 * @param string $xmlStr
	 *
	 * @return array
	 */
	private function _parseResponseXML($xmlStr)
	{
		$arReturn = array();
		if ($xml = simplexml_load_string($xmlStr)) {
			$json = json_encode($xml);
			$arReturn = json_decode($json,TRUE);
		}
		return $arReturn;
	}
}