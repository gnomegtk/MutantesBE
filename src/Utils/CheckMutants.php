<?php
/**
 * Created by PhpStorm.
 * User: evandro
 * Date: 21/12/18
 * Time: 16:21
 */

namespace Utils;

class CheckMutants
{
    public function isMutant($dna)
    {
        $qtyLetters = strlen($dna[0]);
        $qtyWords   = count($dna);

        // Iterating over each letter
        for ($iLetter = 0; $iLetter < $qtyLetters; $iLetter++) {

            // Iterating over each word except the last
            for ($iWord = 0; $iWord < $qtyWords - 1; $iWord++) {

                // Return true if there is lacking letter
                if (!$iLetter && (strlen($dna[$iWord]) != strlen($dna[$iWord + 1]))) {
                    return true;
                }

                // Repetition on the same word, except the last letter
                if (($iLetter != $qtyLetters - 1) && ($dna[$iWord][$iLetter] == $dna[$iWord][$iLetter + 1])) {
                    return true;
                }

                // Repetition on the next word
                if ($dna[$iWord][$iLetter] == $dna[$iWord + 1][$iLetter]) {
                    return true;
                }
            }
        }

        return false;
    }
}