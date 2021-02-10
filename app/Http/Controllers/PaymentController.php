<?php

namespace App\Http\Controllers;

use App\Models\MpesaPaymentConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public $BusinessShortCode = "174379";
    public $LipaNaMpesaPassKey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
    public $TransactionType  = "CustomerPayBillOnline";
    //public $Amount  = "8";
    //public $PartyA =  "254790818789";
    //public $PartyB =  "174379";//same as business short code
    //public $PhoneNumber   = "254790818789"; //same as party a
    public $CallBackURL  = "http://everydayapps.org/api/v1/user/confirm_mpesa_payment/";
    //public $AccountReference = "MEDITEST Diagnostic Services";
    //public $TransactionDesc = "Paid for Testts";
    //public $Remarks = "Good day";

    public function initiatePayment(Request $request){
        $rules = [
            'amount'=>'required|numeric',
            'party_a'=>'required|min:12|max:12',
            'account_ref'=>'required',
            'trans_desc'=>'required',
            'remarks'=>'required',
            'booking_id'=>'required'
        ];
        $val = Validator::make($request->all(),$rules);
        if($val->fails()){
            return response()->json(['errors'=>$val->errors()]);
        }
        $booking_id = $request->booking_id;

        return response()->json($this->STKPushSimulation(
            $this->BusinessShortCode,
            $this->LipaNaMpesaPassKey,
            $this->TransactionType,
            $request->amount,
            $request->party_a,
            $this->BusinessShortCode,
            $request->party_a,//phone number
            $this->CallBackURL.$booking_id,
            $request->account_ref,
            $request->trans_desc,
            $request->remarks));

    }


    public function STKPushSimulation(
        $BusinessShortCode,
        $LipaNaMpesaPasskey,
        $TransactionType,
        $Amount,
        $PartyA,
        $PartyB,
        $PhoneNumber,
        $CallBackURL,
        $AccountReference,
        $TransactionDesc,
        $Remarks){

        $env = 'sandbox';
        if($env == 'live'){
            $url = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
            $token = $this->generateAccessToken();
        }elseif($env == 'sandbox'){
            $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
            $token = $this->generateAccessToken();
        }else{
            return response()->json(['message'=>'invalid application status']);
        }
        $timestamp='20'.date("ymdhis");
        $password=base64_encode($BusinessShortCode.$LipaNaMpesaPasskey.$timestamp);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$token));
        $curl_post_data = array(
            'BusinessShortCode' => $BusinessShortCode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => $TransactionType,
            'Amount' => $Amount,
            'PartyA' => $PartyA,
            'PartyB' => $PartyB,
            'PhoneNumber' => $PhoneNumber,
            'CallBackURL' => $CallBackURL,
            'AccountReference' => $AccountReference,
            'TransactionDesc' => $TransactionDesc,
            'Remarks'=>$Remarks
        );

        $data_string = json_encode($curl_post_data);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $curl_response=curl_exec($curl);
        return $curl_response;
    }
    public function generateAccessToken()
    {

        $consumer_key="zkpd8SMiXHGyZzMFgQG5SkKftm0U9rab";
        $consumer_secret="NfYAsYNYa6E3jONT";
        $credentials = base64_encode($consumer_key.":".$consumer_secret);
        $url = "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Basic ".$credentials));
        curl_setopt($curl, CURLOPT_HEADER,false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);
        $access_token=json_decode($curl_response);
        //return response()->json($access_token->access_token);
        return $access_token->access_token;
    }

    public function testCallBackUrl(Request $request){

        $mpesa_confirmation = new MpesaPaymentConfirmation();
        $mpesa_confirmation->merchant_request_id =$request->merchant_request_id;
        $mpesa_confirmation->checkout_request_id =$request->checkout_request_id;
        $mpesa_confirmation->result_code =$request->result_code;
        $mpesa_confirmation->result_desc =$request->result_desc;
        $mpesa_confirmation->amount =$request->amount;
        $mpesa_confirmation->mpesa_receipt_number =$request->mpesa_receipt_number;
        $mpesa_confirmation->balance =$request->balance;
        $mpesa_confirmation->transaction_date =$request->transaction_date;
        $mpesa_confirmation->phone_number =$request->phone_number;
        $mpesa_confirmation->dump =$request->dump;
        $mpesa_confirmation->save();

        return response()->json(['msg'=>'makomani_ngomani']);
    }
    public function put($fromMpesa) {
        try {
            $attemptToWriteText = "The file has ";
            $attemptToWriteText .= $fromMpesa;
            Storage::put('attempt3.txt', $attemptToWriteText);
       } catch (\Exception $e) {

       }
     }
    public function writeToFile(){
        $this->put(" some text");
    }



    public function callBackUrl($booking_id){

        $callbackJSONData=file_get_contents('php://input');
        $callbackData=json_decode($callbackJSONData);

        $this->writeToFile($callbackData);

        $merchant_request_id=$callbackData->Body->stkCallback->MerchantRequestID;
        $checkout_request_id=$callbackData->Body->stkCallback->CheckoutRequestID;
        $result_code=$callbackData->Body->stkCallback->ResultCode;
        $result_desc=$callbackData->Body->stkCallback->ResultDesc;
        if($result_code == 0){
            $amount=$callbackData->Body->stkCallback->CallbackMetadata->Item[0]->Value;
            $mpesa_receipt_number=$callbackData->Body->stkCallback->CallbackMetadata->Item[1]->Value;
            $balance=$callbackData->Body->stkCallback->CallbackMetadata->Item[2]->Value;
            $transaction_date=$callbackData->Body->stkCallback->CallbackMetadata->Item[3]->Value;
            $phone_number=$callbackData->Body->stkCallback->CallbackMetadata->Item[4]->Value;

            $mpesa_confirmation = new MpesaPaymentConfirmation();
            $mpesa_confirmation->merchant_request_id =$merchant_request_id;
            $mpesa_confirmation->checkout_request_id =$checkout_request_id;
            $mpesa_confirmation->result_code =$result_code;
            $mpesa_confirmation->result_desc =$result_desc;
            $mpesa_confirmation->amount =$amount;
            $mpesa_confirmation->mpesa_receipt_number =$mpesa_receipt_number;
            $mpesa_confirmation->balance =$balance;
            $mpesa_confirmation->transaction_date =$transaction_date;
            $mpesa_confirmation->phone_number =$phone_number;
            $mpesa_confirmation->dump =$booking_id;
            $mpesa_confirmation->save();

        }else{

        }


    }

    public function getMpesaPayments(){

        $payments = MpesaPaymentConfirmation::select('amount','mpesa_receipt_number','balance','phone_number','dump','created_at')
        ->get();

        return response()->json($payments);
    }



}
