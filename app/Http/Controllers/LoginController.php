<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Login;
use App\Models\User;
use App\Models\ForgotPassword;
use App\Mail\PasswordMail;
use App\Mail\VerifyMail;
use Illuminate\Support\Facades\Mail;


class LoginController extends Controller
{
    function login()
    {
        $path = public_path('js\\country.json');
        $json = json_decode(file_get_contents($path), true); 
        return view('login.login')
               ->withCountrys($json);
    }
    function logData(Request $request)
    {
        $userName = $request->userName;
        $password = md5($request->password);

        $userData = [];


        if(is_numeric($userName)){
            $code = $request->country;
            $phone = $code.$userName;

            $log = Login::where('mobile_no','=',$phone)
                        ->where('password','=',$password)
    					->first();
            if($log)
            {
                $user = User::where('login_id','=',$log->id)
                            ->where('status','!=', 4)
                            ->first();
                if($user)
                {
                    if($log->otp_verify== 0)
                    {
                        session()->put('phone', $phone);
                        session()->put('user_id', $user->id);
                        return redirect(route('verifyPhone'));
                    }
                    else{
                        $userData = $user;
                        
                    }
                }
                else
                {
                    return back()
                    ->with('error', 'No account exist.');
                }

            }
            else
            {
                $check = Login::where('mobile_no','=',$phone)->first();
                if($check)
                {
                    if($check->password != $password)
                    {
                        return back()
                        ->with('error', 'Password incorrect.');
                    }
                }
                else{
                    return back()
                    ->with('error', 'Phone Number incorrect.');
                }
            }
                    
        } else {
            $log = Login::where('user_name','=',$userName)
                        ->where('password','=',$password)
    					->first();
            if($log)
            {
                $user = User::where('login_id','=',$log->id)
                            ->where('status', '!=', 4)
                            ->first();
                if($user)
                {
                    $userData = $user;
                }
                else
                {
                    return back()
                    ->with('error', 'No account exist.');
                }

            }
            else
            {
                $check = Login::where('user_name','=',$userName)->first();
                if($check)
                {
                    if($check->password != $password)
                    {
                        return back()
                        ->with('error', 'Password incorrect.');
                    }
                }
                else{
                    return back()
                    ->with('error', 'User Name incorrect.');
                }
            }
        }


        if($userData){
            if($userData->status == 1){
                $request->session()->put('user', $userData);
                $session = session('user');
                session()->put('page', 'All');
                session()->put('emotion', 'All');
                session()->put('dateFilter', 'All');
    
                return redirect(route('index'))
                ->with('success','You have successfully logged in');
            }
            else if($userData->status == 2){
                return back()
                ->with('error', 'Your Acccount is Temporary Banned');
            }
            else if($userData->status == 3){
                return back()
                ->with('error', 'Your Acccount is Banned');
            }
            else if($userData->status == 4){
                return back()
                ->with('error', 'Please Verify Your Account');
            }
            else{
                return back()
                ->with('error', 'This User Does not Exist');
            }
        }

    }


    
    function logout(Request $request) {
        $request->session()->flush();
        return redirect(Route('login'));
    }
    
    // Registration
    function registration()
    {
        $path = public_path('js\\country.json');
        $json = json_decode(file_get_contents($path), true); 
        return view('login.registration')
               ->withCountrys($json);
    }
    function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'userName' => 'required|alpha',
            'email' => 'required|email'
        ]);

        $name = $request->name;
        $email = $request->email;
        $phone = $request->phone;
        $code = $request->country;
        $phone = $code.$phone;
        $userName = $request->userName;
        $password = $request->password;

        $oldUserName = User::where('user_name','=',$userName)
                            ->where('status', '!=', 4)
                            ->first();
        if($oldUserName)
        {
            return back()
            ->with('error','This username is already taken.');
        }
        $oldUserEmail = Login::where('email','=', $email)
                            ->first();
        if($oldUserEmail)
        {
            $userEmail = User::where('login_id','=',$oldUserEmail->id)
                            ->where('status', '!=', 4)
                            ->first();
            if($userEmail)
            {
                return back()
                ->with('error','This email is already taken.');
            }
            
        }
        $login = new Login;
        $user = new User;
        

        $verifyToken = md5(time().rand());


        $login->user_name = $userName;
        $login->email = $email;
        $login->mobile_no = $phone;
        $login->password = md5($password);
        $login->verify_token = $verifyToken;
        //$login->save();

        $loginId = $login->id;

        $user->user_name = $userName;
        $user->full_name = $name;
        $user->login_id = $loginId;
        $user->status = 4;
        //$user->save();

        if($user->save()){
            $route = route('verifyEmail');
            $emailLink = $route.'?token='.$verifyToken;

            Mail::to($email)->send(new VerifyMail($emailLink));

            $this->score($user->id, 1, 'registration', null);

            session()->put('phone', $phone);
            session()->put('user_id', $user->id);
            return redirect(route('verifyPhone'))
            ->withPhone($phone)
            ->withUserId($user->id);
        }
    }

    // verify phone
    function verifyPhone()
    {
        return view('login.verifyPhone')
                ->withPhone('')
                ->withUserId('');
    }

    function otpVerifyUrl($uesr_id)
    {
        if($uesr_id)
        {
            $userId = $uesr_id;

            $user = User::where('id', '=', $userId)
                        ->where('status', '!=', 4)
                        ->first();
            if($user)
            {

                $loginId = $user->login_id;
                $user->status = 1;
                $user->update();

                $login = Login::where('id', '=', $loginId)
                            ->first();
                if($login)
                {
                    $login->otp_verify = 1;
                    $login->update();
                }

                return redirect(route('login'))
                ->with('success','Otp verified successfully.');
                
            }
            else
            {
                return back()
                ->with('error','This user does not exist.');
            }
        }
    }

    

    // Forgot Password
    function forgotPassword()
    {
        return view('login.forgotPassword');
    }

    function sendPassword(Request $request)
    {
        $email = $request->email;

        $login = Login::where('email','=',$email)
    					    ->first();
        if($login)
        {
            $forgot = new ForgotPassword;
            $token = md5(time().rand());
            $forgot->login_id = $login->id;
            $forgot->token = $token;
            $forgot->save();
            if($forgot)
            {
                $route = route('changePassword');
                $emailLink = $route.'?token='.$token;

                Mail::to($email)->send(new PasswordMail($emailLink));

                return back()
                ->with('success','Your Password is Send to your Email.');
            }
        }
        else{
            return back()
            ->with('error','This email does not have any account.');
        }
    }

    function changePassowrd(Request $request)
    {
        $token = $request->token;

        if($token == null)
        {
            return redirect(route('404'));
        }
        $forgot = ForgotPassword::where('token','=',$token)
                                ->where('status', '=', 0)
    					        ->first();
        
        if($forgot)
        {
            return view('login.changePassword')
                ->withToken($token);
        }

        else{
            return view('login.changePassword')
                ->withToken(null);
        }
        
    }
    function savePassword(Request $request)
    {
        $password = md5($request->password);
        $token = $request->token;

        $forgot = ForgotPassword::where('token','=',$token)
                                ->where('status', '=', 0)
    					        ->first();
        
        if($forgot)
        {
            $login = Login::find($forgot->login_id);
            $login->password = $password;
            $login->update();

            if($login)
            {
                $forgot->status = 1;
                $forgot->update();


                return redirect(route('login'))
                ->with('success','Your password Changed Successfully');
            }
        }

        else{
            return view('login.changePassword')
                ->withToken(null);
        }
        
    }

    //Verify Email
    function verifyEmail(Request $request)
    {
        $token = $request->token;

        if($token == null)
        {
            return redirect(route('404'));
        }
        $login = Login::where('verify_token','=',$token)
    					->first();
        
        if($login)
        {
            if($login->email_verify == 0)
            {
                $login->email_verify = 1;
                $login->update();

                $user = User::where('login_id','=',$login->id)
                            ->where('user_name','=',$login->user_name)
                            ->where('status', '!=', 4)
                            ->first();
                
                if($user){
                    $user->status = 1;
                    $user->update();
                }

                return view('login.verifyEmail')
                    ->withText('Your email is Verified!');
            }
            else{
                return view('login.verifyEmail')
                ->withText('Your email is already Verified!');
            }
            
        }

        else{
            return redirect(route('404'));
        }
        
    }


}
