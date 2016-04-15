<?php
namespace DrdPlus\Person\ProfessionLevels\EnumTypes;

use Doctrine\DBAL\Types\Type;
use Doctrineum\DateTimeImmutable\DateTimeImmutableType;
use DrdPlus\Professions\EnumTypes\ProfessionType;
use DrdPlus\Properties\Base\EnumTypes\AgilityType;
use DrdPlus\Properties\Base\EnumTypes\CharismaType;
use DrdPlus\Properties\Base\EnumTypes\IntelligenceType;
use DrdPlus\Properties\Base\EnumTypes\KnackType;
use DrdPlus\Properties\Base\EnumTypes\StrengthType;
use DrdPlus\Properties\Base\EnumTypes\WillType;

class ProfessionLevelsEnumsRegistrarTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function I_can_register_all_needed_enums_at_once()
    {
        ProfessionLevelsEnumRegistrar::registerAll();

        self::assertTrue(Type::hasType(LevelRankType::getTypeName()));
        self::assertTrue(Type::hasType(LevelRankType::getTypeName()));
        self::assertTrue(Type::hasType(ProfessionType::getTypeName()));
        self::assertTrue(Type::hasType(StrengthType::getTypeName()));
        self::assertTrue(Type::hasType(AgilityType::getTypeName()));
        self::assertTrue(Type::hasType(KnackType::getTypeName()));
        self::assertTrue(Type::hasType(WillType::getTypeName()));
        self::assertTrue(Type::hasType(IntelligenceType::getTypeName()));
        self::assertTrue(Type::hasType(CharismaType::getTypeName()));
        self::assertTrue(Type::hasType(DateTimeImmutableType::getTypeName()));
    }
}
