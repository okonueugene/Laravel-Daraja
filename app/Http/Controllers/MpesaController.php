<?php

namespace App\Http\Controllers;

use App\Models\Mpesa;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class MpesaController extends Controller
{

    public function generateAccessToken()
    {
        $consumer_key = 'YOUR_CONSUMER_KEY';//use the consumer key generated from your safaricom developer account stored in your .env file eg env('CONSUMER_KEY')

        $consumer_secret = 'your_consumer_secret';//use the consumer secret generated from your safaricom developer account stored in your .env file eg env('CONSUMER_SECRET')

        $credentials = base64_encode($consumer_key.':'.$consumer_secret);

        $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$credentials)); //setting a custom header
        curl_setopt($curl , CURLOPT_HEADER, false);
        curl_setopt($curl , CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl , CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);
        $access_token = json_decode($curl_response);
        return $access_token->access_token;
        
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

    public function mpesaRegisterUrls()
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/registerurl');
        curl_setopt($curl , CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization: Bearer '. $this->generateAccessToken()));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode([
            'ShortCode' => 'YOUR_STORE_NUMBER',
            'ResponseType' => 'Completed',
            'ConfirmationURL' => "https://YOUR_CONFIRMATION_URL",
            'ValidationURL' => "https://YOUR_VALIDATION_URL"
        ]));

        $curl_response = curl_exec($curl);
        echo $curl_response;

    }

    public function mpesaConfirmation(Request $request)
    {
        $content = json_decode($request->getContent());

        $mpesa_transaction =Mpesa::create([
            'TransactionType' => $content['TransactionType'],
            'TransID' => $content['TransID'],
            'TransTime' => $content['TransTime'],
            'TransAmount' => $content['TransAmount'],
            'BusinessShortCode' => $content['BusinessShortCode'],
            'BillRefNumber' => $content['BillRefNumber'],
            'InvoiceNumber' => $content['InvoiceNumber'],
            'OrgAccountBalance' => $content['OrgAccountBalance'],
            'ThirdPartyTransID' => $content['ThirdPartyTransID'],
            'MSISDN' => $content['MSISDN'],
            'FirstName' => $content['FirstName'],
            'MiddleName' => $content['MiddleName'],
            'LastName' => $content['LastName'],
         ]);
        return $this->createValidationResponse(0, "Accepted confirmation request");
    }

}
