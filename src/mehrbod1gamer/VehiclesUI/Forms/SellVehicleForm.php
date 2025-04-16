<?php

namespace mehrbod1gamer\VehiclesUI\Forms;

use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\ModalForm;
use jojoe77777\FormAPI\SimpleForm;
use mehrbod1gamer\VehiclesUI\main;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use onebone\economyapi\EconomyAPI;

class SellVehicleForm
{
    public $buyer;
    public static function openForm(Player $player, string $carName)
    {
        $to_list = ['server'];
        foreach (main::getMain()->getServer()->getOnlinePlayers() as $onlinePlayer) {
            $name = $onlinePlayer->getName();
            if ($name != $player->getName()) $to_list[] = $name;
        }

        $carPrice = main::getMain()->getConfig()->get($carName);

        $form = new CustomForm(function (Player $player, $data) use ($to_list, $carName, $carPrice) {
            if ($data == null) {
                return false;
            }
            $whom = $to_list[$data[0]];
            self::acceptForm($player, $carName, $carPrice, $whom);
            return true;
        });
        $form->setTitle(TextFormat::YELLOW . 'Sell Vehicles - MagicLand');
        $form->addDropdown(TextFormat::AQUA . 'select buyer', $to_list);
        $form->sendToPlayer($player);
    }

    public static function acceptForm(Player $player, $carName, $price, $whom)
    {
        $form = new ModalForm(function (Player $player, $data) use ($whom, $price, $carName) {
            if ($data === null) {
                return false;
            }
            if ($data) {
                if ($whom == 'server') {
                    EconomyAPI::getInstance()->addMoney($player, $price);
                    $vehicles = explode(',' ,main::getMain()->carsCfg->get($player->getName()));
                    foreach ($vehicles as $index => $vehicle) {
                        if ($vehicle == $carName) unset($vehicles[(int)$index]);
                    }
                    if (count($vehicles) == 0) {
                        main::getMain()->carsCfg->remove($player->getName());
                        main::getMain()->carsCfg->save();
                    } else {
                        main::getMain()->carsCfg->set($player->getName(), implode(',', $vehicles));
                        main::getMain()->carsCfg->save();
                    }
                    foreach (main::getMain()->spawnedVehicles as $spawnedVehicle) {
                        if ($spawnedVehicle->getOwner() == $player->getName() and $spawnedVehicle->getModel() == $carName) {
                            $spawnedVehicle->close();
                        }
                    }
                    $player->sendMessage(TextFormat::BLUE . "[" . TextFormat::DARK_BLUE . "VehicleUi" . TextFormat::BLUE . "]  " . TextFormat::GREEN . 'mashin Shoma Be Server Forokhte Shod !');
                    $player->addActionBarMessage(TextFormat::GREEN . '+ ' . $price . '$');
                } else {
                    $buyer = main::getMain()->getServer()->getPlayerExact($whom);
                    if ($buyer instanceof Player) {
                        if (main::getMain()->isOwned($buyer, $carName)) {
                            $player->sendMessage(TextFormat::BLUE . "[" . TextFormat::DARK_BLUE . "VehicleUi" . TextFormat::BLUE . "]  " . TextFormat::RED . "Player " . TextFormat::DARK_RED . $buyer->getName() . TextFormat::RED . ' Saheb Mashin Mibashad !');
                            return false;
                        }
                        foreach (main::getMain()->spawnedVehicles as $spawnedVehicle) {
                            if ($spawnedVehicle->getOwner() == $player->getName() and $spawnedVehicle->getModel() == $carName) {
                                if (!$player->getInventory()->getItemInHand() == $spawnedVehicle->key) {
                                    $player->sendMessage(TextFormat::BLUE . "[" . TextFormat::DARK_BLUE . "VehicleUi" . TextFormat::BLUE . "]  " . TextFormat::RED . "Kelid Mashin " . TextFormat::DARK_RED . $carName . TextFormat::RED . " Ra Dar Dast Begirid !");
                                    return false;
                                } else {
                                    $item = $player->getInventory()->getItemInHand();
                                    $count = $item->getCount();
                                    $item->setCount($item->getCount() - (int)$count);
                                    $player->getInventory()->setItemInHand($item);
                                    $spawnedVehicle->changeOwner($buyer);
                                    $player->sendMessage(TextFormat::BLUE . "[" . TextFormat::DARK_BLUE . "VehicleUi" . TextFormat::BLUE . "]  " . TextFormat::GREEN . 'Mashin ' . TextFormat::DARK_GREEN . $carName . TextFormat::GREEN . " Be Name " . TextFormat::RED . $buyer->getName() . TextFormat::GREEN . " Shod !");
                                    $buyer->sendMessage(TextFormat::BLUE . "[" . TextFormat::DARK_BLUE . "VehicleUi" . TextFormat::BLUE . "]  " . TextFormat::GREEN . 'Mashin ' . TextFormat::DARK_GREEN . $carName . TextFormat::GREEN . ' Be Name Shoma Shod !');
                                    $buyer->sendMessage(TextFormat::BLUE . "[" . TextFormat::DARK_BLUE . "VehicleUi" . TextFormat::BLUE . "]  " . TextFormat::GOLD . "Baraye daryaft kelid bezanid /vehicle key");
                                    $vehicles = explode(',' ,main::getMain()->carsCfg->get($player->getName()));
                                    foreach ($vehicles as $index => $vehicle) {
                                        if ($vehicle == $carName) unset($vehicles[(int)$index]);
                                    }
                                    if (count($vehicles) == 0) {
                                        main::getMain()->carsCfg->remove($player->getName());
                                        main::getMain()->carsCfg->save();
                                    } else {
                                        main::getMain()->carsCfg->set($player->getName(), implode(',', $vehicles));
                                        main::getMain()->carsCfg->save();
                                    }
                                }
                            }
                        }
                    } else $player->sendMessage(TextFormat::RED . $whom . ' is offline');
                }
            } else return false;
            return true;
        });
        $form->setTitle(TextFormat::RED . 'Accept Form');
        $form->setContent(
            TextFormat::YELLOW . 'To Dari Mashin ' . TextFormat::RED . $carName . TextFormat::GREEN . ' Ra Be Name ' . TextFormat::RED . $price . '$' . TextFormat::YELLOW . 'Mikoni !' .
            TextFormat::YELLOW . "\nAya Motmaeni ?"
        );
        $form->setButton1(TextFormat::GREEN . 'Yes');
        $form->setButton2(TextFormat::RED . 'No');
        $form->sendToPlayer($player);
    }
}