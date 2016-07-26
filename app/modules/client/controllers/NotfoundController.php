<?php

class NotfoundController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($args = null)
    {
        return Response::view('client::old.notfound', array(), 404);
    }

}