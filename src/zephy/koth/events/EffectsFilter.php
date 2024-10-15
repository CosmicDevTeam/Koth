<?php

namespace zephy\koth\events;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\StringToEffectParser;
use pocketmine\player\Player;

class EffectsFilter
{
	public static function applyEffect(Player $player, string $name, int $duration = 20, int $level = 1): bool
	{
		$effect = StringToEffectParser::getInstance()->parse($name);
		$player->sendMessage($name);

		if ($effect === null) {
			return false;
		}

		$time = $duration * 20;

		if ($player->getEffects()->has($effect)) {
			return false;
		}

		$add = new EffectInstance($effect, $time, $level - 1);
		$player->getEffects()->add($add);
		return true;
	}
}
