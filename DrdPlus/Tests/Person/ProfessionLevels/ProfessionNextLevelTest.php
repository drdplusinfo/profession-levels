<?php
namespace DrdPlus\Tests\Person\ProfessionLevels;

use DrdPlus\Codes\ProfessionCodes;
use DrdPlus\Person\ProfessionLevels\LevelRank;
use DrdPlus\Person\ProfessionLevels\ProfessionLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionNextLevel;
use DrdPlus\Properties\Base\Agility;
use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Intelligence;
use DrdPlus\Properties\Base\Knack;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Properties\Base\Will;
use DrdPlus\Properties\PropertyInterface;
use Mockery\MockInterface;

class ProfessionNextLevelTest extends AbstractTestOfProfessionLevel
{

    /**
     * @test
     * @dataProvider provideProfessionCode
     * @param string $professionCode
     * @return ProfessionLevel
     */
    public function I_can_create_it($professionCode)
    {
        $professionNextLevel = ProfessionNextLevel::createNextLevel(
            $profession = $this->createProfession($professionCode),
            $levelRank = $this->createLevelRank(2),
            $strengthIncrement = $this->createStrength($professionCode),
            $agilityIncrement = $this->createAgility($professionCode),
            $knackIncrement = $this->createKnack($professionCode),
            $willIncrement = $this->createWill($professionCode),
            $intelligenceIncrement = $this->createIntelligence($professionCode),
            $charismaIncrement = $this->createCharisma($professionCode),
            $levelUpAt = new \DateTimeImmutable()
        );
        $this->assertInstanceOf(ProfessionNextLevel::class, $professionNextLevel);
        /** @var ProfessionLevel $professionNextLevel */
        $this->assertNull($professionNextLevel->getId());
        $this->assertSame($professionCode, $professionNextLevel->getProfession()->getValue());
        $this->assertFalse($professionNextLevel->isFirstLevel());
        $this->assertTrue($professionNextLevel->isNextLevel());
        $this->assertSame($levelRank, $professionNextLevel->getLevelRank());
        foreach ([Strength::STRENGTH, Agility::AGILITY, Knack::KNACK, Will::WILL, Intelligence::INTELLIGENCE, Charisma::CHARISMA] as $propertyCode) {
            $this->assertSame($this->isPrimaryProperty($propertyCode, $professionCode), $professionNextLevel->isPrimaryProperty($propertyCode));
            $this->assertInstanceOf(
                $this->getPropertyClassByCode($propertyCode),
                $propertyIncrement = $professionNextLevel->getBasePropertyIncrement($propertyCode)
            );
            $this->assertSame(
                $this->isPrimaryProperty($propertyCode, $professionCode) ? 1 : 0,
                $propertyIncrement->getValue()
            );
        }
        $this->assertSame($levelUpAt, $professionNextLevel->getLevelUpAt());
    }

    public function provideProfessionCode()
    {
        return [
            [ProfessionCodes::FIGHTER],
            [ProfessionCodes::THIEF],
            [ProfessionCodes::RANGER],
            [ProfessionCodes::WIZARD],
            [ProfessionCodes::THEURGIST],
            [ProfessionCodes::PRIEST]
        ];
    }

    /**
     * @test
     * @dataProvider provideProfessionCode
     * @param string $professionCode
     */
    public function I_can_get_level_details($professionCode)
    {
        /** @var ProfessionLevel $professionNextLevel */
        $professionNextLevel = new ProfessionNextLevel(
            $profession = $this->createProfession($professionCode),
            $levelRank = $this->createLevelRank(2),
            $strengthIncrement = $this->createStrength($professionCode),
            $agilityIncrement = $this->createAgility($professionCode),
            $knackIncrement = $this->createKnack($professionCode),
            $willIncrement = $this->createWill($professionCode),
            $intelligenceIncrement = $this->createIntelligence($professionCode),
            $charismaIncrement = $this->createCharisma($professionCode),
            $levelUpAt = new \DateTimeImmutable()
        );
        $this->assertSame($profession, $professionNextLevel->getProfession());
        $this->assertSame($levelRank, $professionNextLevel->getLevelRank());
        $this->assertSame($strengthIncrement, $professionNextLevel->getStrengthIncrement());
        $this->assertSame($agilityIncrement, $professionNextLevel->getAgilityIncrement());
        $this->assertSame($knackIncrement, $professionNextLevel->getKnackIncrement());
        $this->assertSame($intelligenceIncrement, $professionNextLevel->getIntelligenceIncrement());
        $this->assertSame($charismaIncrement, $professionNextLevel->getCharismaIncrement());
        $this->assertSame($willIncrement, $professionNextLevel->getWillIncrement());
        $this->assertSame($levelUpAt, $professionNextLevel->getLevelUpAt());
    }

    /**
     * @param string $rankValue
     * @return LevelRank
     */
    private function createLevelRank($rankValue)
    {
        /** @var LevelRank|\Mockery\MockInterface $levelRank */
        $levelRank = $this->mockery(LevelRank::class);
        $levelRank->shouldReceive('getValue')
            ->andReturn($rankValue);
        $levelRank->shouldReceive('isFirstLevel')
            ->andReturn($rankValue == 1);
        $levelRank->shouldReceive('isNextLevel')
            ->andReturn($rankValue > 1);

        return $levelRank;
    }

    /**
     * @param string $professionCode
     * @param int|null $propertyValue = null
     * @return Strength
     */
    private function createStrength($professionCode, $propertyValue = null)
    {
        return $this->createProperty($professionCode, Strength::class, Strength::STRENGTH, $propertyValue);
    }

    /**
     * @param string $professionCode
     * @param string $propertyClass
     * @param string $propertyCode
     * @param string|null $propertyValue = null
     * @return MockInterface|PropertyInterface
     */
    private function createProperty($professionCode, $propertyClass, $propertyCode, $propertyValue = null)
    {
        $property = $this->mockery($propertyClass);
        $this->addPropertyExpectation($professionCode, $property, $propertyCode, $propertyValue);

        return $property;
    }

    private function addPropertyExpectation(
        $professionCode,
        MockInterface $property,
        $propertyName,
        $propertyValue = null
    )
    {
        $property->shouldReceive('getValue')
            ->andReturn((is_null($propertyValue)
                ? ($this->isPrimaryProperty($propertyName, $professionCode)
                    ? 1
                    : 0
                )
                : $propertyValue
            ));
        $property->shouldReceive('getCode')
            ->andReturn($propertyName);
    }

    /**
     * @param string $professionCode
     * @param int|null $value
     * @return Agility
     */
    private function createAgility($professionCode, $value = null)
    {
        return $this->createProperty($professionCode, Agility::class, Agility::AGILITY, $value);
    }

    /**
     * @param string $professionCode
     * @param int|null $value
     * @return Knack
     */
    private function createKnack($professionCode, $value = null)
    {
        return $this->createProperty($professionCode, Knack::class, Knack::KNACK, $value);
    }

    /**
     * @param string $professionCode
     * @param int|null $value
     * @return Will
     */
    private function createWill($professionCode, $value = null)
    {
        return $this->createProperty($professionCode, Will::class, Will::WILL, $value);
    }

    /**
     * @param string $professionCode
     * @param int|null $value
     * @return Intelligence
     */
    private function createIntelligence($professionCode, $value = null)
    {
        return $this->createProperty($professionCode, Intelligence::class, Intelligence::INTELLIGENCE, $value);
    }

    /**
     * @param string $professionCode
     * @param int|null $value
     * @return Charisma
     */
    private function createCharisma($professionCode, $value = null)
    {
        return $this->createProperty($professionCode, Charisma::class, Charisma::CHARISMA, $value);
    }

    private function getPropertyClassByCode($propertyCode)
    {
        switch ($propertyCode) {
            case Strength::STRENGTH :
                return Strength::class;
            case Agility::AGILITY :
                return Agility::class;
            case Knack::KNACK :
                return Knack::class;
            case Will::WILL :
                return Will::class;
            case Intelligence::INTELLIGENCE :
                return Intelligence::class;
            case Charisma::CHARISMA :
                return Charisma::class;
            default :
                throw new \LogicException('Where did you get that? ' . $propertyCode);
        }
    }

    /**
     * @test
     */
    public function I_can_create_it_with_default_level_up_at()
    {
        $ProfessionNextLevel = ProfessionNextLevel::createNextLevel(
            $profession = $this->createProfession(ProfessionCodes::FIGHTER),
            $levelRank = $this->createLevelRank(2),
            $strengthIncrement = $this->createStrength(ProfessionCodes::FIGHTER),
            $agilityIncrement = $this->createAgility(ProfessionCodes::FIGHTER),
            $knackIncrement = $this->createKnack(ProfessionCodes::FIGHTER),
            $willIncrement = $this->createWill(ProfessionCodes::FIGHTER),
            $intelligenceIncrement = $this->createIntelligence(ProfessionCodes::FIGHTER),
            $charismaIncrement = $this->createCharisma(ProfessionCodes::FIGHTER)
        );
        $levelUpAt = $ProfessionNextLevel->getLevelUpAt();
        $this->assertInstanceOf(\DateTimeImmutable::class, $levelUpAt);
        $this->assertSame(time(), $levelUpAt->getTimestamp());
    }

    /**
     * @test
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\MaximumLevelExceeded
     */
    public function I_can_not_create_higher_next_level_than_twenty()
    {
        new ProfessionNextLevel(
            $this->createProfession(ProfessionCodes::FIGHTER),
            $this->createLevelRank(21),
            $this->createStrength(ProfessionCodes::FIGHTER),
            $this->createAgility(ProfessionCodes::FIGHTER),
            $this->createKnack(ProfessionCodes::FIGHTER),
            $this->createWill(ProfessionCodes::FIGHTER),
            $this->createIntelligence(ProfessionCodes::FIGHTER),
            $this->createCharisma(ProfessionCodes::FIGHTER)
        );
    }

    /**
     * @test
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\MinimumLevelExceeded
     */
    public function I_can_not_create_lesser_next_level_than_two()
    {
        new ProfessionNextLevel(
            $this->createProfession(ProfessionCodes::FIGHTER),
            $this->createLevelRank(1),
            $this->createStrength(ProfessionCodes::FIGHTER),
            $this->createAgility(ProfessionCodes::FIGHTER),
            $this->createKnack(ProfessionCodes::FIGHTER),
            $this->createWill(ProfessionCodes::FIGHTER),
            $this->createIntelligence(ProfessionCodes::FIGHTER),
            $this->createCharisma(ProfessionCodes::FIGHTER)
        );
    }

    /**
     * @test
     * @dataProvider provideProfessionCode
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidNextLevelPropertiesSum
     * @expectedExceptionMessageRegExp " 2, got 6$"
     * @param string $professionCode
     */
    public function I_can_not_create_next_level_with_too_high_properties_sum($professionCode)
    {
        new ProfessionNextLevel(
            $this->createProfession($professionCode),
            $levelRank = LevelRank::getIt(2),
            Strength::getIt(1),
            Agility::getIt(1),
            Knack::getIt(1),
            Will::getIt(1),
            Intelligence::getIt(1),
            Charisma::getIt(1)
        );
    }

    /**
     * @test
     * @dataProvider provideProfessionCode
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidNextLevelPropertiesSum
     * @expectedExceptionMessageRegExp " 2, got 0$"
     * @param string $professionCode
     */
    public function I_can_not_create_next_level_with_too_low_properties_sum($professionCode)
    {
        new ProfessionNextLevel(
            $this->createProfession($professionCode),
            $levelRank = LevelRank::getIt(2),
            Strength::getIt(0),
            Agility::getIt(0),
            Knack::getIt(0),
            Will::getIt(0),
            Intelligence::getIt(0),
            Charisma::getIt(0)
        );
    }

    /**
     * @param string $propertyCodeToNegative
     *
     * @test
     * @dataProvider providePropertyCodeOneByOne
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidNextLevelPropertyValue
     */
    public function I_can_not_create_next_level_with_negative_properties_sum($propertyCodeToNegative)
    {
        new ProfessionNextLevel(
            $this->createProfession(ProfessionCodes::FIGHTER),
            $levelRank = LevelRank::getIt(2),
            Strength::getIt($propertyCodeToNegative === Strength::STRENGTH ? -1 : 0),
            Agility::getIt($propertyCodeToNegative === Agility::AGILITY ? -1 : 0),
            Knack::getIt($propertyCodeToNegative === Knack::KNACK ? -1 : 0),
            Will::getIt($propertyCodeToNegative === Will::WILL ? -1 : 0),
            Intelligence::getIt($propertyCodeToNegative === Intelligence::INTELLIGENCE ? -1 : 0),
            Charisma::getIt($propertyCodeToNegative === Charisma::CHARISMA ? -1 : 0)
        );
    }

    public function providePropertyCodeOneByOne()
    {
        return [
            [Strength::STRENGTH],
            [Agility::AGILITY],
            [Knack::KNACK],
            [Will::WILL],
            [Intelligence::INTELLIGENCE],
            [Charisma::CHARISMA],
        ];
    }
}
