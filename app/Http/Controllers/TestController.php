<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Hungarian;

class TestController extends Controller
{

    private $fileDestinations;
    private $fileDrivers;
    
    /**
     * Construct to initialize object and files
     *
     * @return void
     */
    public function __construct(string $fileDestinations, string $fileDrivers)
    { 
        $this->fileDestinations = $fileDestinations;
        $this->fileDrivers = $fileDrivers;
    }

    /**
     * Main function
     *
     * @return Array    Array with the total and the best combination human readable
     */
    public function test():array
    {
        //Reading files
        $fileDestinations = file_get_contents($this->fileDestinations);
        $fileDrivers = file_get_contents($this->fileDrivers);

        if (!$fileDestinations || !$fileDrivers) {
            //Error if file not founds
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

    /**
     * Explode the file input on array
     *
     * @filedata string
     * 
     * @return array    Array with all records of the file
     */
    private function getFileData($fileData):array
    {
        $records = explode("\n", $fileData);
        return $records;
    }

    /**
     * Get the matrix with all cost combinations
     *
     * @drivers array   records of the drivers
     * @destinations array  records of the destinations
     * 
     * @return array    Array with all records of the file
     */
    private function getMatrixCombinations(array $drivers, array $destinations):array
    {
        $matrixCombinations = [];

        foreach ($drivers as $driverIndex => $driver) {
            foreach ($destinations as $destinationIndex => $destination) {
                $matrixCombinations[$driverIndex][$destinationIndex] = (int)($this->getSuitabilityScore($driver, $destination)*10);
            }
        }

        return $matrixCombinations;
    }

    /**
     * Get the best combination with the hungarian algoryth and it also returns the total of min SS
     *
     * @matrixCombinations array   Matrix with all cost combinations
     * 
     * @return array    Array with the best combination and the total of min SS
     */
    private function getBestCombinations(array $matrixCombinations):array
    {
        $positions = [];
        $minSS = 0;
        //Call to our helper
        $hungarian = new Hungarian($matrixCombinations);
        $result = $hungarian->solve();
        //Change the format of returned array and get the min SS
        foreach ($result as $posX => $posY) {
            $positions[] = ["X" => $posX, "Y" => $posY];
            $minSS += $matrixCombinations[$posX][$posY];
        }
        $minSS /= 10;
        return ["positions" => $positions, "total" => $minSS];
    }

    /**
     * Calculate the suitability score based on practice statement
     *
     * @driver string        The driver's name
     * @destination string   The destination string
     * 
     * @return float    The SS score
     */
    private function getSuitabilityScore(string $driver, string $destination):float
    {
        $score = strlen($destination) % 2 == 0 ? ($this->countVowels($driver) * 1.5) : $this->countConsonants($driver);

        if ($this->getCommonFactors(strlen($driver), strlen($destination)) !== 1) {
            $score *= 1.5;
        }

        return $score;
    }

    /**
     * Get the result on readable format
     *
     * @drivers array        Drivers array to get names
     * @destinations array   Destinations array to get destination
     * @positions array   The best combination possible [(x,y)]
     * @matrix array   All the matrix to get individualy cost of combinations
     * 
     * @return array    Return readable combinations
     */
    private function getResultText(array $drivers, array $destinations, array $positions, array $matrix):array
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

    /**
     * Count how many vowels has a string
     *
     * @string string    String to count vowels
     * 
     * @return integer    Return number of vowels
     */
    private function countVowels(string $string):int
    {
        return preg_match_all('/[aeiou]/i',$string);
    }

    /**
     * Count how many consonants has a string
     *
     * @string string    String to count consonants
     * 
     * @return integer    Return number of consonants
     */
    private function countConsonants(string $string):int
    {
        return preg_match_all('/[bcdfghjklmnpqrstvwxyz]/i',$string);
    }

    /**
     * Finds the common factor betweens two numbers
     *
     * @numberA integer   Number a 
     * @numberB integer   Number b
     * 
     * @return integer  If returns 1 Just 1 is the common factor
     */
    private function getCommonFactors($numberA, $numberB):int
    {
        return ($numberA % $numberB) ? $this->getCommonFactors($numberB, $numberA % $numberB) : $numberB;
    }
}
