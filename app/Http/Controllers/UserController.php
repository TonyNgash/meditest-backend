<?php

namespace App\Http\Controllers;

use App\Http\Resources\DependantResource;
use App\Http\Resources\BookingResource;
use App\Mail\AccountCreatedMailer;
use App\Models\Assignment;
use App\Models\BookedPatient;
use App\Models\BookedService;
use App\Models\Dependant;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Booking;
use App\Models\Phlebo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use App\Models\Service;

class UserController extends Controller
{

    public function addDependants(Request $request)
    {
        $rules = [
            'first_name'=>'required|min:2|max:20',
            'sirname'=>'required|min:2|max:20',
            'last_name'=>'required|min:2|max:20',
            'gender' => 'required|in:male,female',
            'address'=> 'required|min:4|max:50',
            'dob'=>'required|date_format:Y-m-d',
            'user_id'=>'required|numeric',
            'relationship' => 'required|in:child,spouse,parent,extended,sibling,other,'
        ];
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            return response()->json(['status_code'=>400, 'message'=>$validator->errors()]);
        }
        $dependant = new Dependant();
        $dependant->first_name = $request->first_name;
        $dependant->sirname = $request->sirname;
        $dependant->last_name = $request->last_name;
        $dependant->gender = $request->gender;
        $dependant->address = $request->address;
        $dependant->dob = $request->dob;
        $dependant->user_id = $request->user_id;
        $dependant->relationship = $request->relationship;
        $res = $dependant->save();
        if($res){
            return response()->json(['status_code'=>200, 'message'=>'Dependent Created Successfuly']);
        }else{
            return response()->json(['status_code'=>400, 'message'=>"Something went wrong"]);
        }

    }
    public function updateDependant(Request $request, $id){
        $rules = [
            'first_name'=>'required|min:2|max:20',
            'sirname'=>'required|min:2|max:20',
            'last_name'=>'required|min:2|max:20',
            'gender' => 'required|in:male,female',
            'address'=> 'required|min:4|max:50',
            'dob'=>'required|date_format:Y-m-d',
            'phone' => 'nullable|min:12|max:12|unique:users,phone',
            'relationship' => 'required|in:child,spouse,parent,extended,sibling,other,'
        ];
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            return response()->json(['status_code'=>400, 'message'=>$validator->errors()]);
        }
        $dependant = Dependant::findOrFail($id);
        $dependant->first_name = $request->first_name;
        $dependant->sirname = $request->sirname;
        $dependant->last_name = $request->last_name;
        $dependant->gender = $request->gender;
        $dependant->address = $request->address;
        $dependant->dob = $request->dob;
        $dependant->relationship = $request->relationship;
        $res = $dependant->save();
        if($res){
            return response()->json(['status_code'=>200, 'message'=>'Dependent Updated Successfuly']);
        }else{
            return response()->json(['status_code'=>400, 'message'=>"Something went wrong"]);
        }
    }
    public function removeDependant($id){
        $dependant = Dependant::findOrFail($id);
        $res = $dependant->delete();
        if($res){
            return response()->json(['status_code'=>200, 'message'=>'Dependent Removed Successfuly']);
        }
    }

    public function getDependants($id){
        $dependants = Dependant::all()->where("user_id",$id);
        return DependantResource::collection($dependants);
    }


    public function makeBooking(Request $request){
        $rules = [
            'user_id'=>'required|numeric',
            'self'=>'required|boolean',
            'paid'=>'required|boolean',
            'scheduled_date'=>'required|date_format:Y-m-d|after:today',
            'scheduled_time'=>'required|date_format:H:i',
            'total_amount'=>'required|numeric',
            'phone'=>'nullable|min:12|max:12',
            'address_desc'=>'nullable',
            'lat'=>'required|numeric',
            'lon'=>'required|numeric',
            'locality'=>'nullable',
            'admin_area'=>'nullable',
            'sub_admin_area'=>'nullable',
            'dependant_id' => 'array|required_if:self,false',
            'service_id'=>'required|array'
        ];
        $messages = '';
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            return response()->json(['status_code'=>400, 'message'=>$validator->errors()]);
        }
        $booking = new Booking();
        $booking->user_id = $request->user_id;
        $booking->self = $request->self;
        $booking->paid = $request->paid;
        $booking->scheduled_date = $request->scheduled_date;
        $booking->scheduled_time = $request->scheduled_time;
        $booking->total_amount = $request->total_amount;
        $booking->phone = $request->phone;
        $booking->address_desc = $request->address_desc;
        $booking->lat = $request->lat;
        $booking->lon = $request->lon;
        $booking->locality = $request->locality;
        $booking->admin_area = $request->admin_area;
        $booking->sub_admin_area = $request->sub_admin_area;
        $res = $booking->save();
        $booking_id = $booking->id;
        $self = $booking->self;
        if($res){
            //$messages .= ":Inserted into booking table:";
        }
        if($self){//is true
            //$messages .= ":Self is ".$self.":";
            //don't add to booked_patients as the patient is the user
        }else{//is false
            //add to booked_patients
            foreach($request->dependant_id as $dependant_id){
                $booked_patient = new BookedPatient();
                $booked_patient->booking_id = $booking_id;
                $booked_patient->dependant_id = $dependant_id;
                $booked_patient->save();
                //$messages .= ":Inserted into booked_patient table:";
            }
            //$messages .= ":Self is ".$self.":";
        }

        foreach($request->service_id as $service_id){
            $booked_service = new BookedService();
            $booked_service->booking_id = $booking_id;
            $booked_service->service_id = $service_id;
            $booked_service->save();
            //$messages .= ":Inserted into booked_services:";
        }
        $messages="Booking Succesfully Created";
        return response()->json(['status_code'=>200, 'message'=>$messages,'booking'=>$booking]);
    }
    /**
     * name: viewBooking($id)
     * desc: we want the user to be able to view bookings they've made.
     */
    public function userViewAllBookings($id)//user_id is beign passed to this method through url
    {

        //find all new bookings where user_id and old_new match the given ones.
        $bookings = Booking::select('id','paid','scheduled_date','scheduled_time','total_amount','status')
        ->where('user_id',$id)
        ->where('old_new',1)
        ->where('status','!=','done')
        ->get();
        return response()->json(['data'=>$bookings]);

    }
    public function userViewOneBooking($id){//receives booking id
        //we want...
        //self - booking table
        //paid - booking table
        //phone1 - booking table
        //total_amount - booking table
        //scheduled_date - booking table
        //scheduled_time - booking table
        //address_desc - booking table

        //booked patients - booked_patients table -> dependant table
            //dependant_id - booked_patients table
            //first_name - dependant table
            //sirname - dependant table
            //last_name - depandant table
        //booked services - booked_services table -> service table
            //service_id - booked_services table
            //test_name - service table
            //test_price - service table
        //phlebo_status - assignment table

        //phone2 - users table

        //booking_details
        $booking_table=Booking::select('user_id','self','paid','scheduled_date','scheduled_time','total_amount','phone','address_desc')
        ->where('id',$id)
        ->get();

        //patients
        //if self is true, patient is user, else patient is dependant
        $patients = [];
        if($booking_table[0]['self']){
            $patients = User::select('first_name','sirname','last_name')
            ->where('id',$booking_table[0]['user_id']+0)
            ->get();

        }else{
            //select dependant_id from booked_patients where booking_id is as given
            $dependent_ids = BookedPatient::select('dependant_id')->where('booking_id',$id)->get();
            //loop through dependant_ids and get corresponding dependant details
            for($i = 0;$i < count($dependent_ids); $i++){
                //select first_name, sirname, last_name from dependants table where dependant_id is as given
                $patients[] = Dependant::select('first_name','sirname','last_name')
                ->where('id',$dependent_ids[$i]['dependant_id']+0)
                ->get()[0];
            }

        }

        //services
        //select serivice_id(s) from booked_services table where booking_id is as given
        $service_ids = BookedService::select('service_id')
        ->where('booking_id',$id)
        ->get();
        //select test_name , test_price from services table where service id
        $services = [];
        for($i = 0; $i < count($service_ids); $i++){
            $services[] = Service::select('test_name','test_price')
            ->where('id',$service_ids[$i]['service_id']+0)
            ->get()[0];
        }

        //booking phone
        $booker_phone = User::select('phone')
        ->where('id',$booking_table[0]['user_id']+0)
        ->get();

        return response()->json(['booker_phone'=>$booker_phone,'services'=>$services,'patients'=>$patients,'booking_details'=>$booking_table]);
    }

    public function phleboAssignedDetails($id){//booking_id is passed to this table
        //phlebo details - assignment table, phlebo table
            //phlebo_id - assignemt table
            //first_name - phlebo table
            //sirname - phlebo table
            //lastname - phlebo table
            //gender - phlebo table
            //qualifications - phlebo table
            //phone - phlebo table

        //phlebo_details
        //select phlebo_id from assignment table where booking_id is as given
        $phlebo_id = Assignment::select('phlebo_id')
        ->where('id',$id)
        ->get()[0];
        //select first_name, sirname, last_name, qualifications,phone from phlebos where phlebo id is as given
        $phlebo_details = Phlebo::select('first_name','sirname','last_name','gender','qualifications')
        ->where('id',$phlebo_id['phlebo_id']+0)
        ->get();
        return response()->json($phlebo_details);

    }

    public function phleboActiveDetails($id){//booking id is passed to this method
        /** We want...
         * Plebo details - assignment table, phlebo table
         * phlebo_id - assignmet table
         *      first_name - phlebo table
         *      sirname - phlebo table
         *      lastname - phlebo table
         *      gender - phlebo table
         *      qualifications - phlebo table
         *      phone - phlebo table
         * lat - phlebo_position_table
         * lon - phlebo_postitin_table
         */
        //phlebo_details
        //select phlebo_id from assignment table where booking_id is as given
        $phlebo_id = Assignment::select('phlebo_id')
        ->where('booking_id',$id)
        ->get()[0];
        $phlebo_details = Phlebo::select('first_name','sirname','last_name','gender','qualifications','phone')
        ->where('id',$phlebo_id['phlebo_id']+0)
        ->get();
        return response()->json($phlebo_details);
    }

    public function findFreeBookingSlot(){
        //check scheduled dates
        //return all free slots of particular day
        return response()->json(['status_code'=>200, 'message'=>'Free Time Slots coming soon']);
    }
    public function sendTestSms(Request $request){

        // $rules = [
        //     'phone' => 'required|min:10|max:10',
        //     'message'=> 'required'
        // ];
        // $val = Validator::make($request->all(),$rules);
        // if($val->fails()){
        //     return response()->json($val->errors());
        // }

        $url = 'https://ujumbesms.co.ke/api/messaging';
        $api_key = 'OGIxOWY3MjIyNjc2MWZmZWYyNjBiMjc1ODU3MWNh';
        $email = 'info@meditestdiagnostic.com';
        $number = $request->phone;
        $message = $request->message;
        $sender = "MEDITEST-DEV-TEST";

        $data = [
            "data" => [[
                    "message_bag" => [
                        "numbers"=>$number,
                        "message"=>$message,
                        "sender"=>$sender
                        ]
                    ]]
                ];

        $sms_data = json_encode($data);

        $curl= curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $sms_data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: '.strlen($sms_data),
            'X-Authorization: '.$api_key,
            'email: '.$email
        ));
        $resp = curl_exec($curl);
        if($resp === false){
            $err = 'Curl error: '.curl_error($curl);
            curl_close($curl);
            return response()->json($err);
        }else{
            curl_close($curl);
            return response()->json(json_decode($resp));
        }

    }

}
