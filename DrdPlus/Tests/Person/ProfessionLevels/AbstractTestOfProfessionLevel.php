<?php
namespace DrdPlus\Tests\Person\ProfessionLevels;

use DrdPlus\Codes\ProfessionCodes;
use DrdPlus\Person\ProfessionLevels\FighterLevel;
use DrdPlus\Person\ProfessionLevels\PriestLevel;
use DrdPlus\Person\ProfessionLevels\RangerLevel;
use DrdPlus\Person\ProfessionLevels\TheurgistLevel;
use DrdPlus\Person\ProfessionLevels\ThiefLevel;
use DrdPlus\Person\ProfessionLevels\WizardLevel;
use DrdPlus\Professions\Fighter;
use DrdPlus\Professions\Priest;
use DrdPlus\Professions\Ranger;
use DrdPlus\Professions\Theurgist;
use DrdPlus\Professions\Thief;
use DrdPlus\Professions\Wizard;
use DrdPlus\Properties\Base\Agility;
use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Intelligence;
use DrdPlus\Properties\Base\Knack;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Properties\Base\Will;
use DrdPlus\Person\ProfessionLevels\LevelRank;
use DrdPlus\Person\ProfessionLevels\ProfessionLevel;
use \DrdPlus\Professions\Profession;
use DrdPlus\Properties\PropertyInterface;
use DrdPlus\Tools\Tests\TestWithMockery;
use Mockery\MockInterface;

abstract class AbstractTestOfProfessionLevel extends TestWithMockery
{

    /**
     * @return ProfessionLevel
     *
     * @test
     */
    public function I_can_create_first_level()
    {
        $professionLevelClass = $this->getProfessionLevelClass();
        $professionLevel = new $professionLevelClass(
            $this->createProfession(),
            $this->createFirstLevelRank(),
            $this->createFirstLevelStrength(),
            $this->createFirstLevelAgility(),
            $this->createFirstLevelKnack(),
            $this->createFirstLevelWill(),
            $this->createFirstLevelIntelligence(),
            $this->createFirstLevelCharisma()
        );
        $this->assertInstanceOf($this->getProfessionLevelClass(), $professionLevel);
        /** @var ProfessionLevel $professionLevel */
        $this->assertNull($professionLevel->getId());
        $this->assertSame($this->getProfessionCode(), $professionLevel->getProfession()->getValue());
        $this->assertTrue($professionLevel->isFirstLevel());
        $this->assertFalse($professionLevel->isNextLevel());
        foreach ([Strength::STRENGTH, Agility::AGILITY, Knack::KNACK, Will::WILL, Intelligence::INTELLIGENCE, Charisma::CHARISMA] as $propertyCode) {
            $this->assertSame($this->isPrimaryProperty($propertyCode), $professionLevel->isPrimaryProperty($propertyCode));
            $this->assertInstanceOf(
                $this->getPropertyClassByCode($propertyCode),
                $propertyIncrement = $professionLevel->getBasePropertyIncrement($propertyCode)
            );
            $this->assertSame($this->isPrimaryProperty($propertyCode) ? 1 : 0, $propertyIncrement->getValue());
        }

        return $professionLevel;
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

    /** @return LevelRank */
    private function createFirstLevelRank()
    {
        /** @var LevelRank|\Mockery\MockInterface $levelRank */
        $levelRank = \Mockery::mock(LevelRank::class);
        $levelRank->shouldReceive('getValue')
            ->andReturn(1);
        $levelRank->shouldReceive('isFirstLevel')
            ->andReturn(true);
        $levelRank->shouldReceive('isNextLevel')
            ->andReturn(false);

        return $levelRank;
    }

    /**
     * @param int|null $propertyValue = null
     * @return Strength
     */
    private function createFirstLevelStrength($propertyValue = null)
    {
        return $this->createFirstLevelProperty(Strength::class, Strength::STRENGTH, $propertyValue);
    }

    /**
     * @param string $propertyClass
     * @param string $propertyCode
     * @param string|null $propertyValue = null
     * @return MockInterface|PropertyInterface
     */
    private function createFirstLevelProperty($propertyClass, $propertyCode, $propertyValue = null)
    {
        $property = \Mockery::mock($propertyClass);
        $this->addPropertyFirstLevelExpectation($property, $propertyCode, $propertyValue);

        return $property;
    }

    private function addPropertyFirstLevelExpectation(MockInterface $property, $propertyName, $propertyValue = null)
    {
        $property->shouldReceive('getValue')
            ->andReturn((is_null($propertyValue)
                ? ($this->isPrimaryProperty($propertyName)
                    ? 1
                    : 0
                )
                : $propertyValue
            ));
        $property->shouldReceive('getCode')
            ->andReturn($propertyName);
    }

    /**
     * @param string $propertyCode
     *
     * @return bool
     */
    private function isPrimaryProperty($propertyCode)
    {
        return in_array($propertyCode, $this->getPrimaryProperties());
    }

    /**
     * @return string[]
     */
    abstract protected function getPrimaryProperties();

    /**
     * @param int|null $value
     * @return Agility
     */
    private function createFirstLevelAgility($value = null)
    {
        return $this->createFirstLevelProperty(Agility::class, Agility::AGILITY, $value);
    }

    /**
     * @param int|null $value
     * @return Knack
     */
    private function createFirstLevelKnack($value = null)
    {
        return $this->createFirstLevelProperty(Knack::class, Knack::KNACK, $value);
    }

    /**
     * @param int|null $value
     * @return Will
     */
    private function createFirstLevelWill($value = null)
    {
        return $this->createFirstLevelProperty(Will::class, Will::WILL, $value);
    }

    /**
     * @param int|null $value
     * @return Intelligence
     */
    private function createFirstLevelIntelligence($value = null)
    {
        return $this->createFirstLevelProperty(Intelligence::class, Intelligence::INTELLIGENCE, $value);
    }

    /**
     * @param int|null $value
     * @return Charisma
     */
    private function createFirstLevelCharisma($value = null)
    {
        return $this->createFirstLevelProperty(Charisma::class, Charisma::CHARISMA, $value);
    }

    /**
     * @return MockInterface|Profession|Fighter|Wizard|Priest|Theurgist|Thief|Ranger
     */
    private function createProfession()
    {
        $profession = \Mockery::mock($this->getProfessionClass());
        $profession->shouldReceive('isPrimaryProperty')
            ->andReturnUsing(
                function ($propertyCode) {
                    return in_array($propertyCode, $this->getPrimaryProperties());
                }
            );
        $profession->shouldReceive('getPrimaryProperties')
            ->andReturn($this->getPrimaryProperties());
        $profession->shouldReceive('getValue')
            ->andReturn($this->getProfessionCode());

        return $profession;
    }

    /**
     * @return string
     */
    private function getProfessionCode()
    {
        return strtolower(preg_replace('~^.+\\\~', '', $this->getProfessionClass()));
    }

    /**
     * @return string|Profession
     */
    private function getProfessionClass()
    {
        $reflection = new \ReflectionClass(Profession::class);
        $namespace = $reflection->getNamespaceName();
        $this->assertNotEmpty(preg_match('~[\\\](?<basename>\w+)LevelTest$~', static::class, $matches));

        return $namespace . '\\' . $matches['basename'];
    }

    /**
     * @return ProfessionLevel|FighterLevel|WizardLevel|PriestLevel|TheurgistLevel|ThiefLevel|RangerLevel
     */
    private function getProfessionLevelClass()
    {
        return preg_replace('~Tests[\\\](.+)Test$~', '$1', static::class);
    }

    /**
     * @test
     */
    public function I_can_let_create_first_level_by_factory()
    {
        $professionLevel = $this->createFirstLevelByFactoryFor($profession = $this->createProfession());
        /** @var ProfessionLevel $professionLevel */
        $this->assertSame($profession, $professionLevel->getProfession());
        $this->assertSame(1, $professionLevel->getLevelRank()->getValue());
        $this->assertSame($this->isPrimaryProperty(Strength::STRENGTH) ? 1 : 0, $professionLevel->getStrengthIncrement()->getValue());
        $this->assertSame($this->isPrimaryProperty(Agility::AGILITY) ? 1 : 0, $professionLevel->getAgilityIncrement()->getValue());
        $this->assertSame($this->isPrimaryProperty(Knack::KNACK) ? 1 : 0, $professionLevel->getKnackIncrement()->getValue());
        $this->assertSame($this->isPrimaryProperty(Will::WILL) ? 1 : 0, $professionLevel->getWillIncrement()->getValue());
        $this->assertSame($this->isPrimaryProperty(Intelligence::INTELLIGENCE) ? 1 : 0, $professionLevel->getIntelligenceIncrement()->getValue());
        $this->assertSame($this->isPrimaryProperty(Charisma::CHARISMA) ? 1 : 0, $professionLevel->getCharismaIncrement()->getValue());
        $this->assertEquals(time(), $professionLevel->getLevelUpAt()->getTimestamp());

        $firstLevelWithOurLevelUpAt = $this->createFirstLevelByFactoryFor(
            $profession,
            $levelUpAt = new \DateTimeImmutable('Wed Nov 18 11:44:09 2015 +0100')
        );
        $this->assertSame($levelUpAt, $firstLevelWithOurLevelUpAt->getLevelUpAt());
    }

    private function createFirstLevelByFactoryFor(Profession $profession, \DateTimeImmutable $levelUpAt = null)
    {
        switch ($profession->getValue()) {
            case ProfessionCodes::FIGHTER :
                /** @var Fighter $profession */
                return FighterLevel::createFirstLevel($profession, $levelUpAt);
            case ProfessionCodes::WIZARD :
                /** @var Wizard $profession */
                return WizardLevel::createFirstLevel($profession, $levelUpAt);
            case ProfessionCodes::PRIEST :
                /** @var Priest $profession */
                return PriestLevel::createFirstLevel($profession, $levelUpAt);
            case ProfessionCodes::THEURGIST :
                /** @var Theurgist $profession */
                return TheurgistLevel::createFirstLevel($profession, $levelUpAt);
            case ProfessionCodes::THIEF :
                /** @var Thief $profession */
                return ThiefLevel::createFirstLevel($profession, $levelUpAt);
            case ProfessionCodes::RANGER :
                /** @var Ranger $profession */
                return RangerLevel::createFirstLevel($profession, $levelUpAt);
            default :
                throw new \LogicException('Where did you get that? ' . $profession->getValue());
        }
    }

    /**
     * @test
     * @depends I_can_create_first_level
     */
    public function I_can_let_time_of_level_up_to_generate()
    {
        $professionLevelClass = $this->getProfessionLevelClass();
        $professionLevel = new $professionLevelClass(
            $this->createProfession(),
            $this->createFirstLevelRank(),
            $this->createFirstLevelStrength(),
            $this->createFirstLevelAgility(),
            $this->createFirstLevelKnack(),
            $this->createFirstLevelWill(),
            $this->createFirstLevelIntelligence(),
            $this->createFirstLevelCharisma()
        );
        /** @var ProfessionLevel $professionLevel */
        $this->assertEquals(time(), $professionLevel->getLevelUpAt()->getTimestamp());
    }

    /**
     * @test
     * @depends I_can_create_first_level
     */
    public function I_can_get_level_details()
    {
        $professionLevelClass = $this->getProfessionLevelClass();
        /** @var ProfessionLevel $professionLevel */
        $professionLevel = new $professionLevelClass(
            $profession = $this->createProfession(),
            $levelRank = $this->createFirstLevelRank(),
            $strengthIncrement = $this->createFirstLevelStrength(),
            $agilityIncrement = $this->createFirstLevelAgility(),
            $knackIncrement = $this->createFirstLevelKnack(),
            $willIncrement = $this->createFirstLevelWill(),
            $intelligenceIncrement = $this->createFirstLevelIntelligence(),
            $charismaIncrement = $this->createFirstLevelCharisma(),
            $levelUpAt = new \DateTimeImmutable()
        );
        $this->assertSame($profession, $professionLevel->getProfession());
        $this->assertSame($levelRank, $professionLevel->getLevelRank());
        $this->assertSame($strengthIncrement, $professionLevel->getStrengthIncrement());
        $this->assertSame($agilityIncrement, $professionLevel->getAgilityIncrement());
        $this->assertSame($knackIncrement, $professionLevel->getKnackIncrement());
        $this->assertSame($intelligenceIncrement, $professionLevel->getIntelligenceIncrement());
        $this->assertSame($charismaIncrement, $professionLevel->getCharismaIncrement());
        $this->assertSame($willIncrement, $professionLevel->getWillIncrement());
        $this->assertSame($levelUpAt, $professionLevel->getLevelUpAt());
    }

    /**
     * @param ProfessionLevel $professionLevel
     *
     * @test
     * @depends I_can_create_first_level
     */
    public function I_can_get_first_level_strength(ProfessionLevel $professionLevel)
    {
        $this->assertSame(
            $this->getPropertyFirstLevelModifier(Strength::STRENGTH),
            $professionLevel->getStrengthIncrement()->getValue()
        );
    }

    private function getPropertyFirstLevelModifier($propertyName)
    {
        return $this->isPrimaryProperty($propertyName)
            ? 1
            : 0;
    }

    /**
     * @param ProfessionLevel $professionLevel
     *
     * @test
     * @depends I_can_create_first_level
     */
    public function I_can_get_first_level_agility(ProfessionLevel $professionLevel)
    {
        $this->assertSame(
            $this->getPropertyFirstLevelModifier(Agility::AGILITY),
            $professionLevel->getAgilityIncrement()->getValue()
        );
    }

    /**
     * @param ProfessionLevel $professionLevel
     *
     * @test
     * @depends I_can_create_first_level
     */
    public function I_can_get_first_level_knack(ProfessionLevel $professionLevel)
    {
        $this->assertSame(
            $this->getPropertyFirstLevelModifier(Knack::KNACK),
            $professionLevel->getKnackIncrement()->getValue()
        );
    }

    /**
     * @param ProfessionLevel $professionLevel
     *
     * @test
     * @depends I_can_create_first_level
     */
    public function I_can_get_first_level_charisma(ProfessionLevel $professionLevel)
    {
        $this->assertSame(
            $this->getPropertyFirstLevelModifier(Charisma::CHARISMA),
            $professionLevel->getCharismaIncrement()->getValue()
        );
    }

    /**
     * @param ProfessionLevel $professionLevel
     *
     * @test
     * @depends I_can_create_first_level
     */
    public function I_can_get_first_level_intelligence(ProfessionLevel $professionLevel)
    {
        $this->assertSame(
            $this->getPropertyFirstLevelModifier(Intelligence::INTELLIGENCE),
            $professionLevel->getIntelligenceIncrement()->getValue()
        );
    }

    /**
     * @param ProfessionLevel $professionLevel
     *
     * @test
     * @depends I_can_create_first_level
     */
    public function I_can_get_first_level_will(ProfessionLevel $professionLevel)
    {
        $this->assertSame(
            $this->getPropertyFirstLevelModifier(Will::WILL),
            $professionLevel->getWillIncrement()->getValue()
        );
    }

    /**
     * @test
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\MaximumLevelExceeded
     */
    public function I_can_not_create_higher_level_than_twenty()
    {
        /** @var LevelRank|\Mockery\MockInterface $levelRank */
        $levelRank = \Mockery::mock(LevelRank::class);
        $levelRank->shouldReceive('getValue')
            ->andReturn(21);
        $professionLevelClass = $this->getProfessionLevelClass();

        new $professionLevelClass(
            \Mockery::mock($this->getProfessionClass()),
            $levelRank,
            \Mockery::mock(Strength::class),
            \Mockery::mock(Agility::class),
            \Mockery::mock(Knack::class),
            \Mockery::mock(Will::class),
            \Mockery::mock(Intelligence::class),
            \Mockery::mock(Charisma::class)
        );
    }

    /**
     * @test
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\MinimumLevelExceeded
     */
    public function I_can_not_create_lesser_level_than_one()
    {
        /** @var LevelRank|\Mockery\MockInterface $zeroLevelRank */
        $zeroLevelRank = \Mockery::mock(LevelRank::class);
        $zeroLevelRank->shouldReceive('getValue')
            ->andReturn(0);
        $professionLevelClass = $this->getProfessionLevelClass();

        new $professionLevelClass(
            \Mockery::mock($this->getProfessionClass()),
            $zeroLevelRank,
            \Mockery::mock(Strength::class),
            \Mockery::mock(Agility::class),
            \Mockery::mock(Knack::class),
            \Mockery::mock(Will::class),
            \Mockery::mock(Intelligence::class),
            \Mockery::mock(Charisma::class)
        );
    }

    /**
     * @test
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidFirstLevelPropertyValue
     * @dataProvider getTooHighFirstLevelPropertiesOneByOne
     *
     * @param $strength
     * @param $agility
     * @param $knack
     * @param $will
     * @param $intelligence
     * @param $charisma
     */
    public function I_can_not_use_greater_than_allowed_first_level_property(
        $strength, $agility, $knack, $will, $intelligence, $charisma
    )
    {
        $professionLevelClass = $this->getProfessionLevelClass();

        new $professionLevelClass(
            $this->createProfession(),
            $this->createFirstLevelRank(),
            $this->createFirstLevelStrength($strength),
            $this->createFirstLevelAgility($agility),
            $this->createFirstLevelKnack($knack),
            $this->createFirstLevelWill($will),
            $this->createFirstLevelIntelligence($intelligence),
            $this->createFirstLevelCharisma($charisma)
        );
    }

    public function getTooHighFirstLevelPropertiesOneByOne()
    {
        $values = [];
        $singleTestValuesPattern = [
            $this->createFirstLevelStrength()->getValue(),
            $this->createFirstLevelAgility()->getValue(),
            $this->createFirstLevelKnack()->getValue(),
            $this->createFirstLevelWill()->getValue(),
            $this->createFirstLevelIntelligence()->getValue(),
            $this->createFirstLevelCharisma()->getValue(),
        ];
        foreach ($singleTestValuesPattern as $index => $value) {
            $singleTestValues = $singleTestValuesPattern;
            $singleTestValues[$index] = $value + 1;
            $values[] = $singleTestValues;
        }

        return $values;
    }

    /**
     * @test
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidFirstLevelPropertyValue
     * @dataProvider getTooLowFirstLevelPropertiesOneByOne
     *
     * @param $strength
     * @param $agility
     * @param $knack
     * @param $will
     * @param $intelligence
     * @param $charisma
     */
    public function I_can_not_use_lesser_than_allowed_first_level_property(
        $strength, $agility, $knack, $will, $intelligence, $charisma
    )
    {
        $professionLevelClass = $this->getProfessionLevelClass();

        new $professionLevelClass(
            $this->createProfession(),
            $this->createFirstLevelRank(),
            $this->createFirstLevelStrength($strength),
            $this->createFirstLevelAgility($agility),
            $this->createFirstLevelKnack($knack),
            $this->createFirstLevelWill($will),
            $this->createFirstLevelIntelligence($intelligence),
            $this->createFirstLevelCharisma($charisma)
        );
    }

    public function getTooLowFirstLevelPropertiesOneByOne()
    {
        $values = [];
        $singleTestValuesPattern = [
            $this->createFirstLevelStrength()->getValue(),
            $this->createFirstLevelAgility()->getValue(),
            $this->createFirstLevelKnack()->getValue(),
            $this->createFirstLevelWill()->getValue(),
            $this->createFirstLevelIntelligence()->getValue(),
            $this->createFirstLevelCharisma()->getValue(),
        ];
        foreach ($singleTestValuesPattern as $index => $value) {
            $singleTestValues = $singleTestValuesPattern;
            $singleTestValues[$index] = $value - 1;
            $values[] = $singleTestValues;
        }

        return $values;
    }

    /**
     * @test
     */
    public function I_can_create_next_level()
    {
        $professionLevelClass = $this->getProfessionLevelClass();
        $profession = $this->createProfession();

        /**
         * @var ProfessionLevel $nextLevel
         */
        $nextLevel = new $professionLevelClass(
            $profession,
            $levelRank = LevelRank::getIt(2),
            Strength::getIt($profession->isPrimaryProperty(Strength::STRENGTH) ? 1 : 0),
            Agility::getIt($profession->isPrimaryProperty(Agility::AGILITY) ? 1 : 0),
            Knack::getIt($profession->isPrimaryProperty(Knack::KNACK) ? 1 : 0),
            Will::getIt($profession->isPrimaryProperty(Will::WILL) ? 1 : 0),
            Intelligence::getIt($profession->isPrimaryProperty(Intelligence::INTELLIGENCE) ? 1 : 0),
            Charisma::getIt($profession->isPrimaryProperty(Charisma::CHARISMA) ? 1 : 0)
        );

        $this->assertSame($levelRank, $nextLevel->getLevelRank());
    }

    /**
     * @test
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidNextLevelPropertiesSum
     * @expectedExceptionMessageRegExp " 2, got 6$"
     */
    public function I_can_not_create_next_level_with_too_high_properties_sum()
    {
        $professionLevelClass = $this->getProfessionLevelClass();

        /**
         * @var ProfessionLevel $nextLevel
         */
        $nextLevel = new $professionLevelClass(
            $this->createProfession(),
            $levelRank = LevelRank::getIt(2),
            Strength::getIt(1),
            Agility::getIt(1),
            Knack::getIt(1),
            Will::getIt(1),
            Intelligence::getIt(1),
            Charisma::getIt(1)
        );

        $this->assertSame($levelRank, $nextLevel->getLevelRank());
    }

    /**
     * @test
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidNextLevelPropertiesSum
     * @expectedExceptionMessageRegExp " 2, got 0$"
     */
    public function I_can_not_create_next_level_with_too_low_properties_sum()
    {
        $professionLevelClass = $this->getProfessionLevelClass();

        /**
         * @var ProfessionLevel $nextLevel
         */
        $nextLevel = new $professionLevelClass(
            $this->createProfession(),
            $levelRank = LevelRank::getIt(2),
            Strength::getIt(0),
            Agility::getIt(0),
            Knack::getIt(0),
            Will::getIt(0),
            Intelligence::getIt(0),
            Charisma::getIt(0)
        );

        $this->assertSame($levelRank, $nextLevel->getLevelRank());
    }

    /**
     * @param ProfessionLevel $professionLevel
     *
     * @test
     * @depends I_can_create_first_level
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\UnknownBaseProperty
     */
    public function I_am_stopped_on_use_of_unknown_property_code(ProfessionLevel $professionLevel)
    {
        $professionLevel->getBasePropertyIncrement('invalid');
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
        $professionLevelClass = $this->getProfessionLevelClass();

        new $professionLevelClass(
            $this->createProfession(),
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
