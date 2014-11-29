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

Route::pattern('id',    '[0-9]+');
Route::pattern('year',  '[0-9]{4}');
Route::pattern('month', '0[1-9]|1[012]|[1-9]');
Route::pattern('endYear',  '[0-9]{4}');
Route::pattern('endMonth', '0[1-9]|1[012]|[1-9]');

use \Markfee\MyCarbon;
use Feenance\CategoryReportController;

Route::get('/login', function()  {
  return View::make('login');
});

Route::post('/login', 'Feenance\Controllers\AdminController@login');



Route::group(['before' => ''], function() {
  Route::get('/',                   function()  {  return View::make('transactions');  });
  Route::get('/import',             function()  {  return View::make('import'); });

  Route::get('/reports/categories/{year?}', function($year=null) {
    return CategoryReportController::category_report($year);
  });
  Route::get('/reports/categories/{year}/{month}', function($year, $month) {
    return CategoryReportController::category_report($year, $month);
  });
  Route::get('/reports/categories/{year}-{month}', function($year, $month) {
    return CategoryReportController::category_report($year, $month);
  });
  Route::get('/reports/categories/{year}-{month}/{endYear}-{endMonth}', function($year, $month, $endYear, $endMonth)  {
    return CategoryReportController::category_report($year, $month, $endYear, $endMonth);
  });
  Route::get('/reports/categories/{year}/{month}/{endYear}/{endMonth?}', function($year, $month, $endYear, $endMonth=12)  {
    return CategoryReportController::category_report($year, $month, $endYear, $endMonth);
  });

  Route::get('/standing_orders',  function()  {  return View::make('standing_orders'); });



  Route::group(['prefix' =>  'api/v1/'], function()
  {
    $NAMESPACE = 'Feenance\\Api\\';

    Route::post('transfers',             $NAMESPACE.'TransfersController@joinTwoTransactionsAsTransfer');
    Route::get('transfers',              $NAMESPACE.'TransfersController@index');
    Route::get('transfers/potential',    $NAMESPACE.'TransfersController@getPotentialTransfers');
    Route::get('transfers/{id}',         $NAMESPACE.'TransfersController@show');
    Route::delete('transfers/{id}',      $NAMESPACE.'TransfersController@destroy');

    Route::get('accounts/{id}/transactions',                  $NAMESPACE.'TransactionsController@index');
    Route::get('accounts/{id}/transactions/reconciled',       $NAMESPACE.'TransactionsController@reconciled');
    Route::get('accounts/{id}/transactions/unreconciled',     $NAMESPACE.'TransactionsController@unreconciled');
    Route::delete('accounts/{id}/transactions/unreconciled',  $NAMESPACE.'TransactionsController@deleteUnreconciled');
    Route::get('accounts/{id}/transactions/unreconciled/count',     $NAMESPACE.'TransactionsController@unreconciledCount');
    Route::post('accounts/{id}/transactions/reconcile',  $NAMESPACE.'TransactionsController@reconcileAll');

    Route::get('accounts/upload',               $NAMESPACE.'TransactionsController@upload');
    Route::post('accounts/upload',              $NAMESPACE.'TransactionsController@upload');
    Route::resource('accounts',                 $NAMESPACE.'AccountsController');

    Route::get('transactions/totals',                   $NAMESPACE.'TransactionReportsController@totals_by_year');
    Route::get('transactions/totals/{year}',            $NAMESPACE.'TransactionReportsController@totals_by_month');
    Route::get('transactions/totals/categories',        $NAMESPACE.'TransactionReportsController@categories_by_year');
    Route::get('transactions/totals/categories/{year}/{month?}', $NAMESPACE.'TransactionReportsController@categories_by_month');
    Route::get('transactions/totals/categories/{year}-{month}', $NAMESPACE.'TransactionReportsController@categories_by_month');
    Route::get('transactions/totals/categories/{year}-{month}/{endYear}-{endMonth}', $NAMESPACE.'TransactionReportsController@categories_by_month');

  /*
    Route::get('transactions/totals',           $NAMESPACE.'TransactionsController@total');
    Route::get('transactions/totals/month',     $NAMESPACE.'TransactionsController@month_totals');
    Route::get('transactions/totals/month',     $NAMESPACE.'TransactionsController@month_totals');
  */

    Route::get('transactions/reconciled',         $NAMESPACE.'TransactionsController@reconciled');
    Route::get('transactions/unreconciled',       $NAMESPACE.'TransactionsController@unreconciled');
    Route::get('transactions/unreconciled/count', $NAMESPACE.'TransactionsController@unreconciledCount');


    Route::resource('transactions',             $NAMESPACE.'TransactionsController');
    Route::resource('payees',                   $NAMESPACE.'PayeesController');
    Route::resource('categories',               $NAMESPACE.'CategoriesController');
    Route::resource('maps',                     $NAMESPACE.'MapsController');

    Route::resource('bank_strings',               $NAMESPACE.'BankStringsController');
    Route::get('bank_strings/{id}/transactions',  $NAMESPACE.'TransactionsController@bank_strings');
    Route::post('bank_strings/{id}/transactions', $NAMESPACE.'TransactionsController@bank_strings_update');

    // TODO CHANGE THESE TO POSTS
    Route::get('standing_orders/{id}/generate/{endDate?}',    $NAMESPACE.'StandingOrdersController@generate');
    Route::get('standing_orders/generate/{endDate?}',         $NAMESPACE.'StandingOrdersController@generateAll');

    Route::put('standing_orders/{id}/increment',              $NAMESPACE.'StandingOrdersController@increment');

    Route::resource('standing_orders',                        $NAMESPACE.'StandingOrdersController');
    Route::resource('transaction_statuses',                   $NAMESPACE.'TransactionStatusesController');

  /*
    // Generate transactions for a specific standing_order for a period (default == the next month)
    Route::get('standing_orders/{id}/transactions/{period}',  $NAMESPACE.'StandingOrdersController@generate');
    Route::get('standing_orders/{id}/transactions',           $NAMESPACE.'StandingOrdersController@generate');
    // Generate transactions for all standing_orders for a period (default == the next month)
    Route::get('standing_orders/transactions/{period}',       $NAMESPACE.'StandingOrdersController@generate');
    Route::get('standing_orders/transactions',                $NAMESPACE.'StandingOrdersController@generate');
  */
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
});