#!/bin/bash
rm nohup.out
export MALLOC_ARENA_MAX=1
nohup ./dogecoind -dbcache=64 -maxmempool=64 -maxconnections=5 -printtoconsole -rpcuser=RPCUSER -rpcpassword=RPCUSER -prune=2200 -walletnotify="/var/www/html/app/Currency/WalletNotify/Unix/walletnotify.sh doge %s" -blocknotify="/var/www/html/app/Currency/BlockNotify/Unix/blocknotify.sh doge %s" & disown
