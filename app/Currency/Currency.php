<?php namespace App\Currency;

use App\Currency\Option\WalletOption;
use App\Events\Deposit;
use App\Invoice;
use App\Transaction;
use App\User;
use Illuminate\Support\Facades\Cache;
use MongoDB\BSON\Decimal128;
use VK\Actions\Wall;

abstract class Currency {

    abstract function id(): string;

    abstract function name(): string;

    abstract function alias(): string;

    abstract function displayName(): string;

    abstract function icon(): string;

    abstract function style(): string;

    abstract function newWalletAddress(): string;

    protected abstract function options(): array;

    public function tokenPrice() {
        if(!Cache::has('conversions:'.$this->alias()))
            Cache::put('conversions:'.$this->alias(), file_get_contents("https://api.coingecko.com/api/v3/coins/{$this->alias()}?localization=false&market_data=true"), now()->addHours(1));
        $json = json_decode(Cache::get('conversions:'.$this->alias()));
        return $json->market_data->current_price->usd;
    }

    public function convertUSDToToken(float $usdAmount) {
        return $usdAmount / $this->tokenPrice();
    }

    public function convertTokenToUSD(float $tokenAmount) {
        return $tokenAmount * $this->tokenPrice();
    }

    /** @return WalletOption[] */
    public function getOptions(): array {
        return array_merge($this->options(), [
            new class extends WalletOption {
                public function id() {
                    return "transfer_address";
                }

                public function name(): string {
                    return "Transfer deposits to this address";
                }

                public function readOnly(): bool {
                    return true;
                }
            },
            new class extends WalletOption {
                public function id() {
                    return 'withdraw_address';
                }

                public function name(): string {
                    return 'Transfer withdraws from this address';
                }

                public function readOnly(): bool {
                    return true;
                }
            },
            new class extends WalletOption {
                public function id() {
                    return 'withdraw_manual_trigger';
                }

                public function name(): string {
                    return 'Verify withdrawal manually if account balance including total withdrawal amount is higher than';
                }
            },
            new class extends WalletOption {
                function id() {
                    return "confirmations";
                }

                function name(): string {
                    return "Required confirmations";
                }
            },
            new class extends WalletOption {
                function id() {
                    return "demo";
                }

                function name(): string {
                    return "Demo value";
                }
            },
            new class extends WalletOption {
                public function id() {
                    return 'min_invest';
                }

                public function name(): string {
                    return 'Min. investment amount';
                }
            },
            new class extends WalletOption {
                public function id() {
                    return 'invest_commission';
                }

                public function name(): string {
                    return 'Investment withdraw commission (%)';
                }
            },
            new class extends WalletOption {
                function id() {
                    return 'high_roller_requirement';
                }

                function name(): string {
                    return '"High Rollers" tab min bet amount';
                }
            },
            new class extends WalletOption {
                public function id() {
                    return 'quiz';
                }

                public function name(): string {
                    return 'Trivia answer reward';
                }
            },
            new class extends WalletOption {
                public function id() {
                    return 'discord';
                }

                public function name(): string {
                    return 'Discord reward';
                }
            },
            new class extends WalletOption {
                public function id() {
                    return 'weekly_bonus';
                }

                public function name(): string {
                    return 'Bronze VIP Weekly Bonus (Multiplied by user VIP level)';
                }
            },
            new class extends WalletOption {
                public function id() {
                    return 'rain';
                }

                public function name(): string {
                    return 'Rain reward';
                }
            },
            new class extends WalletOption {
                public function id() {
                    return 'bonus_wheel';
                }

                public function name(): string {
                    return 'Bonus wheel minimal reward';
                }
            },
            new class extends WalletOption {
                public function id() {
                    return 'referral_bonus_wheel';
                }

                public function name(): string {
                    return '10 active referrals bonus wheel minimal reward';
                }
            },
            new class extends WalletOption {
                public function id() {
                    return 'referral_bonus';
                }

                public function name(): string {
                    return 'Active referral bonus';
                }
            },
            new class extends WalletOption {
                function id() {
                    return "fee";
                }

                function name(): string {
                    return "Transaction fee";
                }
            },
            new class extends WalletOption {
                function id() {
                    return "withdraw";
                }

                function name(): string {
                    return "Minimal withdraw amount";
                }
            },
            new class extends WalletOption {
                public function id() {
                    return "vip_bronze";
                }

                public function name(): string {
                    return "Bronze VIP wager requirement";
                }
            },
            new class extends WalletOption {
                public function id() {
                    return "vip_silver";
                }

                public function name(): string {
                    return "Silver VIP wager requirement";
                }
            },
            new class extends WalletOption {
                public function id() {
                    return "vip_gold";
                }

                public function name(): string {
                    return "Gold VIP wager requirement";
                }
            },
            new class extends WalletOption {
                public function id() {
                    return "vip_platinum";
                }

                public function name(): string {
                    return "Platinum VIP wager requirement";
                }
            },
            new class extends WalletOption {
                public function id() {
                    return "vip_diamond";
                }

                public function name(): string {
                    return "Diamond VIP wager requirement";
                }
            }
        ]);
    }

    public function option(string $key, string $value = null): string {
        if($value == null) return \App\Currency::where('currency', $this->id())->first()->data[$key] ?? '';
        $data = \App\Currency::where('currency', $this->id())->first()->data;
        $data[$key] = $value;
        \App\Currency::where('currency', $this->id())->first()->update([
            'data' => $data
        ]);
        return $value;
    }

    abstract function isRunning(): bool;

    abstract function process(string $wallet);

    abstract function send(string $from, string $to, float $sum);

    abstract function setupWallet();

    abstract function coldWalletBalance(): float;

    abstract function hotWalletBalance(): float;

    public static function all(): array {
        return [
            new Bitcoin(),
            new Ethereum(),
            new ERC20(),
            new Litecoin(),
            new Dogecoin(),
            new BitcoinCash(),
            new Tron()
        ];
    }

    public static function find(string $id): ?Currency {
        foreach (self::all() as $currency) if($currency->id() == $id) {
            if(\App\Currency::where('currency', $currency->id())->first() == null) {
                \App\Currency::create([
                    'currency' => $currency->id(),
                    'data' => []
                ]);
            }
            return $currency;
        }
        return null;
    }

    protected function accept(int $confirmations, string $wallet, string $id, float $sum) {
        $user = User::where('wallet_'.$this->id(), $wallet)->first();
        if($user == null) return;

        $invoice = Invoice::where('id', $id)->first();
        if($invoice == null) {
            $invoice = Invoice::create([
                'user' => $user->_id,
                'sum' => new Decimal128($sum),
                'type' => 'currency',
                'currency' => $this->id(),
                'id' => $id,
                'confirmations' => $confirmations,
                'status' => 0
            ]);
            event(new Deposit($user, $this, $sum));
        }
        else $invoice->update([
            'confirmations' => $confirmations
        ]);

        if($invoice->status == 0 && $invoice->confirmations >= intval($this->option('confirmations'))) {
            $invoice->update(['status' => 1]);
            $user->balance($this)->add($sum, Transaction::builder()->message('Deposit')->get());
            $this->send($wallet, $this->option('transfer_address'), $sum);
        }
    }

}
