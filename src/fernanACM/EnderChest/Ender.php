<?php

namespace fernanACM\EnderChest;

use pocketmine\Server;
use pocketmine\player\Player;

use pocketmine\utils\Config;

use pocketmine\plugin\PluginBase;

# Libs
use muqsit\invmenu\InvMenuHandler;
use muqsit\simplepackethandler\SimplePacketHandler;
use CortexPE\Commando\PacketHooker;
# My files
use fernanACM\EnderChest\commands\EnderCommand;

class Ender extends PluginBase{
    
    public Config $config;
    public static $instance;
    
    public function onEnable(): void{
        self::$instance = $this;
        $this->saveResource("config.yml");
	$this->config = new Config($this->getDataFolder() . "config.yml");
        $this->saveDefaultConfig();
        $this->loadEvents();
        # Libs - InvMenu, Commando and SimplePacketHandler
        foreach ([
                "InvMenu" => InvMenuHandler::class,
                "Commando" => PacketHooker::class,
                "SimplePacketHandler" => SimplePacketHandler::class
            ] as $virion => $class
        ) {
            if (!class_exists($class)) {
                $this->getLogger()->error($virion . " virion not found. Please download EnderChest from Poggit-CI or use DEVirion (not recommended).");
                $this->getServer()->getPluginManager()->disablePlugin($this);
                return;
            }
        }
        # InvMenu
        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }
        #Commando
        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }
    }
    
    public function loadEvents(){
        $this->getServer()->getCommandMap()->register("enderchest", new EnderCommand($this, "enderchest", "Open EnderChest by fernanACM", ["ec", "ender"]));
    }
    
    public static function getInstance(): Ender{
        return self::$instance;
    }
}
