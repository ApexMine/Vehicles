<?php

namespace mehrbod1gamer\VehiclesUI\Forms;

use jojoe77777\FormAPI\SimpleForm;
use mehrbod1gamer\VehiclesUI\main;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class RespawnForm
{
    public static function openForm(Player $player)
    {	
        $cars = [];
        foreach (main::getMain()->spawnedVehicles as $spawnedVehicle) {
            if ($spawnedVehicle->getOwner() == $player->getName()) {
                $cars [] = $spawnedVehicle;
            }
        }

		if(count($cars) < 1) {
			$player->sendMessage(TextFormat::BLUE . "[" . TextFormat::DARK_BLUE . "VehicleUi" . TextFormat::BLUE . "]  " . TextFormat::RED . '[!] You dont have any spawnedVehicles');
			return false;
		}

        $form = new SimpleForm(function (Player $player, $data) use ($cars){
            if($data === null) {
                return false;
            }
            if ($data == count($cars)) return false;
            $selected = $cars[$data];
            $selected->reSpawn();
            $player->sendMessage(TextFormat::BLUE . "[" . TextFormat::DARK_BLUE . "VehicleUi" . TextFormat::BLUE . "]  " . TextFormat::GREEN . 'Mashin Shoma ReSpawn Shod !');
            return true;
        });
        $form->setTitle(TextFormat::YELLOW . 'ReSpawn Vehicles - LopasMc');
        foreach ($cars as $car) {
            $form->addButton($car->getModel());
        }
        $form->addButton(TextFormat::RED . 'Close');
        $form->sendToPlayer($player);
        return true;
    }
}