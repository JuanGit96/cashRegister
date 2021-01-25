<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::group([
    'prefix' => 'v1', 
    #'as' => 'api.', 
    #'namespace' => 'Api\V1\Admin', 
    #'middleware' => ['auth:api']
    'middleware' => ['jsonify']
], function () {
    /**
     * 
     */
    Route::post('startCashRegisterService', 'Api\TransactionController@startCashRegisterService');

    /**
     * 
     */
    Route::post('endCashRegisterService', 'Api\TransactionController@endCashRegisterService');


    /**
     * 
     */
    Route::post('CashRegisterStatus', 'Api\TransactionController@CashRegisterStatus');


    /**
     * 
     */
    Route::post('makePayment', 'Api\TransactionController@makePayment');


    /**
     * 
     */
    Route::post('cashRegisterEventLog', 'Api\TransactionController@cashRegisterEventLog');


    /**
     * 
     */
    Route::post('CashRegisterStatusByDate', 'Api\TransactionController@CashRegisterStatusByDate');


    /**
     * 
     */
    Route::post('showTransaction/{transaction}', 'Api\TransactionController@showTransaction');    
});


