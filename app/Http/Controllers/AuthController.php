<?php

namespace App\Http\Controllers;
//date_default_timezone_set('Asia/Jakarta');

use App\User;
use App\Http\Resources\UserCollection;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;
class AuthController extends Controller
{
    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->json()->all(), [
           
            'email' => 'required|string|email|max:255',
            'password' => 'required|string',
        ]);
        
           $credentials = $request->json()->all();
           $exp = Carbon::now()->addDay(1);//add 1 minutes exp token
           
           try {
               if (! $token = $this->jwt->attempt($credentials,['exp' => $exp])) {
                   
                   return response()->json(['error' => 'invalid_credentials'], 400);
               }
           }  catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], 500);
    
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
    
            return response()->json(['token_invalid'], 500);
    
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
    
            return response()->json(['token_absent' => $e->getMessage()], 500);
    
        }
           
           //dd($this->jwt->User());get info user
           
           $data = new UserCollection(User::where('email', $request->json('email'))->first());
           $token_exp = $exp->format('d-m-Y H:i:s');
           return response()->json([
                'data' => $data,
                'token' => $token,
                'token_exp' => $token_exp
            ],Response::HTTP_CREATED);
    }
    
}