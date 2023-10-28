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


        $consumerKey = env('CONSUMER_KEY');
        $consumerSecret = env('CONSUMER_SECRET');

        $credentials = base64_encode($consumerKey . ':' . $consumerSecret);

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $credentials,
            'Content-Type' => 'application/json'
        ])->get('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');

        $access_token = $response->json()['access_token'];

        return $access_token;

    }

    public function createValidationResponse($result_code, $result_description)
    {
        $response = array(
            'ResultCode' => $result_code,
            'ResultDesc' => $result_description
        );

        return json_encode($response);
    }


    public function stkPush(Request $request)
    {
        //Retrieve the phone number and the amount from the request
        $phone_number = $request->phoneNumber;
        $amount = $request->amount;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->generateAccessToken(),
            'Content-Type' => 'application/json'
        ])->post(env('STK_PUSH_URL'), [
            'BusinessShortCode' => env('STORE_NUMBER'),
            'Password' => base64_encode(env('STORE_NUMBER') . env('PASSKEY') . date("YmdHis")),
            'Timestamp' => date("YmdHis"),
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => env('PHONE_NUMBER'),
            'PartyB' => env('STORE_NUMBER'),
            'PhoneNumber' => $phone_number,
            'CallBackURL' => 'https://app.askaritechnologies.com/api/shop/callback?token=5f1a2b3c4d56f7g8h9i',
            'AccountReference' => "Test",
            'TransactionDesc' => "Test"
        ]);


        return $response->json();

    }

    public function mpesaConfirmation(Request $request)
    {
        $content = json_decode($request->getContent());

        // Check if "ResultCode" is present in the response
        if (isset($content->Body->stkCallback->ResultCode)) {
            $resultCode = $content->Body->stkCallback->ResultCode;

            if ($resultCode === 0) {
                // The request was successful
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
                // The request encountered an error

                $mpesa_transaction = DB::table('mpesa_transactions')->insert([
                    'MerchantRequestID' => $content->Body->stkCallback->MerchantRequestID,
                    'CheckoutRequestID' => $content->Body->stkCallback->CheckoutRequestID,
                    'ResultCode' => $content->Body->stkCallback->ResultCode,
                    'ResultDesc' => $content->Body->stkCallback->ResultDesc,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        $result_code = $content->Body->stkCallback->ResultCode;
        $result_description = $content->Body->stkCallback->ResultDesc;
        return $this->createValidationResponse($result_code, $result_description);
    }

}
