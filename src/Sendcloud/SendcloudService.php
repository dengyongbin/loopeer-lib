<?php
/**
 * Copyright (C) Loopeer, Inc - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential.
 *
 * User: msy
 * Date: 16-5-5
 * Time: 上午10:44
 */
namespace Loopeer\Lib\Sendcloud;

use Cache;
use Route;
use Session;
use Response;
use Input;
use Redirect;
use Log;

class SendcloudService {

    private $api_user;
    private $api_key;
    private $template_list_api;
    private $template_detail_api;
    private $template_create_api;
    private $template_update_api;
    private $template_delete_api;
    private $template_submit_api;
    private $mail_send_api;
    private $mail_send_template_api;
    private $addresslist_list_api;
    private $addresslist_add_api;
    private $addresslist_delete_api;
    private $addresslist_update_api;
    private $addressmember_list_api;
    private $addressmember_get_api;
    private $addressmember_add_api;
    private $addressmember_update_api;
    private $addressmember_delete_api;

    public function __construct($api_key, $api_user) {
        $this->api_key = $api_key;
        $this->api_user = $api_user;
        $this->template_list_api = 'https://sendcloud.sohu.com/apiv2/template/list?apiUser=' . $this->api_user . '&apiKey=' . $this->api_key;
        $this->template_detail_api = 'https://sendcloud.sohu.com/apiv2/template/get?apiUser=' . $this->api_user . '&apiKey=' . $this->api_key . '&invokeName=';
        $this->template_create_api = 'http://api.sendcloud.net/apiv2/template/add';
        $this->template_update_api = 'http://api.sendcloud.net/apiv2/template/update';
        $this->template_delete_api = 'http://api.sendcloud.net/apiv2/template/delete?apiUser=' . $this->api_user . '&apiKey=' . $this->api_key . '&invokeName=';
        $this->template_submit_api = 'http://api.sendcloud.net/apiv2/template/submit?apiUser=' . $this->api_user . '&apiKey=' . $this->api_key . '&invokeName=';
        $this->mail_send_api = 'http://api.sendcloud.net/apiv2/mail/send';
        $this->mail_send_template_api = 'http://api.sendcloud.net/apiv2/mail/sendtemplate';
        $this->addresslist_list_api = 'http://api.sendcloud.net/apiv2/addresslist/list';
        $this->addresslist_add_api = 'http://api.sendcloud.net/apiv2/addresslist/add';
        $this->addresslist_delete_api = 'http://api.sendcloud.net/apiv2/addresslist/delete';
        $this->addresslist_update_api = 'http://api.sendcloud.net/apiv2/addresslist/update';
        $this->addressmember_list_api = 'http://api.sendcloud.net/apiv2/addressmember/list';
        $this->addressmember_get_api = 'http://api.sendcloud.net/apiv2/addressmember/get';
        $this->addressmember_add_api = 'http://api.sendcloud.net/apiv2/addressmember/add';
        $this->addressmember_update_api = 'http://api.sendcloud.net/apiv2/addressmember/update';
        $this->addressmember_delete_api = 'http://api.sendcloud.net/apiv2/addressmember/delete';
    }

    public function getTemplateDetail($invokeName) {
        $ret = json_decode(file_get_contents($this->template_detail_api . $invokeName));
        $data = $ret->info->data;
        return $data;
    }

    public function getTemplatesList() {
        $ret = json_decode(file_get_contents($this->template_list_api));
        $data = $ret->info->dataList;
        return $data;
    }

    public function addTemplate($data) {
        return $this->post($data, $this->template_create_api);
    }

    public function updateTemplate($data) {
        return $this->post($data, $this->template_update_api);
    }

    public function deleteTemplate($invokeName) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->template_delete_api . $invokeName);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $ret = curl_exec($ch);
        Log::info($ret);
        curl_close($ch);
        return $ret;
    }

    public function submitTemplate($invokeName) {
        $template = $this->getTemplateDetail($invokeName);
        if($template->templateStat == 0) {
            //撤销审核
            $ret = json_decode(file_get_contents($this->template_submit_api . $invokeName . '&cancel=1'));
        } else {
            //提交审核
            $ret = json_decode(file_get_contents($this->template_submit_api . $invokeName . '&cancel=0'));
        }
        return $ret;
    }

    public function send($data) {
        return $this->post($data, $this->mail_send_api);
    }

    public function sendTemplate($data) {
        return $this->post($data, $this->mail_send_template_api);
    }

    public function addresslist($data, $method) {
        switch($method) {
            case 'list' :
                $request_url = $this->addresslist_list_api;
                break;
            case 'add' :
                $request_url = $this->addresslist_add_api;
                break;
            case 'delete' :
                $request_url = $this->addresslist_delete_api;
                break;
            case 'update' :
                $request_url = $this->$addresslist_update_api;
                break;
        }
        return $this->post($data, $request_url);
    }

    public function addressmember($data, $method) {
        switch($method) {
            case 'list' :
                $request_url = $this->addressmember_list_api;
                break;
            case 'add' :
                $request_url = $this->addressmember_add_api;
                break;
            case 'delete' :
                $request_url = $this->addressmember_delete_api;
                break;
            case 'update' :
                $request_url = $this->addressmember_update_api;
                break;
            case 'get' :
                $request_url = $this->addressmember_get_api;
                break;
        }
        return $this->post($data, $request_url);
    }

    private function post($data, $url) {
        $data['apiKey'] = $this->api_key;
        $data['apiUser'] = $this->api_user;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Requested-With:XMLHttpRequest'));
        curl_setopt($ch, CURLOPT_HEADER, 0);//是否显示头信息
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//是否自动显示返回的信息
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $ret = curl_exec($ch);
        Log::info($ret);
        curl_close($ch);
        return $ret;
    }

}