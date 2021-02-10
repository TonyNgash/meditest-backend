<?php

namespace App\Http\Controllers;

use App\Http\Resources\PhleboResource;
use App\Models\Assignment;
use App\Models\BookedPatient;
use App\Models\BookedService;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Dependant;
use App\Models\Phlebo;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{
    /**
     * Call center wants to see all new bookings
     */
    public function getAllBookings(){
        $bookings = DB::table('booking')
        ->join('users','booking.user_id','=','users.id')
        ->select('booking.id', 'booking.total_amount', 'booking.scheduled_date', 'booking.scheduled_time', 'users.first_name', 'users.sirname')
        ->where('seen',0)
        ->get();

        return response()->json(['bookings'=>$bookings]);
    }
    public function getBookingDetails($id){//pass booking_id to this method
        //find the booker from the booking table given the booking id.
        $user_id = Booking::where('id',$id)->first()->user_id;//get the user_id from booking table. this is the user who made the booking.
        $booker = User::select('first_name','sirname','last_name','gender','email','phone','address')->where('id',$user_id)->get();
        //Find out if the booking is self or others
        $self = Booking::where('id',$id)->first()->self;//get the value of self where the given id matches.
        $patient_details=array();//array to store patient information.
        if($self){//if self is true, the user who created this booking is the patient
            //get user information from user table and store it in the array
            //$patient_details = $booker;//User::select('first_name','sirname','last_name','gender','address')->where('id',$user_id)->get();
            array_push($patient_details,User::select('first_name','sirname','last_name','gender')->where('id',$user_id)->get());
        }else{//if not, use booking_id to query booked_patients table to get dependant ids.
            $booked_patients = BookedPatient::where('booking_id',$id)->get();
            $patients = array();
            foreach($booked_patients as $booked_patient){
                //Get each dependant using dependant ids
                //array_push($patients,$booked_patient['dependant_id']);
                array_push($patients,Dependant::select('first_name','sirname','last_name','gender','address','relationship')->where('id',$booked_patient['dependant_id'])->get());
            }
            $patient_details = $patients;
        }
        //get service_ids from booked services table to use in getting the services from services table
        $booked_services = BookedService::where('booking_id',$id)->get();
        $services=[];
        foreach($booked_services as $booked_service){
            array_push($services,Service::where('id',$booked_service['service_id'])->get());
        }
        $stats = ['patients'=>count($patient_details),'services'=>count($services)];
        return response()->json(['booker'=>$booker,'patient'=>$patient_details,'services'=>$services,'stats'=>$stats]);
    }

    public function getAllUsers(){
        $users = User::all();
        return response()->json($users);
    }
    public function getAllPhlebos(){
        $phlebos = Phlebo::all();
        return PhleboResource::collection($phlebos);
    }
    public function assignToPhlebo(Request $request){
        $rules = [
            'booking_id'=>'required|numeric',
            'phlebo_id'=>'required|numeric',
            'scheduled_time'=>'required|date_format:H:i:s'
        ];
        $validate = Validator::make($request->all(),$rules);
        if($validate->fails()){
            return response()->json($validate->errors());
        }
        $booking = Booking::find($request->booking_id);
        $booking->seen = true;
        $booking->status = 'assigned';
        $booking->save();
        $assignment = new Assignment();
        $assignment->booking_id=$request->booking_id;
        $assignment->phlebo_id=$request->phlebo_id;
        $assignment->scheduled_time=$request->scheduled_time;
        $res = $assignment->save();
        if($res){
            return response()->json(['status'=>200,'message'=>'Assigned to phlebo']);
        }

    }
    public function getPhleboAssignments($id){//pass phlebo id

        //select booking_id(s) from assignment table where phlebo_id and status as given.
        $booking_ids = Assignment::select('booking_id')->where('phlebo_id',$id)->where('status','pending')->get();
        //with booking Ids...
        //select scheduled_time, locality,admin_area,sub_admin_area from booking_table where booking_id(s)
        $assignments =[];
        for($i = 0; $i < count($booking_ids); $i++){
            $assignment = Booking::select('id','scheduled_date','scheduled_time','locality','admin_area','sub_admin_area')
            ->where('id',$booking_ids[$i]['booking_id'])
            ->get();
            $assignments[] = $assignment;
        }
        return response()->json($assignments);

    }
    public function getPhleboAssignments2($id){

        $assignments=[];

            $assignment = DB::table('assignments')
            ->select('assignments.id','booking.scheduled_date','booking.scheduled_time','booking.locality','booking.admin_area','booking.sub_admin_area')
            ->join('booking','booking.id','=','assignments.booking_id')
            ->get();
            $assignments[]=$assignment;

        return response()->json($assignments);
    }
    public function phleboGetAssignmentDetails($id){//pass assignment id
        //we want a booking id from the assignment table: one assignment equals one booking.
        $booking_id = Assignment::select('booking_id')->where('id',$id)->get();

        //booking
        //with the booking id we get above we get the booking details from the booking table
        $booking = Booking::select('user_id','self','paid','scheduled_date','scheduled_time','total_amount','phone','address_desc','lat','lon','locality','admin_area','sub_admin_area')
        ->where('id',$booking_id[0]['booking_id']+0)
        ->get();

        //phone(s)
        //we get phone from user table and from booking table
        $phones = [];
        $phones[] = User::select('phone')->where('id',$booking[0]['user_id']+0)->get()[0]['phone'];
        $phones[] = Booking::select('phone')->where('user_id',$booking[0]['user_id']+0)->get()[0]['phone'];

        //TODO: we check if paid or not and remove total amount from $booking array

        //patient(s)
        //We check self to determine the patient
        $dependant_ids=[];
        $dependant_details=[];
        if($booking[0]['self']){//if self is true...
            //the patient is the booker
            $patient = User::select('first_name','sirname','last_name','gender','dob')
            ->where('id',$booking[0]['user_id'])
            ->get();
            $dependant_details=$patient;
        }else{//if self is false
            //the patient is a dependant or dependants
            //we use booking id to get dependant(s) id(s) from booked patients table
            $dependant_id = BookedPatient::select('dependant_id')->where('booking_id',$booking_id[0]['booking_id']+0)->get();

            array_push($dependant_ids,$dependant_id);
            //once we have the ids....
            //we grab their details from dependants table

            //since dependant id can be more than one we loop through the number of ids
            //while grabing each dependant's details
            for($i = 0; $i < count($dependant_ids[0]); $i++){
                $dependant=Dependant::select('first_name','sirname','last_name','gender','dob')
                ->where('id',$dependant_ids[0][$i]['dependant_id']+0)
                ->get();
                $dependant_details[]=$dependant[0];
            }
        }

        //service(s)
        //with booking id, we get service id(s) from booked services table
        $service_ids = BookedService::select('service_id')->where('booking_id',$booking_id[0]['booking_id'])->get();
        $services = [];
        for($j = 0; $j < count($service_ids); $j++){
            $services[] = Service::select('test_code','test_name','test_constituents','test_category','test_prerequisites','test_report_availability','test_desc')
            ->where('id',$service_ids[$j]['service_id'])
            ->get()[0];
        }




        return response()->json(['phones'=>$phones,'booking'=>$booking[0],'patients'=>$dependant_details,'services'=>$services]);


    }

}
