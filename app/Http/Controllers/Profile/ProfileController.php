<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Validator;
use App\User;
use Hash;
use Event;
use App\Events\SendMail;
use Illuminate\Support\Facades\Redirect;
use Session;

class ProfileController extends Controller
{
    public $makeHidden;

    public function __construct()
    {
        // $this->makeHidden = ['password', 'email_verified_at', 'remember_token', 'created_at', 'updated_at', 'verified'];
    }

    public function passwordCheck($hashedPassword, $password)
    {
        if(!Hash::check($password, $hashedPassword))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    // public function tokenCompare($token)
    // {
    //     $userId = Session::get("user_id");
    //     if($userId)
    //     {
    //         $user = UsersModel::where("id", $userId)->first();
    //         if(!Hash::check($user->id.$user->email, $token))
    //         {
    //             return false;
    //         }
    //         else
    //         {
    //             $user->makeHidden($this->makeHidden);
    //             return $user;
    //         }
    //     }
    // }

    // public function getUser(Request $req)
    // {
    //     $token = $req->token;
    //     $user  = $this->tokenCompare($token);
    //     if($user)
    //     {
    //         return $user;
    //     }
    // }

    public function getUser()
    {
        try {
            $user = auth()->userOrFail();
            // $user = $user->makeHidden($this->makeHidden);
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            $user = $e;
            return response()->json(['message' => 'refresh']);
        }
        return $user;
    }
    
    public function changeAvatar(Request $req)
    {
        $user = $this->getUser();
        // dd($data);  // TODO if wrong token   
        // return response()->json($data);
        $user->update([
            "avatar_id" => $req->avatar_id
        ]);
        return $user;
    }

    public function updateUser(Request $req)
    {
        $user = $this->getUser();
        if($this->passwordCheck($user->password, $req->data["password"])){
            $validator = Validator::make($req->all(), [
                'name' => ['required', 'string', 'min:3', 'max:15', "unique:users,name"],
            ]);
            if($validator->fails() && $req->name == $user->name){
                return response()->json(['message' => 'name']);
            }
            else{
                $user->update([
                    "name" => $req->data["name"]
                ]);
                return response()->json([
                    'message' => "success",
                    'user' => $user
                ]);
            }
        }
        else{
            return response()->json(['message' => "password"]);
        }
    }
}
