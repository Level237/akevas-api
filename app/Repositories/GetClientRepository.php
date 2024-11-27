<?php

namespace App\Repositories;

use Laravel\Passport\Client;

class GetClientRepository{

    public function getClient(){
        $client=Client::where("id",2)->first();

        return $client;
    }
}
