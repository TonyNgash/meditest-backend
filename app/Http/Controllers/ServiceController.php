<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Http\Resources\ServiceResource;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$service = Service::where('status','active')->paginate(4);
        $service = Service::all()->where('status','active');
        return ServiceResource::collection($service);
    }
    public function getAllServices(){
        $service = Service::all();
        return response()->json(['data'=>$service]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'test_code'=>'required|min:3|max:10',
            'test_name'=>'required|min:3',
            'test_price'=>'required|numeric',
            'test_constituents'=>'nullable',
            'test_category_id'=>'required',
            'test_prerequisites'=>'nullable',
            'test_report_availability'=>'nullable',
            'test_desc'=>'nullable',
            'home'=>'required',
            'creator_job_id'=>'required',
            'status'=>'required',
        ];

        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            return response()->json(['status_code'=>400, 'message'=>$validator->errors()]);
        }
        $service = new Service;
        $service->test_name = $request->test_name;
        $service->test_desc = $request->test_desc;
        $service->test_price = $request->test_price;
        $service->image_path = $request->image_path;
        $service->creator_job_id = $request->creator_job_id;
        $service->status = $request->status;
        $res = $service->save();
        if($res){
            return response()->json(['status_code'=>200, 'message'=>'Service Created Successfuly']);
        }else{
            return response()->json(['status_code'=>400, 'message'=>"Something went wrong"]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $service = Service::findOrFail($id);
        return new ServiceResource($service);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        $service->test_name = $request->test_name;
        $service->test_desc = $request->test_desc;
        $service->test_price = $request->test_price;
        $service->image_path = $request->image_path;
        $service->creator_job_id = $request->creator_job_id;
        $service->status = $request->status;
        $res = $service->save();
        if($res){
            return new ServiceResource($service);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $res = $service->delete();
        if($res){
            return new ServiceResource($service);
        }
    }
}
