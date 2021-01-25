<?php 

namespace App\Management\Repositories;

use Illuminate\Support\Facades\DB;

class DenominationRepository
{
    const DENOMINATIONS = [
        "fifty_cop" => "50",
        "hundred_cop" => "100",
        "two_hundred_cop" => "200",
        "five_hundred_cop" => "500",
        "one_thousand_cop" => "1000",
        "two_thousand_cop" => "2000",
        "five_thousand_cop" => "5000",
        "ten_thousand_cop" => "10000",
        "twenty_thousand_cop" => "20000",
        "fifty_thousand_cop" => "50000",
        "one_hundred_thousand_cop" => "100000"
    ];

    const TABLES_DENOMINATIONS = [
        "inflows" => "cash_inflows",
        "outflows" => "cash_outflows"
    ];

    private $modelDenomination;

    private $date;

    public function __construct()
    {
        $this->date = null;       
    }

    public function getQuantityByElements()
    {
        $query =
                $this->modelDenomination;
        
        foreach(self::DENOMINATIONS  as $denomination)
        {
            $query =
                $query->sum($denomination);
        }

        return $query->get()->toArray();
    }

    public function getSumOfDenominations()
    {
        $arrayCash = $this->getQuantityByElements();

        $result = 0;

        foreach($arrayCash as $denomination => $quantity)
        {
            if(isset(self::DENOMINATIONS[$denomination]))
                $result = $result + (self::DENOMINATIONS[$denomination] * $quantity);
        }

        return $result;
    }

    public function getDataDenominations()
    {
        $data = [];

        foreach(self::TABLES_DENOMINATIONS as $key => $table)
        {
            $this->modelDenomination = 
                            DB::table($table)
                            ->where('created_at', $this->date);

            $data[$key] = $this->getSumOfDenominations();
        }

        $data["status"] = $this->getProcessStatusDenominations($data);

        return $data;
    }

    public function setDate(string $date)
    {
        $this->date = $date;
    }

    public function getProcessStatusDenominations($data)
    {
        $result = [];

        foreach(self::DENOMINATIONS  as $denomination => $value)
        {
           $result[$denomination] = $data[]
        }
    }

}