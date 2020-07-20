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

function get_wechat_access_token()
{
	$now = time();
	$info = db('config')
		->field('id,cvalue')
		->where('ckey', 'wechat_access_token')
		->find();
	$json = $info['cvalue'] ? json_decode($info['cvalue'], true) : null;
	
	$end_time = data('end_time', $json, 0);
	if ($end_time - $now > 200) {
		return $json['access_token'];
	}
	
	$config = config('wechat.');
	$url = "{$config['api_host']}/cgi-bin/token?grant_type=client_credential&appid={$config['app_id']}&secret={$config['app_secret']}";
	
	$json = curl_get($url);
	$json = json_decode($json, true);
	$json['end_time'] = $now + $json['expires_in'];
	
	db('config')->where('id', '=', $info['id'])->update([
		'cvalue' => json_encode($json),
	]);
	
	return $json['access_token'];
}


