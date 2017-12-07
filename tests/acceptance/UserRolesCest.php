<?php


class UserRolesCest
{
    protected $user;

    public function tryToSuccessfullyLogIn(AcceptanceTester $I) {
        $I->wantTo('successfully log in');

        $this->loginUser( $I , 'admin@mt2.com' , 'admin' , 'Admin McAdmin' );
        $I->see( 'Admin McAdmin' );
        $I->seeInCurrentUrl( '/home' );
    }

    public function tryToViewProfile(AcceptanceTester $I) {
        $I->wantTo('view profile page');

        $this->tryToSuccessfullyLogIn($I);

        $I->click('Admin McAdmin');
        $I->amOnPage( '/myprofile' );
    }

    public function tryToCreateNewClient(AcceptanceTester $I) {
        $I->wantTo('create a new client record');

        $this->tryToSuccessfullyLogIn($I);

        $I->click('Clients', '#mainSideNav');
        $I->amOnPage('/client');
        $I->click('Add Client');
        $I->amOnPage('/client/create');
    }

    private function loginUser( AcceptanceTester $I , $email , $password, $name ) {
        $I->amOnPage('/login');
        $I->fillField(['name' => 'login'], $email );
        $I->fillField( 'password' , $password );
        $I->click('Login' , '.btn');
    }
}