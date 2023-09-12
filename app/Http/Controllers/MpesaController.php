<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Mpesa;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
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
            'Amount' => '100000',
            'PartyA' => env('PHONE_NUMBER'),
            'PartyB' => env('STORE_NUMBER'),
            'PhoneNumber' => env('PHONE_NUMBER'),
            'CallBackURL' => "https://api.optitech.co.ke/api/deliverance/confirmation",
            'AccountReference' => "Test",
            'TransactionDesc' => "Test"
        );

        $data_string = json_encode($curl_post_data);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);

        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

        $curl_response = curl_exec($curl);

        //return json response
        return $curl_response;

    }

    public function mpesaConfirmation(Request $request)
    {
        $content = json_decode($request->getContent());


        if($content->Body->stkCallback->ResultCode == 0) {

            // $mpesa_transaction = new Mpesa();
            // $mpesa_transaction->MerchantRequestID = $content->Body->stkCallback->MerchantRequestID;
            // $mpesa_transaction->CheckoutRequestID = $content->Body->stkCallback->CheckoutRequestID;
            // $mpesa_transaction->ResultCode = $content->Body->stkCallback->ResultCode;
            // $mpesa_transaction->ResultDesc = $content->Body->stkCallback->ResultDesc;
            // $mpesa_transaction->Amount = $content->Body->stkCallback->CallbackMetadata->Item[0]->Value;
            // $mpesa_transaction->MpesaReceiptNumber = $content->Body->stkCallback->CallbackMetadata->Item[1]->Value;
            // $mpesa_transaction->TransactionDate = (string)$content->Body->stkCallback->CallbackMetadata->Item[2]->Value;
            // $mpesa_transaction->PhoneNumber = (string)$content->Body->stkCallback->CallbackMetadata->Item[3]->Value;
            // $mpesa_transaction->created_at = Carbon::now();
            // $mpesa_transaction->updated_at = Carbon::now();
            // $mpesa_transaction->save();

            $mpesa_transaction = DB::table('mpesa_transactions')->insert([
                'MerchantRequestID' => $content->Body->stkCallback->MerchantRequestID,
                'CheckoutRequestID' => $content->Body->stkCallback->CheckoutRequestID,
                'ResultCode' => $content->Body->stkCallback->ResultCode,
                'ResultDesc' => $content->Body->stkCallback->ResultDesc,
                'Amount' => $content->Body->stkCallback->CallbackMetadata->Item[0]->Value,
                'MpesaReceiptNumber' => $content->Body->stkCallback->CallbackMetadata->Item[1]->Value,
                'TransactionDate' => (string)$content->Body->stkCallback->CallbackMetadata->Item[2]->Value,
                'PhoneNumber' => (string)$content->Body->stkCallback->CallbackMetadata->Item[3]->Value,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),

            ]);
        } else {
            // $mpesa_transaction = new Mpesa();
            // $mpesa_transaction->MerchantRequestID = $content->Body->stkCallback->MerchantRequestID;
            // $mpesa_transaction->CheckoutRequestID = $content->Body->stkCallback->CheckoutRequestID;
            // $mpesa_transaction->ResultCode = $content->Body->stkCallback->ResultCode;
            // $mpesa_transaction->ResultDesc = $content->Body->stkCallback->ResultDesc;
            // //created_at
            // //updated_at
            // $mpesa_transaction->created_at = Carbon::now();
            // $mpesa_transaction->updated_at = Carbon::now();
            // $mpesa_transaction->save();

            $mpesa_transaction = DB::table('mpesa_transactions')->insert([
                'MerchantRequestID' => $content->Body->stkCallback->MerchantRequestID,
                'CheckoutRequestID' => $content->Body->stkCallback->CheckoutRequestID,
                'ResultCode' => $content->Body->stkCallback->ResultCode,
                'ResultDesc' => $content->Body->stkCallback->ResultDesc,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

        }


        $result_code = $content->Body->stkCallback->ResultCode;
        $result_description = $content->Body->stkCallback->ResultDesc;
        return $this->createValidationResponse($result_code, $result_description);
    }

}
