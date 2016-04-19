<?php
namespace Vsekroham\Helpers;
/**
 * Class EnvironmentHelper
 *
 * Хелпер для работы с окружением и конфигурацией
 *
 * @package Vsekroham\Helpers
 * @author Artem Luchnikov <artem@luchnikov.ru>
 */

class EnvironmentHelper
{
	/**
	 * key-value хранилище различных конфигурационных констант
	 *
	 * @var array
	 */
	private static $config = [];

	/**
	 * Возвращает параметр конфигурации
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public static function getParam($key)
	{
		return isset(self::$config[$key]) ? self::$config[$key] : null;
	}

	/**
	 * Устанавливает параметр конфигурации
	 *
	 * @param string $key
	 * @param $value
	 */
	public static function setParam($key, $value)
	{
		self::$config[$key] = $value;
	}

	/**
	 * Устанавливает список конфигурационных параметров
	 *
	 * @param array $config
	 * @param bool $isAppend
	 */
	public static function setConfiguration(array $config, $isAppend = true)
	{
		if ($isAppend) {
			self::$config = array_merge(self::$config, $config);
		} else {
			self::$config = $config;
		}
	}
}
