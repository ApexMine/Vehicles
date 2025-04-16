<?php

namespace mehrbod1gamer\VehiclesUI\entity\Vehicles;

use mehrbod1gamer\VehiclesUI\entity\VehicleBase;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;

class yamaha_rx115 extends VehicleBase
{
    public $maxPass = 1;
    public $speed = 0.16;
    public $maxSpeed = 100;
    public $scale = 1;
    public $hasTrunk = false;

    public function __construct(World $level, CompoundTag $nbt)
    {
        $this->passSeat = [
            new Vector3(0,0.4,-1)
        ];
        $this->riderSeat = new Vector3(0,0.4,-0.5);
        parent::__construct($level, $nbt);
    }
}