<?php
/**
 * Copyright (C) Loopeer, Inc - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential.
 *
 * User: DengYongBin
 * Date: 15/6/5
 * Time: 下午2:00
 */
use Loopeer\Push;

class JPushTest extends PHPUnit_Framework_TestCase {

    public function push() {
        $jpush = new Loopeer\Lib\Push\JPush('dd1066407b044738b6479275', '6b135be0037a5c1e693c3dfa', false);
        $jpush->pushNotification('0a0e477959a', 'test jpush msg', null, null);
    }
}
