<?php

namespace App\Http\Controllers;

use App\Http\Requests\PositionRec;
use RealRashid\SweetAlert\Facades\Alert;
use App\Helpers\ApiHelper;
use App\Helpers\ApiUrlHelper;

class PositionController extends Controller
{
   
    public function index()
    {
        $pos_api = new ApiHelper();

        $pos_api->url(ApiUrlHelper::url('Position'))->get();
        
        $positions = $pos_api->getData()->map(function($e) {
            return (object) $e;
        });

        return view('admin.position')->with(['positions' => $positions, 'positions_count' => $pos_api->response->get('count')]);
    }

    public function store(PositionRec $request)
    {
        $request->validated();

        $api = new ApiHelper();

        $api->url(ApiUrlHelper::url('Position'));

        $employee = $api->post($request->all());

        flash()->success('Success','Position Record has been created successfully !');

        return redirect()->route('positions.index')->with('success');
    }

 
    public function update(PositionRec $request, $id)
    {
        $request->validated();

        $api = new ApiHelper();

        $api->url(ApiUrlHelper::url('Position.Update'));

        $employee = $api->put($id, $request->all());

        flash()->success('Success','Position Record has been Updated successfully !');

        return redirect()->route('positions.index')->with('success');
    }


    public function destroy($id)
    {
        $api = new ApiHelper();

        $api->url(ApiUrlHelper::url('Position.Update'));

        $api->delete($id);

        flash()->success('Success','Position Record has been Deleted successfully !');
        return redirect()->route('positions.index')->with('success');
    }
}
