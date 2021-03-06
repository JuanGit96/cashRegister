<?php 

namespace App\Management\Repositories;

use App\Traits\FileManagement;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

class TransactionRepository
{
    use FileManagement;

    const STATUS = [
        "closed" => 0,
        "open" => 1,
        "transaction" => 2,
    ];

    const DENOMINATIONS = [
        "fifty_cop" => 50,
        "hundred_cop" => 100,
        "two_hundred_cop" => 200,
        "five_hundred_cop" => 500,
        "one_thousand_cop" => 1000,
        "two_thousand_cop" => 2000,
        "five_thousand_cop" => 5000,
        "ten_thousand_cop" => 10000,
        "twenty_thousand_cop" => 20000,
        "fifty_thousand_cop" => 50000,
        "one_hundred_thousand_cop" => 100000,
    ];
    
    public function __construct()
    {}

    /**
     * Valida si la caja registradora está abieta
     *
     * @return boolean
     */
    public function cashRegisterIsOpen(): bool
    {
        $transaction = Transaction::latest()->first();
        
        if($transaction == null)
            return false;

        return $transaction->isOpen();
    }  
    
    public function hasRegisters()
    {
        $transaction = Transaction::first();

        if($transaction == null)
            return false;

        return true;
    }

    /**
     * Proceso de apertura de caja registradora
     *
     * @param array $data
     * @return Transaction
     */
    public function openCashRegister(array $data): Transaction
    {
        $transaction_data["current_status"] = self::STATUS["open"];

        $transaction_data["cash_inflows"] =  $this->processData($data,'inflow');

        $transaction_data["cash_outflows"] = $this->processData([],'outflow');

        $transaction_data["current_cash_status"] = $this->processData($data, 'status');

        return Transaction::create($transaction_data);
    }

    /**
     * Construye Json con la data enviada
     *
     * @param array $data
     * @param string $type
     * @return string
     */
    public function processData(array $data, string $type=''): string
    {
        $data_response = [
            "total_".$type => $this->sumDataDenominations($data)
        ];

        foreach(self::DENOMINATIONS as $denomination => $value)
        {
            if(isset($data[$denomination]))
                $data_response[$denomination] = (int)$data[$denomination];
            else
                $data_response[$denomination] = 0;
        }

        return json_encode($data_response);
    }

    /**
     * Suma todas las denominaciones en la data y entrega un valor
     *
     * @param array $data
     * @return integer
     */
    public function sumDataDenominations(array $data): int
    {
        $sum = 0;

        if($data == [])
            return $sum;

        foreach($data as $denomination => $quantity)
        {
            if(!isset(self::DENOMINATIONS[$denomination]))
                continue;

            $value_denomination = self::DENOMINATIONS[$denomination];

            $total_value_denomination = $value_denomination * $quantity;

            $sum = $sum + $total_value_denomination;
        }

        return (int)$sum;
    }

    public function closeCashRegister()
    {
        $transaction = Transaction::latest()->first();

        $current_cash = $this->deserializeData($transaction->current_cash_status);

        $new_status_cash = $this->getNewStatusCash($current_cash, [], $current_cash);

        $transaction_data["current_status"] = self::STATUS["closed"];

        $transaction_data["cash_inflows"] =  $this->processData([],'inflow');
        
        $transaction_data["cash_outflows"] = $this->processData($current_cash,'outflow');
        
        $transaction_data["current_cash_status"] = $this->processData($new_status_cash, 'status');

        return Transaction::create($transaction_data);       
    }

    /**
     * Convierte string json en Array
     *
     * @param string $data
     * @return array
     */
    public function deserializeData(string $data): array
    {
        return json_decode($data, true);
    }

    /**
     * Retorna arreglo con el nuevo estado de la caja registradora
     *
     * @param array $current_satatus
     * @param array $inflows
     * @param array $outflows
     * @return array
     */
    public function getNewStatusCash(array $current_satatus, array $inflows, array $outflows): array
    {
        $response = [];

        foreach($current_satatus as $denomination => $quantity)
        {
            $current = $quantity;

            if(explode("_",$denomination)[0] == "total")
            {
                $inflow = $inflows["total_inflow"] ?? 0;
                $outflow = $outflows["total_outflow"] ?? 0;
                $response[$denomination] = $current + $inflow - $outflow;
            }
            else
            {
                $inflow = $inflows[$denomination] ?? 0;
                $outflow = $outflows[$denomination] ?? 0;
                $response[$denomination] = $current + $inflow - $outflow;
            }
        }

        return $response;
    }

    /**
     * Retorna estado actual de la caja registradora
     *
     * @return string
     */
    public function getCurrentCashRegisterStatus(): string
    {
        $transaction = Transaction::latest()->first();
        
        return $transaction->current_cash_status;
    }

    /**
     * Retorna todos los movimientos de la caja registradora
     *
     * @return void
     */
    public function getEventLogs(): Collection
    {
        $transaction = Transaction::all();
        
        return $transaction;
    }

    /**
     * Retorna estado de la caja registradora por fecha
     *
     * @param array $data
     */
    public function getCashRegisterStatusByDate(array $data)
    {
        $date = $this->formatDate($data);

        $transactions = Transaction::
                        where('created_at', '<=', $date);

            $transaction =
                    $transactions->latest()
                                ->first();
        
        return $transaction;
    }

    /**
     * Foramtear fecha
     *
     * @param array $date
     * @return string
     */
    public function formatDate(array $date): string
    {
        $day = $date["day"] ?? 1;
        $month = $date["month"] ?? 1;
        $year = $date["year"] ?? 2000;
        $seconds = $date["seconds"] ?? 0;
        $minutes = $date["minutes"] ?? 0;
        $hours = $date["hours"] ?? 0;

        $dateString = $year.'-'.$month.'-'.$day.' '.$hours.':'.$minutes.':'.$seconds;

        //$dateString = strtotime($dateString);
        $dateString = new Carbon($dateString);

        return $dateString;
    }


    /**
     * Verifica que es posible hacer el pago entrante
     *
     * @param array $data
     * @return array
     */
    public function paymentIsVerify(array $data): array
    {
        $errors = [];

        $custom_data = $this->generateCustomVerifyData($data);

        $payment_is_complete = $this->paymentIsComplete($custom_data);

        if(!$payment_is_complete)
            $errors[] = "El pago no está completo, por favor pida el dinero completo";

        $is_possible_to_return_cash = $this->isPossibleToReturnCash($custom_data);

        if(!$is_possible_to_return_cash)
            $errors[] = "No hay dinero suficiente para dar el cambio";

        $have_cash_to_return = $this->haveCashToReturn($custom_data);

        if(!$have_cash_to_return["isGeneratedChange"])
            $errors[] = "Hay dinero, pero no es posible juntar el cambio por las denominaciones en caja";        
    
        if(count($errors))
            return [
                "response" => false,
                "errors" => $errors
            ];


        return [
            "response" => true,
            "data_outflow" => $have_cash_to_return["data_response"],
        ];
    }

    /**
     * genera data necesaria para verificar transaccion
     *
     * @param array $data
     * @return array
     */
    public function generateCustomVerifyData(array $data): array
    {
        $current_cash_status = $this->getCurrentCashRegisterStatus();

        $current_cash_status = $this->deserializeData($current_cash_status);

        $custom_data["current_status"] = $current_cash_status;

        $custom_data["amount_payment"] = (int)$data["total_pay"];

        $custom_data["sum_inflow"] = $this->sumDataDenominations($data);
        
        return $custom_data;   
    }

    /**
     * Verifica si el pago está completo en la transaccion
     *
     * @param array $data
     * @return boolean
     */
    public function paymentIsComplete(array $data): bool
    {
        return ($data["sum_inflow"] >= $data["amount_payment"]);
    }

    /**
     * Sabemos si hay dinero suficiente para retornar
     *
     * @param array $data
     * @return boolean
     */
    public function isPossibleToReturnCash(array $data): bool
    {
        $return_cash_value = $this->getCashToReturn($data);

        return ($data["current_status"]["total_status"] >= $return_cash_value);
    }

    /**
     * Sabemos la cantidad de dinero a retornar
     *
     * @param array $data
     * @return integer
     */
    public function getCashToReturn(array $data): int
    {
        return $data["sum_inflow"] - $data["amount_payment"];
    }

    /**
     * retorna si con el dinero actual se 
     * puede dar cambio y cual es la mejor forma de hacerlo
     *
     * @param array $data
     * @return array
     */
    public function haveCashToReturn(array $data): array
    {
        $return_cash_value = $this->getCashToReturn($data);

        $better_change = $this->generateBetterCashChange($data["current_status"], $return_cash_value);

        if(!$better_change["isGeneratedChange"])
        {
            $better_change = $this->generateBetterCashChangeSecond($better_change["data_response"], $better_change["remaining_cash"], $data["current_status"]);
        }

        return $better_change;
    }

    /**
     * Primer paso en el algoritmo para obtener mejor tipo de cambio
     *
     * @param array $current_cash_status
     * @param integer $return_cash_value
     * @return array
     */
    public function generateBetterCashChange(array $current_cash_status, int $return_cash_value): array
    {
        $isGeneratedChange = false;

        $denominations = self::DENOMINATIONS;

        arsort($denominations);

        $data_response = [];

        $sum_value = 0;

        $previus_value = 0;

        foreach($denominations as $denomination => $value)
        {
            if(!isset($current_cash_status[$denomination]))
                continue;
            $is_older = false;
            $reset_iteration = false;
            $count = 0;
            $imposible_transaction = false;

            while(!$is_older)
            {
                $previus_count = $count;
                $count = $count + 1;

                if($current_cash_status[$denomination] < $count)
                {
                    $is_older = true;
                    $imposible_transaction = true;
                    //continue;
                }

                $previus_sum = $sum_value;
                $sum_value = $sum_value + $value;
                if(!$imposible_transaction)
                    $is_older = ($sum_value >= $return_cash_value);
                $isGeneratedChange = (($sum_value == $return_cash_value) && !$imposible_transaction);
                $reverse_previus_denominations = ($is_older && ($previus_count == 0) );
            }


            if($is_older || $imposible_transaction)
            {
                if(!$isGeneratedChange)
                {  
                    $sum_value = $previus_sum;
                    $count = $previus_count;
                }
            }

            $data_response[$denomination] = $count;
            $previus_denomination = $denomination;
            $previus_value = $value;

            if($isGeneratedChange)
            {
                break;
            }
        }

        return [
            "isGeneratedChange" => $isGeneratedChange,
            "data_response" => $data_response,
            "remaining_cash" => ($return_cash_value - $sum_value)
        ];
        
    }

    /**
     * Segundo paso en el algoritmo para obtener mejor tipo de cambio
     *
     * @param [type] $data_response
     * @param [type] $remaining_cash
     * @param [type] $current_cash_status
     * @return array
     */
    public function generateBetterCashChangeSecond($data_response, $remaining_cash, $current_cash_status): array
    {
        $denominations = self::DENOMINATIONS;

        $array_to_work = [];

        $isGeneratedChange = false;

        foreach($denominations as $denomination => $value)
        {
            if($data_response[$denomination] <= 0)
            {
                $array_to_work[$denomination] = $current_cash_status[$denomination];
                continue;
            }
            else
            {
                while(($data_response[$denomination] > 0) && !$isGeneratedChange )
                {
                    $data_response[$denomination] = $data_response[$denomination] - 1;
                    $remaining_cash = $remaining_cash + $value;
    
                    $new_current_cash_status = [];
                    foreach($array_to_work as $denomination => $quantity)
                    {
                        $new_current_cash_status[$denomination] = $quantity;
                    }
    
                    $_newResponseBetterCashChange = $this->generateBetterCashChange($new_current_cash_status, $remaining_cash);
                    //dd($_newResponseBetterCashChange);
                    $isGeneratedChange = $_newResponseBetterCashChange["isGeneratedChange"];

                    if($isGeneratedChange)
                        break;
                }

                if($isGeneratedChange)
                {//dd("entra");
                    break;
                }
                else
                {
                    $array_to_work[$denomination] = $current_cash_status[$denomination]; 
                }
            }

        }

        if($isGeneratedChange)
        {
            //dd($_newResponseBetterCashChange, $data_response, $array_to_work);
            foreach($data_response as $denomination => $quantity)
            {
                if(isset($_newResponseBetterCashChange["data_response"][$denomination]))
                {
                    $data_response[$denomination] = $_newResponseBetterCashChange["data_response"][$denomination];
                }
            }

            //return $data_response;
        }
        else
        {
            //retorna para el error
            //return false;
        }

        return [
            "isGeneratedChange" => $isGeneratedChange,
            "data_response" => $data_response,
        ];

    }

    /**
     * Creacion de la transaccion
     *
     * @param array $data_inflow
     * @param array $data_outflow
     * @return Transaction
     */
    public function makePayment(array $data_inflow, array $data_outflow): Transaction
    {
        $transaction = Transaction::latest()->first();

        $current_cash = $this->deserializeData($transaction->current_cash_status);


        $transaction_data["current_status"] = self::STATUS["transaction"];

        $transaction_data["cash_inflows"] =  $this->processData($data_inflow,'inflow');

        $transaction_data["cash_outflows"] = $this->processData($data_outflow,'outflow');

        $_data_cash_inflows = $this->deserializeData($transaction_data["cash_inflows"]);
        $_data_cash_outflows = $this->deserializeData($transaction_data["cash_outflows"]);

        $new_status_cash = $this->getNewStatusCash($current_cash, $_data_cash_inflows, $_data_cash_outflows);

        $transaction_data["current_cash_status"] = $this->processData($new_status_cash, 'status');

        return Transaction::create($transaction_data);
    }

}