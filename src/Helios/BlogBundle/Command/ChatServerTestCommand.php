<?php

namespace Helios\BlogBundle\Command; 

//use Lsw\MemcacheBundle\DataCollector\MemcacheDataCollector;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

use Ratchet\Session\SessionProvider;
use Symfony\Component\HttpFoundation\Session\Storage\Handler;
use Ratchet\App;

use Helios\BlogBundle\Chat\Chat1;
use Helios\BlogBundle\Chat\FOSUserProvider;


require dirname(__DIR__) . './../../../vendor/autoload.php';

class ChatServerTestCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('chattest:server')
            ->setDescription('Start the Chat server');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //$chat = $this->getContainer()->get('heliosblog.chat');
        $memcache = new \Memcache();
        $memcache->connect('localhost', 11211);

        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new SessionProvider(
                        new Chat1(),
                            new Handler\MemcacheSessionHandler($memcache)
                    )
                )
            ),
            8080
        );

        echo "Chat server is running\n";

        $server->run();
    }
}