<?php

namespace App\Interfaces;

interface GenerateTokenInterface{

    public function generate($clientData,$userData,$password,$request);
}
