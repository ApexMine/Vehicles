<?php

namespace mehrbod1gamer\VehiclesUI\entity;

use mehrbod1gamer\VehiclesUI\main;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\SetActorLinkPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityLink;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use pocketmine\world\World;

class VehicleBase extends Human
{
    public $key;
    /* @var Vector3 */
    public $riderSeat;

    /* @var Vector3[] */
    public $passSeat = [];

    public $passengers = [];

    /* @var float */
    public $speed;
    public $reelSpeed = 1;
    public $maxSpeed;

    public $rider = null;
    public $scale;
    public $stopping = true;
    public $hasTrunk = true;
    public $x;
    public $namedtag;
    public $z;

    public function __construct(World $level, CompoundTag $nbt)
    {
        parent::__construct($level, $nbt);
        foreach (main::getMain()->spawnedVehicles as $spawnedVehicle) {
            if ($spawnedVehicle->getModel() == $this->getModel() and $spawnedVehicle->getOwner() == $this->getOwner()) {
                $this->close();
                return;
            }
        }
        main::getMain()->spawnedVehicles[$this->getId()] = $this;
    }

    public function setRider(Player $player)
    {
        $this->rider = $player;
        $pk = new SetActorLinkPacket();
        $pk->link = new EntityLink($this->getId(), $player->getId(), EntityLink::TYPE_RIDER, true, true);
        foreach (main::getMain()->getServer()->getOnlinePlayers() as $onlinePlayer) {
            $onlinePlayer->sendDataPacket($pk);
        }
        $player->getDataPropertyManager()->setVector3(Entity::DATA_RIDER_SEAT_POSITION, $this->riderSeat);
        main::getMain()->riders[$player->getName()] = $this->getId();
    }

    public function unsetRider(Player $player)
    {
        unset(main::getMain()->riders[$player->getName()]);
        $this->rider = null;
        $pk = new SetActorLinkPacket();
        $pk->link = new EntityLink($this->getId(), $player->getId(), EntityLink::TYPE_REMOVE, true, true);
        foreach (main::getMain()->getServer()->getOnlinePlayers() as $onlinePlayer) {
            $onlinePlayer->sendDataPacket($pk);
        }
    }

    public function setPass(Player $player)
    {
        if ($this->getStatus() == 'lock') {
            $player->sendMessage(TextFormat::RED . 'In Mashin GHofl Mobashad!');
            return false;
        }

        if ($this->countPass() < $this->maxPass) {
            $seatPass = $this->passSeat[$this->countPass()];
            $pk = new SetActorLinkPacket();
            $pk->link = new EntityLink($this->getId(), $player->getId(), EntityLink::TYPE_PASSENGER, true, true);
            foreach (main::getMain()->getServer()->getOnlinePlayers() as $onlinePlayer) {
                $onlinePlayer->sendDataPacket($pk);
            }
            $player->getDataPropertyManager()->setVector3(Entity::DATA_RIDER_SEAT_POSITION, $seatPass);
            main::getMain()->passengers[$player->getName()] = $this->getId();
            $this->passengers[$player->getId()] = $player;
        } else {
            $player->sendMessage('In Mashin Sandali Kafi Nadarad!');
        }
        return true;
    }

    public function unsetPass(Player $player)
    {
        unset(main::getMain()->passengers[$player->getName()]);
        unset($this->passengers[$player->getId()]);
        $pk = new SetActorLinkPacket();
        $pk->link = new EntityLink($this->getId(), $player->getId(), EntityLink::TYPE_REMOVE, true, true);
        foreach (main::getMain()->getServer()->getOnlinePlayers() as $onlinePlayer) {
            $onlinePlayer->sendDataPacket($pk);
        }
    }

    public function updateMove(): void
    {
        $this->stopping = false;
        if (($this->reelSpeed - 1) * 50 < $this->maxSpeed) {
            $this->reelSpeed += 0.02;
        }
        $targetVector = $this->getDirectionVector();
        $targetBlock = $this->getTargetBlock(1);
        $this->move($targetVector->getX() * $this->speed * $this->reelSpeed, $targetVector->getY() * $this->speed * $this->reelSpeed, $targetVector->getZ() * $this->speed * $this->reelSpeed);
        if ($this->getWorld()->getBlock(new Vector3($targetBlock->getPosition()->getX(), $targetBlock->getPosition()->getY() - 1, $targetBlock->getPosition()->getZ())) != Block::AIR and $this->getWorld()->getBlock(new Vector3($targetBlock->getPosition()->getX(), $targetBlock->getPosition()->getY(), $targetBlock->getPosition()->getZ())) == Block::AIR) {
            $this->move(0, 1.5, 0);
        }
    }

    public function onUpdate(int $currentTick): bool
    {
        if ($this->isUnderwater()) {
            $this->close();
            return false;
        }

        if ($this->getScale() < $this->scale) $this->setScale($this->scale);

        if ($this->stopping and $this->reelSpeed > 1) $this->reelSpeed -= 0.02;

        if ($this->rider instanceof Player and !$this instanceof FlyingVehicle) {
            $shownSpeed = round(($this->reelSpeed - 1) * 50);
            $this->rider->addActionBarMessage(
                TextFormat::GREEN . 'Speed: ' . TextFormat::DARK_RED . $shownSpeed . ' km/h' . "\n" .
                TextFormat::GREEN . 'Status: ' . TextFormat::DARK_RED . $this->getStatus() . 'ed'
            );
        }

        //vehicle collide system
        foreach (main::getMain()->spawnedVehicles as $spawnedVehicle) {
            if ($spawnedVehicle !== $this) {
                if ($this->distance($spawnedVehicle) < 0.5) {
                    $speed = abs($this->getMotion()->x) + abs($this->getMotion()->z);
                    if ($speed > 2) {
                        $spawnedVehicle->knockBack($this, 0, $spawnedVehicle->x - $this->x, $spawnedVehicle->z - $this->z, 0.3);
                        $this->knockBack($spawnedVehicle, 0, $this->x - $spawnedVehicle->x, $this->z - $spawnedVehicle->z, 0.1);
                        break;
                    }
                    if ($speed < 2) {
                        $spawnedVehicle->knockBack($this, 0, $spawnedVehicle->x - $this->x, $spawnedVehicle->z - $this->z, 0.2);
                        $this->knockBack($spawnedVehicle, 0, $this->x - $spawnedVehicle->x, $this->z - $spawnedVehicle->z, 0.1);
                        break;
                    }
                }
            }
        }

        return parent::onUpdate($currentTick);
    }

    public function lock()
    {
        if ($this->getStatus() == 'open') {
            $this->namedtag->setString('status', 'lock');
        } else {
            $this->namedtag->setString('status', 'open');
        }
    }

    public function changeOwner(Player $player)
    {
        //unset vehicle from seller
        $vehicles = explode(',', main::getMain()->carsCfg->get($this->getOwner()));
        foreach ($vehicles as $index => $vehicle) {
            if ($vehicle == $this->getModel()) unset($vehicles[(int)$index]);
        }
        if (count($vehicles) == 0) {
            main::getMain()->carsCfg->remove($player->getName());
            main::getMain()->carsCfg->save();
        } else {
            main::getMain()->carsCfg->set($player->getName(), implode(',', $vehicles));
            main::getMain()->carsCfg->save();
        }
        $player->sendMessage(TextFormat::GREEN . "Mashin Shoma Forokhte Shod !");
        //set vehicle for buyer
        if (isset(main::getMain()->carsCfg->getAll()[$player->getName()])) {
            $owned = explode(',', main::getMain()->carsCfg->get($player->getName()));
        } else {
            $owned = [];
        }
        $owned[] = $this->getModel();
        main::getMain()->carsCfg->set($player->getName(), implode(',', $owned));
        main::getMain()->carsCfg->save();

        $this->namedtag->setString('owner', $player->getName());
    }

    public function reSpawn()
    {
        $this->teleport($this->getParkPos());
    }

    public function isClosed(): bool
    {
        if ($this->hasRider()) $this->unsetRider($this->getRider());
        if (count($this->passengers) != 0) {
            foreach ($this->passengers as $passenger) {
                $this->unsetPass($passenger);
            }
        }
        unset(main::getMain()->spawnedVehicles[$this->getId()]);
        parent::close();
        return true;
    }

    public function openChest(Player $player)
    {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $menu->setName(TextFormat::YELLOW . 'Sandogh');
        $inv = $menu->getInventory();
        if (count($this->getChestContents()) > 0) {
            $inv->setContents($this->getChestContents());
        }
        $menu->setInventoryCloseListener(function (Player $player, Inventory $inventory): void {
            if (count($inventory->getContents()) > 0) {
                $this->setChestContents($inventory->getContents());
            }
        });
        $menu->send($player);
    }

    public function setChestContents($items)
    {
        $this->namedtag->setByteArray('chest', json_encode($items));
    }

    public function getChestContents()
    {
        $items = [];
        $content = json_decode($this->namedtag->getByteArray('chest'), true);
        foreach ($content as $slot => $item) {
            $newItem = new Item($item['id']);
            if (isset($item['count'])) {
                $newItem->setCount($item['count']);
            }
            $items[$slot] = $newItem;
        }
        return $items;
    }

    public function getRider()
    {
        return $this->rider;
    }

    public function countPass(): int
    {
        return count($this->passengers);
    }

    public function hasRider(): bool
    {
        return !is_null($this->getRider());
    }

    public function getVehicleID()
    {
        return $this->namedtag->getInt('vehicleID');
    }

    public function getModel()
    {
        return $this->namedtag->getString('model');
    }

    public function canCollideWith(Entity $entity): bool
    {
        return false;
    }

    public function getOwner()
    {
        return $this->namedtag->getString('owner');
    }

    public function getStatus()
    {
        return $this->namedtag->getString('status');
    }

    public function setParkPos(Position $position)
    {
        $this->namedtag->setIntArray('park', [$position->x, $position->y, $position->z]);
    }

    public function getParkPos()
    {
        $array = $this->namedtag->getIntArray('park');
        return new Position($array[0], $array[1], $array[2], $this->getWorld());
    }

    public function giveKey(Player $player)
    {
        $this->key = (VanillaItems::IRON_NUGGET());
        $this->key->setCustomName(TextFormat::RED . $this->getModel() . ' ' . TextFormat::YELLOW . 'KEY');
        $this->key->setLore([$this->getVehicleID()]);
        $player->getInventory()->addItem($this->key);
        $player->sendMessage(TextFormat::GREEN . 'Key Be inventory Shoma Ezafeh Shod!');
    }
}