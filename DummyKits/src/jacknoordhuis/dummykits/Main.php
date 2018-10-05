<?php

namespace jacknoordhuis\dummykits;

use pocketmine\plugin\PluginBase;
use pocketmine\entity\Entity;
use pocketmine\utils\TextFormat as TF;
use pocketmine\nbt\tag\StringTag;

use jacknoordhuis\dummykits\entity\HumanDummy;
use jacknoordhuis\dummykits\kit\KitManager;
use jacknoordhuis\dummykits\dummy\DummyManager;
use jacknoordhuis\dummykits\skin\SkinManager;

use jacknoordhuis\dummykits\command\commands\AddDummy;
use jacknoordhuis\dummykits\command\commands\EditDummy;

class Main extends PluginBase {
        
        public static $instance = null;
        
        public $kitManager = null;
        
        public $dummyManager = null;
        
        public $skinManager = null;
        
        public function onEnable() {
                self::$instance = $this;
                $this->registerEntities();
                $this->setKitManager();
                $this->setDummyManager();
                $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        }
        
        public function registerCommands() {
                $this->getServer()->getCommandMap()->registerAll("dk", [
                    new AddDummy($this),
                    new EditDummy($this),
                ]);
        }
        
        public static function getInstance() {
                return self::$instance;
        }
        
        public function registerEntities() {
                Entity::registerEntity(HumanDummy::class, true);
        }
        
        public function setKitManager() {
                if(isset($this->kitManager) and $this->kitManager instanceof KitManager) return;
                $this->kitManager = new KitManager($this);
        }
        
        public function setDummyManager() {
                if(isset($this->dummyManager) and $this->dummyManager instanceof DummyManager) return;
                $this->dummyManager = new DummyManager($this);
        }
        
        public function setSkinManager() {
                if(isset($this->skinManager) and $this->skinManager instanceof SkinManager) return;
                $this->skinManager = new SkinManager($this);
        }
        
        public static function centerString($string, $around) {
                return str_pad($string, strlen($around), " ", STR_PAD_BOTH);
        }
        
        public static function translateColors($string, $symbol = "&") {
                $string = str_replace($symbol . "0", TF::BLACK, $string);
                $string = str_replace($symbol . "1", TF::DARK_BLUE, $string);
                $string = str_replace($symbol . "2", TF::DARK_GREEN, $string);
                $string = str_replace($symbol . "3", TF::DARK_AQUA, $string);
                $string = str_replace($symbol . "4", TF::DARK_RED, $string);
                $string = str_replace($symbol . "5", TF::DARK_PURPLE, $string);
                $string = str_replace($symbol . "6", TF::GOLD, $string);
                $string = str_replace($symbol . "7", TF::GRAY, $string);
                $string = str_replace($symbol . "8", TF::DARK_GRAY, $string);
                $string = str_replace($symbol . "9", TF::BLUE, $string);
                $string = str_replace($symbol . "a", TF::GREEN, $string);
                $string = str_replace($symbol . "b", TF::AQUA, $string);
                $string = str_replace($symbol . "c", TF::RED, $string);
                $string = str_replace($symbol . "d", TF::LIGHT_PURPLE, $string);
                $string = str_replace($symbol . "e", TF::YELLOW, $string);
                $string = str_replace($symbol . "f", TF::WHITE, $string);

                $string = str_replace($symbol . "k", TF::OBFUSCATED, $string);
                $string = str_replace($symbol . "l", TF::BOLD, $string);
                $string = str_replace($symbol . "m", TF::STRIKETHROUGH, $string);
                $string = str_replace($symbol . "n", TF::UNDERLINE, $string);
                $string = str_replace($symbol . "o", TF::ITALIC, $string);
                $string = str_replace($symbol . "r", TF::RESET, $string);
                
                return $string;
        }
        
        public static function removeColors($string, $symbol = "&") {
                $string = str_replace($symbol . "0", "", $string);
                $string = str_replace($symbol . "1", "", $string);
                $string = str_replace($symbol . "2", "", $string);
                $string = str_replace($symbol . "3", "", $string);
                $string = str_replace($symbol . "4", "", $string);
                $string = str_replace($symbol . "5", "", $string);
                $string = str_replace($symbol . "6", "", $string);
                $string = str_replace($symbol . "7", "", $string);
                $string = str_replace($symbol . "8", "", $string);
                $string = str_replace($symbol . "9", "", $string);
                $string = str_replace($symbol . "a", "", $string);
                $string = str_replace($symbol . "b", "", $string);
                $string = str_replace($symbol . "c", "", $string);
                $string = str_replace($symbol . "d", "", $string);
                $string = str_replace($symbol . "e", "", $string);
                $string = str_replace($symbol . "f", "", $string);

                $string = str_replace($symbol . "k", "", $string);
                $string = str_replace($symbol . "l", "", $string);
                $string = str_replace($symbol . "m", "", $string);
                $string = str_replace($symbol . "n", "", $string);
                $string = str_replace($symbol . "o", "", $string);
                $string = str_replace($symbol . "r", "", $string);
                
                $string = TF::clean($string);
                
                return $string;
        }
        
        public static function parseArmor($string) {
                $temp = explode(",", str_replace(" ", "", $string));
                if(isset($temp[3])) {
                        return [Item::get($temp[0]), Item::get($temp[1]), Item::get($temp[2]), Item::get($temp[3])];
                } else {
                        return [];
                }
        }

        public static function parseItems(array $strings) {
                $items = [];
                foreach($strings as $string) {
                        $items[] = self::parseItem($string);
                }
                return $items;
        }

        public static function parseEffects(array $strings) {
                $effects = [];
                foreach($strings as $string) {
                        $temp = explode(",", str_replace(" ", "", $string));
                        if(!isset($temp[3])) {
                                $effects[] = Effect::getEffectByName($temp[0])->setAmplifier($temp[1])->setDuration(20 * $temp[2]);
                        } else {
                                continue;
                        }
                }
                return $effects;
        }
        
        public static function parsePos($string) {
                $temp = explode(",", str_replace(" ", "", $string));
                if(isset($temp[2])) {
                        return new Vector3($temp[0], $temp[1], $temp[2]);
                } else {
                        return;
                }
        }
        
        public static function parseItem($string) {
                $temp = explode(",", str_replace(" ", "", $string));
                if(isset($temp[2])) {
                        return Item::get($temp[0], $temp[1], $temp[2]);
                } else {
                        return;
                }
        }
        
        public static function array2StringTag(array $array) {
                $temp = [];
                foreach($array as $key => $data) {
                        $temp[] = new StringTag($key, $data);
                }
                return $temp;
        }
        
}
