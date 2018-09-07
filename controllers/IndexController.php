<?php
namespace controllers;

class IndexController{
    function index(){
       view('index.index');
    }

    public function info(){
        echo phpinfo();
    }
}