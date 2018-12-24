<?php
/**
 * Created by PhpStorm.
 * User: evandro
 * Date: 24/12/18
 * Time: 12:36
 */

namespace Utils;

use Entities\Verification;
use Doctrine\ORM\EntityManager;

class Stats
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save($dna, $result)
    {
        $entityManager = $this->entityManager;

        $verification = $entityManager->getRepository('Entities\Verification')->findOneBy(array('hash' => md5($dna)));
        if (!$verification) {
            $verification = new \Entities\Verification();
            $verification->setIsMutant($result);
            $verification->setDNA($dna);
            $verification->setHash(md5($dna));
            $verification->setCreatedAt(new \DateTime("now"));
            $verification->setTries(1);
        } else {
            $verification->setTries($verification->getTries() + 1);
        }

        $verification->setUpdatedAt(new \DateTime("now"));

        $entityManager->persist($verification);
        $entityManager->persist($verification);
        $entityManager->flush();
    }

    public function getTotal($isMutant)
    {
        $dql = "SELECT SUM(v.tries) FROM Entities\Verification v WHERE v.is_mutant = :isMutant";

        return (int) $this->entityManager->createQuery($dql)
            ->setParameter('isMutant', $isMutant)
            ->getOneOrNullResult()[1];
    }
}