<?php

namespace App\Repositories;

use Laravel\Passport\Client;

class GetClientRepository{

    public function getClient(){
        $client=Client::where("id",1)->first();

        return $client;
    }
}
