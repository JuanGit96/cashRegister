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

    public function isOpen()
    {
        return $this->current_status >= 1;
    } 

    public function isClosed()
    {
        return $this->current_status = 0;
    } 

    public function isTransaction()
    {
        return $this->current_status = 2;
    } 

}
