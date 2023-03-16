<?php

namespace itoozh;

use JsonException;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class ContadorMuertes extends PluginBase implements Listener
{

    /**
     * @var array
     */
    public array $deathCounter = [];

    public function onEnable(): void
    {
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->deathCounter = $this->getConfig()->getAll();
    }

    /**
     * @throws JsonException
     */
    public function onDisable(): void
    {
        foreach ($this->deathCounter as $player => $kills) {
            $this->getConfig()->set($player, $kills);
            $this->getConfig()->save();
        }
    }

    /**
     * @param PlayerDeathEvent $ev
     * @return void
     */
    public function handlerDeath(PlayerDeathEvent $ev): void
    {
        $p = $ev->getPlayer();
        $this->addDeath($p);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function addDeath(Player $player): void
    {
        if (!isset($this->deathCounter[$player->getName()])){
            $this->deathCounter[$player->getName()] = 1;
        } else {
            $this->deathCounter[$player->getName()] = $this->deathCounter[$player->getName()] + 1;
        }

        Server::getInstance()->broadcastMessage(TextFormat::colorize("&6El jugador &l" . $player->getName() . "&r&6 tiene &c" . $this->getDeaths($player) . " &6muertes"));
    }

    /**
     * @param Player $player
     * @return int
     */
    public function getDeaths(Player $player): int
    {
        return $this->deathCounter[$player->getName()];
    }
}