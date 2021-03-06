<?php

return array(

	'fetch' => PDO::FETCH_CLASS,
	'default' => 'mysql',

	'connections' => array(
        'mmex' => array(
            'driver'   => 'sqlite',
            'database' => '/home/mark/www/.mmex/finances.mmb',
            'prefix'   => '',
        ),

        'mysql' => array(
            'driver'    => 'mysql',
            'host'      => 'mysqltest',
            'database'  => 'feenance',
            'username'  => 'feenance_s',
            'password'  => 'titduWigsojnefateej',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ),
        'mysql_testing' => array(
            'driver'    => 'mysql',
            'host'      => 'mysqltest',
            'engine'    => 'MEMORY',
            'database'  => 'feenance_testing',
            'username'  => 'feenance_s',
            'password'  => 'titduWigsojnefateej',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ),

	),

	'migrations' => 'migrations',

	/*
	|--------------------------------------------------------------------------
	| Redis Databases
	|--------------------------------------------------------------------------
	|
	| Redis is an open source, fast, and advanced key-value store that also
	| provides a richer set of commands than a typical key-value systems
	| such as APC or Memcached. Laravel makes it easy to dig right in.
	|
	*/

	'redis' => array(

		'cluster' => false,

		'default' => array(
			'host'     => '127.0.0.1',
			'port'     => 6379,
			'database' => 0,
		),

	),

);
