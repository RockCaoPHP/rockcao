<?php
function xml2arr($xml)
{
	$entity = libxml_disable_entity_loader(true);
	$data = (array)simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
	libxml_disable_entity_loader($entity);
	return json_decode(json_encode($data), true);
}

function data($key, $data, $default = null)
{
	if (is_string($data)) {
		$data = strtoupper($data);
	} else {
		return isset($data[$key]) ? $data[$key] : $default;
	}
	switch ($data) {
		case 'GET': $data = $_GET; break;
		case 'POST': $data = $_POST; break;
		case 'REQUEST': $data = $_REQUEST; break;
		case 'SERVER': $data = $_SERVER; break;
		case 'COOKIE': $data = $_COOKIE; break;
		case 'SESSION': $data = $_SESSION; break;
		case 'FILES': $data = $_FILES; break;
		case 'ENV': $data = $_ENV; break;
		case 'GB': $data = $GLOBALS; break;
		default: return $default; break;
	}
	return isset($data[$key]) ? $data[$key] : $default;
}

function strip($value)
{
	return \phpgo\Str::strip($value);
}

function curl_get($url, $https = true)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	if ($https) {
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	}
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}


