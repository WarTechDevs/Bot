<?php

declare(strict_types=1);

namespace Bot;

use pocketmine\Player;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\Listener;
use pocketmine\command\{
	Command, CommandSender
};
use pocketmine\plugin\PluginBase;
use pocketmine\utils\{
	Config, TextFormat
};

use Bot\tasks\NPCTask;

class Main extends PluginBase implements Listener{

	public function onEnable(){
		@mkdir($this->getDataFolder());
		$this->saveResource("config.yml");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		Entity::registerEntity(NPCHuman::class, true);
	}

	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool{
		if(count($args) < 1){
			$sender->sendMessage("Usage: /bot <name>");
			return false;
		}

		$this->spawnNPC($sender, $args[0]);
		$sender->sendMessage(TextFormat::GREEN . "Spawned Bot: " . $args[0]);
		return true;
	}

	public function onEntitySpawn(EntitySpawnEvent $e): void{
		$entity = $e->getEntity();

		if($entity instanceof NPCHuman){
			$this->getServer()->getScheduler()->scheduleRepeatingTask(new NPCTask($this, $entity), 200);
		}
	}

	public function getCfg(): Config{
		return new Config($this->getDataFolder() . "config.yml", Config::YAML);
	}

	public function spawnNPC(Player $player, string $name): void{
		$nbt = Entity::createBaseNBT($player, null, 2, 2);
		$nbt->setTag($player->namedtag->getTag("Skin"));
		$npc = new NPCHuman($player->getLevel(), $nbt);
		$npc->setNameTag($name);
		$npc->setNameTagAlwaysVisible(true);
		$npc->spawnToAll();
	}
}