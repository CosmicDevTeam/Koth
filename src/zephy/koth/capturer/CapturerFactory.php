<?php

namespace zephy\koth\capturer;

use zephy\koth\data\Koth;
use pocketmine\utils\SingletonTrait;

class CapturerFactory
{
   use SingletonTrait;

   private array $capturers = [];

   public function addCapturer(string $player, Koth $koth): void
   {
      $this->capturers[$player] = new Capturer($koth, $player);
   }

   public function getCapturer(string $player): ?Capturer
   {
      return $this->capturers[$player] ?? null;
   }

   public function deleteCapturer(string $player): void
   {
      unset($this->capturers[$player]);
   }

   public function unsetAll(): void
   {
      unset($this->capturers);
   }
   
   public function getCapturers(): array
   {
      $capturers = [];

      foreach ($this->capturers as $player => $capturer) {
         $capturers[$player] = $capturer->getSeconds();
      }
      return $capturers;
   }
}
