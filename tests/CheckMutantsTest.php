<?php

final class CheckMutantsTest extends \Silex\WebTestCase
{
    public function createApplication()
    {
        require __DIR__ . '/../web/index.php';

        return $app;
    }

    public function testBeginningSameWord()
    {
        $checkMutants = new \Utils\CheckMutants();

        $this->assertTrue($checkMutants->isMutant(['AAQWER', 'QWERTY']));
    }

    public function testMiddleSameWord()
    {
        $checkMutants = new \Utils\CheckMutants();

        $this->assertTrue($checkMutants->isMutant(['QWAAER', 'QWERTY']));
    }

    public function testEndSameWord()
    {
        $checkMutants = new \Utils\CheckMutants();

        $this->assertTrue($checkMutants->isMutant(['QWERAA', 'QWERAA']));
    }

    public function testBeginningOtherWord()
    {
        $checkMutants = new \Utils\CheckMutants();

        $this->assertTrue($checkMutants->isMutant(['ABQWER', 'ABREWQ']));
    }

    public function testMiddleOtherWord()
    {
        $checkMutants = new \Utils\CheckMutants();

        $this->assertTrue($checkMutants->isMutant(['QWABER', 'REABWQ']));
    }

    public function testEndOtherWord()
    {
        $checkMutants = new \Utils\CheckMutants();

        $this->assertTrue($checkMutants->isMutant(['QWABER', 'EABWQR']));
    }

    public function testLackLetter()
    {
        $checkMutants = new \Utils\CheckMutants();

        $this->assertTrue($checkMutants->isMutant(['QWABER', 'REABW']));
    }

    public function testNotMutant()
    {
        $checkMutants = new \Utils\CheckMutants();

        $this->assertFalse($checkMutants->isMutant(['QWERTY', 'YTREWQ']));
    }

    public function testWebserviceIsMutant() {

        $client = $this->createClient();
        $client->request(
            'POST',
            '/mutant',
            array(
                "dna" => '["ATGCGA","CAGTGC","TTATGT","AGAAGG","CCCCTA","TCACTG"]'
            ),
            array(),
            array()
        );

        $this->assertTrue($client->getResponse()->isOk());
    }

    public function testWebserviceNotIsMutant() {

        $client = $this->createClient();
        $client->request(
            'POST',
            '/mutant',
            array(
                "dna" => '["ATGCGA","CACTCG","TGACAT","ATCTCG","CATGAT","TCACTG"]'
            ),
            array(),
            array()
        );

        $this->assertTrue($client->getResponse()->isForbidden());
    }

    public function testCountIncrementMutant() {
        $stats  = new \Utils\Stats($this->app['orm.em']);

        $before = $stats->getTotal(true);

        $client = $this->createClient();
        $client->request(
            'POST',
            '/mutant',
            array(
                "dna" => '["ATGCGA","CAGTGC","TTATGT","AGAAGG","CCCCTA","TCACTG"]'
            ),
            array(),
            array()
        );

        $this->assertSame($stats->getTotal(true), $before + 1);
    }

    public function testCountIncrementIsNotMutant() {
        $stats  = new \Utils\Stats($this->app['orm.em']);

        $before = $stats->getTotal(false);

        $client = $this->createClient();
        $client->request(
            'POST',
            '/mutant',
            array(
                "dna" => '["ATGCGA","CACTCG","TGACAT","ATCTCG","CATGAT","TCACTG"]'
            ),
            array(),
            array()
        );

        $this->assertSame($stats->getTotal(false), $before + 1);
    }
}