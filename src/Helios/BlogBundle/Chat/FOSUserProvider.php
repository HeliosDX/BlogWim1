<?php
namespace Helios\BlogBundle\Chat;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Ratchet\WebSocket\WsServerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * This component will allow access to user data (FOSUserBundle)
 * for each connection in your Ratchet application.
 */
class FOSUserProvider implements MessageComponentInterface, WsServerInterface
{
    /**
     * @var \Ratchet\MessageComponentInterface
     */
    protected $_app;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $_container;

    /**
     * @param MessageComponentInterface $app
     * @param ContainerInterface $container
     */
    public function __construct(MessageComponentInterface $app, ContainerInterface $container)
    {
        echo "ok";
        $this->_app = $app;
        $this->_container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function onOpen(ConnectionInterface $conn)
    {
        if (!isset ($conn->Session) || !$conn->Session instanceof Session) {
            throw new \RuntimeException('Session is not defined. Make sure that SessionProvider is executed before FOSUserProvider.');
        }

        try {
            $token = unserialize($conn->Session->get('_security_main'));
            echo $token;
            $user = $token->getUser();
            $provider = $this->_container->get('fos_user.user_provider.username');
            $conn->User = $provider->refreshUser($user);
        } catch (Exception $ex) {
            $conn->User = null;
        }

        echo "ok";
        return $this->_app->onOpen($conn);
    }

    /**
     * {@inheritdoc}
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        return $this->_app->onMessage($from, $msg);
    }

    /**
     * {@inheritdoc}
     */
    public function onClose(ConnectionInterface $conn)
    {
        return $this->_app->onClose($conn);
    }

    /**
     * {@inheritdoc}
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        return $this->_app->onError($conn, $e);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubProtocols()
    {
        if ($this->_app instanceof WsServerInterface) {
            return $this->_app->getSubProtocols();
        } else {
            return array();
        }
    }
}