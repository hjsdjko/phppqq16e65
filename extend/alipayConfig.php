<?php
$config_pay = array (	
		//应用ID,您的APPID。
		'app_id' => "",

		//商户私钥
        'merchant_private_key' => "",
		
		//异步通知地址
		'notify_url' => "http://localhost:8080/phppqq16e65/",
		
		//同步跳转
		'return_url' => "http://localhost:8080/phppqq16e65/admin/dist/index.html#/",

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关
		'gatewayUrl' => "https://openapi-sandbox.dl.alipaydev.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
        'alipay_public_key' => "",

        //日志路径
        'log_path' => "",
);