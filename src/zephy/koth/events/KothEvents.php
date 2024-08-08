<?php

namespace zephy\koth\events;

use pocketmine\utils\SingletonTrait;
use pocketmine\utils\Config;
use zephy\koth\Loader;
class KothEvents {
   use SingletonTrait;
   private array $effects = [];
   public function getEffects(): array {
      return $this->effects;
   }
   
   public function loadEffects(): void {
      $config = new Config(Loader::getInstance()->getDataFolder(). "effects.yml");
      
      foreach($config->getAll() as $effect => $data){
         $this->effects[$effect] = $data["time"];
      }
   }
   
   public function getEffectByTime(int $time): ?string {
      foreach($this->effects as $effect){
         if($time === ($timer = $this->effects[array_search($effect, $this->effects)])){
            return array_search($timer, $this->effects);
         }
      }
      return null;
   }
}