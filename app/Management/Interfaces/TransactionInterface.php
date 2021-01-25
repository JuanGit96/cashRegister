<?php

namespace App\Management\Interfaces;

interface TransactionInterface
{
    /**
     * @param $data
     * @return 
     */
    public function create($data);    
}