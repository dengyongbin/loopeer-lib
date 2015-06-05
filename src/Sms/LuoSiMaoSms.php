<?php
/**
 * Copyright (C) Loopeer, Inc - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential.
 *
 * User: DengYongBin
 * Date: 15/6/5
 * Time: 上午11:52
 */
namespace Loopeer\Sms;

class LuoSiMaoSms {

    protected $apiKey;

    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
    }
    public function setApiKey($apiKey) {
        $this->apiKey = $apiKey;
    }
    /**
     * @param $phone
     * @param $msg 验证码：%s，请于30分钟内输入使用 【绿葡科技】
     * @param $captcha
     * @return mixed
     */
    public function sendSms($phone, $msg, $captcha) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://sms-api.luosimao.com/v1/send.json");
        curl_setopt($ch, CURLOPT_ENCODING ,"UTF-8");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->apiKey);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
                'mobile' => $phone,
                'message' => sprintf($msg, $captcha)
            )
        );
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }
}