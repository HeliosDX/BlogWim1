<?php
namespace Helios\BlogBundle\Chat;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\WebSocket\WsServerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Chat1 implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = array();
    }

    public function onOpen(ConnectionInterface $conn) {
        $pseudo = $conn->WebSocket->request->getQuery()->__toString();

        // Store the new connection to send messages to later
        $this->clients[$pseudo] = $conn;
        //$this->clients->attach($conn);

        echo $pseudo . " s'est connecté ! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg);
        $date = date('Y-m-d');
        $numRecv = count($this->clients) - 1;
        echo sprintf('['.$date.'] Connection %d sending message "%s" to %s' . "\n"
            , $from->resourceId, $data->{'data'}, $data->{'to'});

        $client = $this->clients[$data->{'to'}];
        $client->send($data->{'data'});
    }

    public function onClose(ConnectionInterface $conn) {
        $pseudo = $conn->WebSocket->request->getQuery()->__toString();
        // The connection is closed, remove it, as we can no longer send it messages
        unset($this->clients[$pseudo]);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}