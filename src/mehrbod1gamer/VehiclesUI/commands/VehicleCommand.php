<?php

namespace mehrbod1gamer\VehiclesUI\commands;

use mehrbod1gamer\VehiclesUI\entity\VehicleBase;
use mehrbod1gamer\VehiclesUI\Forms\OwnForm;
use mehrbod1gamer\VehiclesUI\Forms\RespawnForm;
use mehrbod1gamer\VehiclesUI\Forms\SellVehiclesForm;
use mehrbod1gamer\VehiclesUI\Forms\VehicleForm;
use mehrbod1gamer\VehiclesUI\Forms\vehicleTypesForm;
use mehrbod1gamer\VehiclesUI\main;
use mehrbod1gamer\VehiclesUI\skin;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\nbt\tag\ByteArrayTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class VehicleCommand extends Command
{
    public function __construct(string $name)
    {
        parent::__construct($name, 'vehicles main command', '/vehicle {vehicle name}', ['vh']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) {
            return false;
        }

        if (!is_null(main::getMain()->getConfig()->get('world'))) {
            $level = $sender->getWorld()->getDisplayName();
            if ($level != main::getMain()->getConfig()->get('world')) {
                $sender->sendMessage(TextFormat::RED . '[ ! ] In Command Bayad Dar World ' . TextFormat::GREEN . main::getMain()->getConfig()->get('world') . TextFormat::RED . ' Estefadeh Shavad. ');
                return false;
            }
        }

        if (count($args) > 0) {
            if ($args[0] == 'list') {
                $sender->sendMessage(TextFormat::GREEN . '--Vehicles--LopasMc--');
                $vehicles = implode(TextFormat::RED . ', ' . TextFormat::WHITE, main::getMain()->vehiclesStr);
                $sender->sendMessage($vehicles);
                $sender->sendMessage(TextFormat::GREEN . '------------------');
            } elseif ($args[0] == 'own') {
                OwnForm::openForm($sender);
            } elseif ($args[0] == 'key') {
                main::getMain()->keyWant[$sender->getName()] = true;
                $sender->sendMessage(TextFormat::BLUE . "[" . TextFormat::DARK_BLUE . "VehicleUi" . TextFormat::BLUE . "]  " . TextFormat::GREEN . 'Bar Roy Mashin Khod Click Konid');
            } elseif ($args[0] == 'sell') {
                SellVehiclesForm::openForm($sender);
            } elseif ($args[0] == 'lock') {
                if (isset(main::getMain()->riders[$sender->getName()])) {
                    $car = $sender->getWorld()->getEntity(main::getMain()->riders[$sender->getName()]);
                    if ($car instanceof VehicleBase) $car->lock();
                    $sender->sendMessage(TextFormat::BLUE . "[" . TextFormat::DARK_BLUE . "VehicleUi" . TextFormat::BLUE . "]  " . TextFormat::GREEN . 'Your vehicle was ' . $car->getStatus() . 'ed');
                } else $sender->sendMessage(TextFormat::BLUE . "[" . TextFormat::DARK_BLUE . "VehicleUi" . TextFormat::BLUE . "]  " . TextFormat::RED . 'In Mashin Baraye Shoma Nist !');
            } elseif ($args[0] == 'park') {
                if (isset(main::getMain()->riders[$sender->getName()])) {
                    $car = $sender->getWorld()->getEntity(main::getMain()->riders[$sender->getName()]);
                    if ($car instanceof VehicleBase) {
                        $car->setParkPos($car->getPosition());
                        $sender->sendMessage(TextFormat::BLUE . "[" . TextFormat::DARK_BLUE . "VehicleUi" . TextFormat::BLUE . "]  " . TextFormat::GREEN . 'Makan Park Mashin Set Shod.');
                    }
                } else $sender->sendMessage(TextFormat::BLUE . "[" . TextFormat::DARK_BLUE . "VehicleUi" . TextFormat::BLUE . "]  " . TextFormat::RED . 'In Mashin Baraye Shoma Nist !');
            } elseif ($args[0] == 'respawn') {
                RespawnForm::openForm($sender);
            } elseif ($args[0] == 'remove') {
                if (!$sender->getServer()) return false;
                foreach (main::getMain()->spawnedVehicles as $vehicle) {
                    $vehicle->close();
                }
            } elseif ($args[0] == 'inv') {
                if (!isset(main::getMain()->riders[$sender->getName()])) {
                    $sender->sendMessage(TextFormat::BLUE . "[" . TextFormat::DARK_BLUE . "VehicleUi" . TextFormat::BLUE . "]  " . TextFormat::RED . 'In Mashin Baraye Shoma Nist !');
                    return false;
                }

                $vehicle = $sender->getWorld()->getEntity(main::getMain()->riders[$sender->getName()]);
                if ($vehicle instanceof VehicleBase) {
                    $vehicle->openChest($sender);
                }
            } elseif ($args[0] == 'help') {
                $sender->sendMessage(TextFormat::GREEN . '=+=+=+=HeavenMine=+=+=+=');
                $sender->sendMessage(TextFormat::YELLOW . 'Commands: ');
                $sender->sendMessage(TextFormat::AQUA . '/vehicle list : see list of all vehicles');
                $sender->sendMessage(TextFormat::AQUA . '/vehicle own : see list of owned vehicles');
                $sender->sendMessage(TextFormat::AQUA . '/vehicle sell : sell owned vehicles');
                $sender->sendMessage(TextFormat::AQUA . '/vehicle lock : lock/unlock your vehicle');
                $sender->sendMessage(TextFormat::AQUA . '/vehicle park : set park position of your vehicle');
                $sender->sendMessage(TextFormat::AQUA . '/vehicle respawn : respawn vehicle to park position');
                $sender->sendMessage(TextFormat::AQUA . '/vehicle key : get new vehicle key');
                $sender->sendMessage(TextFormat::AQUA . '/vehicle inv : open vehicle inventory');
                $sender->sendMessage(TextFormat::GREEN . '=+=+=+=+=+=+=+=+=+=+=+=+=');
            } else {
                $sender->sendMessage(TextFormat::RED . 'use /vehicle help');
            }
        } else {
            vehicleTypesForm::openForm($sender);
        }
        return true;
    }
}