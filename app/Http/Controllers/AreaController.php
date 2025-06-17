<?php

namespace App\Http\Controllers;

use App\Http\Requests\AreaRec;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\Area;

class AreaController extends Controller
{
   
    public function index()
    {
       $areas = Area::simplePaginate(100);

       return view('admin.area')->with(['areas' => $areas, 'areas_count' => $areas->count()]);
    }

    public function store(AreaRec $request)
    {
        $request->validated();

        $area = new Area();
        $area->area_code = $request->area_code;
        $area->area_name = $request->area_name;
        $area->parent_area_id = $request->parent_area;
        $area->company_id = 1; // Default company ID
        $area->is_default = false;
        $area->save();

        flash()->success('Success','Area Record has been created successfully !');

        return redirect()->route('areas.index');
    }

 
    public function update(AreaRec $request, $id)
    {
        $request->validated();

        $area = Area::findOrFail($id);
        $area->area_code = $request->area_code;
        $area->area_name = $request->area_name;
        $area->parent_area_id = $request->parent_area;
        $area->save();

        flash()->success('Success','Area Record has been Updated successfully !');

        return redirect()->route('areas.index');
    }


    public function destroy($id)
    {
        $area = Area::findOrFail($id);
        $area->delete();

        flash()->success('Success','Area Record has been Deleted successfully !');
        return redirect()->route('areas.index');
    }
}
