<?php

namespace mehrbod1gamer\VehiclesUI\entity\Vehicles;

use mehrbod1gamer\VehiclesUI\entity\VehicleBase;
use pocketmine\world\World;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;

class bmw_m3_nfs extends VehicleBase
{
    public $maxPass = 1;
    public $speed = 0.18;
    public $maxSpeed = 180;
    public $scale = 1;
    public $hasTrunk = true;

    public function __construct(World $level, CompoundTag $nbt)
    {
        $this->passSeat = [
            new Vector3(-0.5,-0.4,-0.8)
        ];
        $this->riderSeat = new Vector3(0.5,-0.4,-0.8);
        parent::__construct($level, $nbt);
    }
}