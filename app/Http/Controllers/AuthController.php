<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\support\Str;
use Twilio\Rest\Client;


class AuthController extends Controller
{

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'phone_number' => ['required', 'unique:users', ]
        ]);

        if($validator->fails()){
            return ResponseFormatter::error([
                'message' => 'Nomor Handphone sudah terdaftar'
            ],'Unregistered', 200);
        }

        $code = random_int(100000, 999999);

        $account_id = env('TWILIO_SID');
        $account_token = env('TWILIO_AUTH_TOKEN');

        $client = new Client("ACc439ed2210aec38d9ec3300f11a01439" , "812c55e30ff94a8c0010e4658c266412");
        $message = $client->messages 
                    ->create($request->phone_number, // to 
                            array(   
                                "from" => "+19785742126",      
                                "body" => "Your code : {$code}"
                            ) 
                    ); 
        
        $user = User::create([
            'phone_number' => $request->phone_number,
            'verification_code' => $code
        ]);
        
        $token = $user->generateToken();

        return ResponseFormatter::success(
            ['access_token' => $token,
            'token_type' => 'Bearer',
            'verification_code' => $code], 'Nomor berhasil terdaftar');
    }
    
    public function verify(Request $request){
        
        if($request->verification_code == Auth::user()->verification_code){
            
            $user = Auth::user();
            $user->phone_verified_at = now();
            $user->save();

            return ResponseFormatter::success(null, 'Nomor berhasil di verifikasi');
            
        }else{
            
            return ResponseFormatter::error([
                'message' => 'Unauthorized'
            ],'Authentication Failed', 500);
        }
    }
    
    public function login(Request $request)
    {
        // return $request;
        
        $this->validate($request, [
            'phone_number' => 'required',
        ]);

        $user = User::where('phone_number', '=', $request->phone_number)->first();
        
        if($user){
            if(!($user->phone_verified_at)){
                $token = $user->generateToken();
                return ResponseFormatter::success(
                    ['access_token' => $token,
                    'token_type' => 'Bearer',
                    'user' => $user], 'Berhasil Login');
            }else{
                return ResponseFormatter::error([
                    'message' => 'Nomor Handphone belum terdaftar'
                ], 200);
            }
        }
        else{
            return ResponseFormatter::error([
                'message' => 'Nomor Handphone belum terdaftar'
            ], 200);
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