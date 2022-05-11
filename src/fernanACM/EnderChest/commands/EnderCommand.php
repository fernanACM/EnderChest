<?php

namespace fernanACM\EnderChest\commands;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
# Lib - InvMenu
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\type\InvMenuTypeIds;
use muqsit\invmenu\InvMenu;
# Lib - Commando
use CortexPE\Commando\BaseCommand;

use fernanACM\EnderChest\Ender;
use fernanACM\EnderChest\utils\PluginUtils;

class EnderCommand extends BaseCommand{
    
    protected function prepare(): void{
        $this->setPermission("enderchest.acm");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void{
        if (!$sender instanceof Player) {
              $sender->sendMessage("Use this command in-game");
               return;
        }
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
        $menu->setName(Ender::getInstance()->config->get("Settings")["EnderChest-name"]);
        $inv = $menu->getInventory();
        $inv->setContents($sender->getEnderInventory()->getContents());
        $menu->setListener(function (InvMenuTransaction $transaction) use ($sender): InvMenuTransactionResult{
        $sender->getEnderInventory()->setItem($transaction->getAction()->getSlot(), $transaction->getIn());
        return $transaction->continue();
        });
        $menu->send($sender);
        if(Ender::getInstance()->config->get("Settings")["EnderChest-no-sound"]){
            PluginUtils::PlaySound($sender, Ender::getInstance()->config->get("Settings")["EnderChest-sound"], 50, 1);   
        }
    }
}
