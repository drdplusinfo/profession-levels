<?php
namespace DrdPlus\Person\ProfessionLevels\EnumTypes;

use DrdPlus\Professions\EnumTypes\ProfessionType;
use DrdPlus\Properties\EnumTypes\PropertiesEnumRegistrar;

class ProfessionLevelsRegistrar
{
    public static function registerAll()
    {
        LevelRankType::registerSelf();
        ProfessionType::registerSelf();
        PropertiesEnumRegistrar::registerAll();
    }
}
