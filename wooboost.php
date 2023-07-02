<?php

/*
Plugin Name: Wooboost
Plugin URI: https://github.com/RC-Regalado/wooboost-plugin
GitHub Plugin URI: https://github.com/RC-Regalado/wooboost-plugin
Description: Plugin para mejora de administración
Version: 0.3
Author: Ruben Regalado
License: GPL2
*/

defined("ABSPATH") or die("A ver, a ver ¿Que pasó?");

function activate()
{

}

function deactivate()
{
}

register_activation_hook(__FILE__, 'activate');
register_deactivation_hook(__FILE__, 'deactivate');

define('ADMIN', plugin_dir_path(__FILE__));

include(ADMIN . '/admin/payment.php');
include(ADMIN . '/admin/product.php');
include(ADMIN . '/admin/billing.php');
