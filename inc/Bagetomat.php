<?php
class Bagetomat
{
    private $machineCoins;
    private $returnCoinsSlot;
    private $pickupSlot;
    private $productCount;

    public function __construct()
    {
        $this->returnCoinsSlot = null;
        $this->pickupSlot = null;
    }

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
        if ($insertedCoins >= $productPrice && $productCount > 0 && $machineCoins >= $returnCoins) {
            $productCount--;
            $machineCoins -= $returnCoins;
            $this->pickupSlot = $productName;
            $this->returnCoinsSlot = $returnCoins;
            self::saveChanges($productCode, $productCount, $machineCoins);
            return true;
        } else {
            return false;
        }
    }


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

    public function getMachineCoins()
    {
        $stats = self::getStats();
        return $stats['machineCoins'];
    }

    private function getProductName($productCode)
    {
        $stats = self::getStats();
        return $stats['products'][$productCode]['name'];
    }

    private function getProductPrice($productCode)
    {
        $stats = self::getStats();
        return $stats['products'][$productCode]['price'];
    }

    private function getProductCount($productCode)
    {
        $stats = self::getStats();
        return $stats['products'][$productCode]['count'];
    }

    private function getStats($file = "stats.json")
    {
        return json_decode(file_get_contents($file), true);
    }

    private function saveChanges($productCode, $productCount, $machineCoins)
    {
        $stats = self::getStats();
        $stats['products'][$productCode]['count'] = $productCount;
        $stats['machineCoins'] = $machineCoins;
        var_dump($stats);
        file_put_contents("stats.json", json_encode($stats, JSON_PRETTY_PRINT));
    }
}
