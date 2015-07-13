<?php namespace Wisemood\Num2Text;

class Num2Text
{

    // Ana sınıflar arttırılabilir ancak PHP standart olarak 14 haneden sonra +E notasyonuna geçiyor.
    public $mainLevels = ["", "Bin ", "Milyon ", "Milyar ", "Trilyon "];
    public $subLevels = [
        1 => ["", "Bir ", "İki ", "Üç ", "Dört ", "Beş ", "Altı ", "Yedi ", "Sekiz ", "Dokuz "],
        2 => ["", "On ", "Yirmi ", "Otuz ", "Kırk ", "Elli ", "Altmış ", "Yetmiş ", "Seksen ", "Doksan "],
        3 => ["", "Yüz ", "İki Yüz ", "Üç Yüz ", "Dört Yüz ", "Beş Yüz ", "Altı Yüz ", "Yedi Yüz ", "Sekiz Yüz ", "Dokuz Yüz "]
    ];

    public function cevir($value, $lira = "Lira", $kurus = "Kuruş")
    {
        // php.ini dosyasından rakamı ne kadar büyük yazabileceğimizi öğrenelim.
        $precision   = ini_get("precision");
        $maxPossible = pow(10, $precision) - 1;

        /*
         * Eğer sayı E notasyonuna geçmeden döndürebileceğimiz rakamdan büyükse hiç ısrar etmeden
         * bir exception ile işletimi sonlandıralım.
         */
        if ($value > $maxPossible) {
            throw new \Exception("Sayı çok büyük!");
        }

        $rawValue = round($value, 2);

        $cardinalValue = floor($rawValue);
        $cardinalText  = $this->toText($cardinalValue);

        $decimalValue = round(($rawValue - $cardinalValue) * 100);
        $decimalText  = "";
        if (!empty($decimalValue)) {
            $decimalText = " ve " . $this->toText($decimalValue) . $kurus;
        }

        return trim($cardinalText . $lira . $decimalText . ".");
    }

    private function toText($value)
    {
        $len = strlen($value);

        $values    = [];
        $mainLevel = 0;
        $subLevel  = 0;
        for ($i = $len - 1; $i > -1; $i--) {
            $subLevel++;
            if ($subLevel > 3) {
                $subLevel = 1;

                $mainLevel++;
                $values[] = $this->mainLevels[$mainLevel];
            }

            $number   = substr($value, $i, 1);
            $values[] = $this->subLevels[$subLevel][$number];
        }

        return implode(array_reverse($values));
    }
}
