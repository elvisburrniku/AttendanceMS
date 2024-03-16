<?php

namespace App\Http\Controllers;

use App\Http\Requests\AreaRec;
use RealRashid\SweetAlert\Facades\Alert;
use App\Helpers\ApiHelper;
use App\Helpers\ApiUrlHelper;

class AreaController extends Controller
{
   
    public function index()
    {
        $api = new ApiHelper();

        $api->url(ApiUrlHelper::url('Area'))->get();
        
        $areas = $api->getData()->map(function($e) {
            return (object) $e;
        });

        return view('admin.area')->with(['areas' => $areas, 'areas_count' => $api->response->get('count')]);
    }

    public function store(AreaRec $request)
    {
        $request->validated();

        $api = new ApiHelper();

        $api->url(ApiUrlHelper::url('Area'));

        $employee = $api->post($request->all());

        flash()->success('Success','Area Record has been created successfully !');

        return redirect()->route('areas.index')->with('success');
    }

 
    public function update(AreaRec $request, $id)
    {
        $request->validated();

        $api = new ApiHelper();

        $api->url(ApiUrlHelper::url('Area.Update'));

        $employee = $api->put($id, $request->all());

        flash()->success('Success','Area Record has been Updated successfully !');

        return redirect()->route('areas.index')->with('success');
    }


    public function destroy($id)
    {
        $api = new ApiHelper();

        $api->url(ApiUrlHelper::url('Area.Update'));

        $api->delete($id);

        flash()->success('Success','Area Record has been Deleted successfully !');
        return redirect()->route('areas.index')->with('success');
    }
}
