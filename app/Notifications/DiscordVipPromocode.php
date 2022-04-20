<?php namespace App\Notifications;

use App\Settings;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

class DiscordVipPromocode extends Notification {

    private string $code;
    private int $usages;
    private float $sum;

    public function __construct(string $code, int $usages, float $sum) {
        $this->code = $code;
        $this->usages = $usages;
        $this->sum = $sum;
    }

    public function via($notifiable) {
        return [DiscordChannel::class];
    }

    public function routeNotificationForDiscord() {
        return Settings::where('name', 'discord_vip_promocode_channel')->first()->value;
    }

    public function toDiscord($notifiable) {
        return (new DiscordMessage())->embed([
            'title' => 'Новый VIP промокод!',
            'description' => "**{$this->code}** - ".number_format($this->sum, 2, '.', '')." руб., {$this->usages} активаций\nАктивировать: https://win5x.com/bonus",
            'color' => '15158332'
        ]);
    }

}
