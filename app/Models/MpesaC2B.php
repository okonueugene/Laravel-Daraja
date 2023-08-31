<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MpesaC2B extends Model
{
    use HasFactory;

    protected $fillable = [
        'TransactionType',
        'TransID',
        'TransTime',
        'TransAmount',
        'BusinessShortCode',
        'BillRefNumber',
        'InvoiceNumber',
        'OrgAccountBalance',
        'ThirdPartyTransID',
        'MSISDN',
        'FirstName',
        'MiddleName',
        'LastName'
    ];

    protected $table = 'mpesa_transactions_c2b';
}
