<?php
/**
 * Copyright (C) Loopeer, Inc - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential.
 *
 * User: DengYongBin
 * Date: 15/6/5
 * Time: 上午11:58
 */
namespace Loopeer\Lib\Push;

class JPush {

    protected $appKey;
    protected $masterSecret;
    //如果目标平台为 iOS 平台 需要在 options 中通过 apns_production 字段来制定推送环境。True 表示推送生产环境，False 表示要推送开发环境；
    protected $apnsProduction = false;
    public function __construct($appKey, $masterSecret, $apnsProduction) {
        $this->appKey = $appKey;
        $this->masterSecret = $masterSecret;
        $this->apnsProduction = $apnsProduction;
    }

    public function setAppKey($appKey) {
        $this->appKey = $appKey;
    }

    public function setMasterSecret($masterSecret) {
        $this->masterSecret = $masterSecret;
    }

    public function setApnsProduction($apnsProduction) {
        $this->apnsProduction = $apnsProduction;
    }

    /**
     * @param $app_user_id 注册设备标识
     * @param $content 内容
     * @param $title android标题,默认应用名称
     * @param $extras array(Key/Value)
     */
    public function pushNotification($app_user_id, $content, $title = null, $extras = null) {
        $client = new JPushClient($this->appKey, $this->masterSecret);
        $result = $client->push()
            ->setPlatform(M\platform('android', 'ios'))
            ->setAudience(M\Audience(M\registration_id(array($app_user_id))))
            ->setNotification(M\notification($content,
                M\android($content, $title, 0, $extras),
                M\ios($content, 'happy', '+1', true, $extras)
            ))
            ->setOptions(M\options(null , null, null , $this->apnsProduction, null))
            ->send();
        return $result->json;
    }
}