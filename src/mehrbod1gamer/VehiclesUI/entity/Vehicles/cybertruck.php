<?php

namespace mehrbod1gamer\VehiclesUI\entity\Vehicles;

use mehrbod1gamer\VehiclesUI\entity\VehicleBase;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;

class cybertruck extends VehicleBase
{
    public $maxPass = 1;
    public $speed = 0.12;
    public $maxSpeed = 65;
    public $scale = 1;
    public $hasTrunk = true;

    public function __construct(World $level, CompoundTag $nbt)
    {
        $this->passSeat = [
            new Vector3(0.6, -0.3, -0.2)
        ];
        $this->riderSeat = new Vector3(-0.6, -0.3, -0.2);
        parent::__construct($level, $nbt);
    }
}