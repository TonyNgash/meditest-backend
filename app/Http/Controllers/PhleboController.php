<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Booking;
use App\Models\PhleboPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class PhleboController extends Controller
{
    public function phleboMarkAsActive($assignment_id)//receives assignment_id
    {
        /***
         * when phlebo marks as active,
         * update assignment.status to active
         * find booking_id from assignment table
         * update booking.status to active
         *
         */

        $assignment = Assignment::find($assignment_id);
        $assignment->status = 'active';
        $res = $assignment->save();
        $msg= "";
        if($res){
            $msg.=':[assignment marked active]:';
        }
        //select booking_id from assignment table
        $booking_id = Assignment::select('booking_id')
        ->where('id',$assignment_id)
        ->get();
        $booking = Booking::find($booking_id[0]['booking_id']+0);
        $booking->status = 'active';
        $res2 = $booking->save();
        if($res2){
            $msg .=':[booking marked active]:';
        }
        return response()->json(['msg'=>'Successfully marked as active']);


    }
    public function phleboMarkAsDone($assignment_id)//receives assignment id
    {
        /**
         * update assignment.status to done
         * get booking_id from assignment table
         * update booking.status to done and booking.old_new to false
         */
        $assignment = Assignment::find($assignment_id);
        $assignment->status = 'done';
        $res = $assignment->save();
        $msg= "";
        if($res){
            $msg .='assignment marked done';
        }
        $booking_id = Assignment::select('booking_id')
        ->where('id',$assignment_id)
        ->get();

        $booking = Booking::find($booking_id[0]['booking_id']+0);
        $booking->status = 'done';
        $res2 = $booking->save();
        if($res2){
            $msg .=':[booking marked done]:';
        }
        return response()->json(['msg'=>'Successfully marked as done']);
    }
    public function phleboMarkAsFailed($assignment_id){
        /**
         * update assingment.status to failed
         * get booking_id from booking table
         * update booking.status to failed
         *
         */
        $assignment = Assignment::find($assignment_id);
        $assignment->status = 'failed';
        $res = $assignment->save();
        $msg="";
        if($res){
            $msg .= "[assignment marked failed]";
        }

        $booking_id = Assignment::select('booking_id')
        ->where('id',$assignment_id)
        ->get();

        $booking = Booking::find($booking_id[0]['booking_id']+0);
        $booking->status = 'failed';
        $res2 = $booking->save();
        if($res2){
            $msg .= "[assignment marked failed]";
        }
        return response()->json(['msg'=>'Successfully marked as Failed']);
    }
    public function phleboPostCurrent(Request $request)
    {

        $rules = [
            'phlebo_id'=>'required|numeric',
            'assignment_id'=>'nullable|numeric',
            'lat'=>'required|numeric',
            'lon'=>'required|numeric'
        ];
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            return response()->json(['status_code'=>400, 'message'=>$validator->errors()]);
        }
        $phleboPosition = new PhleboPosition();
        $phleboPosition->phlebo_id = $request->phlebo_id;
        $phleboPosition->assignment_id = $request->assignment_id;
        $phleboPosition->lat = $request->lat;
        $phleboPosition->lon = $request->lon;
        $res = $phleboPosition->save();
        $msg = "";
        if($res){
            $msg .= "postition recorded";
        }else{
            $msg .= "postition recording failed";
        }
        return response()->json($msg);
    }
    public function phleboGetCurrent($phlebo_id){

        $lat_lon = DB::table('phlebo_positions')
        ->where('phlebo_id',$phlebo_id)
        ->latest('created_at')
        ->first();

        return response()->json($lat_lon);

    }
}
