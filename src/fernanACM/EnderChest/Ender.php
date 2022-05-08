<?php

namespace fernanACM\EnderChest;

use pocketmine\Server;
use pocketmine\player\Player;

use pocketmine\utils\Config;

use pocketmine\plugin\PluginBase;

# Libs
use muqsit\invmenu\InvMenuHandler;
# My files
use fernanACM\EnderChest\commands\EnderCommand;

class Ender extends PluginBase{
    
    public Config $config;
    
    public function onEnable(): void{
        $this->saveResource("config.yml");
		$this->config = new Config($this->getDataFolder() . "config.yml");
        $this->saveDefaultConfig();
        $this->loadEvents();
        # Lib - InvMenu
        foreach ([
                "InvMenu" => InvMenuHandler::class
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
    }
    
    public function loadEvents(){
        $this->getServer()->getCommandMap()->register("enderchest", new EnderCommand($this));
    }
}
