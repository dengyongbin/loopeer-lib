<?php
/**
 * Copyright (C) Loopeer, Inc - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential.
 *
 * User: DengYongBin
 * Date: 15/11/11
 * Time: 下午3:08
 */
namespace Loopeer\Lib\Push;

use PushSDK;

/**
 * 百度推送封装类
 * Class BPush
 * @package Loopeer\Lib\Push
 */
class BPush {

    protected $apiKey;
    protected $secretKey;
    protected $sdk;
    protected $opts;

    /**
     * @param $apiKey
     * @param $secretKey
     * @param int $msgType 消息类型 [0-透传、1-通知](ios不支持透传)
     * @param int $deployStatus ios推送 [1-开发、2-生产]
     */
    public function __construct($apiKey, $secretKey, $msgType = 1, $deployStatus = 1) {
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;
        $this->sdk = new PushSDK($this->apiKey, $this->secretKey);
        $this->opts = array (
            'msg_type' => $msgType,
            'deploy_status' => $deployStatus,
        );
    }

    /**
     * 推送单条消息给个人
     * @param $channelId
     * @param $description
     * @param $custom_content
     * @return string
     */
    public function pushSingleMessage($channelId, $description, $custom_content) {
        $msg = self::buildMessage($description, $custom_content);
        // 向目标设备发送一条消息
        $rs = $this->sdk->pushMsgToSingleDevice($channelId, $msg, $this->opts);
        return $this->printResult($rs, $this->sdk);
    }

    /**
     * 批量推送消息
     * @param $accountIds
     * @param $description
     * @param $custom_content
     * @return string
     */
    public function pushBatchMessage($accountIds, $description, $custom_content){
        // 向一批设备发送一条消息
        $message = self::buildMessage($description, $custom_content);
        $rs = $this->sdk->pushBatchUniMsg($accountIds, $message, $this->opts);
        return $this->printResult($rs);
    }

    /**
     * 向所有设备推送消息
     * @param $description
     * @param $custom_content
     * @return string
     */
    public function pushAllMessage($description, $custom_content) {
        $message = self::buildMessage($description, $custom_content);
        // 向一批设备发送一条消息
        $rs = $this->sdk->pushMsgToAll($message, $this->opts);
        return $this->printResult($rs);
    }

    /**
     * 消息内容
     * @param $description
     * @param $custom_content
     * @return array
     */
    private function buildMessage($description, $custom_content = array()) {
        $message = array(
            'description' => $description,
            'created_at' => time(),
            'custom_content' => $custom_content
        );
        return $message;
    }

    /**
     * 打印推送结果
     * @param $rs
     * @return string
     */
    private function printResult($rs) {
        // 判断返回值,当发送失败时, $rs的结果为false, 可以通过getError来获得错误信息.
        $result = 'push success';
        if($rs === false){
            $result = 'push error code = ' . $this->sdk->getLastErrorCode() . ' error_msg = ' . $this->sdk->getLastErrorMsg();
        }
        return $result;
    }
}