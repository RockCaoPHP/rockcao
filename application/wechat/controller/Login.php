<?php
namespace app\wechat\controller;

class Login
{
	public function index()
	{
		return view();
	}
	
	public function check()
	{
		echo $_SERVER['HTTP_USER_AGENT'];
		echo '<hr />';
		dump($_SERVER);
		dump($_REQUEST);
		dump($_GET);
		dump($_POST);
		halt($_COOKIE);
	}
}
