<?php
declare(strict_types = 1);

namespace DrdPlus\Person\ProfessionLevels\EnumTypes;

use DrdPlus\Professions\EnumTypes\ProfessionsEnumRegistrar;
use DrdPlus\Properties\EnumTypes\PropertiesEnumRegistrar;

class ProfessionLevelsEnumRegistrar
{
    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function registerAll(): void
    {
        LevelRankType::registerSelf();
        ProfessionsEnumRegistrar::registerAll();
        PropertiesEnumRegistrar::registerAll();
    }
}