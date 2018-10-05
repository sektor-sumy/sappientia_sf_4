<?php

namespace App\Exception;

/**
 * Class UserAlreadyRegisteredException
 */
class UserAlreadyRegisteredException extends BasicException
{
    protected $message = 'User already registered.';
}
