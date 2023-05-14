<?php

namespace App\Interfaces;

interface AuthInterface
{
    /**
     * Create New User
     *
     * @return object Registered User
     */
    public function register(array $data);
}
