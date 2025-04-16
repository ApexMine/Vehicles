<?php

namespace mehrbod1gamer\VehiclesUI\entity\Vehicles;

use mehrbod1gamer\VehiclesUI\entity\VehicleBase;
use pocketmine\world\World;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;

class camaro extends VehicleBase
{
    public $maxPass = 1;
    public $speed = 0.2;
    public $maxSpeed = 160;
    public $scale = 1;
    public $hasTrunk = true;

    public function __construct(World $level, CompoundTag $nbt)
    {
        $this->passSeat = [
            new Vector3(-0.6, -0.2, -0.1)
        ];
        $this->riderSeat = new Vector3(0.6, -0.2, -0.1);
        parent::__construct($level, $nbt);
    }
}