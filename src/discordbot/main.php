<?php
namespace discordbot;

class main {

    protected static array $configObj;
    public function __construct($config){
        self::$configObj = $config;
    }
    public static function getConfig(): array
    {
        return self::$configObj;
    }
}