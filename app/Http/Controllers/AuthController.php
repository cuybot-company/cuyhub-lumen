<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\UsersModel;
use Firebase\JWT\JWT;
use Carbon\Carbon;

/**
 * @OA\Info(title="CUYHUB API", version="0.1")
 */

class AuthController extends Controller
{
    /**
     * @OA\Post(path="/v1/auth/login",
     *   tags={"Auth"},
     *   summary="Login User",
     *   description="login user.",
     *   operationId="loginUser",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              schema="LoginRequest",
     *              type="object",
     *              title="LoginRequest",
     *              required={"username", "password"},
     *              properties={
     *                  @OA\Property(property="username", type="string"),
     *                  @OA\Property(property="password", type="string")
     *              }
     *          )
     *       )
     *   ),
     *   @OA\Response(
     *      response=200, 
     *      description="get json result with token"
     *   ),
     *   @OA\Response(
     *      response=422, 
     *      description="there is a body that is needed there is not"
     *   ),
     *  @OA\Response(
     *      response=401,
     *      description="Validation Error"
     *  )
     * )
     */

    public function login(Request $request)
    {

        if ($request->isMethod('post')) {

            $this->validate($request, [
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            $username = $request->input('username'); # username or email
            $password = $request->input('password');

            $user = UsersModel::where('username', $username)
                ->orWhere('email', $username)
                ->first();

            if (!$user) return api()->status(401)->data([])->message("Email or Username not valid");

            $isValidPassword = Hash::check($password, $user->password);

            if (!$isValidPassword) return api()->status(401)->data([])->message("Wrong Password");

            $payloadJWT = [
                "iat" => intval(microtime(true)),
                "exp" => intval(microtime(true)) + (60 * 60 * 1000), # 1 jam
                "uid" => $user->id
            ];

            $token = JWT::encode($payloadJWT, env('JWT_SECRET'));

            return api()->status(200)->data(["token" => $token])->message("Login Success");
        }
    }

    /**
     * @OA\Post(path="/v1/auth/register",
     *   tags={"Auth"},
     *   summary="Register User",
     *   description="register user.",
     *   operationId="createUser",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              schema="RegisterRequest",
     *              type="object",
     *              title="RegisterRequest",
     *              required={"fullName", "username", "password", "password_confirmation", "email"},
     *              properties={
     *                  @OA\Property(property="fullName", type="string"),
     *                  @OA\Property(property="username", type="string"),
     *                  @OA\Property(property="password", type="string"),
     *                  @OA\Property(property="password_confirmation", type="string"),
     *                  @OA\Property(property="email", type="string"),
     *              }
     *          )
     *       )
     *   ),
     *   @OA\Response(
     *      response=200, 
     *      description="get result form-data users"
     *   ),
     *   @OA\Response(
     *      response=422, 
     *      description="there is a body that is needed there is not"
     *   ),
     *  @OA\Response(
     *      response=401,
     *      description="Validation Error"
     *  )
     * )
     */

    public function register(Request $request)
    {
        if ($request->isMethod('post')) {

            $this->validate($request, [
                'fullName' => 'required|string',
                'username' => 'required|min:6|unique:tbl_users',
                'password' => 'required|string|min:10|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|confirmed',
                'email'    => 'required|email|unique:tbl_users'
            ]);

            $name       = $request->input('fullName');
            $username   = $request->input('username');
            $password   = $request->input('password');
            $email      = $request->input('email');

            $data = [
                "full_name"   => $name,
                "username"   => $username,
                "password"   => Hash::make($password),
                "email"      => $email,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            $insert = UsersModel::insert($data);

            if (!$insert) return api()->status(400)->data([])->message("Failed insert data user");

            return api()->status(200)->data($data)->message("Success insert data user");
        }
    }
}
