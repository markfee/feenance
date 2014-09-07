<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 07/09/14
 * Time: 06:30
 */
return array(
  'default' => 'sqlite',
  'connections' => array(
    'mmex' => array(
      'driver'   => 'sqlite',
      'database' => '/home/mark/www/.mmex/mmexini.db3',
      'prefix'   => '',
    ),
    'sqlite' => array(
      'driver'   => 'sqlite',
      'database' => ':memory:',
      'prefix'   => ''
    ),
    'mysqltest' => array(
      'driver'    => 'mysql',
      'host'      => 'mysqltest',
      'database'  => 'snodbert',
      'username'  => 'snodbert_user',
      'password'  => 'x7%99uIijsjweiLKJGjk3',
      'charset'   => 'utf8',
      'collation' => 'utf8_unicode_ci',
      'prefix'    => '',
    ),
  )
);