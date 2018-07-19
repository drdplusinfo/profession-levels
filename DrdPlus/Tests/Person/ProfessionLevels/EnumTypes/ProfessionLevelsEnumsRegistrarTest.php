<?php
declare(strict_types = 1);

namespace DrdPlus\Person\ProfessionLevels\EnumTypes;

use Doctrine\DBAL\Types\DateTimeImmutableType;
use Doctrine\DBAL\Types\Type;
use DrdPlus\Professions\EnumTypes\ProfessionType;
use DrdPlus\Properties\Base\EnumTypes\AgilityType;
use DrdPlus\Properties\Base\EnumTypes\CharismaType;
use DrdPlus\Properties\Base\EnumTypes\IntelligenceType;
use DrdPlus\Properties\Base\EnumTypes\KnackType;
use DrdPlus\Properties\Base\EnumTypes\StrengthType;
use DrdPlus\Properties\Base\EnumTypes\WillType;
use PHPUnit\Framework\TestCase;

class ProfessionLevelsEnumsRegistrarTest extends TestCase
{
    /**
     * @test
     */
    public function I_can_register_all_needed_enums_at_once(): void
    {
        ProfessionLevelsEnumRegistrar::registerAll();

        self::assertTrue(Type::hasType(LevelRankType::LEVEL_RANK));
        self::assertTrue(Type::hasType(ProfessionType::PROFESSION));
        self::assertTrue(Type::hasType(StrengthType::STRENGTH));
        self::assertTrue(Type::hasType(AgilityType::AGILITY));
        self::assertTrue(Type::hasType(KnackType::KNACK));
        self::assertTrue(Type::hasType(WillType::WILL));
        self::assertTrue(Type::hasType(IntelligenceType::INTELLIGENCE));
        self::assertTrue(Type::hasType(CharismaType::CHARISMA));
        self::assertTrue(Type::hasType(DateTimeImmutableType::DATETIME_IMMUTABLE));
    }
}