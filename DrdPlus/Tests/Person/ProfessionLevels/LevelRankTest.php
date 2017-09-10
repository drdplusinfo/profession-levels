<?php
namespace DrdPlus\Tests\Person\ProfessionLevels;

use DrdPlus\Person\ProfessionLevels\LevelRank;
use Granam\Tests\Tools\TestWithMockery;

class LevelRankTest extends TestWithMockery
{
    /**
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
    public function I_can_create_it_from_to_string_object()
    {
        /** @noinspection PhpParamsInspection */
        $levelRank = LevelRank::getIt($someToStringObject = new SomeToStringObject($value = 12));
        self::assertSame($value, $levelRank->getValue());
        self::assertSame((string)$someToStringObject, (string)$levelRank);
    }

    /**
     * @test
     */
    public function I_can_easily_find_out_if_is_first_or_next_level()
    {
        $zeroLevelRank = LevelRank::getIt(0);
        self::assertTrue($zeroLevelRank->isZeroLevel());
        self::assertFalse($zeroLevelRank->isFirstLevel());
        self::assertFalse($zeroLevelRank->isNextLevel());

        $firstLevelRank = LevelRank::getIt(1);
        self::assertFalse($firstLevelRank->isZeroLevel());
        self::assertTrue($firstLevelRank->isFirstLevel());
        self::assertFalse($firstLevelRank->isNextLevel());

        $nextLevelRank = LevelRank::getIt(123);
        self::assertFalse($firstLevelRank->isZeroLevel());
        self::assertFalse($nextLevelRank->isFirstLevel());
        self::assertTrue($nextLevelRank->isNextLevel());
    }

    /**
     * @param int $prohibitedValue
     * @test
     * @dataProvider provideProhibitedLevelValue
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidLevelRank
     */
    public function I_can_not_create_negative_level($prohibitedValue)
    {
        LevelRank::getIt($prohibitedValue);
    }

    public function provideProhibitedLevelValue()
    {
        return [[-1], [-12345]];
    }
}

/** @inner */
class SomeToStringObject
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function __toString()
    {
        return (string)$this->value;
    }
}