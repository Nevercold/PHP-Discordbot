<?php

use discordbot\main;
use phpcord\channel\BaseTextChannel;
use phpcord\client\Activity;
use phpcord\command\Command;
use phpcord\Discord;
use phpcord\event\client\ClientReadyEvent;
use phpcord\event\EventListener;
use phpcord\event\message\MessageSendEvent;
use phpcord\guild\GuildMessage;
use phpcord\intents\IntentsManager;


require_once __DIR__ . "/vendor/autoload.php";
require __DIR__."/config/config.php";

$main = new discordbot\main($config);

$discord = new Discord([ "debugMode" => $main->getConfig()['debug'] ]);
$discord->enableCommandMap();
$discord->getCommandMap()->addPrefix($main->getConfig()['prefix']);




$activity = new Activity();
$discord->setIntents(IntentsManager::allIntentsSum());
try {
    $discord->registerEvents(new class implements EventListener {
        public function onReady(ClientReadyEvent $event){
            $activity = new Activity();
            if(main::getConfig()['presence']['type'] == "PLAYING") {
                $activity->setPlaying(main::getConfig()['presence']['text']);
            } else if(main::getConfig()['presence']['type'] == "LISTENING"){
                $activity->setListening(main::getConfig()['presence']['text']);
            } else if(main::getConfig()['presence']['type'] == "STREAMING"){
                $activity->setStreaming(main::getConfig()['presence']['text']);
            } else if(main::getConfig()['presence']['type'] == "COMPETING"){
                $activity->setCompeting(main::getConfig()['presence']['text']);
            } else {
                $activity->setPlaying(main::getConfig()['presence']['text']);
            }
            $activity->setStatus(main::getConfig()['presence']['status']);
            Discord::getInstance()->getClient()->setActivity($activity);
        }
        public function onSend(MessageSendEvent $event)
        {

        }


    });
} catch (ReflectionException $e) {
    die("bot cannot be start");
}

$discord->getCommandMap()->register(new class extends Command {
    public function __construct() {
        parent::__construct("say");
    }
    public function execute(BaseTextChannel $channel, GuildMessage $message, $args): void {
        if($message->getMember()->isHuman()) $channel->send($args);
    }
});


try {
    $discord->login(main::getConfig()['bot_token']);
} catch (\phpcord\exception\ClientException $e) {
    die("bot cannot be start");
}