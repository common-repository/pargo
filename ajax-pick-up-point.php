<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';
include_once '../woocommerce/woocommerce.php';

WC()->session = new WC_Session_Handler();
WC()->session->init();

if (!empty($_POST['pargoshipping'])) {
    $cleaned = sanitize_post($_POST);
    WC()->session->set('pargo_shipping_address', $cleaned['pargoshipping']);
}
