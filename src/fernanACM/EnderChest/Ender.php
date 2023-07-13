<?php
    
#      _       ____   __  __ 
#     / \     / ___| |  \/  |
#    / _ \   | |     | |\/| |
#   / ___ \  | |___  | |  | |
#  /_/   \_\  \____| |_|  |_|
# The creator of this plugin was fernanACM.
# https://github.com/fernanACM
 
namespace fernanACM\EnderChest;

use pocketmine\Server;

use pocketmine\utils\Config;

use pocketmine\plugin\PluginBase;
# Libs
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\InvMenuHandler;
use muqsit\simplepackethandler\SimplePacketHandler;

use DaPigGuy\libPiggyUpdateChecker\libPiggyUpdateChecker;

use CortexPE\Commando\PacketHooker;
use CortexPE\Commando\BaseCommand;
# My files
use fernanACM\EnderChest\commands\EnderCommand;

class Ender extends PluginBase{
    
    /** @var Config $config */
    public Config $config;

    /** @var Ender $instance */
    private static Ender $instance;

    # CheckConfig
    public const CONFIG_VERSION = "1.0.0";
    
    /**
     * @return void
     */
    public function onLoad(): void{
        self::$instance = $this;
        $this->loadFiles();
    }

    /**
     * @return void
     */
    public function onEnable(): void{
        $this->loadCheck();
        $this->loadVirions();
        $this->loadCommands();
    }

    /**
     * @return void
     */
    public function loadFiles(): void{
        $this->saveResource("config.yml");
	    $this->config = new Config($this->getDataFolder() . "config.yml");
    }

    /**
     * @return void
     */
    public function loadCheck(){
        # CONFIG
        if((!$this->config->exists("config-version")) || ($this->config->get("config-version") != self::CONFIG_VERSION)){
            rename($this->getDataFolder() . "config.yml", $this->getDataFolder() . "config_old.yml");
            $this->saveResource("config.yml");
            $this->getLogger()->critical("Your configuration file is outdated.");
            $this->getLogger()->notice("Your old configuration has been saved as config_old.yml and a new configuration file has been generated. Please update accordingly.");
        }
    }

    /**
     * @return void
     */
    public function loadVirions(): void{
        foreach([
            "InvMenu" => InvMenu::class,
            "SimplePacketHandler" => SimplePacketHandler::class,
            "Commando" => BaseCommand::class,
            "libPiggyUpdateChecker" => libPiggyUpdateChecker::class
            ] as $virion => $class
        ){
            if(!class_exists($class)){
                $this->getLogger()->error($virion . " virion not found. Please download EnderChest from Poggit-CI or use DEVirion (not recommended).");
                $this->getServer()->getPluginManager()->disablePlugin($this);
                return;
            }
        }
        if(!InvMenuHandler::isRegistered()){
            InvMenuHandler::register($this);
        }

        if(!PacketHooker::isRegistered()){
            PacketHooker::register($this);
        }
        # Update
        libPiggyUpdateChecker::init($this);
    }
    
    /**
     * @return void
     */
    public function loadCommands(): void{
        Server::getInstance()->getCommandMap()->register("enderchest", new EnderCommand($this, "enderchest", "Open EnderChest by fernanACM", ["ec", "ender"]));
    }
    
    /**
     * @return Ender
     */
    public static function getInstance(): Ender{
        return self::$instance;
    }
}
