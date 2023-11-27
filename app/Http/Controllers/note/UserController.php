<?php

namespace App\Http\Controllers\note;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function register(Request $request) {
        //validasi
        $validator = Validator::make($request->all(),[
            'name'=> ['required','max:100'],
            'email' => ['required','unique:users', 'email'],
            'password' => ['required', 'confirmed', 'min:6'],

        ],
        [
            'name.required'=>'Nama harus diisi',
            'email.required'=>'Email harus diisi',
            'email.email'=> 'Email tidak valid',
            'email.unique'=> 'Email sudah digunakan',
            'password.required'=>'Password harus diisi',
            'password.confirmed'=> 'Password tidak sama'
        ],
    );

        if($validator->fails()){
            return response()->json([
                'status'=>false,
                'message'=>$validator->errors()
            ], 400);
        }
        //simpan
        $user = new User();
        $user->name=$request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password) ;
        $simpan = $user->save();
        if($simpan){
            return response()->json([
                'status'=> true,
                'message'=> 'Register Berhasil'      
            ], 201);
        }
    }
    
    public function login(Request $request) {
        //validasi
        $validator = Validator::make($request->all(),[
            'username' => ['required', 'email'],
            'password' => ['required'],
        ],
        [
            'username.required'=>'Username harus diisi',
            'username.email'=> 'Username harus email',
            'password.required'=>'Password harus diisi',
        ],
    );

        if($validator->fails()){
            return response()->json([
                'status'=>false,
                'message'=>$validator->errors()
            ], 400);
        }
        //cek username dan password
        if(!Auth::attempt(['email' => $request->username, 'password' =>$request->password])){
            return response()->json([
                'status'=> false,
                'message'=> 'Username dan atau password invalid'
            ], 400);
        }
        //buat token

        $user = Auth::user();
        $token = $user-> createToken('authToken')->plainTextToken;
        return response()->json([
            'status'=> true,
            'message'=> 'Anda berhasil login',
            'token'=>$token
        ],200);
    }

    public function logout() {
        Auth::user()->tokens()->delete();
        
        return response()->json([
            'status'=> true,
            'message'=> 'Logout Berhasil'
        ], 200);
    }
}
