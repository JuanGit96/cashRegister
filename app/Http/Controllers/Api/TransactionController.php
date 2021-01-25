<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DateRequest;
use App\Http\Requests\startServiceRequest;
use App\Http\Requests\TransactionRequest;
use App\Management\Repositories\DenominationRepository;
use App\Management\Repositories\InflowCash;
use App\Management\Repositories\OutflowCash;
use App\Management\Repositories\TransactionRepository;
use App\Traits\ApiResponser;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class TransactionController extends Controller
{
    use ApiResponser;

    public function __construct()
    {
        
    }

    /**
     * Cargar base a la caja
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Transaction  $transaction
     * @return void
     */    
    public function startCashRegisterService(startServiceRequest $request)
    {
        $transactionRepository = new TransactionRepository();
        
        if($transactionRepository->cashRegisterIsOpen()) 
            return $this->errorResponse('',505);

        $register = $transactionRepository->openCashRegister($request->all());

        return $this->showOne($register, "Caja abierta correctamente");
    }


    /**
     * Vaciar caja
     * @param
     * @return void
     */    
    public function endCashRegisterService()
    {
        $transactionRepository = new TransactionRepository();
        
        if(!$transactionRepository->cashRegisterIsOpen()) 
            return $this->errorResponse('',505);

        $register = $transactionRepository->closeCashRegister();

        return $this->showOne($register, "Caja cerrada correctamente");    
    }


    /**
     * Estado de caja
     * @param
     * @return void
     */    
    public function CashRegisterStatus()
    {
        $transaction = new TransactionRepository();

        #validar que la caja esté llena
        if(!$transaction->hasRegisters()) 
            return $this->errorResponse('',505);

        #procesar estado actual de caja
        $response = $transaction->getCurrentCashRegisterStatus();

        return $this->showString($response, "estado de la caja a ".Date::now());
    }


    /**
     * Realizar un pago
     * @param
     * @return void
     */    
    public function makePayment(TransactionRequest $request)
    {
        $transactionRepository = new TransactionRepository();

        #validar que la caja esté llena
        if(!$transactionRepository->cashRegisterIsOpen()) 
            return $this->errorResponse('',505);

        $payment_is_verify = $transactionRepository->paymentIsVerify($request->all());

        if(!$payment_is_verify["response"])
            return $this->errorsResponse($payment_is_verify["errors"][0],505);

        $data_outflow = $payment_is_verify["data_outflow"];

        $register = $transactionRepository->makePayment($request->all(), $data_outflow);

        return $this->showOne($register, "Pago realizado correctamente");
    }


    /**
     * Ver registro de logs de eventos
     * @param
     * @return void
     */    
    public function cashRegisterEventLog()
    {
        $transaction = new TransactionRepository();

        #validar que la caja esté llena
        if(!$transaction->hasRegisters()) 
            return $this->errorResponse('',505);

        #procesar estado actual de caja
        $response = $transaction->getEventLogs();

        return $this->showAll($response);
    }


    /**
     * Saber estado de la caja según fecha y hora determinada
     * @param
     * @return void
     */    
    public function CashRegisterStatusByDate(DateRequest $request)
    {
        $transaction = new TransactionRepository();

        #validar que la caja esté llena
        if(!$transaction->hasRegisters()) 
            return $this->errorResponse('',505);

        #procesar estado actual de caja
        $response = $transaction->getCashRegisterStatusByDate($request->all());

        if($response == null)
            return $this->showMessage("No se encontraron registros");

        return $this->showString($response->current_cash_status, "Estado de la caja a ".$response->created_at);
    }


    /**
     * Saber estado de la caja según fecha y hora determinada
     * @param
     * @return void
     */    
    public function showTransaction(Transaction $transaction)
    {
        $transactionRepository = new TransactionRepository();

        #validar que la caja esté llena
        if(!$transactionRepository->cashRegisterIsOpen()) 
            return $this->errorResponse('',505);

        return $this->showOne($transaction);
    }
    
    
}
