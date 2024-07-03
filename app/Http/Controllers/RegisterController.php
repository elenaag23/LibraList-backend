<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use DB;


class RegisterController extends Controller
{
     public function register(Request $request)
    {
        log::info("entered register: " . print_r($request->all(), true));
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'password_confirm' => 'required|string|same:password',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            log::info("errors: " . print_r($errors, true));
            $firstError = $errors->first();

            $nameErrors = $errors->get('name');
            $emailErrors = $errors->get('email');
            $passwordErrors = $errors->get('password');

           return response()->json(['message' => $firstError], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $updateDate = DB::table('users')->where('id', $user->id)->update(['recomDate' => Carbon::today()]);
        $createTags = DB::table('userTags')->insertGetId(['userId' => $user->id]);

        return response()->json(['message' => 'User registered successfully'], 201);
    }

    public function login(Request $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json(['token' => $token, 'user' => $user]);
        } else {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }

    public function logout(Request $request)
    {
        auth()->logout();
        return response()->json(['message' => 'Logout successful'], 200);
    }
}
