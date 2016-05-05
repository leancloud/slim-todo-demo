<?php
// Application middleware

use \LeanCloud\LeanClient;
use \LeanCloud\Storage\CookieStorage;
use \LeanCloud\Engine\SlimEngine;

LeanClient::initialize(
    getenv("LEANCLOUD_APP_ID"),
    getenv("LEANCLOUD_APP_KEY"),
    getenv("LEANCLOUD_APP_MASTER_KEY")
);

LeanClient::useMasterKey(false);
LeanClient::setStorage(new CookieStorage());
SlimEngine::enableHttpsRedirect();
$app->add(new SlimEngine());
