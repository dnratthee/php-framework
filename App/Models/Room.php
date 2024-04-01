<?php

namespace App\Models;

use App\Libs\Model;

class Room extends Model
{
    protected $fillable = ['temp1', 'temp2', 'temp3', 'datesave', 'timesave'];
    protected $softDelete = true;
}
