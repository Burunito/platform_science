<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Hungarian;

class TestController extends Controller
{

    private $fileDestinations;
    private $fileDrivers;
    
    public function __construct(string $fileDestinations, string $fileDrivers)
    { 
        $this->fileDestinations = $fileDestinations;
        $this->fileDrivers = $fileDrivers;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function test()
    {
        //Reading files
        $fileDestinations = file_get_contents($this->fileDestinations);
        $fileDrivers = file_get_contents($this->fileDrivers);
        if (!$fileDestinations || !$fileDrivers) {
            return 'Cant find file :'. !$fileDestinations ? $this->fileDestinations : $this->fileDestinations;
        }

        //Get data as array
        $drivers =  $this->getFileData($fileDrivers);
        $destinations = $this->getFileData($fileDestinations);

        //Get matrix combinations. Fill up an array with all posible scores
        $matrixCombinations = $this->getMatrixCombinations($drivers, $destinations);

        //Get the best combinations with lowest scores
        $bestCombinations = $this->getBestCombinations($matrixCombinations);

        //Build the result text for user
        $textResult = $this->getResultText($drivers, $destinations, $bestCombinations['positions'], $matrixCombinations);

        return ["total" => $bestCombinations['total'], "result" => $textResult];
    }

    private function getFileData($fileData)
    {
        $records = explode("\n", $fileData);
        return $records;
    }

    private function getMatrixCombinations(array $drivers, array $destinations)
    {
        $matrixCombinations = [];

        foreach ($drivers as $driverIndex => $driver) {
            foreach ($destinations as $destinationIndex => $destination) {
                $matrixCombinations[$driverIndex][$destinationIndex] = (int)($this->getSuitabilityScore($driver, $destination)*10);
            }
        }

        return $matrixCombinations;
    }

    private function getBestCombinations(array $matrixCombinations)
    {
        $hungarian = new Hungarian($matrixCombinations);
        $result = $hungarian->solve();
        $positions = [];
        $minSS = 0;
        foreach($result as $posX => $posY){
            $positions[] = ["X" => $posX, "Y" => $posY];
            $minSS += $matrixCombinations[$posX][$posY];
        }
        $minSS /= 10;
        return ["positions" => $positions, "total" => $minSS];
    }

    private function getSuitabilityScore(string $driver, string $destination)
    {
        $score = strlen($destination) % 2 == 0 ? ($this->countVowels($driver) * 1.5) : $this->countConsonants($driver);

        if ($this->getCommonFactors(strlen($driver), strlen($destination)) !== 1) {
            $score *= 1.5;
        }

        return $score;
    }

    private function getResultText(array $drivers, array $destinations, array $positions, array $matrix)
    {
        $combinations = [];
        foreach ($positions as $position) {
            $combination = new \stdClass();
            $combination->destination = $destinations[$position["X"]];
            $combination->driver = $drivers[$position["Y"]];
            $combination->score = $matrix[$position["X"]][$position["Y"]] / 10;
            $combinations[] = $combination;
        }
        return $combinations;
    }

    private function countVowels(string $string)
    {
        return preg_match_all('/[aeiou]/i',$string);
    }

    private function countConsonants(string $string)
    {
        return preg_match_all('/[bcdfghjklmnpqrstvwxyz]/i',$string);
    }

    private function getCommonFactors($numberA, $numberB) {
        return ($numberA % $numberB) ? $this->getCommonFactors($numberB, $numberA % $numberB) : $numberB;
    }
}
