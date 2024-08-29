<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    private $messages = [];

    protected function getMessage(string $key):array|string|int{
        return $this->messages[$key];

    }

    protected function setMessage(string $key,array $values):void{
        $this->messages[$key] = $values;
    }


}
