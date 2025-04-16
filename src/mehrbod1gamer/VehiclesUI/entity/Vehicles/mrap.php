<?php

namespace mehrbod1gamer\VehiclesUI\entity\Vehicles;

use mehrbod1gamer\VehiclesUI\entity\VehicleBase;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;

class mrap extends VehicleBase
{
    public $maxPass = 2;
    public $speed = 0.17;
    public $maxSpeed = 120;
    public $scale = 1;
    public $hasTrunk = true;

    public function __construct(World $level, CompoundTag $nbt)
    {
        $this->passSeat = [
            new Vector3(-0.7,1.1,2),
            new Vector3(0,3,-0.5),
        ];
        $this->riderSeat = new Vector3(0.7,1.1,2);
        parent::__construct($level, $nbt);
    }
}