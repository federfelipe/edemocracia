<?php

use App\State;

class UsersTest extends TestCase
{

    public function testHome()
    {
        $this->visit('/')
            ->click('INÍCIO')
            ->seePageIs('/');
    }


    /*
  public function testSocialNetworks()
 {
     //$user = factory(App\User::class, 'admin')->create();
     $user = factory(App\User::class)->create();
     $roleId = $user->socialNetworks();
     return $roleId;
 }


 public function testGetUserByEmail()
  {
      $user = App\User::all()->random();
      $user = factory(App\User::class)->create();

  }*/

}
