<?php

use App\Models\UsersModel;

class AuthTest extends TestCase
{

    /**
     *  task Login
     *  user can view a login with no body (done)
     *  user can login with correct credentials (done)
     * 
     *  user cannot view a login wrong request method (done)
     *  user cannot login with a non-existent email or username (done)
     *  user cannot login with incorrect password (done)
     */

    /**
     * test User Can Visit Login
     *
     * @return void
     */

    public function testUserCanVisitLogin()
    {
        $this->post('/v1/auth/login', [])
            ->seeJsonEquals([
                'username' => ["The username field is required."],
                'password' => ["The password field is required."]
            ]);
    }

    /**
     * test User Can Login Correct
     *
     * @return void
     */

    public function testUserCanLoginCorrect()
    {
        $this->post('/v1/auth/login', [
            "username"      => "admin1",
            "password"      => "admin"
        ])
            ->seeJson([
                'messages'  => ["Login Success"],
                'status'    => 200
            ]);
    }

    /**
     * test User Can Not Visit Login With Wrong Request Method
     *
     * @return void
     */

    public function testUserCanNotVisitLoginWithWrongRequestMethod()
    {
        $responseGet    = $this->call('GET', '/v1/auth/login');
        $responsePut    = $this->call('PUT', '/v1/auth/login');
        $responsePatch  = $this->call('PATCH', '/v1/auth/login');
        $responseDelete = $this->call('DELETE', '/v1/auth/login');

        $this->assertEquals(405, $responseGet->status());
        $this->assertEquals(405, $responsePut->status());
        $this->assertEquals(405, $responsePatch->status());
        $this->assertEquals(405, $responseDelete->status());
    }

    /**
     * test User Can Not Login InCorrect Email Or Username
     *
     * @return void
     */

    public function testUserCanNotLoginInCorrectEmailOrUsername()
    {
        $this->post('/v1/auth/login', [
            "username"      => "Wrong Username",
            "password"      => "admin"
        ])
            ->seeJson([
                'messages'  => ["Email or Username not valid"],
                'status'    => 401
            ]);
    }

    /**
     * test User Can Not Login InCorrect Password
     *
     * @return void
     */

    public function testUserCanNotLoginInCorrectPassword()
    {
        $this->post('/v1/auth/login', [
            "username"      => "admin1",
            "password"      => "Wrong Password"
        ])
            ->seeJson([
                'messages'  => ["Wrong Password"],
                'status'    => 401
            ]);
    }


    /**
     *  task Register
     *  user can view a register form with no body (done)
     *  user can register with correct 
     *      no one existent, 
     *      correct validation, 
     *      and check if data in database (done)
     * 
     *  user cannot view a register wrong request method (done)
     *  user cannot register with incorrect validation fullName (done)
     *  user cannot register with incorrect validation email (done)
     *  user cannot register with incorrect validation username (done)
     *  user cannot register with incorrect validation password (done)
     *  user cannot register with password no same password_confirmation (done)
     *  user cannot register with a existent username (done) 
     *  user cannot register with a existent email (done)
     */

    /**
     * test User Can Visit Register
     *
     * @return void
     */

    public function testUserCanVisitRegister()
    {
        $this->post('/v1/auth/register', [])
            ->seeJsonEquals([
                "fullName"  => ["The full name field is required."],
                "username"  => ["The username field is required."],
                "email"     => ["The email field is required."],
                "password"  => ["The password field is required."],
            ]);
    }

    /**
     * test User Can Register
     *
     *  User can Login with correct validation and no one exits
     * 
     * @return void
     */

    public function testUserCanRegister()
    {
        $this->post('/v1/auth/register', [
            "fullName"              => 'TESTING',
            "username"              => "TESTING12345",
            "email"                 => "TESTING12345@gmail.com",
            "password"              => "tESTING12345",
            "password_confirmation" => "tESTING12345"
        ])
            ->seeJson([
                "messages"          => ["Success insert data user"],
                "status"            => 200,
            ])
            // check table if email in table user
            ->seeInDatabase('tbl_users', [
                'email'             => 'TESTING12345@gmail.com'
            ]);

        // if done register will be remove data in table users
        UsersModel::where('email', 'TESTING12345@gmail.com')->delete();
    }

    /**
     * test User Can Not Visit Register With Wrong Request Method
     *
     * @return void
     */

    public function testUserCanNotVisitRegisterWithWrongRequestMethod()
    {
        $responseGet    = $this->call('GET', '/v1/auth/register');
        $responsePut    = $this->call('PUT', '/v1/auth/register');
        $responsePatch  = $this->call('PATCH', '/v1/auth/register');
        $responseDelete = $this->call('DELETE', '/v1/auth/register');

        $this->assertEquals(405, $responseGet->status());
        $this->assertEquals(405, $responsePut->status());
        $this->assertEquals(405, $responsePatch->status());
        $this->assertEquals(405, $responseDelete->status());
    }

    /**
     * test User Can Not Register InCorrect Validation FullName
     *
     * @return void
     */

    public function testUserCanNotRegisterInCorrectValidationFullName()
    {
        $this->post('/v1/auth/register', [
            "fullName"              => '',
            "username"              => "TESTING12345",
            "email"                 => "TESTING12345@gmail.com",
            "password"              => "tESTING12345",
            "password_confirmation" => "tESTING12345"
        ])
            ->seeJsonEquals([
                "fullName" => [
                    "The full name field is required."
                ]
            ]);
    }

    /**
     * test User Can Not Register InCorrect Validation Email
     *
     * @return void
     */

    public function testUserCanNotRegisterInCorrectValidationEmail()
    {
        $this->post('/v1/auth/register', [
            "fullName"              => 'TESTING',
            "username"              => "TESTING12345",
            "email"                 => "TESTING12345",
            "password"              => "tESTING12345",
            "password_confirmation" => "tESTING12345"
        ])
            ->seeJsonEquals([
                "email" => [
                    "The email must be a valid email address."
                ]
            ]);
    }

    /**
     * test User Can Not Register InCorrect Validation Username
     *
     * @return void
     */

    public function testUserCanNotRegisterInCorrectValidationUsername()
    {
        $this->post('/v1/auth/register', [
            "fullName"              => 'TESTING',
            "username"              => "TEST",
            "email"                 => "TESTING12345@gmail.com",
            "password"              => "tESTING12345",
            "password_confirmation" => "tESTING12345"
        ])
            ->seeJsonEquals([
                "username" => [
                    "The username must be at least 6 characters."
                ]
            ]);
    }

    /**
     * test User Can Not Register InCorrect Validation Passowrd
     *
     * @return void
     */

    public function testUserCanNotRegisterInCorrectValidationPassword()
    {
        $this->post('/v1/auth/register', [
            "fullName"              => 'TESTING',
            "username"              => "TESTING",
            "email"                 => "TESTING12345@gmail.com",
            "password"              => "testing",
            "password_confirmation" => "tESTING12345"
        ])
            ->seeJsonEquals([
                "password"  => [
                    "The password confirmation does not match.",
                    "The password format is invalid.",
                    "The password must be at least 10 characters."
                ]
            ]);
    }

    /**
     * test User Can Not Register InCorrect Validation Passowrd Confirmation
     *
     * @return void
     */

    public function testUserCanNotRegisterInCorrectValidationPasswordConfirm()
    {
        $this->post('/v1/auth/register', [
            "fullName"              => 'TESTING',
            "username"              => "TESTING",
            "email"                 => "TESTING12345@gmail.com",
            "password"              => "tESTING12345",
            "password_confirmation" => "tESTING1234"
        ])
            ->seeJsonEquals([
                "password"  => [
                    "The password confirmation does not match."
                ]
            ]);
    }

    /**
     * test User Can Not Register If Exist Username
     *
     * @return void
     */

    public function testUserCanNotRegisterIfExistUsername()
    {
        $this->post('/v1/auth/register', [
            "fullName"              => 'TESTING',
            "username"              => "admin1",
            "email"                 => "tESTING12345@gmail.com",
            "password"              => "tESTING12345",
            "password_confirmation" => "tESTING12345"
        ])
            ->seeJsonEquals([
                "username"  => [
                    "The username has already been taken."
                ]
            ]);
    }

    /**
     * test User Can Not Register If Exist Email
     *
     * @return void
     */

    public function testUserCanNotRegisterIfExistEmail()
    {
        $this->post('/v1/auth/register', [
            "fullName"              => 'TESTING',
            "username"              => "TESTING",
            "email"                 => "admin@gmail.com",
            "password"              => "tESTING12345",
            "password_confirmation" => "tESTING12345"
        ])
            ->seeJsonEquals([
                "email"  => [
                    "The email has already been taken."
                ]
            ]);
    }
}
