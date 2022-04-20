<?php
/**
*
* @ This file is created by http://DeZender.Net
* @ deZender (PHP7 Decoder for ionCube Encoder)
*
* @ Version			:	4.1.0.1
* @ Author			:	DeZender
* @ Release on		:	29.08.2020
* @ Official site	:	http://DeZender.Net
*
*/

namespace App\Console\Commands;

use App\WebSocket\WebSocketWhisper;

class Subscribe extends \Illuminate\Console\Command
{
	protected $signature = 'win5x:subscribe';
	protected $description = 'Subscribe to redis updates';

	public function __construct()
	{
		parent::__construct();
	}

	public function handle()
	{
		$loop = \React\EventLoop\Factory::create();
		$factory = new \Clue\React\Redis\Factory($loop);

		$client = $factory->createLazyClient(env('APP_DEBUG') ? 'localhost' : 'redis://' . env('REDIS_HOST') . ':' . env('REDIS_PORT'));
		$channel = 'whisper.private-Whisper';
		$client->subscribe($channel)->then(function() use($channel) {
			echo 'Subscribed to ' . $channel . ' channel' . PHP_EOL;
		}, function(\Exception $e) use($client) {
			$client->close();
			echo 'Unable to subscribe: ' . $e->getMessage() . PHP_EOL;
		});
		$client->on('message', function($channel, $message) {
			try {
				$message = json_decode($message);

				if (!isset($message->event)) {
					throw new \Exception('Invalid JSON message: ' . $message);
				}

				$event = str_replace('client-', '', $message->event);
				$whisper = WebSocketWhisper::find($event);

				if ($whisper == NULL) {
					throw new \Exception('Unknown event ' . $event);
				}

				$whisper->user = ($message->data->jwt === '-' ? NULL : \Tymon\JWTAuth\Facades\JWTAuth::setToken($message->data->jwt)->authenticate());
				$whisper->id = $message->data->id;
				$response = $whisper->process($message->data->data);
				$whisper->sendResponse($response);
				echo 'Event ' . $event . ' with data ' . json_encode($message->data->data) . ' -> ' . json_encode($response) . PHP_EOL;
			}
			catch (\Throwable $exception) {
				echo $exception->getMessage() . PHP_EOL;
				echo $exception->getTraceAsString() . PHP_EOL;
			}
		});
		$loop->run();
	}
}

?>
