<?php

namespace App\Http\Controllers;

use App\Models\MpesaC2B;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MpesaC2BController extends Controller
{
    public function generateAccessToken()
    {
        $consumer_key = env('CONSUMER_KEY');
        $consumer_secret = env('CONSUMER_SECRET');
        $credentials = base64_encode($consumer_key.":".$consumer_secret);

        $url = env('GENERATE_ACCESS_TOKEN_URL');

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Basic ".$credentials));

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $curl_response = curl_exec($curl);

        return json_decode($curl_response)->access_token;
    }

    public function registerURLS()
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, env('MPESA_REGISTER_URLS_URL'));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization: Bearer '. $this->generateAccessToken()));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode([
            'ShortCode' => env('STORE_NUMBER'),
            'ResponseType' => env('RESPONSE_TYPE'),
            'ConfirmationURL' => env('CONFIRMATION_URL'),
            'ValidationURL' => env('VALIDATION_URL')
        ]));

        $curl_response = curl_exec($curl);
        echo $curl_response;
    }

    public function simulateTransaction(Request $request)
    {
        $url = env('SIMULATE_TRANSACTION_URL');

        $curl_post_data = array(
            //Fill in the request parameters with valid values
            'ShortCode' => env('STORE_NUMBER'),
            'CommandID' => 'CustomerBuyGoodsOnline',
            'Amount' => 100,
            'Msisdn' => env('PHONE_NUMBER'),
            'BillRefNumber' => 'Test'
        );

        $data_string = json_encode($curl_post_data);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json',"Authorization:Bearer ".$this->generateAccessToken())); //setting custom header

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

        $curl_response = curl_exec($curl);

        return $curl_response;
    }

    public function confirmTransaction(Request $request)
    {
        $content = json_decode($request->getContent());


        $mpesa_transaction = new MpesaC2B();
        $mpesa_transaction->TransactionType = $content->TransactionType;
        $mpesa_transaction->TransID = $content->TransID;
        $mpesa_transaction->TransTime = $content->TransTime;
        $mpesa_transaction->TransAmount = $content->TransAmount;
        $mpesa_transaction->BusinessShortCode = $content->BusinessShortCode;
        $mpesa_transaction->BillRefNumber = $content->BillRefNumber;
        $mpesa_transaction->InvoiceNumber = $content->InvoiceNumber;
        $mpesa_transaction->OrgAccountBalance = $content->OrgAccountBalance;
        $mpesa_transaction->ThirdPartyTransID = $content->ThirdPartyTransID;
        $mpesa_transaction->MSISDN = $content->MSISDN;
        $mpesa_transaction->FirstName = $content->FirstName;
        $mpesa_transaction->MiddleName = $content->MiddleName;
        $mpesa_transaction->LastName = $content->LastName;

        $mpesa_transaction->save();

        return $this->createValidationResponse("0", "Success");
    }



    public function createValidationResponse($result_code, $result_description)
    {
        $result = json_encode([
            'ResultCode' => $result_code,
            'ResultDesc' => $result_description
        ]);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json; charset=utf-8');
        $response->setContent($result);
        return $response;
    }

    public function mpesaValidation()
    {
        $result_code = "0";
        $result_description = "Accepted validation request";
        return $this->createValidationResponse($result_code, $result_description);
    }



}
