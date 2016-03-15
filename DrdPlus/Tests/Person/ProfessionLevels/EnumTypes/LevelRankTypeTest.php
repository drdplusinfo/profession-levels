<?php
namespace DrdPlus\Tests\ProfessionLevels\EnumTypes;

use Doctrine\DBAL\Types\Type;
use DrdPlus\Person\ProfessionLevels\EnumTypes\LevelRankType;
use Granam\Tests\Tools\TestWithMockery;

class LevelRankTypeTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_get_type_name()
    {
        self::assertSame('level_rank', LevelRankType::LEVEL_RANK);
        self::assertSame('level_rank', LevelRankType::getTypeName());
    }

    /**
     * @test
     */
    public function I_can_registered_it()
    {
        LevelRankType::registerSelf();
        self::assertTrue(Type::hasType(LevelRankType::getTypeName()));
    }
}
