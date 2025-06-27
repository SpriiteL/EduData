<?php

class LoginCest
{
    public function loginWithValidCredentials(\AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField('email', 'admin@admin.com');
        $I->fillField('password', 'admin'); 
        $I->click('Connexion');
        $I->see('Inventaire');
    }
}
