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

Route::get('/',                 function()  {  return View::make('home');  });
Route::get('/import',           function()  {  return View::make('import'); });
Route::get('/standing_orders',  function()  {  return View::make('standing_orders'); });


Route::group(['prefix' =>  'api/v1/'], function()
{
  $NAMESPACE = 'Feenance\\Api\\';
  Route::get('accounts/{id}/transactions',    $NAMESPACE.'TransactionsController@index');
  Route::get('accounts/upload',               $NAMESPACE.'TransactionsController@upload');
  Route::post('accounts/upload',              $NAMESPACE.'TransactionsController@upload');
  Route::resource('accounts',                 $NAMESPACE.'AccountsController');
  Route::resource('transactions',             $NAMESPACE.'TransactionsController');
  Route::resource('payees',                   $NAMESPACE.'PayeesController');
  Route::resource('categories',               $NAMESPACE.'CategoriesController');
  Route::resource('maps',                     $NAMESPACE.'MapsController');

  Route::resource('bank_strings',               $NAMESPACE.'BankStringsController');
  Route::get('bank_strings/{id}/transactions',  $NAMESPACE.'TransactionsController@bank_strings');
  Route::post('bank_strings/{id}/transactions', $NAMESPACE.'TransactionsController@bank_strings_update');

  Route::resource('standing_orders',                        $NAMESPACE.'StandingOrdersController');
  // Generate transactions for a specific standing_order for a period (default == the next month)
  Route::get('standing_orders/{id}/transactions/{period}',  $NAMESPACE.'StandingOrdersController@generate');
  Route::get('standing_orders/{id}/transactions',           $NAMESPACE.'StandingOrdersController@generate');
  // Generate transactions for all standing_orders for a period (default == the next month)
  Route::get('standing_orders/transactions/{period}',       $NAMESPACE.'StandingOrdersController@generate');
  Route::get('standing_orders/transactions',                $NAMESPACE.'StandingOrdersController@generate');

});

Route::group(['prefix' =>  'api/v1/mmex'], function()
{
  $NAMESPACE = 'Feenance\\MMEX\\';

  Route::get('accounts',          $NAMESPACE.'MmexController@accounts');
  Route::get('categories',        $NAMESPACE.'MmexController@categories');
  Route::get('payees',            $NAMESPACE.'MmexController@payees');
  Route::get('standing_orders',   $NAMESPACE.'MmexController@standing_orders');
  Route::get('sub_categories',    $NAMESPACE.'MmexController@sub_categories');
  Route::get('transactions',      $NAMESPACE.'MmexController@transactions');
});