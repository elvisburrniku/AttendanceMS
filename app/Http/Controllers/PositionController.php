<?php

namespace App\Http\Controllers;

use App\Http\Requests\PositionRec;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\Position;

class PositionController extends Controller
{
   
    public function index()
    {
        $positions = Position::with('parentPosition')->simplePaginate(100);

        return view('admin.position')->with(['positions' => $positions, 'positions_count' => $positions->count() ]);
    }

    public function store(PositionRec $request)
    {
        $request->validated();

        $position = new Position();
        $position->position_code = $request->position_code;
        $position->position_name = $request->position_name;
        $position->parent_position_id = $request->parent_position;
        $position->company_id = 1; // Default company ID
        $position->is_default = false;
        $position->save();

        flash()->success('Success','Position Record has been created successfully !');

        return redirect()->route('positions.index');
    }

 
    public function update(PositionRec $request, $id)
    {
        $request->validated();

        $position = Position::findOrFail($id);
        $position->position_code = $request->position_code;
        $position->position_name = $request->position_name;
        $position->parent_position_id = $request->parent_position;
        $position->save();

        flash()->success('Success','Position Record has been Updated successfully !');

        return redirect()->route('positions.index');
    }


    public function destroy($id)
    {
        $position = Position::findOrFail($id);
        $position->delete();

        flash()->success('Success','Position Record has been Deleted successfully !');
        return redirect()->route('positions.index');
    }
}
