<p align="center">
  <img src="resources/img/misc/win5x_logo_black.png" alt="Win5X Logo">
</p>
<p align="center">
    <img src="https://img.shields.io/static/v1.svg?label=version&message=2.6.2&color=red">
</p>

## WebSockets Setup

Install **fork** of `laravel-echo-server`:

```
npm install -g laravel-echo-server-whisper
```

- If NPM repository is down, install it from [https://github.com/Win5X-Team/laravel-echo-server](https://github.com/Win5X-Team/laravel-echo-server)

#### WebSockets performance

In ```.env``` you can tune these parameters for async pool to match your server settings:

```
WIN5X_SUBSCRIBE_CONCURRENCY=200 # Number of PHP processes to handle client WS requests
WIN5X_SUBSCRIBE_TIMEOUT=5        # Max process lifetime in seconds 
WIN5X_SUBSCRIBE_SLEEP=100        # Sleep time in microseconds for one process
```

All requests are handled by redis sub and then sent to async pool which will use settings above.

Right settings **will** improve response time (which is already fast by default), but don't overthink it.

For example, too much PHP processes with low sleep time will eat server resources. High sleep time will slow requests down.

### Apache Proxy

```$bash
a2enmod proxy
a2enmod proxy_http
```

VirtualHost configuration

**HTTP (Localhost only!)**
```
ProxyPass        /socket.io http://localhost:8443/socket.io
ProxyPassReverse /socket.io http://localhost:8443/socket.io
```

**SSL**
```
ProxyPass        /socket.io https://localhost:8443/socket.io
ProxyPassReverse /socket.io https://localhost:8443/socket.io
```

### .env

Set **BROADCAST_DRIVER** to redis

### Starting

```
php artisan jwt:secret
laravel-echo-server init (When asked, use port 8443 and setup SSL if you want to use it)
laravel-echo-server start
```

Modify **laravel-echo-server.json**
```$json
"databaseConfig": {
	"redis": {
		"password": "redis password (set "redis" as empty object if none)"
	},
	"sqlite": {
		"databasePath": "/database/laravel-echo-server.sqlite"
	},
	"listenWhishper": true,
	"prefixWhishper": "whisper"
},
```

Finally, run `wager:subscribe`:
```
php artisan wager:subscribe
```

This command processes all game requests. Website won't let you log in if this command is not running.

## Troubleshooting

#### Website and client-side provably fair results are different

Float precision tends to work differently in PHP, so there will be slight float differences.

Difference is very small, but sometimes it will be enough to make result invalid.

To fix this, make this change in ```php.ini```:
```
float_precision = -1
```

#### Website: Infinite "Connecting to the server..."
Laravel-echo-server is configured incorrectly. Make sure it's running. If it's not running after you enabled SSL on your server, then modify laravel-echo-server.json with correct cert/cert key path.

#### Website: Infinite loading with text "Failed to connect to the server. Retrying in 5 second(s)..."

Check that ```php artisan wager:subscribe``` is running.

JWT could be misconfigured (run ```php artisan jwt:secret```) or something else is causing server to give 500 error. Check your logs in storage/logs.

This can also happen because of Ping packet loss as could be seen in console. This usually sometimes happens on localhost.

#### "Lost server connection"

```laravel-echo-server``` is not running. 

Use ```laravel-echo-server start``` to start the server.

## Bitcoin nodes setup

```$bash
cd <node folder>/bin
./start.sh
```

Modify credentials in start.sh files.

Full synchronization may take up to 1 week depending on server internet connection/CPU/etc.

### Ethereum

./start.sh is located in geth folder.

Ethereum lightclient node needs peers to work properly.
You may find them [here](https://gist.github.com/rfikki/e2a8c47f4460668557b1e3ec8bae9c11).

Run ```web3.js``` in project root to process Ethereum payments.
```
cd <your website root>
npm install -g pm2
pm2 start web3.js
```

### BTC/BCH

*You may skip this, default configuration in start.sh file already have this configured.*

Bitcoin Core (BTC) and Bitcoin ABC (BCH) are using same default RPC port and datadir. Since you can't use same settings, you need to change their settings individually, otherwise one of them won't work.

### TRX

This wallet is working on remote node. Note: deposits are not instant on it, it will require 1-10 minutes based on your/remote node server load.

### ERC-20

ERC-20 is working on Ethereum protocol, so geth client is used.

You don't have to do anything, if Ethereum is working, then ERC-20 will work too.

## RPC urls

This script uses node RPC to manage wallets.

Put these URLs in admin panel, "Currencies" page.

### BTC
```
http://user:password@localhost:8445
```

### BCH
```
http://user:password@localhost:8446
```

### DOGE
```
http://user:password@localhost:22555
```

### LTC
```
http://user:password@localhost:9332
```

## Remote wallet server

It's possible to set up nodes on a separate server.

#### Copy node binaries from main server to wallet server

#### Replace RPC urls

Replace `localhost` to your second server ip/domain.

#### Modify blocknotify.sh & walletnotify.sh

In `App\BlockNotify\<System>\walletnotify.<sh/bat>` and `App\WalletNotify\<System>\walletnotify.<sh/bat>` change `localhost` to your wallet server ip/domain.

## Wallet Auto-setup

You can't change some wallet settings initially. That's because they are auto-generated.

***Auto-setup requires every node to be running (Except ERC-20 and TRX)***

Click "Auto-setup" in admin panel or open ```/admin/wallet/autoSetup``` in your browser (authenticated as admin user).

Wallet backups are located in ```/storage/app```. Save them and remove them from your server to prevent leaks.
Store ETH and TRX addresses/private keys in text file manually.

## Multiplayer games

First step is to [setup supervisor](https://laravel.com/docs/7.x/queues#supervisor-configuration).

After setup is complete you should start the chain so the game would work infinitely (```php artisan game:chain <game_id>```).

Example:
```
php artisan queue:clear        # Clear queue so unexpected things wouldn't happen
php artisan game:chain all     # Start chain for all games

# Example (debugging)
php artisan game:chain crash   # Start chain for Crash only
```

# Notes

* Never run ```php artisan wager:subscribe``` twice. If you need to restart it, kill previous process, otherwise events will be processed twice - chat will duplicate messages, users could dupe their balance.
* Never run ```php artisan game:chain``` twice. If you need to clear & restart queue chain, run `php artisan queue:clear` before.
* Subscribe command uses cached php classes for games. If you've changed them - restart command so changes could take effect.
