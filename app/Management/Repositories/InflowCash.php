<?php 

namespace App\Management\Repositories;

use App\CashInflow;
use App\Management\Interfaces\TransactionInterface;

class InflowCash implements TransactionInterface
{
    public function __construct()
    {
        
    }

    public function create($data)
    {
        if($this->hasCompleteCash())
            CashInflow::create($data);     
    }

    public function hasCompleteCash()
    {
        return true;
    }
}