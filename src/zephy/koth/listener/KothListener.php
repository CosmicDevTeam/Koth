<?php

namespace zephy\koth\listener;

use pocketmine\event\Listener;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\world\Position;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use zephy\koth\data\KothFactory;
use zephy\koth\Loader;
use zephy\koth\utils\Utils;

class KothListener implements Listener{
   private ?Position $firstCorner = null;
   private ?Position $secondCorner = null;
   
   public function onInteract(PlayerInteractEvent $event){
      $item = $event->getItem();
      $block = $event->getBlock();
      $player = $event->getPlayer();
      
      if($item->getNamedTag()->getTag("koth") !== null && $item->getCustomName() === "§l§gFirst Corner"){
          $this->firstCorner = $block->getPosition();
                  $player->sendMessage(Loader::PREFIX. "First position setted to " . Utils::getInstance()->positionToString($this->firstCorner));
                  $event->cancel();
          
         }
      if($item->getNamedTag()->getTag("koth") !== null && $item->getCustomName() === "§l§gSecond Corner"){
            $this->secondCorner = $block->getPosition();
                $player->sendMessage(Loader::PREFIX. "Second position setted to " . Utils::getInstance()->positionToString($this->secondCorner));
                  $event->cancel(); 
         }
   }
   
  
   public function onUse(PlayerItemUseEvent $event){
      $item = $event->getItem();
      $player = $event->getPlayer();
      if($item->getNamedTag()->getTag("koth") !== null && $item->getCustomName() === "§l§aConfirm"){
         if($this->firstCorner !== null && $this->secondCorner !== null){
            if($this->firstCorner->getWorld()->getFolderName() === $this->secondCorner->getWorld()->getFolderName()) {
            KothFactory::getInstance()->addKoth($this->firstCorner, $this->secondCorner, $item->getNamedTag()->getString("koth"));
            $player->sendMessage(Loader::PREFIX. "§aKoth Created Successfully");
            $player->getInventory()->clearAll();
            unset($this->firstCorner);
            unset($this->secondCorner);
            }
         }
      }
   }
}
