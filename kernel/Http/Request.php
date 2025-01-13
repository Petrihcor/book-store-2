<?php

namespace Kernel\Http;

class Request
{
    private array $post;
    private array $get;

    public function getPost(): array
    {
        $this->post = $_POST;
        return $this->post;
    }

    public function get()
    {
        $this->get = $_GET;
        return $this->get;
    }


}