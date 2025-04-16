<?php

namespace mehrbod1gamer\VehiclesUI\entity\Vehicles;

use mehrbod1gamer\VehiclesUI\entity\VehicleBase;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;

class mazda_rx7 extends VehicleBase
{
    public $maxPass = 1;
    public $speed = 0.18;
    public $maxSpeed = 140;
    public $scale = 1;
    public $hasTrunk = true;

    public function __construct(World $level, CompoundTag $nbt)
    {
        $this->passSeat = [
            new Vector3(0.5,-0.3,-0.5)
        ];
        $this->riderSeat = new Vector3(-0.5,-0.3,-0.5);
        parent::__construct($level, $nbt);
    }
}