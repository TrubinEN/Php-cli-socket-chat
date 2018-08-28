<?php

namespace TrubinEN;

class Client
{
    private $toIp;
    private $toPort;
    private $pid;

    public function __construct($toIp, $toPort, $pid)
    {
        $this->toIp = $toIp;
        $this->toPort = $toPort;
        $this->pid = $pid;

        $this->run();
    }

    public function run()
    {
        while ($message = fgets(STDIN)) {
            // exit
            if (($message = trim($message)) === ":exit") {
                shell_exec("exec kill -9 {$this->pid}");
                exit(0);
            }
            // Send message
            $clientSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            if ($clientSocket === false) {
                die("Socket create failed " . socket_strerror(socket_last_error()) . PHP_EOL);
            }
            $connect = @socket_connect($clientSocket, $this->toIp, $this->toPort);
            if ($connect === false) {
                echo "\033[31mYour friend is offline!\033[0m" . PHP_EOL;
                continue;
            }
            socket_write($clientSocket, $message, strlen($message));
            socket_close($clientSocket);
        }
    }
}