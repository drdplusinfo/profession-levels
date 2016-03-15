<?php
namespace DrdPlus\Tests\Person\ProfessionLevels;

use DrdPlus\Person\ProfessionLevels\LevelRank;
use Granam\Tests\Tools\TestWithMockery;

class LevelRankTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_get_its_name_from_as_constant()
    {
        self::assertSame('level_rank', LevelRank::LEVEL_RANK);
    }

    /**
     * @return LevelRank
     *
     * @test
     */
    public function I_can_create_it()
    {
        $instance = LevelRank::getIt($value = 12345);
        self::assertInstanceOf(LevelRank::class, $instance);
    }

    /**
     * @test
     */
    public function I_can_get_its_value()
    {
        $levelRank = LevelRank::getIt($value = 12345);
        self::assertSame($value, $levelRank->getValue());
        self::assertSame("$value", "$levelRank");
    }

    /**
     * @test
     */
    public function I_can_easily_find_out_if_is_first_or_next_level()
    {
        $firstLevelRank = LevelRank::getIt(1);
        self::assertTrue($firstLevelRank->isFirstLevel());
        self::assertFalse($firstLevelRank->isNextLevel());

        $nextLevelRank = LevelRank::getIt(123);
        self::assertFalse($nextLevelRank->isFirstLevel());
        self::assertTrue($nextLevelRank->isNextLevel());
    }

    /**
     * @param int $prohibitedValue
     * @test
     * @dataProvider provideProhibitedLevelValue
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidFirstLevelRank
     */
    public function I_can_not_create_zero_or_lesser_level($prohibitedValue)
    {
        LevelRank::getIt($prohibitedValue);
    }

    public function provideProhibitedLevelValue()
    {
        return [[0], [-1], [-12345]];
    }
}
