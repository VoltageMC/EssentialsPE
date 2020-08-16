<?php

declare(strict_types = 1);

namespace EssentialsPE\Commands;

use EssentialsPE\BaseFiles\BaseAPI;
use EssentialsPE\BaseFiles\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\level\particle\HappyVillagerParticle;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Feed extends BaseCommand{
    /**
     * @param BaseAPI $api
     */
    public function __construct(BaseAPI $api){
        parent::__construct($api, "feed", "Feed yourself or other players", "[player]");
        $this->setPermission("essentials.feed.use");
    }

    /**
     * @param CommandSender $sender
     * @param string $alias
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, string $alias, array $args): bool{
        $this->wait = 120;
        $t = microtime(true);

        if (isset($this->cooldown[$sender->getName()]) && $this->cooldown[$sender->getName()] + $this->wait > $t && !$sender->hasPermission(Main::PERMISSION_PREFIX."essentials.feed.instant")) {
            $min = (int)floor(($this->cooldown[$sender->getName()] + $this->wait - $t)/60);
            if($min == 0){
                $sender->sendMessage(TextFormat::colorize("&7You need to wait &b".date("s", (int)$this->cooldown[$sender->getName()] + $this->wait - (int)$t)."&7 seconds before you can use this command again."));
            }else{
                $sender->sendMessage(TextFormat::colorize("&7You need to wait &b" . $min . "&7 minutes and &b".date("s", (int)$this->cooldown[$sender->getName()] + $this->wait - (int)$t)."&7 seconds before you can use this command again."));
            }
            return true;
        }

        if(!$this->testPermission($sender)){
            return false;
        }
        if((!isset($args[0]) && !$sender instanceof Player) || count($args) > 1){
            $this->sendUsage($sender, $alias);
            return false;
        }
        $player = $sender;
        if(isset($args[0]) && !($player = $this->getAPI()->getPlayer($args[0]))){
            $sender->sendMessage(TextFormat::RED . "[Error] Player not found");
            return false;
        }
	    if($player->getName() !== $sender->getName() && !$sender->hasPermission("essentials.feed.other")) {
		    $sender->sendMessage(TextFormat::RED . $this->getPermissionMessage());
		    return false;
	    }
        $player->setFood(20);
        $player->getLevel()->addParticle(new HappyVillagerParticle($player->add(0, 2)));
        $player->sendMessage(TextFormat::GREEN . "You have been fed!");
        $this->cooldown[$sender->getName()] = $t;
        if($player !== $sender){
            $sender->sendMessage(TextFormat::GREEN . $player->getDisplayName() . " has been fed!");
        }
        return true;
    }
}
