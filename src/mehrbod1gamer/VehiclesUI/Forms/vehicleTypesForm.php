<?php

namespace mehrbod1gamer\VehiclesUI\Forms;

use jojoe77777\FormAPI\SimpleForm;
use mehrbod1gamer\VehiclesUI\main;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class vehicleTypesForm
{
    public static function openForm(Player $player) {
        $types = [];
        foreach (scandir(main::getMain()->getDataFolder()) as $dic) {
            if ($dic != '..' and $dic != '.' and $dic != 'config.yml' and $dic != 'cars.json') {
                $types[] = $dic;
            }
        }

        $form = new SimpleForm(function (Player $player, $data) use ($types) {
            if ($data === null) return;

            VehicleForm::openForm($player, $types[$data]);
        });
        $form->setTitle(TextFormat::YELLOW . 'Vehicles Shop - LopasMc');
        foreach ($types as $type) {
            $form->addButton(TextFormat::BLACK . $type);
        }
        $form->sendToPlayer($player);
    }
}