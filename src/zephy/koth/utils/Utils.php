<?php



namespace zephy\koth\utils;

use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\Position;
use pocketmine\player\Player;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;

class Utils
{
    use SingletonTrait;

    public function playSound(Player $player, string $sound)
    {

        $pk = new PlaySoundPacket();
        $pk->x = $player->getPosition()->getX();
        $pk->y = $player->getPosition()->getY();
        $pk->z = $player->getPosition()->getZ();
        $pk->soundName = $sound;
        $pk->volume = 1;
        $pk->pitch = 1;
        $player->getNetworkSession()->sendDataPacket($pk);
    }


    public function positionToString(Position $position): string
    {
        return $position->x . ":" . $position->y . ":" . $position->z . ":" . $position->getWorld()->getFolderName();
    }

    public function stringToPosition(string $position): ?Position
    {
        $data = explode(":", $position);
        if (\count($data) < 4) {
            return null;
        }
        return new Position($data[0], $data[1], $data[2], Server::getInstance()->getWorldManager()->getWorldByName($data[3]));
    }
    public function giveItems(Player $player, string $name): void
    {

        $firstCorner = VanillaItems::DIAMOND_HOE();
        $firstCorner->setCustomName('§l§gFirst Corner');
        $firstCorner->getNamedTag()->setString("koth", $name);


        $secondCorner = VanillaItems::GOLDEN_HOE();
        $secondCorner->setCustomName('§l§gSecond Corner');
        $secondCorner->getNamedTag()->setString("koth", $name);


        $confirm = VanillaItems::DYE()->setColor(DyeColor::LIME());
        $confirm->setCustomName('§l§aConfirm');
        $confirm->getNamedTag()->setString("koth", $name);



        $player->getInventory()->setItem(0, $firstCorner);
        $player->getInventory()->setItem(1, $secondCorner);
        $player->getInventory()->setItem(8, $confirm);
    }
}
