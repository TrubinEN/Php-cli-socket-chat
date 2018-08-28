<?php

namespace TrubinEN;

use TrubinEN\Client;
use TrubinEN\Servet;

class Chat
{
    private $toPort;
    private $fromPort;
    private $toIp;
    private $fromIp;
    private $server;

    private $argv = [
        'toPort',
        'fromPort',
        'toIp',
        'fromIp'
    ];

    public function __construct()
    {
        try {
            if (($error = $this->setSettings()) !== true) {
                throw new \Exception($error->getMessage());
            }

            if (($error = $this->createSocket()) !== true) {
                throw new \Exception($error->getMessage());
            }

            if (($error = $this->build()) !== true) {
                throw new \Exception($error->getMessage());
            }
        } catch (\Exception $exception) {
            die($exception->getMessage() . PHP_EOL);
        }
    }


    /**
     * Get arr getopt value
     *
     * @return array
     */
    public function getArgvName(): array
    {
        $cliArgv = [];
        foreach ($this->argv as $value) {
            $cliArgv[] = $value . '::';
        }
        return $cliArgv;
    }

    /**
     * Set app settings
     *
     * @return bool
     * @throws \Exception
     */
    private function setSettings(): ?bool
    {
        $params = getopt('', $this->getArgvName());

        foreach ($this->argv as $value) {
            if (empty($params[$value])) {
                throw new \Exception("For work, set the parameter: $value");
            }
            $this->$value = $params[$value];
        }

        return true;
    }

    /**
     * Create socket
     *
     */
    public function createSocket(): ?bool
    {
        $this->server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($this->server === false)
            throw new \Exception("Socket create failed: " . socket_strerror(socket_last_error()));

        socket_set_option($this->server, SOL_SOCKET, SO_REUSEADDR, 1);

        if (!socket_bind($this->server, $this->fromIp, $this->fromPort))
            throw new \Exception("Socket create failed: " . socket_strerror(socket_last_error()));

        if (!socket_listen($this->server, 1))
            throw new \Exception("Socket create failed: " . socket_strerror(socket_last_error()));

        return true;
    }

    /**
     * Create fork process client/server
     *
     */
    public function build(): ?bool
    {
        $pid = pcntl_fork();

        if ($pid == -1) {
            throw new \Exception("Failed to create fork process");
        } elseif ($pid) {
            (new Server($this->server));
        } else {
            (new Client($this->toIp, $this->toPort, $pid));
        }

        return true;
    }
}



