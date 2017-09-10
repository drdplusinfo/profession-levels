<?php
namespace DrdPlus\Person\ProfessionLevels\EnumTypes;

use DrdPlus\Professions\EnumTypes\ProfessionsEnumRegistrar;
use DrdPlus\Properties\EnumTypes\PropertiesEnumRegistrar;

class ProfessionLevelsEnumRegistrar
{
    public static function registerAll()
    {
        LevelRankType::registerSelf();
        ProfessionsEnumRegistrar::registerAll();
        PropertiesEnumRegistrar::registerAll();
    }
}