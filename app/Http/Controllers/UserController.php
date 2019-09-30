<?php

namespace App\Http\Controllers;
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
class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }

    public function index(Request $request){
        if ( $request->input('client') ) {
    	    return User::select('id', 'name', 'email')->get();
    	}

        $columns = ['id', 'name', 'email'];

        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');
        $page = $request->input('page');
        //dd($page);
        $query = User::select('id', 'name', 'email')->orderBy($columns[$column], $dir);

        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('name', 'like', '%' . $searchValue . '%')
                ->orWhere('email', 'like', '%' . $searchValue . '%');
            });
        }

        $projects = $query->paginate($length);
        //dd($projects->nextPageUrl());
        return ['data' => $projects, 'draw' => $request->input('draw')];
    }
    public function create(){

    }
    public function store(Request $request){
       
            
            //$request->all();//Ini untuk handle form-data\\
            //$request->json()->all;//Ini untuk handle request json\\
            
            $validator = Validator::make($request->json()->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
            ]);
          
            if($validator->fails()){
                return response()->json($validator->errors()->toJson(), 400);
            }
            //dd(Hash::make($request->json('password')));
            $user = User::create([
                'name' => $request->json('name'),
                'email' => $request->json('email'),
                'password' => Hash::make($request->json('password')),
            ]);
            $exp = Carbon::now()->addDay(1);//add 1 minutes exp token
            $token = $this->jwt->fromUser($user,$exp);//generate token berdasarkan variable user
            $token_exp = $exp->format('d-m-Y H:i:s');
          //  return response()->json(compact('user','token'),201);
            return response()->json([
                'data' => $user,
                'token' => $token,
                'token_exp' => $token_exp
            ],Response::HTTP_CREATED);
        }
        public function show($id){
            dd($id);
        }
    }
   
    

