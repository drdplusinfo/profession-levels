<?php
namespace DrdPlus\Person\ProfessionLevels\EnumTypes;

use Doctrineum\DateTimeImmutable\DateTimeImmutableType;
use DrdPlus\Professions\EnumTypes\ProfessionType;
use DrdPlus\Properties\EnumTypes\PropertiesEnumRegistrar;

class ProfessionLevelsEnumsRegistrar
{
    public static function registerAll()
    {
        LevelRankType::registerSelf();
        ProfessionType::registerSelf();
        PropertiesEnumRegistrar::registerAll();
        DateTimeImmutableType::registerSelf();
    }
}
