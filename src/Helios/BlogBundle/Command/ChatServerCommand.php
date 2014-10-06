<?php

namespace Helios\BlogBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

use Ratchet\Session\SessionProvider;
use Symfony\Component\HttpFoundation\Session\Storage\Handler;
use Ratchet\App;

use Helios\BlogBundle\Chat\Chat;
use Helios\BlogBundle\Chat\FOSUserProvider;


require dirname(__DIR__) . './../../../vendor/autoload.php';

class ChatServerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('chat:server')
            ->setDescription('Start the Chat server');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $chat = $this->getContainer()->get('heliosblog.chat');
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    $chat
                )
            ),
            8080
        );

        $server->run();
    }
}