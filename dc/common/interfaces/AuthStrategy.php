<?php
namespace DynCom\dc\common\interfaces;
use DynCom\dc\dcShop\classes\User;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 05.01.2016
 * Time: 12:32
 */

interface UserAuthenticationStrategy
{

    /**
     * @param array $authData
     * @return mixed
     */
    public function attemptAuthentication(array $authData = []);

    /**
     * @param User $user
     * @return mixed
     */
    public function logoutUser(User $user);

    /**
     * @param User $user
     * @return mixed
     */
    public function forceLogin(User $user);

}