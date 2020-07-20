<?php
namespace app\wechat\controller;

class Notify
{
	private $data = null;
	
	public function index()
	{
		$this->checkSign();
		
		$xml = $this->data['input'];
		$xml = $xml ? xml2arr($xml) : null;
		
		if (data('MsgType', $xml) == 'text' && data('MsgId', $xml)) {
			$this->text($xml);
		}
		
		if (data('MsgType', $xml) == 'event' && data('Event', $xml) == 'subscribe') {
			$this->text($xml);
		}
		
		if (data('MsgType', $xml) == 'event' && data('Event', $xml) == 'unsubscribe') {
			$this->text($xml);
		}
		
		exit('success');
	}
	
	private function checkSign()
	{
		$request = request();
		$this->data = [
			'get' => $request->get(),
			'post' => $request->post(),
			'request' => $request->request(),
			'input' => $request->getInput(),
		];
		
		trace($this->data, 'wechat_notify_data');
		
		$signature = input('get.signature');
		$timestamp = input('get.timestamp');
		$nonce = input('get.nonce');
		$echostr = input('get.echostr');
		$token = config('wechat.token');
		
		$sign = [$token, $timestamp, $nonce];
		sort($sign, SORT_STRING);
		$sign = implode($sign);
		$sign = sha1($sign);
		
		($sign === $signature) or exit('签名错误');
		
		if ($echostr) {
			exit($echostr);
		}
		
		return true;
	}
	
	private function text($arr)
	{
		$time = time();
		$content = data('Content', $arr);
		if (data('Event', $arr) == 'subscribe') {
			$content = '感谢关注!';
		} elseif (data('Event', $arr) == 'unsubscribe') {
			$content = '欢迎再来!';
		}
		$content = $content ? $content : '您好!';
		
		$xml = '<xml>';
		$xml .= "<ToUserName><![CDATA[{$arr['FromUserName']}]]></ToUserName>";
		$xml .= "<FromUserName><![CDATA[{$arr['ToUserName']}]]></FromUserName>";
		$xml .= "<CreateTime>{$time}</CreateTime>";
		$xml .= '<MsgType><![CDATA[text]]></MsgType>';
		$xml .= "<Content><![CDATA[{$content}]]></Content>";
		$xml .= '</xml>';

		echo $xml;
		exit;
	}
	
	public function openid()
	{
		$access_token = $this->getAccessToken();
		halt($access_token);
	}
	
	private function getAccessToken()
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
		$result = curl_get($url);
		$result = json_decode($result, true);
		$result['end_time'] = $now + $result['expires_in'],
		$result['end_date_time'] = date('Y-m-d H:i:s', $result['end_time']);
		
		db('config')->where('id', $info['id'])->update([
			'cvalue' => json_encode($result),
		]);
		
		return $result['access_token'];
	}
}
