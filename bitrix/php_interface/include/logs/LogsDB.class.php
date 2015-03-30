<?php
require 'ILogsDB.class.php';

class LogsDB implements ILogsDB {
	const DB_NAME = '/home/vsekroham/public_html/dev/logs.db';
	protected $_db;

	function __construct() {
		if (is_file(self::DB_NAME)) {
			$this->_db = new SQLite3(self::DB_NAME);
		}
		else {
			$this->_db = new SQLite3(self::DB_NAME);
			$sql = "CREATE TABLE logs(
				id INTEGER PRIMARY KEY AUTOINCREMENT,
				datetime INTEGER,
				event TEXT,
				ip TEXT,
				user INTEGER,
				PRODUCT_ID INTEGER,
				IBLOCK_ID INTEGER,
				CODE TEXT,
				XML_ID TEXT,
				NAME TEXT,
				IBLOCK_SECTION_ID INTEGER,
				ACTIVE TEXT,
				SORT INTEGER,
				PREVIEW_PICTURE INTEGER,
				PREVIEW_TEXT TEXT,
				DETAIL_PICTURE INTEGER,
				DETAIL_TEXT TEXT,
				CREATED_BY INTEGER,
				CREATED_USER_NAME TEXT,
				TIMESTAMP_X TEXT,
				MODIFIED_BY INTEGER,
				USER_NAME TEXT,
				DETAIL_PAGE_URL TEXT,
				PROPERTY_VALUES TEXT
			)";
			$this->_db->exec($sql) or die($this->_db->lastErrorMsg());
		}
	}
	function __destruct() {
		unset($this->_db);
	}
	public function add($event, $ip, $user, $arProduct) {
		$dt = time();
		$arProduct["PREVIEW_PICTURE"] = $arProduct["PREVIEW_PICTURE_ID"]?(int)$arProduct["PREVIEW_PICTURE_ID"]:(int)$arProduct["PREVIEW_PICTURE"]["old_file"];
		$arProduct["DETAIL_PICTURE"] = $arProduct["DETAIL_PICTURE_ID"]?(int)$arProduct["DETAIL_PICTURE_ID"]:(int)$arProduct["DETAIL_PICTURE"]["old_file"];
		$sql = "INSERT INTO logs(datetime, event, ip, user, PRODUCT_ID, IBLOCK_ID, CODE, XML_ID, NAME, IBLOCK_SECTION_ID, ACTIVE, SORT, PREVIEW_PICTURE, PREVIEW_TEXT, DETAIL_PICTURE, DETAIL_TEXT, CREATED_BY, CREATED_USER_NAME, TIMESTAMP_X, MODIFIED_BY, USER_NAME, DETAIL_PAGE_URL, PROPERTY_VALUES)
					VALUES (:datetime,:event,:ip,:user,:PRODUCT_ID,:IBLOCK_ID,:CODE,:XML_ID,:NAME,:IBLOCK_SECTION_ID,:ACTIVE,:SORT,:PREVIEW_PICTURE,:PREVIEW_TEXT,:DETAIL_PICTURE,:DETAIL_TEXT,:CREATED_BY,:CREATED_USER_NAME,:TIMESTAMP_X,:MODIFIED_BY,:USER_NAME,:DETAIL_PAGE_URL,:PROPERTY_VALUES)";
		$stmt = $this->_db->prepare($sql);
		$stmt->bindParam(':datetime', $dt, SQLITE3_INTEGER);
		$stmt->bindParam(':event', $event, SQLITE3_TEXT);
		$stmt->bindParam(':ip', $ip, SQLITE3_TEXT);
		$stmt->bindParam(':user', $user, SQLITE3_INTEGER);
		$stmt->bindParam(':PRODUCT_ID', $arProduct["ID"], SQLITE3_INTEGER);
		$stmt->bindParam(':IBLOCK_ID', $arProduct["IBLOCK_ID"], SQLITE3_INTEGER);
		$stmt->bindParam(':CODE', $arProduct["CODE"], SQLITE3_TEXT);
		$stmt->bindParam(':XML_ID', $arProduct["XML_ID"], SQLITE3_TEXT);
		$stmt->bindParam(':NAME', $arProduct["NAME"], SQLITE3_TEXT);
		$stmt->bindParam(':IBLOCK_SECTION_ID', $arProduct["IBLOCK_SECTION"][0], SQLITE3_TEXT);
		$stmt->bindParam(':ACTIVE', $arProduct["ACTIVE"], SQLITE3_TEXT);
		$stmt->bindParam(':SORT', $arProduct["SORT"], SQLITE3_INTEGER);
		$stmt->bindParam(':PREVIEW_PICTURE', $arProduct["PREVIEW_PICTURE"], SQLITE3_INTEGER);
		$stmt->bindParam(':PREVIEW_TEXT', $arProduct["PREVIEW_TEXT"], SQLITE3_TEXT);
		$stmt->bindParam(':DETAIL_PICTURE', $arProduct["DETAIL_PICTURE"], SQLITE3_INTEGER);
		$stmt->bindParam(':DETAIL_TEXT', $arProduct["DETAIL_TEXT"], SQLITE3_TEXT);
		$stmt->bindParam(':CREATED_BY', $arProduct["CREATED_BY"], SQLITE3_INTEGER);
		$stmt->bindParam(':CREATED_USER_NAME', $arProduct["CREATED_USER_NAME"], SQLITE3_TEXT);
		$stmt->bindParam(':TIMESTAMP_X', $arProduct["TIMESTAMP_X"], SQLITE3_TEXT);
		$stmt->bindParam(':MODIFIED_BY', $arProduct["MODIFIED_BY"], SQLITE3_INTEGER);
		$stmt->bindParam(':USER_NAME', $arProduct["USER_NAME"], SQLITE3_TEXT);
		$stmt->bindParam(':DETAIL_PAGE_URL', $arProduct["DETAIL_PAGE_URL"], SQLITE3_TEXT);
		$stmt->bindParam(':PROPERTY_VALUES', serialize($arProduct["PROPERTY_VALUES"]), SQLITE3_TEXT);
		$result = $stmt->execute();
		$stmt->close();
	}
	public function getList($arSort, $arFilter, $arSelect, $arNav) {
		try {
			$select = '*';
			if (!empty($arSelect))
				$select = implode(', ', $arSelect);
			$sort = '';
			if (!empty($arSort)) {
				foreach ($arSort as $order => $by) {
					$sort[] = $order.' '.$by;
				}
				$sort = 'ORDER BY '.implode(', ', $sort);
			}
			$filter = '';
			if (!empty($arFilter)) {
				foreach ($arFilter as $key => $value) {
					if (is_string($value))
						$filter[] = $key."='".$value."'";
					else
						$filter[] = $key.'='.$value;
				}
				$filter = 'WHERE '.implode(' AND ', $filter);
			}
			$limit = (isset($arNav['nPageSize'])?(int)$arNav['nPageSize']:10);
			if (isset($arNav['iNumPage'])) {
				$offset = ((int)$arNav['iNumPage']-1)*$limit;
			}
			else
				$offset = 0;

			$sql = "SELECT $select
					  FROM logs
					  $filter
					  $sort
					  LIMIT $limit OFFSET $offset";
			$res = $this->_db->query($sql);
			if (!is_object($res)) {
				throw new Exception ($this->_db->lastErrorMsg());
			}
			$arLog["ITEMS"] = $this->db2Arr($res);

			$sql = "SELECT COUNT(*)
					  FROM logs
					  $filter";
			$arLog["COUNT"] = $this->_db->query($sql)->fetchArray();

			return $arLog;
		}
		catch(Exception $e) {
			return false;
		}
	}
	public function delete($id) {
		$sql = "DELETE FROM logs WHERE id=$id";
		$this->_db->exec($sql) or die($this->_db->lastErrorMsg());
	}

	protected function db2Arr($data) {
		$arr = array();
		while ($row = $data->fetchArray(SQLITE3_ASSOC)) {
			$arr[$row["id"]] = $row;
		}
		return $arr;
	}
}
?>