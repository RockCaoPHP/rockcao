<?php
namespace phpgo;
class Str
{
	public static function random($length = 6)
	{
		$arr = [
			'0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
			'A', 'B', 'C', 'D', 'E', 'F', 'G',
			'H', 'I', 'J', 'K', 'L', 'M', 'N',
			'O', 'P', 'Q', 'R', 'S', 'T',
			'U', 'V', 'W', 'X', 'Y', 'Z',
			'a', 'b', 'c', 'd', 'e', 'f', 'g',
			'h', 'i', 'j', 'k', 'l', 'm', 'n',
			'o', 'p', 'q', 'r', 's', 't',
			'u', 'v', 'w', 'x', 'y', 'z',
			'-', '!', '@', '*', '_', '$',
		];
		$max = count($arr) - 1;
		$str = '';
		for ($i = 0; $i < $length; $i++) {
			$index = mt_rand(0, $max);
			$str .= $arr[$index];
		}
		return $str;
	}
	
	public static function createGuid()
	{
		if (function_exists('com_create_guid')) {
			return com_create_guid();
		}
		$id = strtoupper(md5(uniqid(mt_rand(), true)));
		$sp = chr(45);
		$guid = chr(123)
			. substr($id, 0, 8) . $sp
			. substr($id, 8, 4) . $sp
			. substr($id, 12, 4) . $sp
			. substr($id, 16, 4) . $sp
			. substr($id, 20, 12)
			. chr(125);
		return $guid;
	}
	
	public static function guid()
	{
		$arr = [
			'guid' => self::createGuid(),
			'microtime' => microtime(true),
			'mt_rand' => mt_rand(),
			'uniqid' => uniqid(mt_rand(), true),
			'random' => self::random(6),
		];
		$guid = '';
		foreach ($arr as $k => $v) {
			$guid .= "[{$k}={$v}]";
		}
		return strtoupper(md5($guid));
	}
	
	public static function strip($val)
	{
		$sp = chr(32);
		$cnsp = urldecode('%E3%80%80');
		$val = strval($val);
		$val = strip_tags($val);
		$val = htmlspecialchars($val);
		$val = trim($val);
		$regex = '/^(' . $sp . '|\s|' . $cnsp . ')*|(' . $sp . '|\s|' . $cnsp . ')*$/';
		return preg_replace($regex, '', $val);
	}
}
