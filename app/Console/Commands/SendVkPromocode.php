<?php

namespace App\Console\Commands;

use App\Notifications\DiscordPromocode;
use App\Settings;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Intervention\Image\Facades\Image;
use NotificationChannels\Discord\DiscordChannel;
use VK\Client\VKApiClient;

class SendVkPromocode extends Command
{

    // https://oauth.vk.com/authorize?client_id=7434559&display=page&redirect_uri=https://oauth.vk.com/blank.html&scope=offline,photos,wall,groups&response_type=token&v=5.65

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'win5x:send-social-promocode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send vk.com promocode';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $sum = floatval(Settings::where('name', 'vk_promo_sum')->first()->value);
        $usages = intval(Settings::where('name', 'vk_promo_usages')->first()->value);

        $promocode = \App\Promocode::create([
            'code' => \App\Promocode::generate(),
            'used' => [],
            'sum' => $sum,
            'usages' => $usages,
            'times_used' => 0,
            'expires' => \Carbon\Carbon::now()->addHours(3)
        ]);

        $img = Image::make(resource_path('img/misc/template.png'));
        $img->text($promocode->code, 640, 360, function($font) {
            $font->file(resource_path('webfonts/DejaVuSans.ttf'));
            $font->size(95);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('middle');
        });
        $img->text(number_format($promocode->sum, 2, '.', ''), 135, 640, function($font) {
            $font->file(resource_path('webfonts/DejaVuSans.ttf'));
            $font->size(35);
            $font->color('#ffffff');
            $font->align('left');
            $font->valign('middle');
        });

        $path = public_path('img/misc/latest_promocode.png');
        $img->save($path);

        $group_id = intval(Settings::where('name', 'vk_group_id')->first()->value);
        $user_access_token = Settings::where('name', 'vk_user_access_token')->first()->value;

        $vk = new VKApiClient();

        $server = $vk->photos()->getWallUploadServer($user_access_token, ['group_id' => $group_id]);
        $photo = $vk->getRequest()->upload($server['upload_url'], 'photo', $path);

        $saved_photo = $vk->photos()->saveWallPhoto($user_access_token, [
            'server' => $photo['server'],
            'photo' => $photo['photo'],
            'hash' => $photo['hash'],
            'group_id' => $group_id
        ])[0];

        $vk->wall()->post($user_access_token, [
            'owner_id' => -$group_id,
            'message' => 'Новый промокод '.$promocode->code.' -- '.$sum.' руб. на '.$usages.' активаций! Использовать: https://win5x.com/bonus',
            'from_group' => 1,
            'attachments' => 'photo'.$saved_photo['owner_id'].'_'.$saved_photo['id']
        ]);

        Notification::route('discord', Settings::where('name', 'discord_promocode_channel')->first()->value)->notify(new DiscordPromocode($promocode->code, $usages, $sum));
    }
}
