<?php
namespace app\wechat\controller;

class Notify
{
	const TOKEN = 'CSC@2018@LL';
	
	public function index()
	{
		$this->checkSign();
		
		echo 'OK';
	}
	
	private function checkSign()
	{
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
