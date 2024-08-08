<?php

namespace zephy\koth\task;

use pocketmine\scheduler\Task;
use zephy\koth\data\Koth;
use pocketmine\player\Player;
use zephy\koth\events\KothEvents;
use zephy\koth\events\EffectsFilter;
use pocketmine\Server;
use zephy\koth\Loader;
use zephy\koth\utils\Utils;
use zephy\koth\capturer\CapturerFactory;
use pocketmine\utils\TextFormat;
class KothTask extends Task{
   private ?int $tops = 0;
   public function __construct(
      private Koth $koth
      )
      {}
   public function onRun(): void {
      $koth = $this->koth;
      
      
      if(!$koth->isStarted()) {
          $this->getHandler()->cancel();
      }
      if(is_null($koth->getFirstCorner()) || is_null($koth->getSecondCorner())){
         $koth->setStarted(false);
          return;
         
         
      }
      if($koth->getCapturer() === null) {
          $pos = $koth->getFirstCorner();
          foreach($pos->getWorld()->getPlayers() as $player){
              if($player instanceof Player && $player->isSurvival() && $koth->inside($player->getPosition())){
               $koth->setCapturer($player->getName());

         }
      }
    }
      
      
      if($koth->getCapturer() !== null){

          if(!$koth->inside($koth->getCapturer()->getPlayer()->getPosition())){
           $koth->setCapturer(null);
              return;
          }
         $koth->getCapturer()->addSecond();
         foreach(Server::getInstance()->getOnlinePlayers() as $player) {
           $player->sendPopUp("§g". $koth->getCapturer()->getPlayer()->getName(). " §fis capturing the Koth , his time: ". $koth->getCapturer()->getSeconds(). " seconds");
             Utils::getInstance()->playSound($player, "note.bell");
         } 

         
             
         
         if(($effect = KothEvents::getInstance()->getEffectByTime($koth->getCapturer()?->getSeconds())) !== null){
            EffectsFilter::applyEffect($koth->getCapturer()->getPlayer(), $effect);
         }
         if($koth->getCapturer()?->getSeconds() === $koth->getTime()){
            $koth->stop();
            $koth->setCapturer(null);
            $this->getHandler()->cancel();
            
         }
      }
      if($this->tops === 45){
        $capturer = CapturerFactory::getInstance()->getCapturers();
        arsort($capturer);
        $text = "§gKoth §a{$koth->getName()} §gSeconds Claimed";

        for($i = 0; $i < 3; $i++){
            $player = array_keys($capturer);
            $top = array_values($capturer);
            if(isset($player[$i]))
            {
            
        $texto = "§g#" . ($i + 1) . " §f" . $player[$i] . " §g- §f" . $top[$i];
        $text .= PHP_EOL . TextFormat::colorize($texto);
            }
           

            
        }
          Server::getInstance()->broadcastMessage($text);
          $this->tops = 0;
         
       }
       
       $this->tops++;
      
      
   }
}
