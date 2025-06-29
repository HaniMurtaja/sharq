<?php

namespace App\Http\Controllers\Admin;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Branchs;
use App\Models\Governorates;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BranchController extends Controller
{

    public function index(){
        // if (! Gate::allows('')) {
        //     return abort(401);
        // }
        $items = Branch::orderby('id','desc')->paginate(50);
        return view('admin.pages.branch.index', compact( 'items'));
    }
    public function create(){
        // if (! Gate::allows('')) {
        //     return abort(401);
        // }

        return view('admin.pages.branch.add');
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
        $inputs['driver_id']=User::first()->id;
        Branch::create($inputs);
        session()->flash('success', __('messages.Successfully'));
        return redirect()->route('branchnew');
    }
    public function edit($id){
        // if (! Gate::allows('')) {
        //     return abort(401);
        // }
        $item = Branch::find($id);

        return view('admin.pages.branch.edit',compact('item'));
    }
    public function update($id,Request $request){
        // if (! Gate::allows('')) {
        //     return abort(401);
        // }

        $inputs = $request->except(['_token']);
        $clients = Branch::findOrFail($id);
        $validatedData = $request->validate(
            [
                'name'      => 'required',

            ], [], [
            'name'     => trans('messages.name'),

        ]);
        $clients->update($inputs);
        session()->flash('success', trans('messages.updated_record'));
          return redirect()->route('branchnew');
    }
    public function destroy($id)
    {
        // if (! Gate::allows('')) {
        //     return abort(401);
        // }
        $item = Branch::findOrFail($id);
       // $item->delete();
        session()->flash('success', trans('messages.deleted_record'));
     return redirect()->route('branchnew');
    }

}
