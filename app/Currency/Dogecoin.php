<?php namespace App\Currency;

use Illuminate\Support\Facades\Log;
use Nbobtc\Command\Command;
use Nbobtc\Http\Client;

class Dogecoin extends V16RPCBitcoin {

    function id(): string {
        return "doge";
    }

    function name(): string {
        return "DOGE";
    }

    public function alias(): string {
        return "dogecoin";
    }

    public function displayName(): string {
        return "Dogecoin";
    }

    function icon(): string {
        return "fas fa-dogecoin";
    }

    public function style(): string {
        return "#c2a633";
    }

}
