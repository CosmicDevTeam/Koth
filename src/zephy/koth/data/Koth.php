<?php

namespace zephy\koth\data;

use pocketmine\world\Position;
use zephy\koth\capturer\Capturer;
use zephy\koth\capturer\CapturerFactory;
use zephy\koth\task\KothTask;
use pocketmine\Server;
use zephy\koth\utils\Utils;
use pocketmine\math\AxisAlignedBB;
use zephy\koth\Loader;

class Koth
{

   private ?string $capturer = null;

   private ?bool $started = false;

   private ?int $time = 60 * 2;

   public function __construct(
      private string $name,
      private ?Position $firstCorner = null,
      private ?Position $secondCorner = null,
      private ?array $rewards = null
   ) {}

   public function getName(): string
   {
      return $this->name;
   }

   public function getFirstCorner(): ?Position
   {
      return $this->firstCorner;
   }

   public function setFirstCorner(Position $pos)
   {
      $this->firstCorner = $pos;
   }

   public function getSecondCorner(): ?Position
   {
      return $this->secondCorner;
   }

   public function setSecondCorner(Position $pos)
   {
      $this->secondCorner = $pos;
   }

   public function getRewards(): ?array
   {
      return $this->rewards;
   }

   public function setRewards(array $rewards): void
   {
      $this->rewards = $rewards;
   }

   public function getCapturer(): ?Capturer
   {
      if ($this->capturer === null) return null;

      return CapturerFactory::getInstance()->getCapturer($this->capturer) ?? null;
   }
   public function setTime(int $time)
   {
      $this->time = $time;
   }

   public function getTime(): int
   {
      return $this->time;
   }
   public function setCapturer(?string $player = null): void
   {
      $this->capturer = $player;
      if (!is_null($player)) {

         if (CapturerFactory::getInstance()->getCapturer($player) === null) {

            CapturerFactory::getInstance()->addCapturer($player, $this);
         }
      }
   }
   public function getStatus(): string
   {
      return $this->isStarted() ? "§aEnabled" : "§4Disabled";
   }
   public function getAxisAligned(): AxisAlignedBB
   {

      $firstCorner = $this->getFirstCorner();

      $secondCorner = $this->getSecondCorner();

      return new AxisAlignedBB(
         min($firstCorner->getFloorX(), $secondCorner->getFloorX()),
         min($firstCorner->getFloorY(), $secondCorner->getFloorY()),
         min($firstCorner->getFloorZ(), $secondCorner->getFloorZ()),
         max($firstCorner->getFloorX(), $secondCorner->getFloorX()),
         max($firstCorner->getFloorY(), $secondCorner->getFloorY()),
         max($firstCorner->getFloorZ(), $secondCorner->getFloorZ())
      );
   }

   public function inside(?Position $pos = null): bool
   {
      return $this->getAxisAligned()->isVectorInside($pos) && $pos->getWorld()->getFolderName() === $this->getSecondCorner()->getWorld()->getFolderName();
   }

   public function isStarted(): bool
   {
      return $this->started;
   }
   public function setStarted(bool $value): void
   {
      $this->started = $value;
   }
   public function start(): void
   {
      Server::getInstance()->broadcastMessage(Loader::PREFIX . "§aKoth {$this->name} started!");
      foreach (Server::getInstance()->getOnlinePlayers() as $player) {
         Utils::getInstance()->playSound($player, "note.harp");
      }
   }
   public function forcestop(): void
   {
      Server::getInstance()->broadcastMessage(Loader::PREFIX . "§4Koth {$this->name} was forced to stop");
      $this->setStarted(false);
      CapturerFactory::getInstance()->unsetAll();
   }
   public function stop(): void
   {
      $this->setStarted(false);
      if ($this->getCapturer() !== null) {
         if ($this->getRewards() !== null) {
            $this->getCapturer()->giveReward();
         }
         Server::getInstance()->broadcastMessage(Loader::PREFIX . "§aKoth {$this->name} get captured by {$this->getCapturer()->getPlayer()->getName()}");
         foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            Utils::getInstance()->playSound($player, "mob.enderdragon.death");
         }
      }

      CapturerFactory::getInstance()->unsetAll();
   }
   public function tick(): void
   {
      if ($this->isStarted()) {
         Loader::getInstance()->getScheduler()->scheduleRepeatingTask(new KothTask($this), 20);
      }
   }
}
