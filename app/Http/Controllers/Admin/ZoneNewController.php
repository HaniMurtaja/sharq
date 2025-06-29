<?php

namespace App\Http\Controllers\Admin;

use App\Enum\CalculationMethod;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\ZoneNew;
use App\Models\ZoneNews;
use App\Models\Governorates;
use App\Models\Zone;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class ZoneNewController extends Controller
{

    public function index(){
        // if (! Gate::allows('')) {
        //     return abort(401);
        // }
        $items = Zone::orderby('id','desc')->paginate(50);
        return view('admin.pages.ZoneNew.index', compact( 'items'));
    }
    public function create(){
        // if (! Gate::allows('')) {
        //     return abort(401);
        // }

        return view('admin.pages.ZoneNew.add');
    }
    public function store(Request  $request){
        // if (! Gate::allows('')) {
        //     return abort(401);
        // }
        $validatedData = $request->validate(
            [
                'name'=>'required',
            ], [], [

        ]);
        $inputs = $request->except(['_token']);

        Zone::create($inputs);
        session()->flash('success', __('messages.Successfully'));
        return redirect()->route('ZoneNew');
    }
    public function edit($id){
        // if (! Gate::allows('')) {
        //     return abort(401);
        // }
        $item = Zone::find($id);

        return view('admin.pages.ZoneNew.edit',compact('item'));
    }
    public function update($id,Request $request){
        // if (! Gate::allows('')) {
        //     return abort(401);
        // }

        $inputs = $request->except(['_token']);
        $clients = Zone::findOrFail($id);
        $validatedData = $request->validate(
            [
                'name'      => 'required',

            ], [], [
            'name'     => trans('messages.name'),

        ]);
        $clients->update($inputs);
        session()->flash('success', trans('messages.updated_record'));
          return redirect()->route('ZoneNew');
    }
    public function destroy($id)
    {
        // if (! Gate::allows('')) {
        //     return abort(401);
        // }
        $item = Zone::findOrFail($id);
       // $item->delete();
        session()->flash('success', trans('messages.deleted_record'));
     return redirect()->route('ZoneNew');
    }

}
