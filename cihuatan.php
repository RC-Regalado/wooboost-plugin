<?php

/*
Plugin Name: Cihuatan Admin
Plugin URI: https://therumshopsv.com/
Description: Plugin de administracion de Ron Cihuatan
Version: 0.2
Author: Ruben Regalado
License: GPL2
*/

defined("ABSPATH") or die("A ver, a ver ¿Que pasó?");

function activate()
{
    global $wpdb;
    $tabla_promociones = $wpdb->prefix . 'promociones';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $tabla_promociones (
            id INT(11) NOT NULL AUTO_INCREMENT,
            nombre_promocion VARCHAR(255) NOT NULL,
            producto_condicion INT(5) NOT NULL,
            condicion INT(3) NOT NULL,
            producto_gratis INT(5) NOT NULL,
            PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function deactivate()
{
}

register_activation_hook(__FILE__, 'activate');
register_deactivation_hook(__FILE__, 'deactivate');

define('ADMIN', plugin_dir_path(__FILE__));

include(ADMIN . '/admin/payment.php');
include(ADMIN . '/admin/product.php');
