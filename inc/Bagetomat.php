<?php
class Bagetomat
{
    private $machineCoins;
    private $returnCoinsSlot;
    private $pickupSlot;

    public function __construct()
    {
        $this->machineCoins = self::getMachineCoins();
        $this->returnCoinsSlot = null;
        $this->pickupSlot = null;
        
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
            
        } else {
            exit("Wrong factory reset code!");
        }
    }

    public function getMachineCoins($file = "stats.json")
    {
        $stats = json_decode(file_get_contents($file), true);
        return $stats['machineCoins'];
    }
}
