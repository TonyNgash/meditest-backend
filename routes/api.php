<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PhleboController;
use App\Http\Controllers\Tifi;
use App\Models\PhleboPosition;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['prefix'=>'v1'], function(){

    //general unauthenticated routes here

    Route::group(['prefix'=>'user'],function(){
        //unauthenticated routes for users here

        //get all services available
        Route::get('/services',[ServiceController::class,'index']);

        //get one service with given Id
        Route::get('/services/{id}',[ServiceController::class,'show']);

        //register a user
        Route::post('/register',[RegisterController::class,'registerUser']);

        //send user account creation confirmation email
        //Route::post('user-account-created-email',[UserController::class,'userAccountCreatedEmail']);

        //log a user in
        Route::post('/login',[LoginController::class,'loginUser']);

        //Forgot Password?
        //Route to send the password reset also takes the email
        Route::post('/forgot-password',[UserController::class,'forgotPassword']);

        //Route to validate the reset code
        Route::post('/reset-password-token',[UserController::class,'resetPassword']);

        //Route to take in the new password
        Route::post('/new-password',[UserController::class,'setNewPassword']);

        //Testing the fact that a controller is just a stand alone file and requires
        //no special references anywhere else
        Route::get('/chapo',[PaymentController::class,'writeToFile']);

        Route::post('/send-sms',[UserController::class,'sendTestSms']);

        //Mpesa callback url
        Route::post('/confirm_mpesa_payment/{booking_id}',[PaymentController::class,'callBackUrl']);

        Route::group(['middleware'=>['auth:sanctum']],function(){
            //authenticated routes for users here

            //user add dependants
            Route::post('/add-dependant',[UserController::class,'addDependants']);

            //user view their own dependants
            Route::post('/dependants/{id}',[UserController::class,'getDependants']);

            //user update dependant
            Route::post('/update-dependant/{id}',[UserController::class,'updateDependant']);

            //user remove dependants
            Route::post('/remove-dependant/{id}',[UserController::class,'removeDependant']);

            //user make booking
            Route::post('/book',[UserController::class,'makeBooking']);

            //user view their own bookings - pass user_id
            Route::get('/bookings/{id}',[UserController::class,'userViewAllBookings']);

            //user view individual booking details - pass booking_id
            Route::get('/booking/{id}',[UserController::class,'userViewOneBooking']);

            //user view phlebo details on assignment
            Route::get('/phlebo-assigned-details/{id}',[UserController::class,'phleboAssignedDetails']);

            //user view phlebo details on active
            Route::get('/phlebo-active-details/{id}',[UserController::class,'phleboActiveDetails']);

            //user view their history of past bookings
            Route::get('/booking-historys',[UserController::class,'viewBookingHistory']);

            //user view individual booking history details - pass booking_history_id
            Route::get('/booking-history/{id}',[UserController::class,'viewBookingHistoryDetails']);

            //user make payment
            Route::post('/payment',[PaymentController::class,'initiatePayment']);

            //user logout
            Route::post('/logout',[LoginController::class,'logoutUser']);
        });
    });
    //User routes end here*****************************************
    Route::group(['prefix'=>'staff'],function(){
        //Unauthenticated routes for staff here

        //Get a list of all users
        Route::get('/users',[StaffController::class,'getAllUsers']);

        //Get a list of all bookings
        Route::get('/bookings',[StaffController::class,'getAllBookings']);

        //Get details of a specific booking
        Route::get('/bookings/{id}',[StaffController::class,'getBookingDetails']);

        //Get a list of all services
        Route::get('/services',[ServiceController::class,'getAllServices']);

        //Add new services
        //Route::post('/add-service',[ServiceController::class,'store']);

        //Route::put('/services/{id}',[ServiceController::class,'update']);
        //Route::delete('/services/{id}',[ServiceController::class,'destroy']);

        //Get a list of all phlebos
        Route::get('/phlebos',[StaffController::class,'getAllPhlebos']);

        //gives a specific task to a specific phlebo
        Route::post('/assign',[StaffController::class,'assignToPhlebo']);

        //Get a list of a specific phlebo assignments by giving phlebo id.
        Route::get('/phlebo-assignments/{id}',[StaffController::class,'getPhleboAssignments2']);

        //Get details of a specific assignment
        Route::get('/phlebo-assignment/{id}',[StaffController::class,'phleboGetAssignmentDetails']);

        //Phlebo marks assignment as active -pass assignment_id
        Route::post('/phlebo-mark-as/active/{id}',[PhleboController::class,'phleboMarkAsActive']);

        //Phlebo marks assignment as done -pass assignment_id
        Route::post('/phlebo-mark-as/done/{id}',[PhleboController::class,'phleboMarkAsDone']);

        //Phlebo marks assignment as failed -pass assignment_id
        Route::post('/phlebo-mark-as/failed/{id}',[PhleboController::class,'phleboMarkAsFailed']);

        Route::post('/phlebo-post-current',[PhleboController::class,'phleboPostCurrent']);

        Route::get('/phlebo-get-current/{id}',[PhleboController::class,'phleboGetCurrent']);

        //Call center view all mpesa transactions
        Route::get('/get-mpesa-confirmations',[PaymentController::class,'getMpesaPayments']);

    });



});
