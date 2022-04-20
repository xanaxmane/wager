<?php namespace App\Currency;

use App\Currency\Option\WalletOption;
use App\User;
use Bezhanov\Ethereum\Converter;
use Illuminate\Support\Facades\Log;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;
use Web3\Web3;

class Ethereum extends Currency {

    function id(): string {
        return 'eth';
    }

    function name(): string {
        return "ETH";
    }

    public function alias(): string {
        return "ethereum";
    }

    public function displayName(): string {
        return "Ethereum";
    }

    function icon(): string {
        return "fab fa-eth";
    }

    public function style(): string {
        return "#627eea";
    }

    public function isRunning(): bool {
        return $this->coldWalletBalance() != -1;
    }

    public function newWalletAddress(): string {
        $returnedValue = 'Error';

        $web3 = $this->getClient();
        $web3->getPersonal()->newAccount(auth()->user()->_id, function($err, $account) use(&$returnedValue) {
            if($err !== null) {
                Log::critical($err);
                return null;
            }

            $returnedValue = $account;
        });
        return $returnedValue;
    }

    private function balance($account) {
        try {
            $returnedValue = 0;
            $web3 = $this->getClient();
            $web3->getEth()->getBalance($account, function ($err, $balance) use (&$returnedValue) {
                if ($err != null) $returnedValue = $balance;
            });
            return (new Converter())->fromWei($returnedValue);
        } catch (\Exception $e) {
            return -1;
        }
    }

    public function setupWallet() {
        $web3 = $this->getClient();
        $hotWallet = 'Error'; $coldWallet = 'Error';

        $hotPass = substr(md5(mt_rand()), 0, 32);
        $coldPass = substr(md5(mt_rand()), 0, 32);

        $this->option('transfer_password', $coldPass);
        $this->option('withdraw_password', $hotPass);

        $web3->getPersonal()->newAccount($hotPass, function($err, $account) use(&$hotWallet) {
            $hotWallet = $account;
        });
        $web3->getPersonal()->newAccount($coldPass, function($err, $account) use (&$coldWallet) {
            $coldWallet = $account;
        });
        if($hotWallet === 'Error' || $coldWallet === 'Error') return null;

        $this->option('transfer_address', $coldWallet);
        $this->option('withdraw_address', $hotWallet);
    }

    public function send(string $from, string $to, float $sum) {
        if($from === $this->option('transfer_address')) $password = $this->option('transfer_password');
        else if($from === $this->option('withdraw_address')) $password = $this->option('withdraw_password');
        else $password = User::where('wallet_eth', $from)->first()->_id;

        $this->getClient()->getPersonal()->unlockAccount($from, $password, function ($err, $unlocked) use($to, $sum, $from) {
            if($err != null) {
                Log::critical($err);
                return;
            }

            $this->getClient()->getEth()->sendTransaction([
                'to' => $to,
                'from' => $from,
                'value' => '0x' . dechex(intval((new Converter())->toWei(strval($sum), 'ether')))
            ], function ($err) {
                if ($err !== null) Log::critical($err);
            });
        });
    }

    public function hotWalletBalance(): float {
        return $this->balance($this->option('withdraw_address')) ?? -1;
    }

    public function coldWalletBalance(): float {
        return $this->balance($this->option('transfer_address')) ?? -1;
    }

    protected function getClient() {
        return new Web3(new HttpProvider(new HttpRequestManager('http://localhost:8545', 30)));
    }

    public function process(string $wallet) {
        $this->getClient()->getEth()->blockNumber(function($err, $number) use($wallet) {
            if($err != null) {
                Log::critical($err);
                return;
            }
            if($number == null) return;

            $this->getClient()->getEth()->getTransactionByHash($wallet, function($err, $response) use($number, $wallet) {
                if($err != null) {
                    Log::critical($err);
                    return;
                }
                if($response == null) return;

                //if(isset($response->blockNumber)) $confirmations = intval($number->toString()) - hexdec($response->blockNumber);
                if(isset($response->to) && isset($response->blockNumber)) $this->accept(intval(Currency::find('eth')->option('confirmations')), $response->to, $wallet, (new Converter())->fromWei(intval($response->value)));
            });
        });
    }

    protected function options(): array {
        return [
            new class extends WalletOption {
                public function id() {
                    return 'transfer_password';
                }

                public function name(): string {
                    return 'Transfer address password';
                }

                public function readOnly(): bool {
                    return true;
                }
            },
            new class extends WalletOption {
                function id() {
                    return 'withdraw_password';
                }

                function name(): string {
                    return 'Withdraw address password';
                }

                public function readOnly(): bool {
                    return true;
                }
            }
        ];
    }

}
