<?php

namespace mehrbod1gamer\VehiclesUI\entity\Vehicles;

use mehrbod1gamer\VehiclesUI\entity\VehicleBase;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;

class lamborghini554 extends VehicleBase
{
    public $maxPass = 1;
    public $speed = 0.35;
    public $maxSpeed = 300;
    public $scale = 1;
    public $hasTrunk = true;

    public function __construct(World $level, CompoundTag $nbt)
    {
        $this->passSeat = [
            new Vector3(-0.5,-0.55,0)
        ];
        $this->riderSeat = new Vector3(0.5,-0.55,0);
        parent::__construct($level, $nbt);
    }
}