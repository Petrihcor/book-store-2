<?php

namespace App\Post;

class Post
{

    public function __construct(
        public int $id,
        public string $category,
        public string $user,
        public string $createData,
        public string $updateData,
        public string $content
    )
    {
    }

    public function getId(): int
    {
        return $this->id;
    }


    public function getCategory(): string
    {
        return $this->category;
    }


    public function getUser(): string
    {
        return $this->user;
    }

    public function getCreateData(): string
    {
        return $this->createData;
    }


    public function getUpdateData(): string
    {
        return $this->updateData;
    }


    public function getContent(): string
    {
        return $this->content;
    }


}