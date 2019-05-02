<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Batch extends Model
{
    function getAmount($id) {
        return json_decode(DB::table('batches')->where('id', $id)->get('amount'))[0]->amount;
    }
}
