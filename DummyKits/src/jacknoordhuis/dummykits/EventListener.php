<?php

namespace jacknoordhuis\dummykits;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\protocol\InteractPacket;
use pocketmine\event\player\PlayerMoveEvent;

use jacknoordhuis\dummykits\entity\Dummy;
use jacknoordhuis\dummykits\kit\Kit;

class EventListener implements Listener {
        
        private $plugin = null;
        
        public function __construct(Main $plugin) {
                $this->plugin = $plugin;
        }
        
        public function onPacketRecive(DataPacketReceiveEvent $event) {
                if($event->getPacket() instanceof InteractPacket) {
                        $player = $event->getPlayer();
                        if(($target = $player->getLevel()->getEntity($event->getPacket()->target)) instanceof Dummy) {
                                $event->setCancelled(true);
                                foreach($target->kits as $kitName) {
                                        if(($kit = $this->plugin->kitManager->getKit(Main::removeColors($kitName))) instanceof Kit) {
                                                $this->plugin->kitManager->applyKit($player, $kit);
                                        } else {
                                                $this->plugin->getLogger()->error(Main::translateColors("&cCouldn't find a kit named " . $kitName ."!"));
                                                continue;
                                        }
                                }
                                foreach($target->commands as $cmd) {
                                        $this->plugin->getServer()->dispatchCommand($player, str_replace("{player}", $player->getName(), $cmd));
                                }
                        }
                }
        }
        
        public function onMove(PlayerMoveEvent $event) {
                $player = $event->getPlayer();
                foreach($player->level->getNearbyEntities($player->boundingBox->grow(4, 4, 4), $player) as $entity) {
                        $distance = $player->distance($entity);
                        if($entity instanceof Dummy) {
                                $event->setCancelled(true);
                                if($entity->knockback and $distance <= 1.4) {
                                        $player->knockBack($entity, 0, ($player->x - $entity->x), ($player->z - $entity->z), 0.4);
                                }
                                if($entity->move and $distance <= 10) {
                                        $entity->look($player);
                                }
                        } else {
                                continue;
                        }
                }
        }
        
}
