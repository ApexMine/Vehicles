<?php

namespace mehrbod1gamer\VehiclesUI\entity\Vehicles;

use mehrbod1gamer\VehiclesUI\entity\FlyingVehicle;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;

class helicopter extends FlyingVehicle
{
    public $maxPass = 3;
    public $speed = 0.7;
    public $scale = 2;
    public $hasTrunk = false;

    public function __construct(World $level, CompoundTag $nbt)
    {
        $this->passSeat = [
            new Vector3(-0.40,0.4,0.6),
            new Vector3(0.40,0.4,-0.6),
            new Vector3(-0.45,0.4,-0.6),
        ];
        $this->riderSeat = new Vector3(0.35, 0.4,0.6);
        parent::__construct($level, $nbt);
    }
}