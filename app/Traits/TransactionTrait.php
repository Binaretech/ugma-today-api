<?php

namespace App\Traits;

use App\Exceptions\DatabaseException;
use Illuminate\Support\Facades\DB;

trait TransactionTrait
{
    public static function transaction(
        $function,
        $error_status = 500,
        $error_message = null
    ) {
        DB::transaction(function () use ($function, $error_message, $error_status) {
            try {
                $function();
            } catch (\Exception $e) {
                throw new DatabaseException($error_message ?
                    $error_message : trans('exceptions.internal'), $error_status, $e);
            }
        });
    }
}
