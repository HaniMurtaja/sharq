<?php

namespace App\Http\Controllers\Admin;

use App\Enum\CalculationMethod;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\ClientsGroupNew;
use App\Models\ClientsGroupNews;
use App\Models\Governorates;
use App\Models\ClientsGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class ClientsGroupNewController extends Controller
{

    public function index(){
        // if (! Gate::allows('')) {
        //     return abort(401);
        // }
        $items = ClientsGroup::orderby('id','desc')->paginate(50);
        return view('admin.pages.ClientsGroupNew.index', compact( 'items'));
    }
    public function create(){
        // if (! Gate::allows('')) {
        //     return abort(401);
        // }
        $calculationMethod = CalculationMethod::values();
        return view('admin.pages.ClientsGroupNew.add',compact('calculationMethod'));
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

        ClientsGroup::create($inputs);
        session()->flash('success', __('messages.Successfully'));
        return redirect()->route('ClientsGroupNew');
    }
    public function edit($id){
        // if (! Gate::allows('')) {
        //     return abort(401);
        // }
        $item = ClientsGroup::find($id);

        return view('admin.pages.ClientsGroupNew.edit',compact('item'));
    }
    public function update($id,Request $request){
        // if (! Gate::allows('')) {
        //     return abort(401);
        // }

        $inputs = $request->except(['_token']);
        $clients = ClientsGroup::findOrFail($id);
        $validatedData = $request->validate(
            [
                'name'      => 'required',

            ], [], [
            'name'     => trans('messages.name'),

        ]);
        $clients->update($inputs);
        session()->flash('success', trans('messages.updated_record'));
          return redirect()->route('ClientsGroupNew');
    }
    public function destroy($id)
    {
        // if (! Gate::allows('')) {
        //     return abort(401);
        // }
        $item = ClientsGroup::findOrFail($id);
       // $item->delete();
        session()->flash('success', trans('messages.deleted_record'));
     return redirect()->route('ClientsGroupNew');
    }

}
