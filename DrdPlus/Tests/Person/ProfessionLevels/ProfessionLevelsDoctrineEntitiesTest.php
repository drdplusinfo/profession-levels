<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Person\ProfessionLevels;

use Doctrineum\Tests\Entity\AbstractDoctrineEntitiesTest;
use DrdPlus\Person\ProfessionLevels\EnumTypes\ProfessionLevelsEnumRegistrar;
use DrdPlus\Person\ProfessionLevels\LevelRank;
use DrdPlus\Person\ProfessionLevels\ProfessionFirstLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionLevels;
use DrdPlus\Person\ProfessionLevels\ProfessionNextLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionZeroLevel;
use DrdPlus\Professions\Commoner;
use DrdPlus\Professions\Fighter;
use DrdPlus\Professions\Theurgist;
use DrdPlus\Professions\Wizard;
use DrdPlus\Properties\Base\Agility;
use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Intelligence;
use DrdPlus\Properties\Base\Knack;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Properties\Base\Will;

class ProfessionLevelsDoctrineEntitiesTest extends AbstractDoctrineEntitiesTest
{
    protected function setUp()
    {
        ProfessionLevelsEnumRegistrar::registerAll();

        parent::setUp();
    }

    protected function getDirsWithEntities()
    {
        $reflection = new \ReflectionClass(ProfessionLevel::class);

        return dirname($reflection->getFileName());
    }

    protected function createEntitiesToPersist(): array
    {
        return self::createEntities();
    }

    /**
     * @return array
     */
    public static function createEntities(): array
    {
        $professionLevels = new ProfessionLevels(
            ProfessionZeroLevel::createZeroLevel(Commoner::getIt()),
            ProfessionFirstLevel::createFirstLevel($profession = Theurgist::getIt())
        );
        $professionLevels->addLevel(
            ProfessionNextLevel::createNextLevel(
                $profession,
                LevelRank::getIt(2),
                Strength::getIt(1),
                Agility::getIt(0),
                Knack::getIt(0),
                Will::getIt(0),
                Intelligence::getIt(1),
                Charisma::getIt(0)
            )
        );

        return [
            ProfessionZeroLevel::createZeroLevel(Commoner::getIt()),
            ProfessionFirstLevel::createFirstLevel(Fighter::getIt()),
            ProfessionNextLevel::createNextLevel(
                Wizard::getIt(),
                LevelRank::getIt(2),
                Strength::getIt(1),
                Agility::getIt(1),
                Knack::getIt(0),
                Will::getIt(0),
                Intelligence::getIt(0),
                Charisma::getIt(0)
            ),
            $professionLevels,
        ];
    }

}