<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AdminRequests\PlanStoreRequest;
use App\Http\Requests\AdminRequests\PlanUpdateRequest;
use App\Models\Plan;

class AdminPlanController extends Controller
{
    public function index()
    {
        // Your admin dashboard logic here
        $PlanInfo = Plan::where('Status','=','0')->paginate(10);
        return view("admin-view.plan.index", compact("PlanInfo"));
    }

    public function store(PlanStoreRequest $request)
    {
        $name=$request->name;
        $chinese_name=$request->chinese_name;
        $korean_name=$request->korean_name;
        $monthly_price=$request->monthly_price;
        $monthly_famillies=$request->monthly_famillies;
        $monthly_members=$request->monthly_members;
        $monthly_private=$request->monthly_private;
        $yearly_price=$request->yearly_price;
        $yearly_famillies=$request->yearly_famillies;
        $yearly_members=$request->yearly_members;
        $yearly_private=$request->yearly_private;

        $pdfexport=$request->pdfexport;
        $heritatefamilies=$request->heritatefamilies;
        $support=$request->support;
        $showads=$request->showads;
        $createalbums=$request->createalbums;

        if ($pdfexport == NULL) {
         $pdfexport = '0';
        }
        if ($heritatefamilies == NULL) {
         $heritatefamilies = '0';
        }
        if ($support == NULL) {
         $support = '0';
        }
        if ($showads == NULL) {
         $showads = '0';   
        }
        if ($createalbums == NULL) {
         $createalbums = '0';   
        }


        $now = date('Y-m-d H:i:s');
        $update_date = date('Y-m-d H:i:s');


        try {
          $values = array("chinese_name"=>$chinese_name,"korean_name"=>$korean_name,'name' => $name , 'monthly_price' => $monthly_price , 'monthly_famillies' => $monthly_famillies , 'monthly_members' => $monthly_members , 'monthly_private' => $monthly_private , 'yearly_price' => $yearly_price , 'yearly_famillies' => $yearly_famillies , 'yearly_members' => $yearly_members , 'yearly_private' => $yearly_private , 'pdfexport' => $pdfexport , 'heritatefamilies' => $heritatefamilies , 'support' => $support , 'showads' => $showads , 'createalbums' => $createalbums , 'created_at' => $now , 'updated_at' => $update_date);
          Plan::insert($values);
            return response()->json(['message' => 'Add Plan successfully'], 200);
        } catch(\Illuminate\Database\QueryException $ex){
          return Response()->json([

          ]);
        }
    }

    public function delete($id)
    {
      try {
        $plan = Plan::findorFail($id);
        $plan->Status = 2;
        $plan->save();
        return response()->json(['message' => 'Delete plan successfully'], 200);

      } catch(\Illuminate\Database\QueryException $ex){
        
      }
    }

    public function edit($id)
    {
      $PlanEditInfo = Plan::where('id','=',$id)->get();
      return view("admin-view.plan.edit", compact("PlanEditInfo"));
    }

    public function update(PlanUpdateRequest $request)
    {
        $edit_name=$request->edit_name;
         $chinese_name=$request->chinese_name;
        $korean_name=$request->korean_name;
        $edit_monthly_price=$request->edit_monthly_price;
        $edit_monthly_famillies=$request->edit_monthly_famillies;
        $edit_monthly_members=$request->edit_monthly_members;
        $edit_monthly_private=$request->edit_monthly_private;
        $edit_yearly_price=$request->edit_yearly_price;
        $edit_yearly_famillies=$request->edit_yearly_famillies;
        $edit_yearly_members=$request->edit_yearly_members;
        $edit_yearly_private=$request->edit_yearly_private;

        $edit_pdfexport=$request->edit_pdfexport;
        $edit_heritatefamilies=$request->edit_heritatefamilies;
        $edit_support=$request->edit_support;
        $edit_showads=$request->edit_showads;
        $edit_createalbums=$request->edit_createalbums;

        $edit_id=$request->edit_id;
        $update_date = date('Y-m-d H:i:s');

      try {
        Plan::where('id', $edit_id)
         ->update([
             'name' => $edit_name,
             'chinese_name' => $chinese_name,
             'korean_name' => $korean_name,
             'monthly_price' => $edit_monthly_price,
             'monthly_famillies' => $edit_monthly_famillies,
             'monthly_members' => $edit_monthly_members,
             'monthly_private' => $edit_monthly_private,
             'yearly_price' => $edit_yearly_price,
             'yearly_famillies' => $edit_yearly_famillies,
             'yearly_members' => $edit_yearly_members,
             'yearly_private' => $edit_yearly_private,
             'pdfexport' => $edit_pdfexport,
             'heritatefamilies' => $edit_heritatefamilies,
             'support' => $edit_support,
             'showads' => $edit_showads,
             'createalbums' => $edit_createalbums,
             'updated_at' => $update_date,

          ]);
         return response()->json(['message' => 'Upadte Plan successfully'], 200);

      } catch(\Illuminate\Database\QueryException $ex){
        
      }
    }
}
