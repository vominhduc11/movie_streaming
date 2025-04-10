<?php
// server.php
require 'vendor/autoload.php';

// Load các file cấu hình và core
require_once __DIR__ . '/app/config/config.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\Socket\SocketServer;

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new SocketServer()
        )
    ),
    SOCKET_PORT,
    SOCKET_HOST
);

echo "Server đang chạy tại " . SOCKET_HOST . ":" . SOCKET_PORT . "\n";

$server->run();
