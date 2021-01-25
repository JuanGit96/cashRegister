<?php 

namespace App\Management\Repositories;

use App\Management\Interfaces\TransactionInterface;
use App\Transaction;

class InflowCash implements TransactionInterface
{
    public function __construct()
    {
        
    }

    public function create($data)
    {
        Transaction::create($data);     
    }
}