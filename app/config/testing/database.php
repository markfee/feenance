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
    'sqlite' => array(
      'driver'   => 'sqlite',
      'database' => ':memory:',
      'prefix'   => ''
    ),
  )
);