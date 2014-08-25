<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('home');
});

Route::group(['prefix' =>  'api/v1/'], function() {
  $NAMESPACE = 'api\\';
  Route::resource('accounts', $NAMESPACE.'AccountsController');
  Route::resource('transactions', $NAMESPACE.'TransactionsController');
});

Route::group(['prefix' =>  'api/v1/mmex'], function() {
  $NAMESPACE = 'MMEX\\';

  Route::get('accounts',          $NAMESPACE.'MmexController@accounts');
  Route::get('categories',        $NAMESPACE.'MmexController@categories');
  Route::get('payees',            $NAMESPACE.'MmexController@payees');
  Route::get('standing_orders',   $NAMESPACE.'MmexController@standing_orders');
  Route::get('sub_categories',    $NAMESPACE.'MmexController@sub_categories');
  Route::get('transactions',      $NAMESPACE.'MmexController@transactions');
});