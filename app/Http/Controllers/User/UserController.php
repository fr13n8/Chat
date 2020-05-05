<?php

namespace App\Http\Controllers\User;

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

class UserController extends Controller
{

    public $makeHidden;

     /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'registration']]);
        $this->makeHidden = ['password', 'email_verified_at', 'remember_token', 'created_at', 'updated_at', 'verified'];
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $req)
    {
            $validator =  Validator::make($req->all(), [
            'email' => ['required', 'string', 'email', 'max:255', 'exists:users'],
            'password' => ['required', 'string', 'min:5'],
        ]);

        if($validator->fails())
        {
            $data = [
                "message" => $validator->messages(),
                "code" => 0
            ];
            return $data;
        }
        else
        {
            $user = User::where("email", $req->email)->first();
            if(!Hash::check($req->password, $user->password))
            {
                $validator->errors()->add('password', 'Wrong password.');
                $data = [
                    "message" => $validator->messages(),
                    "code" => 0
                ];
                return $data;
            }
            else
            {
                if(!boolval($user->verified))
                {
                    $data = [
                        "message" => [
                            "body" => ["Please check your email for verify your account."]
                        ],
                        "code" => 0
                    ];
                    return $data;
                }
                else{
                    // Session::put("user_id", $user->id);
                    $credentials = request(['email', 'password']);
                    if (! $token = auth()->attempt($credentials)) {
                        return response()->json(['error' => 'Unauthorized'], 401);
                        // return Redirect::to("/");
                    }
                    $token = $this->respondWithToken($token);
                    // $token = Hash::make($user->id.$user->email);
                    $data = [
                        "message" => [
                            "path" => "/dashBoard",
                            "token" => $token
                        ],
                        "code" => 1
                    ];
                    return $data;
                }
            }
        }
    }

    /**
     * User registration
     */
    public function registration(Request $req)
    {
        $validator =  Validator::make($req->all(), [
            'name' => ['required', 'string', 'min:3', 'max:15', "unique:users,name"],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:5', 'confirmed'],
        ],[

        ]);
        
        if($validator->fails())
        {
            $data = [
                "message" => $validator->messages(),
                "code" => 0
            ];
            return $data;
        }
        else
        {
            $name = $req->name;
            $email = $req->email;
            $password = $req->password;
            $user = new User();
            $user->name = $name;
            $user->email = $email;
            $user->password = Hash::make($password);
            $user->save();
            $data = [
                "message" => [
                    "body" => ["Please check your email."]
                ],
                "code" => 1
            ];
            Event::dispatch(new SendMail($user, "register"));
            return $data;
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        try {
            $data = auth()->userOrFail();
            $data = $data->makeHidden($this->makeHidden);
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            $data = $e;
        }
        // dd($data);  // TODO if wrong token   
        return response()->json($data);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }

    // public function signUp(Request $req)
    // {
    //     $validator =  Validator::make($req->all(), [
    //         'userName' => ['required', 'string', 'min:3', 'max:15', "unique:users,name"],
    //         'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
    //         'password' => ['required', 'string', 'min:5', 'confirmed'],
    //     ],[

    //     ]);
        
    //     if($validator->fails())
    //     {
    //         $data = [
    //             "message" => $validator->messages(),
    //             "code" => 0
    //         ];
    //         return $data;
    //     }
    //     else
    //     {
    //         $newUser = new UsersModel;
    //         $newUser -> name     = $req -> userName;
    //         $newUser -> email    = $req -> email;
    //         $newUser -> password = Hash::make($req -> password);
    //         $newUser -> save();
    //         $data = [
    //             "message" => [
    //                 "body" => ["Please check your email."]
    //             ],
    //             "code" => 1
    //         ];
    //         Event::dispatch(new SendMail($newUser, "register"));
    //         return $data;
    //     }
    // }

    public function userVerify($hash, $id)
    {
        $user = User::where("id", $id)->first();
        if($user)
        {
            if(md5($user->id.$user->email) == $hash)
            {
                User::where('id', $id)->update([
                    "verified" => 1,
                    "email_verified_at" => Carbon::now()
                ]);
                return Redirect::to("/signIn");
            }
        }
    }

    // public function signIn(Request $req)
    // {
    //     $validator =  Validator::make($req->all(), [
    //         'email' => ['required', 'string', 'email', 'max:255', 'exists:users'],
    //         'password' => ['required', 'string', 'min:5'],
    //     ]);

    //     if($validator->fails())
    //     {
    //         $data = [
    //             "message" => $validator->messages(),
    //             "code" => 0
    //         ];
    //         return $data;
    //     }
    //     else
    //     {
    //         $user = UsersModel::where("email", $req->email)->first();
    //         if(!Hash::check($req->password, $user->password))
    //         {
    //             $validator->errors()->add('password', 'Wrong password.');
    //             $data = [
    //                 "message" => $validator->messages(),
    //                 "code" => 0
    //             ];
    //             return $data;
    //         }
    //         else
    //         {
    //             if(!boolval($user->verified))
    //             {
    //                 $data = [
    //                     "message" => [
    //                         "body" => ["Please check your email for verify your account."]
    //                     ],
    //                     "code" => 0
    //                 ];
    //                 return $data;
    //             }
    //             else{
    //                 Session::put("user_id", $user->id);
    //                 $token = Hash::make($user->id.$user->email);
    //                 $data = [
    //                     "message" => [
    //                         "path" => "/dashBoard",
    //                         "token" => $token
    //                     ],
    //                     "code" => 1
    //                 ];
    //                 return $data;
    //             }
    //         }
    //     }
    // }

    public function forgotPassword(Request $req)
    {
        $validator =  Validator::make($req->all(), [
            'email' => ['required', 'string', 'email', 'max:255', 'exists:users'],
        ]);

        if($validator->fails())
        {
            $data = [
                "message" => $validator->messages(),
                "code" => 0
            ];
            return $data;
        }
        else
        {
            $user = User::where("email", $req->email)->first();
            Event::dispatch(new SendMail($user, "forgotPassword"));
            $data = [
                "message" => [
                    "body" => ["Please check your email."]
                ],
                "code" => 1
            ];
            return $data;
        }
    }

    public function resetPassword($hash, $id)
    {
        $user = User::where("id", $id)->first();
        if($user)
        {
            if(md5($user->id.$user->email) == $hash)
            {
                $data = [
                    "action" => "RESET_PASSWORD",
                    "email" => $user->email
                ];
                Session::put("data", $data);
                return 1;
            }
            else{
                return 0;
            }
        }
        return 2;
    }

    public function changePassword(Request $req)
    {
        $validator =  Validator::make($req->all(), [
            'password' => ['required', 'string', 'min:5', 'confirmed'],
        ]);

        if($validator->fails())
        {
            $data = [
                "message" => $validator->messages(),
                "code" => 0
            ];
            return $data;
        }
        else
        {
            $user = User::where("id", $req->id)->first();
            if(md5($user->id.$user->email) == $req->token)
            {
                User::where("id", $req->id)->update([
                    "password" => Hash::make($req -> password)
                ]);
                $data = [
                    "message" => [
                        "body" => ["Success! You will be redirected to the login page."]
                    ],
                    "code" => 1
                ];
                return $data;
            }
            else{
                $data = [
                    "message" => [
                        "body" => ["Error: Please check the correctness of the link."]
                    ],
                    "code" => 0
                ];
                return $data;
            }
        }
    }

}
