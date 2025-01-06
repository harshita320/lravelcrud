<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Mail\Websitemail;
use App\Models\Admin;
class AdminController extends Controller
{
    public function AdminDashboard(){
        return view('admin.index');
    }


    public function AdminLogin(){
        return view('admin.login');
    }

    public function AdminLoginSubmit(Request $request){
     
           $request->validate([
            'email' =>  'required|email',
            'password' => 'required',
           ]);

            $check = $request->all();
            $data=[
                'email' => $check['email'],
                'password' =>$check['password']
            ];
            if(Auth::guard('admin')->attempt($data)){
                return redirect()->route('admin.dashboard')->with('success', 'login successfully');
            }
            else{
                return redirect()->route('admin.login')->with('error', 'invalid credential');

            }

    }


    public function AdminLogout(){
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login')->with('success', 'successfully logout ');

    }


    public function AdminForgetPassword(){
        return view('admin.forget_password');
    }

    public function AdminPasswordSubmit(Request $request){
$request->validate([
    'email'=>'required|email'
]);

$admin_data = Admin::where('email',$request->email)->first();

if(!$admin_data){
    return redirect()->back()->with('error','email not found');
}
$token = hash('sha256', time());
$admin_data->token = $token;
$admin_data->update();
$reset_link = url('admin/reset-password/'.$token.'/'.$request->email);
$subject = "reset password";
$message ="please click on below link to reset password <br>";
$message .= "<a href='".$reset_link."'>click here</a>";
\Mail::to($request->email)->send(new Websitemail($subject, $message));
return redirect()->back()->with('success', ' reset pass word link on your mail  ');
    }





    public function AdminResetPassword($token,$email){
        $admin_data= Admin::where('email',$email)->where('token',$token)->first();

        if(!$admin_data){
            return redirect()->route('admin.login')->with('error', 'invalid token or email');
        }
        return view('admin.reset_password',compact('token','email'));

    }


    public function AdminResetPasswordSubmit(Request $request){
            $request->validate([
                'password' =>'required',
                'password_confirmation' => 'required|same:password',
            ]);

            $admin_data= Admin::where('email', $request->email)->where('token',$request->token)->first();
            $admin_data->password = Hash::make($request->password);
            $admin_data->token= "";
            $admin_data->update();

            return redirect()->route('admin.login')->with('success','password reset successfully');
    }

    public function AdminProfile(){
        $id = Auth::guard('admin')->id();
        $profileData = Admin::find($id);
        return view('admin.admin_profile', compact('profileData'));
    }


    public function AdminProfileStore(Request $request){
        $id = Auth::guard('admin')->id();
        $Data = Admin::find($id);
        $Data->name = $request->name;
        $Data->email = $request->email;

        $Data->phone = $request->phone;
        $Data->address = $request->address;
        $oldPhotoPath = $Data->photo;

         if($request->hasFile('photo')){
            $file = $request->file('photo');
            $filename= time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('upload/admin_images'),$filename);
            $Data->photo = $filename;


            if( $oldPhotoPath &&  $oldPhotoPath !==$filename){
                $this->deleteOldImage( $oldPhotoPath);
            }
         }
         $Data->save();

         $notification = array(
            'message' => 'profile updated successfully',
            'alert-type' => 'success'
         );
         return redirect()->back()->with($notification);
    }

    private function deleteOldImage(string $oldPhotoPath): void{
        $fullPath = public_path('upload/admin_images/'. $oldPhotoPath);
        if(file_exists($fullPath)){
            unlink($fullPath);
        }
    }

    //end private method 

    public function AdminChangePassword(){
        $id = Auth::guard('admin')->id();
        $profileData = Admin::find($id);
        return view('admin.admin_change_Password',compact('profileData'));
     }



     public function AdminPasswordUpdate(Request $request){
        $admin = Auth::guard('admin')->user();
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed'
        ]);

        if (!Hash::check($request->old_password,$admin->password)) {
            $notification = array(
                'message' => 'Old Password Does not Match!',
                'alert-type' => 'error'
            );
            return back()->with($notification);
        }
        /// Update the new password 
        Admin::whereId($admin->id)->update([
            'password' => Hash::make($request->new_password)
        ]);
                $notification = array(
                'message' => 'Password Change Successfully',
                'alert-type' => 'success'
            );
            return back()->with($notification);
     }
}
