<?php
namespace Aphec;

use League\Event\ListenerAcceptorInterface;
use League\Event\ListenerProviderInterface;
use Analog\Analog;

class PluginEventProvider implements ListenerProviderInterface
{
    public function provideListeners(ListenerAcceptorInterface $acceptor)
    {
        $acceptor->addListener('member.add', function ($event, $member) {
        });
    }
}
