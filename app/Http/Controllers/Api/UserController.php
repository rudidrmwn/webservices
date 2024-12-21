<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:3,50',
            'email' => 'required|email:rfc,dns',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        //create new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        //create order by user id
        Order::create([
            'user_id' => $user->id
        ]);

        //email title and content
        $mailData = [

            'title' => 'Greeting',

            'body' => 'Hello Congratulation you have registration successfully'

        ];
        //send email to user and system adminitrator
        Mail::to($request->email)->cc(env('MAIL_FROM_ADDRESS'))->send(new SendMail($mailData));

        return (new UserResource($user))
            ->response()
            ->header('Content-Type', 'application/vnd.api+json')
            ->setStatusCode(201);
    }

    public function datausers(Request $request){
        if(isset($request->search)){
            $results = User::selectRaw('id,email,name,created_at')
            ->where('name', $request->search)
            ->whereOr('email',$request->search)
            ->get();
        }elseif($request->sortBy){
            $results = User::selectRaw('id,email,name,created_at')
            ->where('created_at', 'LIKE',"%{$request->sortBy}%")
            ->get();
        }
        
        return response()->json(['page'=>$request->page,'users'=>$results], 201);
    }
}
