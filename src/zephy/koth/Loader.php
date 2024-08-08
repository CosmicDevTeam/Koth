<?php

namespace zephy\koth;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use zephy\koth\data\KothFactory;
use zephy\koth\events\KothEvents;
use zephy\koth\listener\KothListener;
use zephy\koth\command\KothCommand;
use muqsit\invmenu\InvMenuHandler;
class Loader extends PluginBase {
  
   use SingletonTrait;
   const PREFIX = "§g(§6KOTH§g) §r";
   protected function onEnable(): void {
      self::setInstance($this);
      $this->saveResource("koths.json");
      $this->saveResource("effects.yml");
     
      if(!InvMenuHandler::isRegistered()){
         InvMenuHandler::register($this);
      }
     
      KothFactory::getInstance()->load();
     
      KothEvents::getInstance()->loadEffects();
      
      $this->getServer()->getPluginManager()->registerEvents(new KothListener(), $this);

        $this->getServer()->getCommandMap()->register("koth", new KothCommand());
   }
   
   protected function onDisable(): void {
      KothFactory::getInstance()->save();
   }
}
