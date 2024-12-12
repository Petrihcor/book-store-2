<?php

namespace App\User;

class User
{

    public function __construct(
        public string $name,
    )
    {
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }




}