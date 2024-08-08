<?php



namespace zephy\koth\utils;

use pocketmine\item\Item;
use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\nbt\TreeRoot;
use pocketmine\data\SavedDataLoadingException;
use pocketmine\world\format\io\GlobalItemDataHandlers;
use pocketmine\data\bedrock\item\UnsupportedItemTypeException;
use pocketmine\item\VanillaItems;

class ItemSerializer {
    
	public static function encodeItem(Item $item): string
	{
		if($item->isNull()){
			return "null";
		}
		$serializer = new LittleEndianNbtSerializer();
		return base64_encode($serializer->write(new TreeRoot($item->nbtSerialize())));
	}

	public static function decodeItem(string|array $data): ?Item
	{
		if($data === "null"){
			return VanillaItems::AIR();
		}
		if(is_array($data)){
			return self::jsonDeserialize($data);
		}
		$serializer = new LittleEndianNbtSerializer();
		try {
			$item = Item::nbtDeserialize($serializer->read(base64_decode($data))->mustGetCompoundTag());
		} catch (SavedDataLoadingException|\Exception $e) {
			throw new \RuntimeException("Error during decoding of an item, incorrect item: " . $e->getMessage(). ", data: ".$data);
			return null;
		}
		return $item;
	}

	final public static function jsonDeserialize(array $data) : Item{
		$nbt = "";
		
		if(isset($data["nbt"])){
			$nbt = $data["nbt"];
		}elseif(isset($data["nbt_hex"])){
			$nbt = hex2bin($data["nbt_hex"]);
		}elseif(isset($data["nbt_b64"])){
			$nbt = base64_decode($data["nbt_b64"], true);
		}
		$item = GlobalItemDataHandlers::getDeserializer()->deserializeStack(GlobalItemDataHandlers::getUpgrader()->upgradeItemTypeDataInt((int) $data["id"], (int) ($data["damage"] ?? 0), (int) ($data["count"] ?? 1), $nbt !== "" ? (new LittleEndianNbtSerializer())->read($nbt)->mustGetCompoundTag() : null));
		return $item;

	}
}