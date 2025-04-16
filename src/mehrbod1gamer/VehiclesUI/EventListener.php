<?php

namespace mehrbod1gamer\VehiclesUI;

use mehrbod1gamer\VehiclesUI\entity\FlyingVehicle;
use mehrbod1gamer\VehiclesUI\entity\VehicleBase;
use pocketmine\event\block\BlockItemPickupEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\InteractPacket;
use pocketmine\network\mcpe\protocol\PlayerInputPacket;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class EventListener implements Listener
{
    public function __construct()
    {
    }

    public function onRide(EntityDamageByEntityEvent $event)
    {
        if ($event->getCause() == EntityDamageEvent::CAUSE_BLOCK_EXPLOSION or $event->getCause() == EntityDamageByEntityEvent::CAUSE_ENTITY_EXPLOSION) {
            return;
        }

        $entity = $event->getEntity();
        $damager = $event->getDamager();
        if ($entity instanceof VehicleBase and $damager instanceof Player) {
            if (isset(main::getMain()->keyWant[$damager->getName()])) {
                if ($entity->getOwner() == $damager->getName()) {
                    $entity->giveKey($damager);
                } else {
                    $damager->sendMessage(TextFormat::RED . 'This vehicle is not for you');
                }
                unset(main::getMain()->keyWant[$damager->getName()]);
                return;
            }

            if ($damager->getInventory()->getItemInHand()->getCustomName() == VanillaItems::IRON_NUGGET()->getCustomName()) {
                $key = $damager->getInventory()->getItemInHand();
                if ($key->getLore()[0] == $entity->getVehicleID()) {
                    if (!$entity->hasRider()) {
                        $entity->setRider($damager);
                    } else {
                        $damager->sendMessage('This vehicle have rider');
                    }
                } else {
                    $damager->sendMessage(TextFormat::RED . 'This Key is not for this Vehicle');
                }
            } else {
                $entity->setPass($damager);
            }
        }
    }

    public function packetReceive(DataPacketReceiveEvent $event)
    {
        $packet = $event->getPacket();
        switch ($packet->pid()) {
            case PlayerInputPacket::NETWORK_ID:
                $this->inputPacket($event);
                break;
            case InteractPacket::NETWORK_ID:
                $this->interactPacket($event);
        }
    }

    public function interactPacket($event)
    {
        $packet = $event->getPacket();
        $player = $event->getPlayer();
        if ($packet->action === InteractPacket::ACTION_LEAVE_VEHICLE) {
            if (isset(main::getMain()->riders[$player->getName()])) {
                $vehicle = $player->get()->getEntity(main::getMain()->riders[$player->getName()]);
                if ($vehicle instanceof VehicleBase) {
                    $vehicle->unsetRider($player);
                }
            } elseif (isset(main::getMain()->passengers[$player->getName()])) {
                $vehicle = $player->getlevel()->getEntity(main::getMain()->passengers[$player->getName()]);
                if ($vehicle instanceof VehicleBase) {
                    $vehicle->unsetPass($player);
                }
            }
            $event->setCancelled(true);
        }
    }

    public function inputPacket($event)
    {
        $packet = $event->getPacket();
        if (isset(main::getMain()->riders[$event->getPlayer()->getName()])) {
            $event->setCancelled(true);
            $player = $event->getPlayer();
            $level = $player->getLevel();
            $vehicle = $level->getEntity(main::getMain()->riders[$event->getPlayer()->getName()]);

            if ($event->getPacket()->motionY < 0.8) {
                if ($vehicle instanceof FlyingVehicle) {
                    $vehicle->stoppingInAir = true;
                } elseif ($vehicle instanceof VehicleBase) {
                    $vehicle->stopping = true;
                }
                return;
            }

            $vehicle->updateMove();
            $vehicle->yaw = $event->getPlayer()->getYaw();
        }
    }

    public function onDropInTank(PlayerDropItemEvent $event)
    {
        $player = $event->getPlayer();
        if (isset(main::getMain()->riders[$player->getName()])) {
            $entity = $player->getWorld()->getEntity(main::getMain()->riders[$player->getName()]);
            if ($entity instanceof leopard) $event->cancel(true);
        }
    }

    public function onDamage(EntityDamageEvent $event)
    {
        $entity = $event->getEntity();
        if ($entity instanceof VehicleBase) {
            $event->cancel(true);
        } elseif ($entity instanceof Player) {
            if (isset(main::getMain()->riders[$entity->getName()]) or isset(main::getMain()->passengers[$entity->getName()])) {
                $event->cancel();
            }
        }
    }

    public function onPlayerTeleport(EntityTeleportEvent $event)
    {
        if ($event->isCancelled()) return;
        $player = $event->getEntity();
        if ($player instanceof Player) {
            if (isset(main::getMain()->riders[$player->getName()])) {
                $vehicle = $player->getWorld()->getEntity(main::getMain()->riders[$player->getName()]);
                if ($vehicle instanceof VehicleBase) {
                    $vehicle->unsetRider($player);
                }
            } elseif (isset(main::getMain()->passengers[$player->getName()])) {
                $vehicle = $player->getWorld()->getEntity(main::getMain()->passengers[$player->getName()]);
                if ($vehicle instanceof VehicleBase) {
                    $vehicle->unsetPass($player);
                }
            }
        }
    }

    public function onPlayerChangeLevel(BlockItemPickupEvent $event)
    {
        if ($event->isCancelled()) return;
        $player = $event->getItem();
        if ($player instanceof Player) {
            if (isset(main::getMain()->riders[$player->getName()])) {
                $vehicle = $player->getWorld()->getEntity(main::getMain()->riders[$player->getName()]);
                if ($vehicle instanceof VehicleBase) {
                    $vehicle->unsetRider($player);
                }
            } elseif (isset(main::getMain()->passengers[$player->getName()])) {
                $vehicle = $player->getWorld()->getEntity(main::getMain()->passengers[$player->getName()]);
                if ($vehicle instanceof VehicleBase) {
                    $vehicle->unsetPass($player);
                }
            }
        }
    }

    public function onPlayerDeath(PlayerDeathEvent $event)
    {
        $player = $event->getPlayer();
        if (isset(main::getMain()->riders[$player->getName()])) {
            $vehicle = $player->getWorld()->getEntity(main::getMain()->riders[$player->getName()]);
            if ($vehicle instanceof VehicleBase) {
                $vehicle->unsetRider($player);
            }
        } elseif (isset(main::getMain()->passengers[$player->getName()])) {
            $vehicle = $player->getWorld()->getEntity(main::getMain()->passengers[$player->getName()]);
            if ($vehicle instanceof VehicleBase) {
                $vehicle->unsetPass($player);
            }
        }
    }

    public function onInteract(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        if ($event->getAction() == PlayerInteractEvent::RIGHT_CLICK_AIR or $event->getAction() == PlayerInteractEvent::LEFT_CLICK_BLOCK) {
            if (isset(main::getMain()->riders[$player->getName()])) {
                $vehicle = $player->getWorld()->getEntity(main::getMain()->riders[$player->getName()]);
                if ($vehicle instanceof leopard and $event->getItem()->getCustomName() == VanillaItems::FIRE_CHARGE()->getCustomName()) {
                    $vehicle->shootFireBall();
                }
            }
        }
    }

    public function onPickUpItem(BlockItemPickupEvent $event)
    {
        $item = $event->getItem();
        if($item->getCustomName() == TextFormat::YELLOW . 'FIREBALL') $event->cancel(true);
    }

    public function onPlayerQuit(PlayerQuitEvent $event)
    {
        $player = $event->getPlayer();
        if (isset(main::getMain()->riders[$player->getName()])) {
            $vehicle = $player->getWorld()->getEntity(main::getMain()->riders[$player->getName()]);
            if ($vehicle instanceof VehicleBase) {
                $vehicle->unsetRider($player);
            }
        } elseif (isset(main::getMain()->passengers[$player->getName()])) {
            $vehicle = $player->getWorld()->getEntity(main::getMain()->passengers[$player->getName()]);
            if ($vehicle instanceof VehicleBase) {
                $vehicle->unsetPass($player);
            }
        }
    }
}