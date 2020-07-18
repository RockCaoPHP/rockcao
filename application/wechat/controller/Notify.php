<?php
namespace app\wechat\controller;

class Notify
{
	const TOKEN = 'CSC@2018@LL';
	private $data = null;
	
	public function index()
	{
		$this->checkSign();
		
		echo 'OK';
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
}
