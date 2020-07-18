<?php
namespace app\wechat\controller;

class Notify
{
	const TOKEN = 'LL16CSC19CYJ06';
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
		
		$sign = [self::TOKEN, $timestamp, $nonce];
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
/*
<xml><ToUserName><![CDATA[gh_c9a64df72343]]></ToUserName>
<FromUserName><![CDATA[ou9541J6yhERCprBh7xZlHmfubak]]></FromUserName>
<CreateTime>1595097292</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[subscribe]]></Event>
<EventKey><![CDATA[]]></EventKey>
</xml>

<xml><ToUserName><![CDATA[gh_c9a64df72343]]></ToUserName>
<FromUserName><![CDATA[ou9541J6yhERCprBh7xZlHmfubak]]></FromUserName>
<CreateTime>1595097026</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[unsubscribe]]></Event>
<EventKey><![CDATA[]]></EventKey>
</xml>
		
<xml><ToUserName><![CDATA[gh_c9a64df72343]]></ToUserName>
<FromUserName><![CDATA[ou9541J6yhERCprBh7xZlHmfubak]]></FromUserName>
<CreateTime>1595096553</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[你好]]></Content>
<MsgId>22836290069516180</MsgId>
</xml>
*/
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
}
