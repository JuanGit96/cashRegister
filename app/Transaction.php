<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        "cash_inflows",
        "cash_outflows",
        "current_cash_status",
        "current_status"
    ];

    /*const STATUS = [
        0 => "Closed",
        1 => "Open",
        2 => "Inflow",
        3 => "Outflow"
    ];*/

    public function isOpen()
    {
        return $this->current_status >= 1;
    } 

    public function isClosed()
    {
        return $this->current_status = 0;
    } 

    public function isInflowCash()
    {
        return $this->current_status = 2;
    } 

    public function isOutflowCash()
    {
        return $this->current_status = 3;
    } 
}
