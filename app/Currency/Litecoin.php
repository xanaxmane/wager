<?php namespace App\Currency;

use Illuminate\Support\Facades\Log;
use Nbobtc\Command\Command;
use Nbobtc\Http\Client;

class Litecoin extends V16RPCBitcoin {

    function id(): string {
        return "ltc";
    }

    function name(): string {
        return "LTC";
    }

    public function alias(): string {
        return 'litecoin';
    }

    public function displayName(): string {
        return "Litecoin";
    }

    function icon(): string {
        return "fas fa-ltc";
    }

    public function style(): string {
        return "#bfbbbb";
    }

}
