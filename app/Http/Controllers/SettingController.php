<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Login;
use App\Models\User;
use App\Models\Block;
use App\Models\EmailChange;
use App\Mail\VerifyMail;
use Illuminate\Support\Facades\Mail;

class SettingController extends Controller
{
    function password()
    {
        return view('setting.password');
    }
    function passwordChange(Request $request)
    {
        $session = session('user');
        $loginId = $session->login_id;
        $userName = $session->user_name;
        $oldPassword = md5($request->oldPassword);
        $newPassword = md5($request->newPassword);

        $user = Login::where('user_name', '=', $userName)
                    ->where('id', '=', $loginId)
                    ->first();

        if($user->password == $oldPassword)
        {
            $user->password = $newPassword;
            $user->update();

            return back()
                ->with('success','You successfully Changed your Password');
        }
        else{
            return back()
                ->with('error', 'Your Old Password is incorrect');
        }

    }

    function email()
    {
        $session = session('user');
        $loginId = $session->login_id;
        $userName = $session->user_name;

        $user = Login::where('user_name', '=', $userName)
                    ->where('id', '=', $loginId)
                    ->first();
                        
        return view('setting.email')
                ->withUserData($user);
    }
    function sendEmail(Request $request)
    {
        $session = session('user');
        $loginId = $session->login_id;
        $email = $request->email;
        $token = md5(time().rand());

        $ec = new EmailChange;
        $ec->login_id = $loginId;
        $ec->email = $email;
        $ec->token = $token;
        $ec->save();        

        if($ec){
            $route = route('changeEmail');
            $emailLink = $route.'?token='.$token;

            Mail::to($email)->send(new VerifyMail($emailLink));

            return back()
            ->with('success','An email send to you email, please verify for change email.');
        }
    }

        //Verify Email
        function changeEmail(Request $request)
        {
            $token = $request->token;
    
            if($token == null)
            {
                return redirect(route('404'));
            }
            $emailChange = EmailChange::where('token','=',$token)
                            ->first();
            
            if($emailChange)
            {
                if($emailChange->status == 1)
                {
                    $login = Login::find($emailChange->login_id);
                    if($login)
                    {
                        $emailChange->status = 0;
                        $emailChange->update();

                        $login->email = $emailChange->email;
                        $login->update();
    
                        if($login)
                        {
                            return view('setting.changeEmail')
                            ->withText('Your email Changed!');
                        }
                        else{
                            return view('setting.changeEmail')
                            ->withText('Your email changing failed!');
                        }
                    }
                }
                else{
                    return view('setting.changeEmail')
                    ->withText('Your email is already Changed!');
                }
                
            }
    
            else{
                return redirect(route('404'));
            }
            
        }


}
