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
namespace Loopeer\Lib\Sms;

/**
 * 短信平台发送短信和语音类
 * Class LuoSiMaoSms
 * @package Loopeer\Sms
 */
class LuoSiMaoSms {

    // 短信或语音key
    protected $apiKey;

    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
    }
    public function setApiKey($apiKey) {
        $this->apiKey = $apiKey;
    }

    /**
     * 发送短信
     * @param $phone
     * @param $message
     * @return mixed
     */
    public function sendSms($phone, $message) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://sms-api.luosimao.com/v1/send.json");
        curl_setopt($ch, CURLOPT_ENCODING ,"UTF-8");
        $this->setOpt($ch);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
                'mobile' => $phone,
                'message' => $message
            )
        );
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }

    /**
     * 发送语音
     * @param $phone
     * @param $code
     * @return mixed
     */
    public function sendVerify($phone, $code) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://voice-api.luosimao.com/v1/verify.json");
        $this->setOpt($ch);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
                'mobile' => $phone,
                'code' => $code
            )
        );
        $res = curl_exec( $ch );
        curl_close( $ch );
        return $res;
    }

    /**
     * 设置curl参数
     * @param $ch
     */
    private function setOpt($ch) {
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->apiKey);
        curl_setopt($ch, CURLOPT_POST, TRUE);
    }
}