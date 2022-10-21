<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Mail\iChatMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\support\Str;
use Twilio\Rest\Client;


class AuthController extends Controller
{

    public function register(Request $request){
        // $validator = Validator::make($request->all(), [
        //     'phone_number' => ['unique:users'],
        //     'email' => ['unique:users']
        // ]);
        

        // if($validator->fails()){
        //     return ResponseFormatter::error([
        //         'message' => 'Email sudah terdaftar'
        //     ],'Unregistered', 200);
        // }

        $code = random_int(100000, 999999);
        $data = [
            'body'=> $code 
        ];

        Mail::to($request->email)->send(new iChatMail($data));

        $user = User::where('email',$request->email)->first();
        if($user){
            if($user->code_verified_at != null){
                return ResponseFormatter::error([
                    'message' => 'Email sudah terdaftar'
                ],'Unregistered', 200);
            }else{
                User::where('id',$user->id)->update([
                    'verification_code' => $code,
                ]);
            }
        }else{
            $user = User::create([
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'verification_code' => $code
            ]);
        }


        // $account_id = env('TWILIO_SID');
        // $account_token = env('TWILIO_AUTH_TOKEN');

        // $client = new Client("ACc439ed2210aec38d9ec3300f11a01439" , "812c55e30ff94a8c0010e4658c266412");
        // $message = $client->messages 
        //             ->create($request->phone_number, // to 
        //                     array(   
        //                         "from" => "+19785742126",      
        //                         "body" => "Your code : {$code}"
        //                     ) 
        //             ); 

        
        
        $token = $user->generateToken();

        $msg = '';
        if($request->email != null){
           $msg = 'Email berhasil terdaftar'; 
        }
        if($request->phone_number != null){
            $msg = 'Nomor berhasil terdaftar';
        }

        return ResponseFormatter::success(
            ['access_token' => $token,
            'token_type' => 'Bearer',
            'verification_code' => $code], $msg);
    }
    
    public function verify(Request $request){
        
        if($request->verification_code == Auth::user()->verification_code){
            
            $user = Auth::user();
            $user->code_verified_at = now();
            $user->save();

            return ResponseFormatter::success(null, 'Email berhasil di verifikasi');
            
        }else{
            
            return ResponseFormatter::error([
                'message' => 'Unauthorized'
            ],'Authentication Failed', 500);
        }
    }
    
    public function login(Request $request)
    {
        // return $request;
        
        // $this->validate($request, [
        //     'phone_number' => 'required',
        // ]);

        // $user = User::where('phone_number', '=', $request->phone_number)->first();
        
        $user = User::where('email', '=', $request->email)
                    ->where('code_verified_at','!=', null)
                    ->get();

        if(count($user) > 0){
            $user = User::find($user[0]->id);
            $code = random_int(100000, 999999);
            $data = [
                'body'=> $code 
            ];

            Mail::to($request->email)->send(new iChatMail($data));

            User::where('id',$user->id)->update([
                'login_pin' => $code
            ]);

            $user = User::find($user->id);

            $token = $user->generateToken();
            return ResponseFormatter::success(
                ['access_token' => $token,
                'token_type' => 'Bearer',
                'pin' => $code], 'Berhasil Login');
        }
        else{
            return ResponseFormatter::error([
                'message' => 'Email belum terdaftar'
            ], 200);
        }
    }
    public function verify_pin(Request $request){
        
        if($request->login_pin == Auth::user()->login_pin){
            
            $user = Auth::user();
            $user->pin_verified_at = now();
            $user->save();

            return ResponseFormatter::success(['user' => Auth::user()], 'Login Berhasil');
            
        }else{
            
            return ResponseFormatter::error([
                'message' => 'Unauthorized'
            ],'Authentication Failed', 500);
        }
    }

    public function change_profile(Request $request){
        
        $imgName = '';
        if($request->profile_pict){
            $imgName = time().'.'.$request->profile_pict->extension();
    
            $request->profile_pict->storeAs('public/photo', $imgName);
        }
        $user = Auth::user();
        User::where('id', $user->id)->update([
            'name' => $request->name,
            'profile_pict' => $imgName,
        ]);

        return ResponseFormatter::success($user, 'Profile berhasil di update');
        
    }
    
    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->api_token = null;
            $user->save();
            return ResponseFormatter::success(null, 'Berhasil Logout');
        }
    }

}