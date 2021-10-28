<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\UsersModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->id = Auth()->user()->id;
    }

    /**
     * @OA\Get(
     *   path="/v1/profile/",
     *   summary="Get Profile",
     *   tags={"Profile"},
     *   security={{ "bearerAuth": {} }},
     *   @OA\Response(
     *     response=200,
     *     description="get profile"
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthorized"
     *   )
     * )
     */

    public function getProfile(Request $request)
    {
        if ($request->isMethod('get')) {
            $user = UsersModel::where('id', $this->id)->first();
            if (!$user) return api()->status(404)->message("User not found");

            return api()->status(200)->data($user->toArray())->message("success get profile");
        }
    }

    /**
     * @OA\POST(
     *   path="/v1/users/?_method=PATCH",
     *   summary="Update profile",
     *   tags={"Profile"},
     *   security={{ "bearerAuth": {} }},
     *   @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *             schema="UpdateProfileRequest",
     *             type="object",
     *             title="UpdateProfileRequest",
     *             required={"fullName", "username", "email"},
     *             properties={
     *                 @OA\Property(property="fullName", type="string"),
     *                 @OA\Property(property="username", type="string"),
     *                 @OA\Property(property="password", type="string"),
     *                 @OA\Property(property="password_confirmation", type="string"),
     *                 @OA\Property(property="email", type="string"),
     *             }
     *         )
     *       )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="get result form-data users"
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthorized"
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="User not found"
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description="Failed update data user"
     *   )
     * )
     */

    public function updateProfile(Request $request)
    {
        if ($request->isMethod('patch')) {
            $user = UsersModel::where('id', $this->id)->first();
            if (!$user) return api()->status(404)->message("User not found");

            $this->validate($request, [
                'fullName'    => "required|string",
                'username'    => "required|string|min:6|unique:tbl_users,username,$this->id",
                'password'    => "string|min:10|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|confirmed",
                'email'       => "required|email|unique:tbl_users,email,$this->id",
            ]);

            $name           = $request->input('fullName');
            $username       = $request->input('username');
            $password       = $request->input('password');
            $email          = $request->input('email');

            $data = [
                "full_name"  => $name,
                "username"   => $username,
                "password"   => Hash::make($password),
                "email"      => $email,
                'updated_at' => Carbon::now(),
            ];

            if ($request->input('password') == null) unset($data["password"]);


            $update = $user->update($data);
            if (!$update) return api()->status(400)->message("Failed update data user");

            return api()->status(200)->data($data)->message("Success update data user");
        }
    }
}
