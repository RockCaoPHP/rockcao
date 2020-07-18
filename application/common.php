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
