<?php

namespace mehrbod1gamer\VehiclesUI\Forms;

use jojoe77777\FormAPI\SimpleForm;
use mehrbod1gamer\VehiclesUI\main;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as c;

class VehicleForm
{
    public static function openForm(Player $player, string $type)
    {
        $vehiclesInType = [];
        foreach (scandir(main::getMain()->getDataFolder()) as $dic) {
            if ($dic == $type) {
                foreach (scandir(main::getMain()->getDataFolder() . '/' . $dic) as $file){
                    if ($file != '.' and $file != '..' and explode('.', $file)[1] == 'json') {
                        $vehiclesInType[] = explode('.', $file)[0];
                    }
                }
            }
        }

        $economy = main::getMain()->getServer()->getPluginManager()->getPlugin('EconomyAPI');
        if ($economy === null) {
            return;
        }

        $form = new SimpleForm(function (Player $player, $data) use ($economy, $vehiclesInType, $type) {
            if ($data === null) {
                return;
            }

            $vehicleName = $vehiclesInType[$data];

            $perm = 'buy'.$vehicleName;
            if (!$player->hasPermission($perm)) {
                $player->sendMessage(c::BLUE . "[" . c::DARK_BLUE . "VehicleUi" . c::BLUE . "]  " . c::RED . '[ ! ] Shoma permission kharid in mashin ra nadarid!');
                return;
            }

            if (main::getMain()->isOwned($player, $vehicleName)) {
                $player->sendMessage(c::BLUE . "[" . c::DARK_BLUE . "VehicleUi" . c::BLUE . "]  " . c::GREEN . 'You have this car just use /vehicle own');
                return;
            }

            if ($economy->myMoney($player) >= main::getMain()->getConfig()->get($vehicleName)) {
                if (!$player->isOnGround()) {
                    $player->sendMessage(c::BLUE . "[" . c::DARK_BLUE . "VehicleUi" . c::BLUE . "]  " . c::RED . '[ ! ] Baraye Kharid Mashin Ro ZaMin Bashid');
                    return;
                }
                $economy->reduceMoney($player, main::getMain()->getConfig()->get($vehicleName));
                $player->addActionBarMessage(c::RED . '- ' . main::getMain()->getConfig()->get($vehicleName) . '$');

                if (isset(main::getMain()->carsCfg->getAll()[$player->getName()])) {
                    $owned = explode(',' ,main::getMain()->carsCfg->get($player->getName()));
                } else {
                    $owned = [];
                }

                $owned[] = $vehicleName;
                $owned = implode(',' ,$owned);
                main::getMain()->carsCfg->set($player->getName(), $owned);
                main::getMain()->carsCfg->save();
                main::getMain()->spawnVehicle($vehicleName, $player);

            } else {
                $player->sendMessage(c::BLUE . "[" . c::DARK_BLUE . "VehicleUi" . c::BLUE . "]  " . c::RED . 'Shoma Pool Kafi Ra Nadarid .');
            }
        });

        $form->setTitle(c::BLUE . "[" . c::DARK_BLUE . "VehicleUi" . c::BLUE . "]  " . c::RED . ' LopasMc');
        foreach ($vehiclesInType as $vehicle) {
            $form->addButton(c::BLACK . $vehicle . '  ' . c::GREEN . main::getMain()->getConfig()->get($vehicle) . '$');
        }
        $form->sendToPlayer($player);
    }
}