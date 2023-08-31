<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mpesa extends Model
{
    use HasFactory;

    protected $fillable = [
        'MerchantRequestID',
        'CheckoutRequestID',
        'ResultCode',
        'ResultDesc',
        'Amount',
        'MpesaReceiptNumber',
        'Balance',
        'TransactionDate',
        'PhoneNumber',
    ];

    protected $table = 'mpesa_transactions';

}
