<?php
namespace DrdPlus\Tests\Person\ProfessionLevels;

use Doctrineum\Tests\Entity\AbstractDoctrineEntitiesTest;
use DrdPlus\Codes\ProfessionCodes;
use DrdPlus\Person\ProfessionLevels\EnumTypes\ProfessionLevelsRegistrar;
use DrdPlus\Person\ProfessionLevels\LevelRank;
use DrdPlus\Person\ProfessionLevels\ProfessionFirstLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionLevels;
use DrdPlus\Person\ProfessionLevels\ProfessionNextLevel;
use DrdPlus\Professions\Profession;
use DrdPlus\Properties\Base\Agility;
use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Intelligence;
use DrdPlus\Properties\Base\Knack;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Properties\Base\Will;

class EntitiesTest extends AbstractDoctrineEntitiesTest
{
    protected function setUp()
    {
        ProfessionLevelsRegistrar::registerAll();

        parent::setUp();
    }

    protected function getDirsWithEntities()
    {
        $reflection = new \ReflectionClass(ProfessionLevel::class);

        return dirname($reflection->getFileName());
    }

    protected function getExpectedEntityClasses()
    {
        return [
            ProfessionFirstLevel::class,
            ProfessionNextLevel::class,
            ProfessionLevels::class
        ];
    }

    protected function createEntitiesToPersist()
    {
        return [
            $firstLevel = ProfessionFirstLevel::createFirstLevel(
                $profession = Profession::getItByCode(ProfessionCodes::FIGHTER)
            ),
            ProfessionNextLevel::createNextLevel(
                $profession,
                LevelRank::getIt(2),
                Strength::getIt(1),
                Agility::getIt(1),
                Knack::getIt(0),
                Will::getIt(0),
                Intelligence::getIt(0),
                Charisma::getIt(0)
            ),
            new ProfessionLevels($firstLevel)
        ];
    }

}