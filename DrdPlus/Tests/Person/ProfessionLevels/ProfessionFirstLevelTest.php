<?php
namespace DrdPlus\Tests\Person\ProfessionLevels;

use DrdPlus\Codes\ProfessionCodes;
use DrdPlus\Person\ProfessionLevels\LevelRank;
use DrdPlus\Person\ProfessionLevels\ProfessionFirstLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionLevel;
use DrdPlus\Properties\Base\Agility;
use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Intelligence;
use DrdPlus\Properties\Base\Knack;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Properties\Base\Will;
use DrdPlus\Properties\PropertyInterface;
use Mockery\MockInterface;

class ProfessionFirstLevelTest extends AbstractTestOfProfessionLevel
{

    /**
     * @test
     * @dataProvider provideProfessionCode
     * @param string $professionCode
     * @return ProfessionLevel
     */
    public function I_can_create_it($professionCode)
    {
        $professionFirstLevel = ProfessionFirstLevel::createFirstLevel(
            $this->createProfession($professionCode),
            $levelUpAt = new \DateTimeImmutable('2004-01-01')
        );
        self::assertInstanceOf(ProfessionFirstLevel::class, $professionFirstLevel);
        /** @var ProfessionLevel $professionFirstLevel */
        self::assertNull($professionFirstLevel->getId());
        self::assertSame($professionCode, $professionFirstLevel->getProfession()->getValue());
        self::assertTrue($professionFirstLevel->isFirstLevel());
        self::assertFalse($professionFirstLevel->isNextLevel());
        foreach ([Strength::STRENGTH, Agility::AGILITY, Knack::KNACK, Will::WILL, Intelligence::INTELLIGENCE, Charisma::CHARISMA] as $propertyCode) {
            self::assertSame($this->isPrimaryProperty($propertyCode, $professionCode), $professionFirstLevel->isPrimaryProperty($propertyCode));
            self::assertInstanceOf(
                $this->getPropertyClassByCode($propertyCode),
                $propertyIncrement = $professionFirstLevel->getBasePropertyIncrement($propertyCode)
            );
            self::assertSame(
                $this->isPrimaryProperty($propertyCode, $professionCode) ? 1 : 0,
                $propertyIncrement->getValue()
            );
        }
        self::assertSame($levelUpAt, $professionFirstLevel->getLevelUpAt());
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
        /** @var ProfessionLevel $professionLevel */
        $professionLevel = new ProfessionFirstLevel(
            $profession = $this->createProfession($professionCode),
            $levelRank = $this->createLevelRank(),
            $strengthIncrement = $this->createStrength($professionCode),
            $agilityIncrement = $this->createAgility($professionCode),
            $knackIncrement = $this->createKnack($professionCode),
            $willIncrement = $this->createWill($professionCode),
            $intelligenceIncrement = $this->createIntelligence($professionCode),
            $charismaIncrement = $this->createCharisma($professionCode),
            $levelUpAt = new \DateTimeImmutable()
        );
        self::assertSame($profession, $professionLevel->getProfession());
        self::assertSame($levelRank, $professionLevel->getLevelRank());
        self::assertSame($strengthIncrement, $professionLevel->getStrengthIncrement());
        self::assertSame($agilityIncrement, $professionLevel->getAgilityIncrement());
        self::assertSame($knackIncrement, $professionLevel->getKnackIncrement());
        self::assertSame($intelligenceIncrement, $professionLevel->getIntelligenceIncrement());
        self::assertSame($charismaIncrement, $professionLevel->getCharismaIncrement());
        self::assertSame($willIncrement, $professionLevel->getWillIncrement());
        self::assertSame($levelUpAt, $professionLevel->getLevelUpAt());
    }

    /**
     * @param int $rankValue
     * @return LevelRank
     */
    private function createLevelRank($rankValue = 1)
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
        $property = \Mockery::mock($propertyClass);
        $this->addPropertyExpectation($professionCode, $property, $propertyCode, $propertyValue);

        return $property;
    }

    private function addPropertyExpectation(
        $professionCode,
        MockInterface $property,
        $propertyCode,
        $propertyValue = null
    )
    {
        $property->shouldReceive('getValue')
            ->andReturn((is_null($propertyValue)
                ? ($this->isPrimaryProperty($propertyCode, $professionCode)
                    ? 1
                    : 0
                )
                : $propertyValue
            ));
        $property->shouldReceive('getCode')
            ->andReturn($propertyCode);
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
        $professionFirstLevel = ProfessionFirstLevel::createFirstLevel(
            $this->createProfession(ProfessionCodes::FIGHTER)
        );
        $levelUpAt = $professionFirstLevel->getLevelUpAt();
        self::assertInstanceOf(\DateTimeImmutable::class, $levelUpAt);
        self::assertSame(time(), $levelUpAt->getTimestamp());
    }

    /**
     * @test
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidFirstLevelRank
     */
    public function I_can_not_create_higher_first_level_than_one()
    {
        new ProfessionFirstLevel(
            $this->createProfession(ProfessionCodes::FIGHTER),
            $this->createLevelRank(2),
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
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidFirstLevelRank
     */
    public function I_can_not_create_lesser_first_level_than_one()
    {
        new ProfessionFirstLevel(
            $this->createProfession(ProfessionCodes::FIGHTER),
            $this->createLevelRank(0),
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
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidFirstLevelPropertyValue
     * @dataProvider provideTooHighFirstLevelPropertiesOneByOne
     *
     * @param string $professionCode
     * @param int $strength
     * @param int $agility
     * @param int $knack
     * @param int $will
     * @param int $intelligence
     * @param int $charisma
     */
    public function I_can_not_use_greater_than_allowed_first_level_property(
        $professionCode, $strength, $agility, $knack, $will, $intelligence, $charisma
    )
    {
        new ProfessionFirstLevel(
            $this->createProfession($professionCode),
            $this->createLevelRank(),
            $this->createStrength($professionCode, $strength),
            $this->createAgility($professionCode, $agility),
            $this->createKnack($professionCode, $knack),
            $this->createWill($professionCode, $will),
            $this->createIntelligence($professionCode, $intelligence),
            $this->createCharisma($professionCode, $charisma)
        );
    }

    public function provideTooHighFirstLevelPropertiesOneByOne()
    {
        $values = [];
        foreach ($this->getProfessionCodes() as $professionCode) {
            $singleTestValuesPattern = [
                $this->createStrength($professionCode)->getValue(),
                $this->createAgility($professionCode)->getValue(),
                $this->createKnack($professionCode)->getValue(),
                $this->createWill($professionCode)->getValue(),
                $this->createIntelligence($professionCode)->getValue(),
                $this->createCharisma($professionCode)->getValue(),
            ];
            foreach ($singleTestValuesPattern as $index => $value) {
                $singleTestValues = $singleTestValuesPattern;
                $singleTestValues[$index] = $value + 1;
                array_unshift($singleTestValues, $professionCode);
                $values[] = $singleTestValues;
            }
        }

        return $values;
    }

    private function getProfessionCodes()
    {
        return [
            ProfessionCodes::FIGHTER,
            ProfessionCodes::THIEF,
            ProfessionCodes::RANGER,
            ProfessionCodes::WIZARD,
            ProfessionCodes::THEURGIST,
            ProfessionCodes::PRIEST,
        ];
    }

    /**
     * @test
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidFirstLevelPropertyValue
     * @dataProvider getTooLowFirstLevelPropertiesOneByOne
     *
     * @param $professionCode
     * @param $strength
     * @param $agility
     * @param $knack
     * @param $will
     * @param $intelligence
     * @param $charisma
     */
    public function I_can_not_use_lesser_than_allowed_first_level_property(
        $professionCode, $strength, $agility, $knack, $will, $intelligence, $charisma
    )
    {
        new ProfessionFirstLevel(
            $this->createProfession($professionCode),
            $this->createLevelRank(),
            $this->createStrength($professionCode, $strength),
            $this->createAgility($professionCode, $agility),
            $this->createKnack($professionCode, $knack),
            $this->createWill($professionCode, $will),
            $this->createIntelligence($professionCode, $intelligence),
            $this->createCharisma($professionCode, $charisma)
        );
    }

    public function getTooLowFirstLevelPropertiesOneByOne()
    {
        $values = [];
        foreach ($this->getProfessionCodes() as $professionCode) {
            $singleTestValuesPattern = [
                $this->createStrength($professionCode)->getValue(),
                $this->createAgility($professionCode)->getValue(),
                $this->createKnack($professionCode)->getValue(),
                $this->createWill($professionCode)->getValue(),
                $this->createIntelligence($professionCode)->getValue(),
                $this->createCharisma($professionCode)->getValue(),
            ];
            foreach ($singleTestValuesPattern as $index => $value) {
                $singleTestValues = $singleTestValuesPattern;
                $singleTestValues[$index] = $value - 1;
                array_unshift($singleTestValues, $professionCode);
                $values[] = $singleTestValues;
            }
        }

        return $values;
    }

    /**
     * @test
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\UnknownBaseProperty
     */
    public function I_am_stopped_on_use_of_unknown_property_code()
    {
        ProfessionFirstLevel::createFirstLevel(
            $this->createProfession(ProfessionCodes::FIGHTER)
        )->getBasePropertyIncrement('invalid');
    }

}
