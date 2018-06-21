<?php
// Application middleware

use \LeanCloud\Client;
use \LeanCloud\Storage\CookieStorage;
use \LeanCloud\Engine\SlimEngine;

Client::initialize(
    getenv("LEANCLOUD_APP_ID"),
    getenv("LEANCLOUD_APP_KEY"),
    getenv("LEANCLOUD_APP_MASTER_KEY")
);

Client::useMasterKey(false);
Client::setStorage(new CookieStorage());
SlimEngine::enableHttpsRedirect();
$app->add(new SlimEngine());
