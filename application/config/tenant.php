<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Master Database Configuration
|--------------------------------------------------------------------------
| This is the master database that stores tenant information
*/

$config['master_db'] = array(
    'dsn'      => '',
    'hostname' => 'tasks.inventorysystem-mysqlinventory-ydsxph',
    'username' => 'mysql',
    'password' => 'Zakaria1304@',
    'database' => 'stock_master',
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt'  => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
);

/*
|--------------------------------------------------------------------------
| Tenant Database Template Configuration
|--------------------------------------------------------------------------
| Template for tenant database connections (database name will be dynamic)
*/

$config['tenant_db_template'] = array(
    'dsn'      => '',
    'hostname' => 'tasks.inventorysystem-mysqlinventory-ydsxph',
    'username' => 'mysql',
    'password' => 'Zakaria1304@',
    'database' => '', // Will be set dynamically
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt'  => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
);
