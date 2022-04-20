<?php namespace App\Currency;

use App\Settings;
use Illuminate\Support\Facades\Log;
use Nbobtc\Command\Command;

class BitcoinCash extends V17RPCBitcoin {

    function id(): string {
        return "bch";
    }

    function name(): string {
        return "BCH";
    }

    function icon(): string {
        return "fas fa-bch";
    }

    public function alias(): string {
        return 'bitcoin-cash';
    }

    public function displayName(): string {
        return "Bitcoin Cash";
    }

    function style(): string {
        return "#8dc351";
    }

    public function coldWalletBalance(): float {
        return json_decode(file_get_contents('https://rest.bitcoin.com/v2/address/details/'.$this->option('transfer_address')))->balance;
    }

    public function hotWalletBalance(): float {
        return json_decode(file_get_contents('https://rest.bitcoin.com/v2/address/details/'.$this->option('withdraw_address')))->balance;
    }

}
