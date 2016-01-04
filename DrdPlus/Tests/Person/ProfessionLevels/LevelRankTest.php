<?php
namespace DrdPlus\Tests\Person\ProfessionLevels;

use DrdPlus\Person\ProfessionLevels\LevelRank;
use DrdPlus\Tests\Tools\TestWithMockery;

class LevelRankTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_get_its_name_from_as_constant()
    {
        $this->assertSame('level_rank', LevelRank::LEVEL_RANK);
    }

    /**
     * @return LevelRank
     *
     * @test
     */
    public function I_can_create_it()
    {
        $instance = LevelRank::getIt($value = 12345);
        $this->assertInstanceOf(LevelRank::class, $instance);
    }

    /**
     * @test
     */
    public function I_can_get_its_value()
    {
        $levelRank = LevelRank::getIt($value = 12345);
        $this->assertSame($value, $levelRank->getValue());
        $this->assertSame("$value", "$levelRank");
    }

    /**
     * @test
     */
    public function I_can_easily_find_out_if_is_first_or_next_level()
    {
        $firstLevelRank = LevelRank::getIt(1);
        $this->assertTrue($firstLevelRank->isFirstLevel());
        $this->assertFalse($firstLevelRank->isNextLevel());

        $nextLevelRank = LevelRank::getIt(123);
        $this->assertFalse($nextLevelRank->isFirstLevel());
        $this->assertTrue($nextLevelRank->isNextLevel());
    }

    /**
     * @param int $prohibitedValue
     * @test
     * @dataProvider provideProhibitedLevelValue
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\MinimumLevelExceeded
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
