<?php
namespace DrdPlus\Person\ProfessionLevels\EnumTypes;

use DrdPlus\Professions\EnumTypes\ProfessionType;
use DrdPlus\Properties\EnumTypes\PropertiesRegistrar;

class ProfessionLevelsRegistrar
{
    public static function registerAll()
    {
        LevelRankType::registerSelf();
        ProfessionType::registerSelf();
        PropertiesRegistrar::registerAll();
    }
}