<?php namespace App\Currency;

use App\Currency\Option\WalletOption;
use App\User;
use EthereumRPC\EthereumRPC;

class ERC20 extends Ethereum {

    function id(): string {
        return 'erc20';
    }

    function name(): string {
        return "ERC-20";
    }

    public function alias(): string {
        return "erc20";
    }

    public function displayName(): string {
        return "ERC-20";
    }

    function icon(): string {
        return "fab fa-eth";
    }

    public function style(): string {
        return "#7e7f81";
    }

    public function send(string $from, string $to, float $sum) {
        if($from === $this->option('transfer_address')) $password = $this->option('transfer_password');
        else if($from === $this->option('withdraw_address')) $password = $this->option('withdraw_password');
        else $password = User::where('wallet_eth', $from)->first()->_id;

        $geth = new EthereumRPC('127.0.0.1', 8545);
        $erc20 = new \ERC20\ERC20($geth);

        $contract = $this->option('contract_address');

        $token = $erc20->token($contract);

        $data = $token->encodedTransferData($to, strval($sum));
        $transaction = $geth->personal()->transaction($from, $contract)
            ->amount("0") // Amount should be ZERO
            ->data($data);

        $transaction->send($password);
    }

    public function setupWallet() {
        $eth = Currency::find('eth');
        $this->option('transfer_password', $eth->option('transfer_password'));
        $this->option('withdraw_password', $eth->option('withdraw_password'));
        $this->option('transfer_address', $eth->option('transfer_address'));
        $this->option('withdraw_address', $eth->option('withdraw_address'));
    }

    protected function options(): array {
        return [
            new class extends WalletOption {
                public function id() {
                    return "contract_address";
                }

                public function name(): string {
                    return "ERC20 Contract Address (https://etherscan.io/tokens)";
                }
            },
            new class extends WalletOption {
                public function id() {
                    return 'transfer_password';
                }

                public function name(): string {
                    return 'Transfer address password (Same as ETH)';
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
                    return 'Withdraw address password (Same as ETH)';
                }

                public function readOnly(): bool {
                    return true;
                }
            }
        ];
    }

}
