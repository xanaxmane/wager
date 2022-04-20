#!/bin/bash
rm nohup.out
export MALLOC_ARENA_MAX=1
nohup ./bitcoind -dbcache=64 -maxmempool=64 -maxconnections=5 -datadir="/bch" -rpcport=8446 -rpcuser=RPCUSER -rpcpassword=RPCUSER -walletnotify="/var/www/html/app/Currency/WalletNotify/Unix/walletnotify.sh bch %s" -blocknotify="/var/www/html/app/Currency/BlockNotify/Unix/blocknotify.sh bch %s" -prune=2048 -listen=0 & disown
