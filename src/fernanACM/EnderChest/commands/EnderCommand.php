<?php

namespace fernanACM\EnderChest\commands;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
# Libs - InvMenu
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\type\InvMenuTypeIds;
use muqsit\invmenu\InvMenu;

use fernanACM\EnderChest\Ender;
use fernanACM\EnderChest\utils\PluginUtils;

class EnderCommand extends Command implements PluginOwned{
    
    private $plugin;

    public function __construct(Ender $plugin){
        $this->plugin = $plugin;
        
        parent::__construct("enderchest", "Open EnderChest by fernanACM", "§cUse: /enderchest | ec", ["ender", "ec"]);
        $this->setPermission("enderchest.acm");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(count($args) == 0){
            if($sender instanceof Player) {
                   $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
                   $menu->setName($this->plugin->config->get("Settings")["EnderChest-name"]);
                   $inv = $menu->getInventory();
                   $inv->setContents($sender->getEnderInventory()->getContents());
                   $menu->setListener(function (InvMenuTransaction $transaction) use ($sender): InvMenuTransactionResult{
                   $sender->getEnderInventory()->setItem($transaction->getAction()->getSlot(), $transaction->getIn());
                   return $transaction->continue();
                   });
                   $menu->send($sender);
                   if($this->plugin->config->get("Settings")["EnderChest-no-sound"]){
                      PluginUtils::PlaySound($sender, $this->plugin->config->get("Settings")["EnderChest-sound"], 50, 1);   
                   }
            } else {
                $sender->sendMessage("Use this command in-game");
            }
        }
        return true;
    }
    
    public function getPlugin(): Plugin{
        return $this->plugin;
    }

    public function getOwningPlugin(): Ender{
        return $this->plugin;
    }
}
