<?php

namespace mehrbod1gamer\VehiclesUI\Forms;

use jojoe77777\FormAPI\SimpleForm;
use mehrbod1gamer\VehiclesUI\main;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class SellVehiclesForm
{
    public static function openForm(Player $player)
    {
        $vehicles = explode(',', main::getMain()->carsCfg->get($player->getName()));
        if (!is_array($vehicles)) {
            $player->sendMessage(TextFormat::BLUE . "[" . TextFormat::DARK_BLUE . "VehicleUi" . TextFormat::BLUE . "]  " . TextFormat::RED . 'Shoma Hich Mashini Nadarid !');
            return false;
        }

        $form = new SimpleForm(function (Player $player, $data) use ($vehicles) {
            if ($data === null) {
                return true;
            }

            if ($data >= count($vehicles)) {
                return false;
            } else {
                $carName = $vehicles[$data];
                SellVehicleForm::openForm($player, $carName);
            }
            return true;
        });
        $form->setTitle(TextFormat::YELLOW . 'Sell Vehicles');
        foreach ($vehicles as $vehicle) {
            $form->addButton(TextFormat::BLACK . $vehicle . '   ' . TextFormat::GREEN . main::getMain()->getConfig()->get($vehicle) . '$');
        }
        $form->addButton(TextFormat::RED . 'Close');
        $form->sendToPlayer($player);
        return true;
    }
}