<?php
/**
 * Food and drink machine.
 */

/**
 * Machine class and all related methods.
 */
class Bagetomat
{
    /**
     * Current coins in the machine loaded from json file.
     *
     * @var int
     */
    private $machineCoins;
    /**
     * Coins in the coins return slot.
     *
     * @var int
     */
    private $returnCoinsSlot;
    /**
     * Contents of the pickup slot.
     *
     * @var string
     */
    private $pickupSlot;
    /**
     * Current count of the selected product loaded from json.
     *
     * @var int
     */
    private $productCount;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->returnCoinsSlot = null;
        $this->pickupSlot = null;
    }

    /**
     * Does all the calculations and checks when buying a product.
     *
     * @param int $insertedCoins Coins that were inserted into the machine.
     * @param string $productCode Unique ID of the product.
     * @return bool|string Returns true if executed successfully
     * or error message if something gone wrong.
     */
    public function buyProduct($insertedCoins, $productCode)
    {
        if (empty($insertedCoins)) {
            return("Nic jsi nevhodil!");
        }

        if (empty($productCode)) {
            return("Nic jsi nestiskl!");
        }
        
        $machineCoins = self::getMachineCoins();
        $productName = self::getProductName($productCode);
        $productPrice = self::getProductPrice($productCode);
        $productCount = self::getProductCount($productCode);

        $returnCoins = $insertedCoins - $productPrice;

        if ($insertedCoins >= $productPrice) {
            if ($productCount > 0) {
                if ($machineCoins >= $returnCoins) {
                    $productCount--;
                    $machineCoins -= $returnCoins;
                    $machineCoins += $productPrice;
                    $this->pickupSlot = $productName;
                    $this->returnCoinsSlot = $returnCoins;
                    self::saveChanges($productCode, $productCount, $machineCoins);
                    return true;
                } else {
                    return "Nedostatek mincí v automatu na vrácení!";
                }
            } else {
                return "Nedostatek zvoleného produktu v automatu!";
            }
        } else {
            return "Nevhodil jsi dostatek mincí!";
        }
    }

    /**
     * Resets json file to default setting.
     *
     * @param string $superSecretCode Security code.
     * @return bool|string True value or exit function with status message.
     */
    public function factoryReset($superSecretCode)
    {
        if (sha1($superSecretCode) == "608dd2678f7c12f8bf1c93df7f36ef0a19bf366d") {
            $json = array('machineCoins' => 1000,
            'products' => array(
                '1A' => array(
                  'name' => 'Nenazrana Kaja',
                  'price' => 200,
                  'count' => 1,
                  ),
                '2C' => array(
                  'name' => 'Mlsny Koky',
                  'price' => 60,
                  'count' => 20,
                  ),
                '3B' => array(
                  'name' => 'Liny Jenda',
                  'price' => 20,
                  'count' => 20,
                  ),
                )
            );
            file_put_contents("stats.json", json_encode($json, JSON_PRETTY_PRINT));
            return true;
        } else {
            exit("Wrong factory reset code!");
        }
    }

    /**
     * Returns current coins in the machine.
     *
     * @return int 
     */
    public function getMachineCoins()
    {
        $stats = self::getStats();
        return $stats['machineCoins'];
    }

    /**
     * Returns pickup slot contents.
     *
     * @return string Name of the contents.
     */
    public function getPickupSlot()
    {
        return $this->pickupSlot;
    }

    /**
     * Returns calculated coins surplus.
     *
     * @return int Coins surplus.
     */
    public function getReturnCoinsSlot()
    {
        return $this->returnCoinsSlot;
    }

    /**
     * Returns product name selected by product code.
     *
     * @param string $productCode Unique product code.
     * @return string Name of the product.
     */
    private function getProductName($productCode)
    {
        $stats = self::getStats();
        return $stats['products'][$productCode]['name'];
    }

    /**
     * Returns product price selected by product code.
     *
     * @param string $productCode Unique product code.
     * @return int Price of the product.
     */
    private function getProductPrice($productCode)
    {
        $stats = self::getStats();
        return $stats['products'][$productCode]['price'];
    }

    /**
     * Returns product stock selected by product code.
     *
     * @param string $productCode Unique product code.
     * @return int Stock of the product.
     */
    private function getProductCount($productCode)
    {
        $stats = self::getStats();
        return $stats['products'][$productCode]['count'];
    }

    /**
     * Returns associative array cointaining statistics loaded from json file.
     *
     * @param string $file
     * @return array
     */
    public function getStats($file = "stats.json")
    {
        return json_decode(file_get_contents($file), true);
    }

    /**
     * Saves all the changes made by buying a product such as coins exchange
     * and product stock.
     *
     * @param string $productCode
     * @param int $productCount
     * @param int $machineCoins
     * @return bool
     */
    private function saveChanges($productCode, $productCount, $machineCoins)
    {
        $stats = self::getStats();
        $stats['products'][$productCode]['count'] = $productCount;
        $stats['machineCoins'] = $machineCoins;
        file_put_contents("stats.json", json_encode($stats, JSON_PRETTY_PRINT));
        return true;
    }
}
