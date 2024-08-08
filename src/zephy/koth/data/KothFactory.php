<?php

namespace zephy\koth\data;

use pocketmine\utils\SingletonTrait;
use pocketmine\utils\Config;
use pocketmine\world\Position;
use zephy\koth\Loader;
use zephy\koth\utils\Utils;
use zephy\koth\utils\ItemSerializer;
class KothFactory {
   use SingletonTrait;
   private array $koths = [];
   
   public function addKoth(Position $firstCorner, Position $secondCorner, string $name){
           $this->koths[$name] = new Koth($name, $firstCorner, $secondCorner);
   }
   
   public function getKoth(string $koth): ?Koth {
      return $this->koths[$koth] ?? null;
   }
   
   public function existsKoth(string $koth): bool {
      return isset($this->koths[$koth]);
   }
   
   public function deleteKoth(string $koth): void {
      unset($this->koths[$koth]);
   }
   
   public function getKoths(): array {
      return $this->koths;
   }
   
   public function isRunningKoths(): bool {
       foreach($this->koths as $name => $koth) {
           if($koth->isStarted()) {
               return true;
           } 
     
       } 
       return false;
   } 
   public function load(): void {
      $config = new Config(Loader::getInstance()->getDataFolder(). "koths.json");
      foreach($config->getAll() as $name => $koth){
         $firstCorner = Utils::getInstance()->stringToPosition($koth["firstCorner"]);
         
         $secondCorner = Utils::getInstance()->stringToPosition($koth["secondCorner"]);
         $items = [];
         
         foreach($koth["rewards"] as $item){
            $items[] = ItemSerializer::decodeItem($item);
         }
         $this->koths[$name] = new Koth($name, $firstCorner, $secondCorner, $items);
         
      }
   }
   public function save(): void {
      $config = new Config(Loader::getInstance()->getDataFolder(). "koths.json");
      foreach($this->koths as $name => $koth){
         
         $items = [];
         foreach($koth->getRewards() as $item){
            $items[] = ItemSerializer::encodeItem($item);
         }
         
         $config->set($name, [
            "firstCorner" => Utils::getInstance()->positionToString($koth->getFirstCorner()),
            "secondCorner" => Utils::getInstance()->positionToString($koth->getSecondCorner()), 
            "rewards" => $items
            ]);
         $config->save();
      }
   }
}
