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

namespace App\Games\Kernel;

class ProvablyFair
{
    private $game;
    private $client_seed;
    private $server_seed;
    private $nonce;

    public function __construct(Game $game, ?string $server_seed = NULL)
    {
        $this->game = $game;
        $this->client_seed = $game->client_seed();
        $this->server_seed = ($server_seed == NULL ? $game->server_seed() : $server_seed);
        $this->nonce = $game->nonce();
    }

    static public function generateServerSeed(): string
    {
        return substr(str_shuffle(MD5(microtime())), 0, 32);
    }

    public function result(): ProvablyFairResult
    {


        return new class ($this->game, $this->client_seed, $this->server_seed, $this->nonce) extends ProvablyFairResult {
        private $server_seed;
        private $client_seed;
        private $nonce;
        private $results;

        public function __construct(Game $game, $client_seed, $server_seed, $nonce)
        {
            $this->server_seed = $server_seed;
            $this->client_seed = $client_seed;
            $this->nonce = $nonce;
            $this->results = $game->result($this);
        }

        public function extractFloats($count): array
        {
            $generator = $this->byteGenerator();
            $bytes = [];

            for ($i = 0; $i < ($count * 4); $i++) {
                array_push($bytes, $generator->current());
                $generator->next();
            }
            $chunks = array_chunk($bytes, 4);
            $result = [];
            array_map(function($byte) use(&$result) {
                $index = -1;
                array_push($result, array_reduce($byte, function($result, $value) use(&$index) {
                    $index++;
                    return $result + ($value / (256 ** ($index + 1)));
                }, 0));
            }, $chunks);
            return $result;
        }

        public function extractFloat(): float
        {
            return $this->extractFloats(1)[0];
        }

        private function byteGenerator()
        {
            $currentRound = 0;

            while (true) {
                $hash = hash_init('sha256', HASH_HMAC, $this->server_seed);
                hash_update($hash, $this->client_seed . ':' . $this->nonce . ':' . $currentRound);
                $final = hash_final($hash);
                $currentRound++;
                $buffer = $this->digestSHA256($final);

                for ($i = 0; $i < 32; $i++) {
                    yield $buffer[$i];
                }
            }
        }

        private function digestSHA256($hash)
        {
            $chunks = str_split($hash, 8);

            for ($i = 0; $i < count($chunks); $i++) {
                $chunks[$i] = hexdec($chunks[$i]);
            }

            $h0 = $chunks[0];
            $h1 = $chunks[1];
            $h2 = $chunks[2];
            $h3 = $chunks[3];
            $h4 = $chunks[4];
            $h5 = $chunks[5];
            $h6 = $chunks[6];
            $h7 = $chunks[7];
            $arr = [($h0 >> 24) & 255, ($h0 >> 16) & 255, ($h0 >> 8) & 255, $h0 & 255, ($h1 >> 24) & 255, ($h1 >> 16) & 255, ($h1 >> 8) & 255, $h1 & 255, ($h2 >> 24) & 255, ($h2 >> 16) & 255, ($h2 >> 8) & 255, $h2 & 255, ($h3 >> 24) & 255, ($h3 >> 16) & 255, ($h3 >> 8) & 255, $h3 & 255, ($h4 >> 24) & 255, ($h4 >> 16) & 255, ($h4 >> 8) & 255, $h4 & 255, ($h5 >> 24) & 255, ($h5 >> 16) & 255, ($h5 >> 8) & 255, $h5 & 255, ($h6 >> 24) & 255, ($h6 >> 16) & 255, ($h6 >> 8) & 255, $h6 & 255];
            array_push($arr, ($h7 >> 24) & 255, ($h7 >> 16) & 255, ($h7 >> 8) & 255, $h7 & 255);
            return $arr;
        }

        public function server_seed(): string
        {
            return $this->server_seed;
        }

        public function nonce(): int
        {
            return $this->nonce;
        }

        public function result(): array
        {
            return $this->results;
        }
    };
	}
}

abstract class ProvablyFairResult
{
    abstract public function extractFloats($count): array;
    abstract public function extractFloat(): float;
    abstract public function server_seed(): string;
    abstract public function nonce(): int;
    abstract public function result(): array;
}

?>
