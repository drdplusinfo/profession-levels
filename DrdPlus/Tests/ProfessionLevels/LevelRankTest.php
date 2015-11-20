<?php
namespace DrdPlus\Tests\ProfessionLevels;

use DrdPlus\ProfessionLevels\LevelRank;
use DrdPlus\Tools\Tests\TestWithMockery;

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
    public function I_can_easily_find_out_if_is_first_level()
    {
        $levelRank = LevelRank::getIt(1);
        $this->assertTrue($levelRank->isFirstLevel());
    }
}
