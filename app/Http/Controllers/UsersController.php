<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\UsersModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="apiKey",
 *     name="Authorization",
 *     in="header",
 * )
 */

class UsersController extends Controller
{
    /**
     * @OA\Get(
     *   path="/v1/users/",
     *   summary="List users",
     *   tags={"Users"},
     *   security={{ "bearerAuth": {} }},
     *   @OA\Response(
     *     response=200,
     *     description="A list with users"
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthorized"
     *   )
     * )
     */

    public function index()
    {
        // echo Auth()->user()->full_name;
        $data = UsersModel::all()->toArray();
        return api()->status(200)->data($data)->message("success get all users");
    }

    /**
     * @OA\Post(path="/v1/users/",
     *   tags={"Users"},
     *   summary="Add User",
     *   description="add user.",
     *   security={{ "bearerAuth": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *              schema="AddUserRequest",
     *              type="object",
     *              title="AddUserRequest",
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
     *      description="Get result form-data users"
     *   ),
     *   @OA\Response(
     *      response=422, 
     *      description="There is a body that is needed there is not"
     *   ),
     *  @OA\Response(
     *      response=401,
     *      description="Validation Error or Unauthorized"
     *  ),
     *  @OA\Response(
     *      response=400,
     *      description="Failed insert data user"
     *  ),
     * )
     */

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {

            $this->validate($request, [
                'fullName' => 'required|string',
                'username' => 'required|string|min:6|unique:tbl_users',
                'password' => 'required|string|min:10|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|confirmed',
                'email'    => 'required|email|unique:tbl_users'
            ]);

            $name       = $request->input('fullName');
            $username   = $request->input('username');
            $password   = $request->input('password');
            $email      = $request->input('email');

            $data = [
                "full_name"  => $name,
                "username"   => $username,
                "password"   => Hash::make($password),
                "email"      => $email,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            $insert = UsersModel::insert($data);

            if (!$insert) return api()->status(400)->message("Failed insert data user");

            return api()->status(200)->data($data)->message("Success insert data user");
        }
    }

    /**
     * @OA\Get(
     *   path="/v1/users/{id}",
     *   summary="Get user by ID",
     *   tags={"Users"},
     *   security={{ "bearerAuth": {} }},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID user",
     *         required=true,
     *      ),
     *   @OA\Response(
     *     response=200,
     *     description="get information user by id user"
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthorized"
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="User not found"
     *   )
     * )
     */

    public function read(Request $request, $id)
    {
        $data = UsersModel::where('id', $id)->first();

        if (!$data) return api()->status(404)->message("User not found");
        return api()->status(200)->data($data->toArray())->message("success get user");
    }

    /**
     * @OA\POST(
     *   path="/v1/users/{id}?_method=PATCH",
     *   summary="Update user by ID",
     *   tags={"Users"},
     *   security={{ "bearerAuth": {} }},
     *   @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *             schema="UpdateUserRequest",
     *             type="object",
     *             title="UpdateUserRequest",
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
     *   @OA\Parameter(
     *      name="id",
     *      in="path",
     *      description="ID user",
     *      required=true,
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

    public function update(Request $request, $id)
    {
        if ($request->isMethod('patch')) {

            $user = UsersModel::find($id);
            if (!$user) return api()->status(404)->message("User not found");

            $this->validate($request, [
                'fullName'    => "required|string",
                'username'    => "required|string|min:6|unique:tbl_users,username,$id",
                'password'    => "string|min:10|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|confirmed",
                'email'       => "required|email|unique:tbl_users,email,$id",
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

    /**
     * @OA\Delete(
     *   path="/v1/users/{id}",
     *   summary="Delete user by ID",
     *   tags={"Users"},
     *   security={{ "bearerAuth": {} }},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID user",
     *         required=true,
     *      ),
     *   @OA\Response(
     *     response=200,
     *     description="Delete user by ID"
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthorized"
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="User not found"
     *   )
     * )
     */

    public function delete(Request $request, $id)
    {
        if ($request->isMethod('delete')) {

            $user = UsersModel::find($id);
            if (!$user) return api()->status(404)->message("User not found");

            $user->delete();
            return api()->status(200)->message("Success delete data user");
        }
    }
}
