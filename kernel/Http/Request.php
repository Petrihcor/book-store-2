<?php

namespace Kernel\Http;

class Request
{
    private array $post;


    public function getPost(): array
    {
        $this->post = $_POST;
        return $this->post;
    }


}