<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Gateway;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6',
            'retypepassword' => 'required|same:password',
        ]);

        $admin = Auth::guard("admin")->user();
         $admin = Admin::find($admin->id);
        $admin->password = Hash::make($request->password);
        $admin->save();

        return response()->json(['success' => true, 'message' => 'Password updated successfully']);
    }
    public function updateProfile(Request $request)
    {
        // dd($request->all());
        $admin = Auth::guard("admin")->user();
        $admin = Admin::find($admin->id);
        $admin->name          = $request->name;
        $admin->email         = $request->email;
        $admin->language      = $request->language;
        $admin->company_name  = $request->company_name;
        $admin->dba_name      = $request->dba_name;
        $admin->address_1      = $request->address1;
        $admin->address_2      = $request->address2;
        $admin->zip_code      = $request->zip_code;
        $admin->city          = $request->city;
        $admin->state         = $request->state;
        $admin->country       = $request->country;
        $admin->phone         = $request->phone;
        $admin->mobile_number = $request->mobile_number;
        $admin->fax           = $request->fax;
        // dd($admin);
        $admin->save();

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
    public function index()
    {
        $UserInfo = User::where('Status', '!=', '2')->paginate(10);
        return view("admin-view.user.index", compact("UserInfo"));
    }

    public function deactivate($id)
    {
        try {
            $User = User::findorFail($id);
            $User->Status = 1;
            $User->save();
            return response()->json(['message' => 'User deactivated successfully'], 200);
        } catch (\Illuminate\Database\QueryException $ex) {
        }
    }

    public function activate($id)
    {
        try {
            $User = User::findorFail($id);
            $User->Status = 0;  // Set the status to 0 for activation
            $User->save();
            return response()->json(['message' => 'User activated successfully'], 200);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['message' => 'Error occurred while activating the user'], 500);
        }
    }
    public function update(Request $request)
    {
        $gateway = Gateway::first() ?? new Gateway();

        $gateway->cod_status           = $request->cod_status === 'on' ? 1 : 0;
        $gateway->digital_status       = $request->digital_status === 'on' ? 1 : 0;
        $gateway->paypal_status        = $request->paypal_toggle === 'on' ? 1 : 0;
        $gateway->paypal_client_id     = $request->paypal_client_id;
        $gateway->paypal_secret        = $request->paypal_secret;
        $gateway->stripe_status        = $request->stripe_toggle === 'on' ? 1 : 0;
        $gateway->stripe_publish_key   = $request->stripe_publish_key;
        $gateway->stripe_api_key       = $request->stripe_api_key;

        $gateway->save();

        return redirect()->back()->with('success', 'Gateway settings updated.');
    }
}
