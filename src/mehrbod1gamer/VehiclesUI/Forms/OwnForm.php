<?php

namespace mehrbod1gamer\VehiclesUI\Forms;

use jojoe77777\FormAPI\SimpleForm;
use mehrbod1gamer\VehiclesUI\entity\VehicleBase;
use mehrbod1gamer\VehiclesUI\main;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class OwnForm
{
    public static function openForm(Player $player)
    {
        $vehicles = explode(',' ,main::getMain()->carsCfg->get($player->getName()));
        if (!is_array($vehicles) or empty($vehicles[0])) {
            $player->sendMessage(TextFormat::BLUE . "[" . TextFormat::DARK_BLUE . "VehicleUi" . TextFormat::BLUE . "]  " . TextFormat::RED . 'Shoma Hich Mashini Nadarid !');
            return false;
        }

        $form = new SimpleForm(function (Player $player, $data) use ($vehicles) {
            if ($data === null) {
                return false;
            }
            $vehicleName = $vehicles[$data];
            foreach (main::getMain()->spawnedVehicles as $spawnedVehicle) {
                if ($spawnedVehicle->getOwner() == $player->getName() and $spawnedVehicle->getModel() == $vehicleName) {
                    $player->teleport($spawnedVehicle);
                    return true;
                }
            }
            main::getMain()->spawnVehicle($vehicleName, $player);
            return true;
        });
        $form->setTitle('Owned Vehicles');
        $form->setContent(TextFormat::GREEN . 'Owned: ' . TextFormat::AQUA . count($vehicles) . '/' . count(main::getMain()->vehiclesStr));
        foreach ($vehicles as $vehicle) {
            $form->addButton(TextFormat::YELLOW . $vehicle);
        }
        $form->sendToPlayer($player);
        return true;
    }
}