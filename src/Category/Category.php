<?php

namespace App\Category;

class Category
{
    public function __construct(
        public int $id,
        public string $name,
        public string $user,
        public string $description,
        public string $createData,
        public string $updateData,
    )
    {
    }
}