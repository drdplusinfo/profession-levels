<?php
namespace DrdPlus\Person\ProfessionLevels\EnumTypes;

use DrdPlus\Professions\EnumTypes\ProfessionType;
use DrdPlus\Properties\Base\EnumTypes\AgilityType;
use DrdPlus\Properties\Base\EnumTypes\CharismaType;
use DrdPlus\Properties\Base\EnumTypes\IntelligenceType;
use DrdPlus\Properties\Base\EnumTypes\KnackType;
use DrdPlus\Properties\Base\EnumTypes\StrengthType;
use DrdPlus\Properties\Base\EnumTypes\WillType;

class ProfessionLevelsRegistrarTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function I_can_register_all_needed_enums_at_once()
    {
        ProfessionLevelsRegistrar::registerAll();

        self::assertTrue(LevelRankType::isRegistered());
        self::assertTrue(ProfessionType::isRegistered());
        self::assertTrue(StrengthType::isRegistered());
        self::assertTrue(AgilityType::isRegistered());
        self::assertTrue(KnackType::isRegistered());
        self::assertTrue(WillType::isRegistered());
        self::assertTrue(IntelligenceType::isRegistered());
        self::assertTrue(CharismaType::isRegistered());
    }
}
