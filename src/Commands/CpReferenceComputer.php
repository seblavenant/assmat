<?php

namespace Assmat\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Assmat\DataSource\Constants;
use Symfony\Component\Console\Input\InputOption;
use Assmat\DataSource\Repositories;
use Assmat\DataSource\DataTransferObjects as DTO;
use Assmat\Services\Lignes;
use Assmat\Services\Lignes\Computers\CpAnneeReference;
use Assmat\DataSource\Repositories\Mysql\Mysql;

class CpReferenceComputer extends Command
{
    const
        NAME= 'cp:reference:computer';

    private
        $db,
        $anneeReference,
        $cpReferenceRepository;

    public function __construct(Mysql $db, Repositories\CpReference $cpReferenceRepository)
    {
        $this->db = $db;
        $this->anneeReference = (new CpAnneeReference())->compute();
        $this->cpReferenceRepository = $cpReferenceRepository;

        parent::__construct(self::NAME);
    }

    protected function configure()
    {
        $this
            ->setName(self::NAME)
            ->setDescription('Enregistre les données de référence pour le calcul des CP')
            ->addOption('anneeReference', null, InputOption::VALUE_OPTIONAL, 'Année de référence (début)', $this->anneeReference)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $anneeReference = $input->getOption('anneeReference');
        $output->writeln(sprintf('Année de référence = %s', $anneeReference));

        $cpAcquisContrats = $this->loadCpAcquisByContrats($anneeReference);

        $remunerationContrats = $this->loadRemunerationByContrats($anneeReference);
        foreach($remunerationContrats as $remunerationContrat)
        {
            $this->saveCpReference($remunerationContrat, $cpAcquisContrats, $remunerationContrat['contratId'], $anneeReference);
        }
    }

    private function saveCpReference($remunerationContrat, $cpAcquisContrats, $contratId, $anneeReference)
    {
        $tauxJournalier = $this->computeTauxJournalier($remunerationContrat, $cpAcquisContrats);

        $cpReferenceDTO = new DTO\CpReference();
        $cpReferenceDTO->annee = $anneeReference;
        $cpReferenceDTO->contratId = $contratId;
        $cpReferenceDTO->nbJours = $cpAcquisContrats[$contratId];
        $cpReferenceDTO->tauxJournalier = $tauxJournalier;

        $this->cpReferenceRepository->persist($cpReferenceDTO);
    }

    private function computeTauxJournalier($remunerationAnneeReference, $cpAcquis)
    {
        $contratId = (int) $remunerationAnneeReference['contratId'];
        if(! array_key_exists($contratId, $cpAcquis))
        {
            return;
        }

        return $remunerationAnneeReference['total'] * 0.1 / $cpAcquis[$contratId];
    }

    private function loadCpAcquisByContrats($anneeReference)
    {
        $cpAcquisByContratsQuery = sprintf('
            SELECT SUM(valeur) AS total, contrat_id AS contratId
            FROM (
                SELECT valeur, contrat_id
                FROM ligne
                LEFT JOIN bulletin ON(ligne.bulletin_id = bulletin.id)
                WHERE ligne.type_id = %3$s
                AND concat( bulletin.annee, LPAD( bulletin.mois, 2, "0" )) BETWEEN "%1$s06" AND "%2$s05"
            ) AS cpAcquis
            GROUP BY contrat_id
            ',
            $anneeReference,
            $anneeReference + 1,
            Constants\Lignes\Type::CONGES_PAYES_ACQUIS
        );

        $cpAquisByContrats = $this->db->fetchAll($cpAcquisByContratsQuery);

        $cpAcquis = [];
        foreach($cpAquisByContrats as $cpAquisByContrat)
        {
            $cpAcquis[$cpAquisByContrat['contratId']] = $cpAquisByContrat['total'];
        }

        return $cpAcquis;
    }

    private function loadRemunerationByContrats($anneeReference)
    {
        $remunerationByContratsQuery = sprintf('
            SELECT SUM(valeur) AS total, contrat_id AS contratId
            FROM (
                SELECT valeur, contrat_id
                FROM ligne
                LEFT JOIN bulletin ON(ligne.bulletin_id = bulletin.id)
                WHERE ligne.context_id = %3$s
                AND concat( bulletin.annee, LPAD( bulletin.mois, 2, "0" )) BETWEEN "%1$s06" AND "%2$s05"
            ) AS remuneration
            GROUP BY contrat_id
            ',
            $anneeReference,
            $anneeReference + 1,
            Constants\Lignes\Context::REMUNERATION
        );

        return $this->db->fetchall($remunerationByContratsQuery);
    }
}
