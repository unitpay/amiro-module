<?php

$params = $_GET['params'];
$_GET['order_id'] = $params['account'];

require_once $GLOBALS['DEFAULT_INCLUDES_PATH'] . 'eshop_final.php';