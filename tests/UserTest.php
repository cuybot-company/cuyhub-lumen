<?php

use App\Models\UsersModel;

class UserTest extends TestCase
{
    public function loginWithUserGetJWT()
    {
        $content = $this
            ->post(
                '/v1/auth/login',
                [
                    'username' => "admin1",
                    'password' => 'admin'
                ]
            )
            ->seeStatusCode(200)
            ->response->getContent();

        $token = json_decode($content)->data->token;

        return $token;
    }

    /**
     * task user
     * 
     * Cannot visit list users with wrong request method
     * Cannot visit get user by id with wrong request method
     * Cannot visit add user with wrong request method
     * Cannot visit update user with wrong request method
     * Cannot visit delete user with wrong request method
     * 
     * can get list users with authorization
     * can get user By ID with authorization
     * can add user with authorization
     * can update user By ID with authorization
     * can delete user By ID with authorization
     * 
     * Cannot get user By ID with no-existent ID and authorization 
     * Cannot add user with a existent username with authorization
     * Cannot add user with a existent email with authorization
     * Cannot update user with a no-existent id and authorization
     * Cannot update user with a existent id, invalid Validation and authorization
     * Cannot delete user By ID with no-existent ID and authorization
     * 
     * Cannot get list users without authorization
     * Cannot get user By ID without authorization
     * cannot add user without authorization
     * cannot update user By ID without authorization
     * cannot delete user By ID without authorization
     * 
     * 
     */

    /**
     * test Can Get List Users
     *
     * @return void
     */

    public function testCanGetListUsers()
    {
        $token = $this->loginWithUserGetJWT();
    }

    /**
     * test Can Get User By ID
     *
     * @return void
     */

    public function testCanGetUserByID()
    {
        # code...
    }

    /**
     * test Can Add User
     *
     * @return void
     */

    public function testCanAddUser()
    {
    }

    /**
     * test Can Update User By ID
     *
     * @return void
     */

    public function testCanUpdateUserByID()
    {
        # code...
    }

    /**
     * test Can Delete User By ID
     *
     * @return void
     */

    public function testCanDeleteUserByID()
    {
        # code...
    }

    /**
     * test Can Not Get List Users WithOut Auth
     *
     * @return void
     */

    public function testCanNotGetListUsersWithOutAuth()
    {
        # code...
    }

    /**
     * test Can Not Get User By ID WithOut Auth
     *
     * @return void
     */

    public function testCanNotGetUserByIDWithOutAuth()
    {
        # code...
    }

    /**
     * test Can Not Add User WithOut Auth
     *
     * @return void
     */

    public function testCanNotAddUserWithOutAuth()
    {
    }

    /**
     * test Can Not Update User By ID WithOut Auth
     *
     * @return void
     */

    public function testCanNotUpdateUserByIDWithOutAuth()
    {
        # code...
    }

    /**
     * test Can Not Delete User By ID WithOut Auth
     *
     * @return void
     */

    public function testCanNotDeleteUserByIDWithOutAuth()
    {
        # code...
    }
}
