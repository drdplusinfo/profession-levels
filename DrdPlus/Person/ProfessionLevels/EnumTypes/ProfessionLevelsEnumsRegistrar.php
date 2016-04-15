<?php
namespace DrdPlus\Person\ProfessionLevels\EnumTypes;

use Doctrineum\DateTimeImmutable\DateTimeImmutableType;
use DrdPlus\Professions\EnumTypes\ProfessionsEnumsRegistrar;
use DrdPlus\Properties\EnumTypes\PropertiesEnumRegistrar;

class ProfessionLevelsEnumsRegistrar
{
    public static function registerAll()
    {
        LevelRankType::registerSelf();
        ProfessionsEnumsRegistrar::registerAll();
        PropertiesEnumRegistrar::registerAll();
        DateTimeImmutableType::registerSelf();
    }
}
