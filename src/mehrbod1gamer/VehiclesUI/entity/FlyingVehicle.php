<?php

namespace mehrbod1gamer\VehiclesUI\entity;

use mehrbod1gamer\VehiclesUI\main;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class FlyingVehicle extends VehicleBase
{
    /* @var float */
    public $flightHeight = 1.0;

    public $stoppingInAir = false;

    public function updateMove(): void
    {
        $this->stoppingInAir = false;
        if (!$this->rider instanceof Player) return;
        $targetVector = $this->getDirectionVector();
        $playerEye = $this->rider->getDirectionVector();

        $targetBlock = $this->getTargetBlock(1);
        if ($this->getWorld()->getBlock(new Vector3($targetBlock->getPosition()->getX(), $targetBlock->getPosition()->getY() - 1, $targetBlock->getPosition()->getZ())) != Block::AIR and $this->getWorld()->getBlock(new Vector3($targetBlock->getPosition()->getX(), $targetBlock->getPosition()->getY(), $targetBlock->getPosition()->getZ())) == Block::AIR) {
            $this->move(0, 1.5, 0);
        }

        if ($playerEye->y > 0.2) {
            $this->move(0, 0.5, 0);
            $this->flightHeight += 0.5;
        } elseif ($playerEye->y < -0.3 and !$this->isOnGround()) {
            $this->move(0, -0.5, 0);
            $this->flightHeight -= 0.5;
        } else {
            $this->move($targetVector->getX() * $this->speed, $targetVector->getY() * $this->speed, $targetVector->getZ() * $this->speed);
        }
    }

    public function onUpdate(int $currentTick): bool
    {
        main::getMain()->spawnedVehicles[$this->getId()] = $this;

        if ($this->onGround) $this->flightHeight = 1.0;

        if ($this->hasRider() and !$this->isOnGround()) {
            if ($this->stoppingInAir) {
                $this->gravity = 0.08;
            } else {
                $this->gravity = 0;
            }
        } else {
            $this->gravity = 0.08;
        }

        if ($this->rider instanceof Player) {
            $this->rider->addActionBarMessage(TextFormat::GREEN . 'Height: ' . TextFormat::RED . (string)$this->flightHeight);
        }
        return parent::onUpdate($currentTick);
    }
}