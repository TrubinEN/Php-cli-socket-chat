<?php

namespace TrubinEN;

class Server
{

    private $server;

    public function __construct($server)
    {
        $this->server = $server;
        $this->run();
    }

    /**
     * Run socket server
     *
     */
    public function run()
    {
        while (true) {
            $socket = socket_accept($this->server);
            $message = '';
            while(($line = socket_read($socket, 1024)) !== ""){
                $message .= $line;
            }
            $message = trim($message);
            echo "\033[34mFriend\033[0m: {$message}" . PHP_EOL;
            socket_close($socket);
        }
    }
}