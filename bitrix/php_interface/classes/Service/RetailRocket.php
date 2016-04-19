<?php
namespace Vsekroham\Service;
use Vsekroham\Helpers\EnvironmentHelper;
/**
 * Class Edost
 * Содержит свойства и методы для работы с API сервиса RetailRocket
 *
 * @package Vsekroham\Service
 *
 * @author Artem Luchnikov <artem@luchnikov.ru>
 */

class RetailRocket
{
	/**
	 * Адрес к API RetailRocket
	 */
	const API_URL = 'http://api.retailrocket.ru';

	/**
	 * Версия API
	 */
	const API_VER = '/api/2.0';

	/**
	 * Путь к запросу рекомендаций
	 */
	const API_RECOMENDATION_PATH = '/recommendation';

	/**
	 * ID аккаунта (Partner Id)
	 *
	 * @var string
	 */
	private $partnerId;

	/**
	 * Анонимизированный идентификатор пользователя. Содержится в cookie rcuid.
	 *
	 * @var string
	 */
	public $rcuid = '';

	public function __construct()
	{
		$this->partnerId = EnvironmentHelper::getParam('rrPartnerId');
		$this->rcuid = (isset($_COOKIE['rcuid']) ? $_COOKIE['rcuid'] : '');
	}

	/**
	 * Запрашивает популярные товары магазина, персонализированные для пользователя
	 *
	 * @return array
	 */
	public function getPersonalizedPopular()
	{
		$arReturn = [];

		$url = self::API_URL . self::API_VER . self::API_RECOMENDATION_PATH .
				'/personalized/popular/' . $this->partnerId . '?session=' . $this->rcuid;
		$arReturn = $this->_execute($url);

		return $arReturn;
	}

	/**
	 * Запрашивает новинки магазина, персонализированные для пользователя
	 *
	 * @return array
	 */
	public function getPersonalizedLatest()
	{
		$arReturn = [];

		$url = self::API_URL . self::API_VER . self::API_RECOMENDATION_PATH .
				'/personalized/latest/' . $this->partnerId . '?session=' . $this->rcuid;
		$arReturn = $this->_execute($url);

		return $arReturn;
	}

	/**
	 * Запрашивает популярные товары магазина, имеющие скидку
	 *
	 * @return array
	 */
	public function getSaleByPopular()
	{
		$arReturn = [];

		$url = self::API_URL . self::API_VER . self::API_RECOMENDATION_PATH .
				'/saleByPopular/' . $this->partnerId . '?categoryIds=0';
		$arReturn = $this->_execute($url);

		return $arReturn;
	}

	/**
	 * Запрашивает новинки магазина, имеющие скидку
	 *
	 * @return array
	 */
	public function getSaleByLatest()
	{
		$arReturn = [];

		$url = self::API_URL . self::API_VER . self::API_RECOMENDATION_PATH .
				'/saleByLatest/' . $this->partnerId . '?categoryIds=0';
		$arReturn = $this->_execute($url);

		return $arReturn;
	}

	/**
	 * Запрашивает товарные рекомендации для страницы товарной категории
	 *
	 * @param int $category_id - идентификатор товарной категории магазина
	 *
	 * @return array
	 */
	public function getPopular($category_id)
	{
		$arReturn = [];

		$url = self::API_URL . self::API_VER . self::API_RECOMENDATION_PATH .
				'/popular/' . $this->partnerId . '?categoryIds=' . $category_id;
		$arReturn = $this->_execute($url);

		return $arReturn;
	}

	/**
	 * Запрашивает товарные рекомендации для карточки товара
	 *
	 * @param int $item_id - идентификатор товара, для которого нужно получить рекомендации
	 *
	 * @return array
	 */
	public function getAlternative($item_id)
	{
		$arReturn = [];

		$url = self::API_URL . self::API_VER . self::API_RECOMENDATION_PATH .
				'/alternative/' . $this->partnerId . '?itemIds=' . $item_id;
		$arReturn = $this->_execute($url);

		return $arReturn;
	}

	/**
	 * Запрашивает товарные рекомендации для корзины
	 *
	 * @param array $item_ids - массив идентификаторов товаров, лежащих в корзине
	 *
	 * @return array
	 */
	public function getRelated($item_ids)
	{
		$arReturn = [];

		$url = self::API_URL . self::API_VER . self::API_RECOMENDATION_PATH .
				'/related/' . $this->partnerId . '?itemIds=' . implode(',', $item_ids);
		$arReturn = $this->_execute($url);

		return $arReturn;
	}

	/**
	 * Запрашивает поисковые рекомендации
	 *
	 * @param string $keyword - поисковый запрос пользователя
	 *
	 * @return array
	 */
	public function search($keyword)
	{
		$arReturn = [];

		$url = self::API_URL . self::API_VER . self::API_RECOMENDATION_PATH .
				'/search/' . $this->partnerId . '?phrase=' . $keyword;
		$arReturn = $this->_execute($url);

		return $arReturn;
	}

	/**
	 * Запрашивает персональные рекомендации
	 *
	 * @return array
	 */
	public function getPersonal()
	{
		$arReturn = [];

		$url = self::API_URL . self::API_VER . self::API_RECOMENDATION_PATH .
				'/personal/' . $this->partnerId . '/?partnerUserSessionId=' . $this->rcuid;
		$arReturn = $this->_execute($url);

		return $arReturn;
	}

	/**
	 * Запрашивает популярные товары указанного бренда
	 *
	 * @param string $vendor - название бренда
	 * @param int $category_id - идентификатор товарной категории магазина. 0 - для всего каталога магазина
	 *
	 * @return array
	 */
	public function getPopularByBrand($vendor, $category_id = 0)
	{
		$arReturn = [];

		$url = self::API_URL . self::API_VER . self::API_RECOMENDATION_PATH .
				'/popular/' . $this->partnerId . '/?vendor=' . $vendor . '&categoryIds=' . $category_id;
		$arReturn = $this->_execute($url);

		return $arReturn;
	}

	/**
	 * Отправляет запрос на сервер RetailRocket
	 *
	 * @param string $url - URL запроса
	 *
	 * @return array
	 */
	protected function _execute($url)
	{
		return json_decode(file_get_contents($url), true);
	}

	/**
	 * Возвращает строку js-кода, отслеживающего клик по ссылке с рекомендацией
	 *
	 * @param int $item_id - идентификатор товара, для которого нужно получить рекомендации
	 * @param string $method_name - объект в котором передается название алгоритма, по которому сформированны рекомендации, в которые произошел клик (Alternative, Related, Popular и т.д.)
	 *
	 * @return string
	 */
	public static function getRecomMouseDown($item_id, $method_name)
	{
		if (!$item_id || !$method_name) {
			return '';
		}

		return sprintf(
			'try { rrApi.recomMouseDown(%s, {methodName: \'%s\'}) } catch(e) {}',
			$item_id,
			$methodName
		);
	}

	/**
	 * Возвращает строку js-кода, отслеживающего добавление товара в корзину из блока с рекомендациями
	 *
	 * @param int $item_id - идентификатор товара, для которого нужно получить рекомендации
	 * @param string $method_name - объект в котором передается название алгоритма, по которому сформированны рекомендации, в которые произошел клик (Alternative, Related, Popular и т.д.)
	 *
	 * @return string
	 */
	public static function getRecomAddToCart($item_id, $method_name)
	{
		if (!$item_id || !$method_name) {
			return '';
		}

		return sprintf(
			'try { rrApi.recomAddToCart(%s, {methodName: \'%s\'}) } catch(e) {}',
			$item_id,
			$methodName
		);
	}
}
