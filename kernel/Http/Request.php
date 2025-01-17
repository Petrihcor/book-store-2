<?php

namespace Kernel\Http;

class Request
{
    private array $post;
    private array $get;
    private array $files;

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


    public function getFiles(): array
    {
        $this->files = $_FILES;
        return $this->files;
    }




}