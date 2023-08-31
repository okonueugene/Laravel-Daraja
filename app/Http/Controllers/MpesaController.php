<?php

namespace App\Http\Controllers;

use App\Models\Mpesa;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class MpesaController extends Controller
{
    public function generateAccessToken()
    {
        $response = Http::withBasicAuth(env('CONSUMER_KEY'), env('CONSUMER_SECRET'))->get(env('GENERATE_ACCESS_TOKEN_URL'));

        $access_token = json_decode($response->getBody());

        return $access_token->access_token;

    }

    public function createValidationResponse($result_code, $result_description)
    {
        $response = array(
            'ResultCode' => $result_code,
            'ResultDesc' => $result_description
        );

        return json_encode($response);
    }


    public function stkPush()
    {
        $access_token = $this->generateAccessToken();

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, env('STK_PUSH_URL'));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$access_token)); //setting custom header

        $curl_post_data = array(
            //Fill in the request parameters with valid values
            'BusinessShortCode' => env('STORE_NUMBER'),
            'Password' => base64_encode(env('STORE_NUMBER').env('PASSKEY').date("YmdHis")),
            'Timestamp' => date("YmdHis"),
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => '1',
            'PartyA' => env('PHONE_NUMBER'),
            'PartyB' => env('STORE_NUMBER'),
            'PhoneNumber' => env('PHONE_NUMBER'),
            'CallBackURL' => env('CALLBACK_URL'),
            'AccountReference' => "Test",
            'TransactionDesc' => "Test"
        );

        $data_string = json_encode($curl_post_data);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);

        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

        $curl_response = curl_exec($curl);
        echo $curl_response;

    }

    public function mpesaConfirmation(Request $request)
    {
        $content = json_decode($request->getContent());


        if($content->Body->stkCallback->ResultCode == 0) {

            $mpesa_transaction = new Mpesa();
            $mpesa_transaction->MerchantRequestID = $content->Body->stkCallback->MerchantRequestID;
            $mpesa_transaction->CheckoutRequestID = $content->Body->stkCallback->CheckoutRequestID;
            $mpesa_transaction->ResultCode = $content->Body->stkCallback->ResultCode;
            $mpesa_transaction->ResultDesc = $content->Body->stkCallback->ResultDesc;
            $mpesa_transaction->Amount = $content->Body->stkCallback->CallbackMetadata->Item[0]->Value;
            $mpesa_transaction->MpesaReceiptNumber = $content->Body->stkCallback->CallbackMetadata->Item[1]->Value;
            $mpesa_transaction->TransactionDate = (string)$content->Body->stkCallback->CallbackMetadata->Item[2]->Value;
            $mpesa_transaction->PhoneNumber = (string)$content->Body->stkCallback->CallbackMetadata->Item[3]->Value;
            $mpesa_transaction->save();
        } else {
            $mpesa_transaction = new Mpesa();
            $mpesa_transaction->MerchantRequestID = $content->Body->stkCallback->MerchantRequestID;
            $mpesa_transaction->CheckoutRequestID = $content->Body->stkCallback->CheckoutRequestID;
            $mpesa_transaction->ResultCode = $content->Body->stkCallback->ResultCode;
            $mpesa_transaction->ResultDesc = $content->Body->stkCallback->ResultDesc;
            $mpesa_transaction->save();

        }


        $result_code = $content->Body->stkCallback->ResultCode;
        $result_description = $content->Body->stkCallback->ResultDesc;
        return $this->createValidationResponse($result_code, $result_description);
    }

}
