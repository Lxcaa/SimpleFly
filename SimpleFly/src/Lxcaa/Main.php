<?php

namespace Lxcaa;

use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\entity\Entity;
use jojoe77777\FormAPI\SimpleForm;
use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\ModalForm;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener
{
    private $config;

    public function getPrefix(): string {
        return $this->getConfig()->get("Prefix");
    }

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        $this->saveResource('config.yml');
        $this->config = new Config($this->getDataFolder() . 'config.yml', Config::YAML);

        $this->getLogger()->info("Activate Fly Plugin..");
        $this->getLogger()->info("Activated successfully!");
        $this->getLogger()->info("Code by Lxcaa#4260");
    }

    public function onDisable()
    {
        $this->getLogger()->info("Deactivate Fly Plugin..");
        $this->getLogger()->info("Deactivated successfully!");
        $this->getLogger()->info("Code by Lxcaa#4260");
    }

    public function onFlyUI(Player $player)
    {
        if ($player->hasPermission($this->getConfig()->get("Permission"))) {
            $api = $this->getServer()->getInstance()->getPluginManager()->getPlugin("FormAPI");
            if ($api === null || $api->isDisabled()) {
                return;
            }
            $form = $api->createSimpleForm(function (Player $player, ?int $result = null) {
                if ($result === null) {
                    return true;
                }
                switch ($result) {
                    case 0:
                        $player->setAllowFlight(true);
                        $player->setFlying(true);
                        $player->sendMessage($this->getPrefix() . $this->getConfig()->get("Enable-Fly"));
                        break;
                    case 1:
                        $player->setAllowFlight(false);
                        $player->setFlying(false);
                        $player->sendMessage($this->getPrefix() . $this->getConfig()->get("Disable-Fly"));
                        break;
                }
            });
            $form->setTitle($this->getConfig()->get("title"));

            $form->setContent($this->getConfig()->get("content"));

            $form->addButton($this->getConfig()->get("button1"), 0);

            $form->addButton($this->getConfig()->get("button2"), 1);

            $form->sendToPlayer($player);
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        switch ($command->getName()) {
            case "fly":
                if ($this->getConfig()->get("flyui") === false) {
                    if ($sender instanceof Player) {
                        if ($sender->hasPermission("Permission")) {
                            if (!$sender->getAllowFlight()) {
                                $sender->sendMessage($this->getPrefix() . $this->getConfig()->get("Enable-Fly"));
                                $sender->setAllowFlight(true);
                                $sender->setFlying(true);
                            } else {
                                $sender->sendMessage($this->getPrefix() . $this->getConfig()->get("Disable-Fly"));
                                $sender->setAllowFlight(false);
                                $sender->setFlying(false);
                            }
                        } else {
                            $sender->sendMessage($this->getPrefix() . $this->getConfig()->get("No-Permission"));
                        }
                    } else {
                        $sender->sendMessage("§cUse this Command in-game!");
                    }
                }
                if ($this->getConfig()->get("flyui") === true) {
                    if ($sender->hasPermission($this->getConfig()->get("Permission"))) {
                        $this->onFlyUI($sender);
                    }
                }
                return true;
        }
    }
}