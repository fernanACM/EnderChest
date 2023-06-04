<?php
    
#      _       ____   __  __ 
#     / \     / ___| |  \/  |
#    / _ \   | |     | |\/| |
#   / ___ \  | |___  | |  | |
#  /_/   \_\  \____| |_|  |_|
# The creator of this plugin was fernanACM.
# https://github.com/fernanACM
 
namespace fernanACM\EnderChest\commands;

use pocketmine\player\Player;

use pocketmine\utils\TextFormat;

use pocketmine\command\CommandSender;

use pocketmine\inventory\Inventory;
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
    
    public function __construct(){
        parent::__construct(Ender::getInstance(), "enderchest", "Open EnderChest by fernanACM", ["ender", "ec"]);
        $this->setPermission("enderchest.acm");
    }

    /**
     * @return void
     */
    protected function prepare(): void{
    }

    /**
     * @param CommandSender $sender
     * @param string $aliasUsed
     * @param array $args
     * @return void
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void{
        if(!$sender instanceof Player) {
            $sender->sendMessage("Use this command in-game");
            return;
        }
        if(!$sender->hasPermission("enderchest.acm")){
            $sender->sendMessage(TextFormat::RED."You don't have permissions for this!");
            return;
        }
        self::openEnderChest($sender);
        if(Ender::getInstance()->config->getNested("Settings.EnderChest-sound")){
            PluginUtils::PlaySound($sender, Ender::getInstance()->config->getNested("Settings.EnderChest-open-soundName"), 1, 1);   
        }
    }

    /**
     * @param Player $player
     * @return void
     */
    private static function openEnderChest(Player $player): void{
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
        $menu->setName(Ender::getInstance()->config->getNested("Settings.EnderChest-name"));
        $inv = $menu->getInventory();
        $inv->setContents($player->getEnderInventory()->getContents());
        $menu->setListener(function(InvMenuTransaction $transaction) use($player): InvMenuTransactionResult{
            $player->getEnderInventory()->setItem($transaction->getAction()->getSlot(), $transaction->getIn());
            return $transaction->continue();
        });
        $menu->setInventoryCloseListener(function(Player $player, Inventory $inventory): void{
            if(Ender::getInstance()->config->getNested("Settings.EnderChest-sound")){
                PluginUtils::PlaySound($player, Ender::getInstance()->config->getNested("Settings.EnderChest-closed-soundName"), 1, 1);
            }
        });
        $menu->send($player);
    }
}
