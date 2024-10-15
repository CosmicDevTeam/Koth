<?php

namespace zephy\koth\command;

use pocketmine\item\VanillaItems;
use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use zephy\koth\data\KothFactory;
use zephy\koth\utils\Utils;
use zephy\koth\Loader;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\inventory\Inventory;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\Plugin;


class KothCommand extends Command implements PluginOwned
{
   public function __construct()
   {
      parent::__construct("koth", "Manage KothSystem");
      $this->setPermission("kothsystem.admin");
   }

   public function execute(CommandSender $sender, string $commandLabel, array $args): void
   {

      if (!$sender instanceof Player) {

         $sender->sendMessage("§4Run this command in game");
         return;
      }

      if (!isset($args[0])) {
         $sender->sendMessage("§4Invalid arguments");
         return;
      }

      switch ($args[0]) {
         case "create":
            if (!isset($args[1])) {
               $sender->sendMessage("§4Try using /koth create (name)");
               return;
            }

            if (!is_string($args[1])) {
               $sender->sendMessage("§4Argument (name) must be type string");
               return;
            }

            if (KothFactory::getInstance()->existsKoth($args[1])) {
               $sender->sendMessage("§4Koth with that name already exists");
               return;
            }

            Utils::getInstance()->giveItems($sender, $args[1]);
            break;
         case "delete":
            if (!isset($args[1])) {
               $sender->sendMessage("§4Try using /koth delete (name)");
               return;
            }
            if (!KothFactory::getInstance()->existsKoth($args[1])) {
               $sender->sendMessage("§4Koth with that name dont exists");
               return;
            }

            KothFactory::getInstance()->deleteKoth($args[1]);
            $sender->sendMessage("§aKoth {$args[1]} deleted successfully");
            break;
         case "start":
            if (!isset($args[1]) || !isset($args[2])) {
               $sender->sendMessage("§4Try using /koth start(name) (time)");
               return;
            }
            if (!KothFactory::getInstance()->existsKoth($args[1])) {
               $sender->sendMessage("§4Koth with that name dont exists");
               return;
            }
            if (!is_numeric($args[2])) {
               $sender->sendMessage("§4Argument (time) must be of type int (numeric)");
               return;
            }
            if (KothFactory::getInstance()->getKoth($args[1])->isStarted()) {
               $sender->sendMessage("§4Koth is already enabled");
               return;
            }
            if (KothFactory::getInstance()->isRunningKoths()) {
               $sender->sendMessage("§4U only can start 1 koth once");
               return;
            }
            $koth = KothFactory::getInstance()->getKoth($args[1]);
            $koth->setTime($args[2]);
            $koth->start();
            $koth->setStarted(true);
            $koth->tick();
            break;
         case "list":
            if (count(KothFactory::getInstance()->getKoths()) <= 0) {
               $sender->sendMessage("§4Theres not koths in database");
               return;
            }

            $koths = "§gKoth List \n";

            foreach (KothFactory::getInstance()->getKoths() as $name => $koth) {
               $koths .= "§g" . $koth->getName() . " : §r" . $koth->getStatus() . "\n§r";
            }
            $sender->sendMessage($koths);
            break;

         case "forcestop":
            if (!isset($args[1])) {
               $sender->sendMessage("§4Try using /koth delete (name)");
               return;
            }
            if (!KothFactory::getInstance()->existsKoth($args[1])) {
               $sender->sendMessage("§4Koth with that name dont exists");
               return;
            }
            KothFactory::getInstance()->getKoth($args[1])->forcestop();
            break;
         case "help":
            $help = [
               '§l§gKoth Commands',
               '-----------------',

               '§f/koth create (name) - §6Crea un KOTH',
               '§f/koth list - §6Lista de todos los KOTHS',
               '§f/koth start (name) (time) - §6Inicia un KOTH',
               '§f/koth forcestop (name) - §6Fuersa el frenado del KOTH',
               '§f/koth delete (name) - §6Elimina un KOTH',
               '§f/koth items (name) - §6Configura los items del KOTH'

            ];
            $sender->sendMessage(implode("\n", $help));
            break;
         case "items":
            if (!isset($args[1])) {
               $sender->sendMessage("§4Try using /koth items (name)");
               return;
            }
            if (!KothFactory::getInstance()->existsKoth($args[1])) {
               $sender->sendMessage("§4Koth with that name dont exists");
               return;
            }
            $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
            if (KothFactory::getInstance()->getKoth($args[1])->getRewards() !== null) {

               $menu->getInventory()->setContents(KothFactory::getInstance()->getKoth($args[1])->getRewards());
            }

            $kothname = $args[1];
            $menu->setInventoryCloseListener(function (Player $player, Inventory $inventory) use ($kothname): void {
               KothFactory::getInstance()->getKoth($kothname)->setRewards($inventory->getContents());
               $player->sendMessage("§aItems placed successfully");
            });
            $menu->send($sender, "Koth contents");
      }
   }
   public function getOwningPlugin(): Plugin
   {
      return Loader::getInstance();
   }
}
