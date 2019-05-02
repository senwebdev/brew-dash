<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Batch;

class BatchController extends Controller
{
    public function showBatch(){
        $amounts = DB::table('batches')->get();
        $token = csrf_token();
        return $amounts;
    }

    public function updateBatch($tank_id, $brew_amount, $direction){
        $batch = new Batch();
        $current_amount = $batch->getAmount($tank_id);

        if($direction == TRUE){
            return DB::table('batches')
                ->where('id', $tank_id)
                ->update(['amount' => $current_amount + $brew_amount ]);
        }

        DB::table('batches')
            ->where('id', $tank_id)
            ->update(['amount' => $current_amount - $brew_amount]);    

        return "Success!";
    }

    public function moveBatch($source_id, $target_id, $move_amount){
        $batch = new Batch();

        $source_amount = $batch->getAmount($source_id);
        $target_amount = $batch->getAmount($target_id);
        
        $source_amount -= $move_amount;
        $target_amount += $move_amount;

        if($source_amount < 0)
            return "Error!";

        DB::table('batches')
            ->where('id', $source_id)
            ->update(['amount' => $source_amount]);
 
        DB::table('batches')
             ->where('id', $target_id)
             ->update(['amount' => $target_amount]);

        return "Success!";
    }

    public function dumpBatch($tank_id){
        return DB::table('batches')
            ->where('id', $tank_id)
            ->update(['state' => 0]);
    }

    public function kegBatch($tank_id){
        return DB::table('batches')
            ->where('id', $tank_id)
            ->update(['state' => 1]);
    }

   
}
