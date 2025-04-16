<?php

namespace mehrbod1gamer\VehiclesUI\entity\Vehicles;

use mehrbod1gamer\VehiclesUI\entity\VehicleBase;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\thread\Worker;
use pocketmine\world\World;

class tosca extends VehicleBase
{
    public $maxPass = 3;
    public $speed = 0.17;
    public $maxSpeed = 120;
    public $scale = 1;
    public $hasTrunk = true;

    public function __construct(World $level, CompoundTag $nbt)
    {
        $this->passSeat = [
            new Vector3(-0.5,-0.2,0.1),
            new Vector3(0.5,-0.2,-0.6),
            new Vector3(-0.5,-0.2,-0.6),
        ];
        $this->riderSeat = new Vector3(0.5,-0.2,0.1);
        parent::__construct($level, $nbt);
    }
}