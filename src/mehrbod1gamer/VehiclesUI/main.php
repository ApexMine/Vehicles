<?php

namespace mehrbod1gamer\VehiclesUI;

use mehrbod1gamer\VehiclesUI\commands\VehicleCommand;
use mehrbod1gamer\VehiclesUI\entity\VehicleBase;
use mehrbod1gamer\VehiclesUI\entity\Vehicles\pride;
use mehrbod1gamer\VehiclesUI\entity\Vehicles\bus;
use mehrbod1gamer\VehiclesUI\entity\Vehicles\camaro;
use mehrbod1gamer\VehiclesUI\entity\Vehicles\cybertruck;
use mehrbod1gamer\VehiclesUI\entity\Vehicles\d240z;
use mehrbod1gamer\VehiclesUI\entity\Vehicles\db5;
use mehrbod1gamer\VehiclesUI\entity\Vehicles\jeep;
use mehrbod1gamer\VehiclesUI\entity\Vehicles\mazda_rx7;
use mehrbod1gamer\VehiclesUI\entity\Vehicles\helicopter;
use mehrbod1gamer\VehiclesUI\entity\Vehicles\bmw_m3_nfs;
use mehrbod1gamer\VehiclesUI\entity\Vehicles\lamborghini554;
use mehrbod1gamer\VehiclesUI\entity\Vehicles\mrap;
use mehrbod1gamer\VehiclesUI\entity\Vehicles\stealth_fighter;
use mehrbod1gamer\VehiclesUI\entity\Vehicles\car_police;
use mehrbod1gamer\VehiclesUI\entity\Vehicles\nr34;
use mehrbod1gamer\VehiclesUI\entity\Vehicles\tosca;
use mehrbod1gamer\VehiclesUI\entity\Vehicles\yamaha_rx115;
use pocketmine\entity\Entity;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\nbt\tag\ByteArrayTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class main extends PluginBase implements Listener
{
    public $offers = [];

    /* @var Config */
    public $carsCfg;

    /* @var VehicleBase[] */
    public $spawnedVehicles = [];

    public $riders = [];

    public $passengers = [];
    public $vehicles = [
        pride::class,
        bus::class,
        camaro::class,
        yamaha_rx115::class,
        mazda_rx7::class,
        tosca::class,
        helicopter::class,
        lamborghini554::class,
        bmw_m3_nfs::class,
        db5::class,
        nr34::class,
        cybertruck::class,
        jeep::class,
        stealth_fighter::class,
        car_police::class,
        d240z::class,
        mrap::class,
    ];
    public $vehiclesStr = [];
    public $vehicleType = [];
    public $keyWant = [];
    private static $instance;

    public function onEnable(): void
    {

        self::$instance = $this;
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);

        $this->saveDefaultConfig();
        $this->carsCfg = new Config($this->getDataFolder() . 'cars.json', Config::JSON);

        $this->saveAllResources();
        $this->registerVehicles();
        $this->getVehiclesName();

        $this->getServer()->getCommandMap()->register('vehicle', new VehicleCommand('vehicle'));
        parent::onEnable();
    }

    public function saveAllResources() : void
    {
        foreach (scandir($this->getFile() . 'resources') as $file) {
            if ($file != '.' && $file != '..') {
                if (count(explode('.', $file)) < 2) {
                    foreach (scandir($this->getFile() . "resources/$file") as $inner) {
                        if($inner != '.' and $inner != '..') {
                            $this->saveResource("$file/$inner");
                        }
                    }
                } else{
                    $this->saveResource($file);
                }
            }
        }
    }

    public function getVehiclesName(): void
    {
        foreach (scandir(main::getMain()->getDataFolder()) as $dic) {
            if ($dic != '..' and $dic != '.' and $dic != 'config.yml' and $dic != 'cars.json') {
                foreach (scandir($this->getDataFolder() . '/' . $dic) as $file) {
                    if ($file != '.' and $file != '..' and explode('.', $file)[1] == 'json') {
                        $this->vehiclesStr[] = explode('.', $file)[0];
                        $this->vehicleType[explode('.', $file)[0]] = $dic;
                    }
                }
            }
        }
    }

    public function registerVehicles(): void
    {
        foreach ($this->vehicles as $vehicle) {
            Entity::registerEntity($vehicle, true);
        }
    }

    public function spawnVehicle(string $vehicleName, Player $player)
    {
        $type = $this->vehicleType[$vehicleName];
        $skinPath = main::getMain()->getDataFolder() . $type . '/' . $vehicleName . '.png';
        $geoPath = main::getMain()->getDataFolder() . $type . '/' . $vehicleName . '.json';
        $nbt = Entity::createBaseNBT($player);
        $vehicleID = rand(1, 100000);
        $nbt->setInt('vehicleID', $vehicleID);
        $nbt->setString('model', $vehicleName);
        $nbt->setString('owner', $player->getName());
        $nbt->setIntArray('park', [$player->getPosition()->x, $player->getPosition()->y, $player->getPosition()->z]);
        $nbt->setString('status', 'open');
        $nbt->setByteArray('chest', json_encode([]));
        $skin = new CompoundTag('Skin', [
            "Name" => new StringTag("Name", $player->getSkin()->getSkinId()),
            "Data" => new ByteArrayTag("Data", skin::getSkin($skinPath, true)),
            "GeometryName" => new StringTag("GeometryName", "geometry.$vehicleName"),
            "GeometryData" => new ByteArrayTag("GeometryData", file_get_contents($geoPath))
        ]);
        $nbt->setTag($skin);
        $entity = Entity::createEntity($vehicleName, $player->getWorld(), $nbt);
        $entity->spawnToAll();
        $key = (VanillaItems::IRON_NUGGET());
        $key->setCustomName(TextFormat::RED . $vehicleName . ' ' . TextFormat::YELLOW . 'KEY');
        $key->setLore([$vehicleID]);
        $player->sendMessage(TextFormat::BLUE . "[" . TextFormat::DARK_BLUE . "VehicleUi" . TextFormat::BLUE . "]  " . TextFormat::GOLD . "Baraye daryaft kelid bezanid /vehicle key");
    }

    public function isOwned(Player $player, string $vehicleName): bool
    {
        $owned = explode(',', $this->carsCfg->get($player->getName()));
        if (isset($this->carsCfg->getAll()[$player->getName()])) {
            if (in_array($vehicleName, $owned)) {
                return true;
            }
        }
        return false;
    }

    public static function getMain(): self
    {
        return self::$instance;
    }
}