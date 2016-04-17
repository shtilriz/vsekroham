<?php
namespace Vsekroham;
/**
 * Автозагрузчик для пространства имен Vsekroham
 *
 * Class AutoLoader
 *
 * @package Vsekroham
 */

class AutoLoader
{
	static public $flag = false;

	public function __construct()
	{}

	/**
	 * @return string
	 */
	protected static function getBasePath()
	{
		$path = '/bitrix/php_interface/classes';

		if (!empty($_SERVER['DOCUMENT_ROOT'])) {
			return $_SERVER['DOCUMENT_ROOT'] . $path;
		}

		return realpath(__DIR__ . '/../../../') . $path;
	}

	/**
	 * @param string $path
	 * @param string $file
	 *
	 * @return string
	 */
	protected static function generateFilePath($path, $file)
	{
		return str_replace('/Vsekroham/', '/', sprintf('%s/%s.php', $path, str_replace('\\', '/', $file)));
	}

	/**
	 * @param string $file
	 */
	public static function autoLoad($file)
	{
		$path = self::getBasePath();
		$filePath = self::generateFilePath($path, $file);

		if (file_exists($filePath)) {
			require_once($filePath);
		} else {
			self::$flag = true;
			self::recursiveLoad($file, $path);
		}
	}

	/**
	 * @param string $file
	 * @param string $path
	 */
	public static function recursiveLoad($file, $path)
	{
		if (false !== ($handle = opendir($path)) && self::$flag) {
			while (false !== ($dir = readdir($handle)) && self::$flag) {
				if (strpos($dir, '.') === false) {
					$path2 = $path . '/' . $dir;
					$filePath = $path2 . '/' . $file . '.php';

					if (file_exists($filePath)) {
						self::$flag = false;
						require_once($filePath);
						break;
					}

					self::recursiveLoad($file, $path2, self::$flag);
				}
			}

			closedir($handle);
		}
	}
}
