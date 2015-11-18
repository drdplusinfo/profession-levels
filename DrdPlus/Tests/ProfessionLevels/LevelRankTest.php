<?php
namespace DrdPlus\Tests\ProfessionLevels;

use Doctrineum\Scalar\EnumInterface;
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
        $instance = LevelRank::getEnum($value = 12345);
        $this->assertInstanceOf(LevelRank::class, $instance);
        $this->assertSame($value, $instance->getValue());
        $this->assertInstanceOf(EnumInterface::class, $instance);

        $sameInstance = LevelRank::getIt($value);
        $this->assertSame($instance, $sameInstance);

        $differentInstance = LevelRank::getIt($value + 1);
        $this->assertInstanceOf(LevelRank::class, $instance);
        $this->assertNotSame($instance, $differentInstance);

        return $instance;
    }

    /**
     * @test
     */
    public function I_can_get_its_value()
    {
        $levelRank = LevelRank::getEnum($value = 12345);
        $this->assertSame($value, $levelRank->getEnumValue());
        $this->assertSame($value, $levelRank->getValue());
        $this->assertSame("$value", "$levelRank");
    }
}
