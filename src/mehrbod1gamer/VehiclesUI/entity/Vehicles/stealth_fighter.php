<?php

namespace mehrbod1gamer\VehiclesUI\entity\Vehicles;

use mehrbod1gamer\VehiclesUI\entity\FlyingVehicle;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;

class stealth_fighter extends FlyingVehicle
{
    public $maxPass = 0;
    public $speed = 2.0;
    public $scale = 2;
    public $hasTrunk = false;

    public function __construct(World $level, CompoundTag $nbt)
    {
        $this->passSeat = [];
        $this->riderSeat = new Vector3(0,0.8,0.4);
        parent::__construct($level, $nbt);
    }
}