<?php

namespace zephy\koth\capturer;

use zephy\koth\data\Koth;
use pocketmine\player\Player;
use pocketmine\Server;

class Capturer
{
   private int $seconds = 0;

   public function __construct(
      private ?Koth $koth = null,
      private ?string $player = null
   ) {}

   public function getKoth(): ?Koth
   {
      return $this->koth;
   }

   public function getPlayer(): ?Player
   {
      return Server::getInstance()->getPlayerExact($this->player);
   }
   public function giveReward(): bool
   {
      if ($this->getKoth() === null) {
         return false;
      }
      
      foreach ($this->getKoth()->getRewards() as $item) {
         if ($this->getPlayer()->getInventory()->canAddItem($item)) {
            $this->getPlayer()->getInventory()->addItem($item);
         }
      }
      return true;
   }

   public function getSeconds(): int
   {
      return $this->seconds;
   }
   public function addSecond(): void
   {
      $this->seconds++;
   }
}
