<?php


class UserRolesCest
{
    protected $user;

    public function tryToSuccessfullyLogIn(AcceptanceTester $I) {
        $I->wantTo('successfully log in');

        $this->loginUser( $I , 'admin@mt2.com' , 'admin' , 'Admin McAdmin' );
    }

    private function loginUser( AcceptanceTester $I , $email , $password, $name ) {
        $I->amOnPage('/login');
        $I->fillField( 'login' , $email );
        $I->fillField( 'password' , $password );
        $I->click('Login' , '.btn');
        $I->see( $name );
        $I->seeInCurrentUrl( '/home' );
    }
}