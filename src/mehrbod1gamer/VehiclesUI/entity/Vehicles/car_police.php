<?php

namespace mehrbod1gamer\VehiclesUI\entity\Vehicles;

use mehrbod1gamer\VehiclesUI\entity\VehicleBase;
use pocketmine\world\World;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;

class car_police extends VehicleBase
{
    public $maxPass = 3;
    public $speed = 0.2;
    public $maxSpeed = 100;
    public $scale = 1;
    public $hasTrunk = true;

    public function __construct(World $level, CompoundTag $nbt)
    {
        $this->passSeat = [
            new Vector3(0.6, -0.5, -0.1),
            new Vector3(-0.6, -0.5, -1.2),
            new Vector3(0.6, -0.5, -1.2),
        ];
        $this->riderSeat = new Vector3(-0.6, -0.5, -0.1);
        parent::__construct($level, $nbt);
    }
}