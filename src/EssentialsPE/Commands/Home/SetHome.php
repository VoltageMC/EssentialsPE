<?php
namespace EssentialsPE\Commands\Home;

use EssentialsPE\BaseFiles\BaseAPI;
use EssentialsPE\BaseFiles\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class SetHome extends BaseCommand{
    /**
     * @param BaseAPI $api
     */
    public function __construct(BaseAPI $api){
        parent::__construct($api, "sethome");
        $this->setPermission("essentials.sethome");
    }

    /**
     * @param CommandSender $sender
     * @param string $alias
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, $alias, array $args): bool{
        if(!$this->testPermission($sender)){
            return false;
        }
        if(!$sender instanceof Player || count($args) !== 1){
            $this->sendUsage($sender, $alias);
            return false;
        }
        if(strtolower($args[0]) === "bed"){
            $this->sendTranslation($sender, "commands.home.bed-error");
            return false;
        }
        $updated = $this->getAPI()->homeExists($sender, $args[0]);
        if(!$this->getAPI()->setHome($sender, strtolower($args[0]), $sender->getLocation(), $sender->getYaw(), $sender->getPitch())){
            $this->sendTranslation($sender, "error.invalid-name");
            return false;
        }
        $this->sendTranslation($sender, "commands.sethome." . ($updated ? "updated" : "created"), $args[0]);
        return true;
    }
} 
