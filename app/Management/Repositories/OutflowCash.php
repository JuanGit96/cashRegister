<?php 

namespace App\Management\Repositories;

use App\CashOutflow;
use App\Management\Interfaces\TransactionInterface;

class OutflowCash implements TransactionInterface
{
    public function __construct()
    {
        
    }

    public function create($data)
    {
        CashOutflow::create($data);

        return false;
    }
}