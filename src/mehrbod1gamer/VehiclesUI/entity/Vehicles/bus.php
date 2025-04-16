<?php

namespace mehrbod1gamer\VehiclesUI\entity\Vehicles;

use mehrbod1gamer\VehiclesUI\entity\VehicleBase;
use pocketmine\world\World;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;

class bus extends VehicleBase
{
    public $maxPass = 5;
    public $speed = 0.1;
    public $maxSpeed = 60;
    public $scale = 1;
    public $hasTrunk = true;

    public function __construct(World $level, CompoundTag $nbt)
    {
        $this->passSeat = [
            new Vector3(0.6, 0.75, 1.5),
            new Vector3(-0.5, 0.3, -1.2),
            new Vector3(0.5, 0.3, -1.2),
            new Vector3(-0.8, 0.6, -2.9),
            new Vector3(0.9, 0.6, -2.9)
        ];
        $this->riderSeat = new Vector3(0.6, 0.4, 2.2);
        parent::__construct($level, $nbt);
    }
}