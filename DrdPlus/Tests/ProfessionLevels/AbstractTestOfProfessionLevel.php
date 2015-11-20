<?php
namespace DrdPlus\Tests\ProfessionLevels;

use DrdPlus\Properties\Base\Agility;
use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Intelligence;
use DrdPlus\Properties\Base\Knack;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Properties\Base\Will;
use DrdPlus\ProfessionLevels\LevelRank;
use DrdPlus\ProfessionLevels\ProfessionLevel;
use \DrdPlus\Professions\AbstractProfession;
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
        $instance = new $professionLevelClass(
            $this->createProfession(),
            $this->createFirstLevelRank(),
            $this->createFirstLevelStrength(),
            $this->createFirstLevelAgility(),
            $this->createFirstLevelKnack(),
            $this->createFirstLevelWill(),
            $this->createFirstLevelIntelligence(),
            $this->createFirstLevelCharisma()
        );
        $this->assertInstanceOf($this->getProfessionLevelClass(), $instance);
        /** @var ProfessionLevel $instance */
        $this->assertNull($instance->getId());
        $this->assertSame($this->getProfessionCode(), $instance->getProfession()->getCode());

        return $instance;
    }

    /** @return LevelRank */
    private function createFirstLevelRank()
    {
        /** @var LevelRank|\Mockery\MockInterface $levelRank */
        $levelRank = \Mockery::mock(LevelRank::class);
        $levelRank->shouldReceive('getValue')
            ->andReturn(1);

        return $levelRank;
    }

    /** @return Strength */
    private function createFirstLevelStrength()
    {
        return $this->createFirstLevelProperty(Strength::class, Strength::STRENGTH);
    }

    /**
     * @param string $propertyClass
     * @param string $propertyCode
     * @return MockInterface|PropertyInterface
     */
    private function createFirstLevelProperty($propertyClass, $propertyCode)
    {
        $property = \Mockery::mock($propertyClass);
        $this->addPropertyFirstLevelExpectation($property, $propertyCode);

        return $property;
    }

    private function addPropertyFirstLevelExpectation(MockInterface $property, $propertyName)
    {
        $property->shouldReceive('getValue')
            ->atLeast()->once()
            ->andReturn($this->isPrimaryProperty($propertyName) ? 1 : 0);
        $property->shouldReceive('getCode')
            ->atLeast()->once()
            ->andReturn($propertyName);
    }

    /**
     * @param string $propertyName
     *
     * @return bool
     */
    private function isPrimaryProperty($propertyName)
    {
        return in_array($propertyName, $this->getPrimaryProperties());
    }

    /**
     * @return string[]
     */
    abstract protected function getPrimaryProperties();

    /** @return Agility */
    private function createFirstLevelAgility()
    {
        return $this->createFirstLevelProperty(Agility::class, Agility::AGILITY);
    }

    /** @return Knack */
    private function createFirstLevelKnack()
    {
        return $this->createFirstLevelProperty(Knack::class, Knack::KNACK);
    }

    /** @return Will */
    private function createFirstLevelWill()
    {
        return $this->createFirstLevelProperty(Will::class, Will::WILL);
    }

    /** @return Intelligence */
    private function createFirstLevelIntelligence()
    {
        return $this->createFirstLevelProperty(Intelligence::class, Intelligence::INTELLIGENCE);
    }

    /** @return Charisma */
    private function createFirstLevelCharisma()
    {
        return $this->createFirstLevelProperty(Charisma::class, Charisma::CHARISMA);
    }

    /**
     * @return MockInterface|AbstractProfession
     */
    private function createProfession()
    {
        $profession = \Mockery::mock($this->getProfessionClass());
        $profession->shouldReceive('isPrimaryProperty')
            ->atLeast()->once()
            ->andReturnUsing(
                function ($propertyCode) {
                    return in_array($propertyCode, $this->getPrimaryProperties());
                }
            );
        $profession->shouldReceive('getCode')
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
     * @return string|AbstractProfession
     */
    private function getProfessionClass()
    {
        $withProperNamespace = preg_replace('~LevelTest$~', '', static::class);

        return preg_replace('~ProfessionLevels~', 'Professions', $withProperNamespace);
    }

    /**
     * @return string|ProfessionLevel
     */
    private function getProfessionLevelClass()
    {
        return preg_replace('~Test$~', '', static::class);
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
     * @expectedException \DrdPlus\ProfessionLevels\Exceptions\MaximumLevelExceeded
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
}
