<?php
namespace DrdPlus\ProfessionLevels;

use DrdPlus\ProfessionLevels\Exceptions\MultiProfessionsAreProhibited;
use DrdPlus\Properties\Base\Agility;
use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Intelligence;
use DrdPlus\Properties\Base\Knack;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Properties\Base\Will;
use \DrdPlus\Professions\Fighter;
use \DrdPlus\Professions\Priest;
use \DrdPlus\Professions\Profession;
use \DrdPlus\Professions\Ranger;
use \DrdPlus\Professions\Theurgist;
use \DrdPlus\Professions\Thief;
use \DrdPlus\Professions\Wizard;
use DrdPlus\Tools\Tests\TestWithMockery;
use Mockery\MockInterface;

class ProfessionLevelsTest extends TestWithMockery
{

    /**
     * @return ProfessionLevels
     *
     * @test
     */
    public function I_can_create_it()
    {
        $instance = new ProfessionLevels();
        $this->assertNotNull($instance);

        return $instance;
    }

    /*
     * EMPTY AFTER INITIALIZATION
     */

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends I_can_create_it
     */
    public function I_got_everything_empty_or_zeroed_from_new_levels(ProfessionLevels $professionLevels)
    {
        $this->assertSame(0, $professionLevels->getStrengthModifierForFirstProfession());
        $this->assertSame(0, $professionLevels->getPropertyModifierForFirstProfession(Strength::STRENGTH));
        $this->assertSame(0, $professionLevels->getStrengthModifierSummary());
        $this->assertSame(0, $professionLevels->getNextLevelsStrengthModifier());

        $this->assertSame(0, $professionLevels->getAgilityModifierForFirstProfession());
        $this->assertSame(0, $professionLevels->getPropertyModifierForFirstProfession(Agility::AGILITY));
        $this->assertSame(0, $professionLevels->getAgilityModifierSummary());
        $this->assertSame(0, $professionLevels->getNextLevelsAgilityModifier());

        $this->assertSame(0, $professionLevels->getKnackModifierForFirstProfession());
        $this->assertSame(0, $professionLevels->getPropertyModifierForFirstProfession(Knack::KNACK));
        $this->assertSame(0, $professionLevels->getKnackModifierSummary());

        $this->assertSame(0, $professionLevels->getWillModifierForFirstProfession());
        $this->assertSame(0, $professionLevels->getPropertyModifierForFirstProfession(Will::WILL));
        $this->assertSame(0, $professionLevels->getWillModifierSummary());
        $this->assertSame(0, $professionLevels->getNextLevelsWillModifier());

        $this->assertSame(0, $professionLevels->getIntelligenceModifierForFirstProfession());
        $this->assertSame(0, $professionLevels->getPropertyModifierForFirstProfession(Intelligence::INTELLIGENCE));
        $this->assertSame(0, $professionLevels->getIntelligenceModifierSummary());
        $this->assertSame(0, $professionLevels->getNextLevelsIntelligenceModifier());

        $this->assertSame(0, $professionLevels->getCharismaModifierForFirstProfession());
        $this->assertSame(0, $professionLevels->getPropertyModifierForFirstProfession(Charisma::CHARISMA));
        $this->assertSame(0, $professionLevels->getCharismaModifierSummary());
        $this->assertSame(0, $professionLevels->getNextLevelsCharismaModifier());

        $this->assertSame([], $professionLevels->getLevels());

        $this->assertFalse($professionLevels->getFirstLevel());

        $this->assertNull($professionLevels->getId());

        $this->assertEquals([], $professionLevels->getFighterLevels());
        $this->assertEquals([], $professionLevels->getWizardLevels());
        $this->assertEquals([], $professionLevels->getPriestLevels());
        $this->assertEquals([], $professionLevels->getTheurgistLevels());
        $this->assertEquals([], $professionLevels->getThiefLevels());
        $this->assertEquals([], $professionLevels->getRangerLevels());
    }

    /*
     * FIRST LEVELS
     */

    private $propertyCodes = [
        Strength::STRENGTH, Agility::AGILITY, Knack::KNACK,
        Will::WILL, Intelligence::INTELLIGENCE, Charisma::CHARISMA
    ];

    private function addPrimaryPropertiesAnswer(MockInterface $professionLevel, $professionCode)
    {
        $modifiers = [];
        foreach ($this->propertyCodes as $propertyName) {
            $modifiers[$propertyName] = $this->isPrimaryProperty($propertyName, $professionCode) ? 1 : 0;
        }
        $primaryProperties = array_keys(array_filter($modifiers));

        foreach ($this->propertyCodes as $propertyName) {
            $professionLevel->shouldReceive('isPrimaryProperty')
                ->with($propertyName)
                ->andReturn(in_array($propertyName, $primaryProperties));
        }
    }

    private function addFirstLevelAnswer(MockInterface $professionLevel, $isFirstLevel)
    {
        $professionLevel->shouldReceive('isFirstLevel')
            ->andReturn($isFirstLevel);
    }

    private function addNextLevelAnswer(MockInterface $professionLevel, $isNextLevel)
    {
        $professionLevel->shouldReceive('isNextLevel')
            ->andReturn($isNextLevel);
    }

    public function provideProfessionCode()
    {
        return [
            [Fighter::FIGHTER],
            [Wizard::WIZARD],
            [Priest::PRIEST],
            [Theurgist::THEURGIST],
            [Thief::THIEF],
            [Ranger::RANGER]
        ];
    }

    /**
     * @test
     * @dataProvider provideProfessionCode
     * @param string $professionCode
     * @return ProfessionLevels
     */
    public function I_will_get_proper_value_of_first_level_properties($professionCode)
    {
        $firstLevel = $this->createFirstLevelProfession($professionCode);
        $this->assertInstanceOf($this->getProfessionLevelClass($professionCode), $firstLevel);
        $this->addFirstLevelPropertyIncrementGetters($firstLevel, $professionCode);
        $this->addPrimaryPropertiesAnswer($firstLevel, $professionCode);
        $professionLevels = $this->createProfessionLevelsWith($professionCode, $firstLevel);
        $this->assertSame(
            $this->isPrimaryProperty(Strength::STRENGTH, $professionCode) ? 1 : 0,
            $professionLevels->getStrengthModifierForFirstProfession()
        );
        $this->assertSame(
            $this->isPrimaryProperty(Agility::AGILITY, $professionCode) ? 1 : 0,
            $professionLevels->getAgilityModifierForFirstProfession()
        );
        $this->assertSame(
            $this->isPrimaryProperty(Knack::KNACK, $professionCode) ? 1 : 0,
            $professionLevels->getKnackModifierForFirstProfession()
        );
        $this->assertSame(
            $this->isPrimaryProperty(Will::WILL, $professionCode) ? 1 : 0,
            $professionLevels->getWillModifierForFirstProfession()
        );
        $this->assertSame(
            $this->isPrimaryProperty(Intelligence::INTELLIGENCE, $professionCode) ? 1 : 0,
            $professionLevels->getIntelligenceModifierForFirstProfession()
        );
        $this->assertSame(
            $this->isPrimaryProperty(Charisma::CHARISMA, $professionCode) ? 1 : 0,
            $professionLevels->getCharismaModifierForFirstProfession()
        );

        return $professionLevels;
    }

    private function addFirstLevelPropertyIncrementGetters(MockInterface $professionLevel, $professionCode)
    {
        $modifiers = [];
        foreach ($this->propertyCodes as $propertyName) {
            $modifiers[$propertyName] = $this->isPrimaryProperty($propertyName, $professionCode) ? 1 : 0;
        }
        $this->addPropertyIncrementGetters(
            $professionLevel,
            $modifiers[Strength::STRENGTH],
            $modifiers[Agility::AGILITY],
            $modifiers[Knack::KNACK],
            $modifiers[Will::WILL],
            $modifiers[Intelligence::INTELLIGENCE],
            $modifiers[Charisma::CHARISMA]
        );
    }

    private function addPropertyIncrementGetters(
        MockInterface $professionLevel,
        $strengthValue = 0,
        $agilityValue = 0,
        $knackValue = 0,
        $willValue = 0,
        $intelligenceValue = 0,
        $charismaValue = 0
    )
    {
        $professionLevel->shouldReceive('getStrengthIncrement')
            ->andReturn($strength = $this->mockery(Strength::class));
        $professionLevel->shouldReceive('getBasePropertyIncrement')
            ->with(Strength::STRENGTH)
            ->andReturn($strength);
        $this->addValueGetter($strength, $strengthValue);
        $this->addCodeGetter($strength, Strength::STRENGTH);
        $professionLevel->shouldReceive('getAgilityIncrement')
            ->andReturn($agility = $this->mockery(Agility::class));
        $professionLevel->shouldReceive('getBasePropertyIncrement')
            ->with(Agility::AGILITY)
            ->andReturn($agility);
        $this->addValueGetter($agility, $agilityValue);
        $this->addCodeGetter($agility, Agility::AGILITY);
        $professionLevel->shouldReceive('getKnackIncrement')
            ->andReturn($knack = $this->mockery(Knack::class));
        $professionLevel->shouldReceive('getBasePropertyIncrement')
            ->with(Knack::KNACK)
            ->andReturn($knack);
        $this->addValueGetter($knack, $knackValue);
        $this->addCodeGetter($knack, Knack::KNACK);
        $professionLevel->shouldReceive('getWillIncrement')
            ->andReturn($will = $this->mockery(Will::class));
        $professionLevel->shouldReceive('getBasePropertyIncrement')
            ->with(Will::WILL)
            ->andReturn($will);
        $this->addValueGetter($will, $willValue);
        $this->addCodeGetter($will, Will::WILL);
        $professionLevel->shouldReceive('getIntelligenceIncrement')
            ->andReturn($intelligence = $this->mockery(Intelligence::class));
        $professionLevel->shouldReceive('getBasePropertyIncrement')
            ->with(Intelligence::INTELLIGENCE)
            ->andReturn($intelligence);
        $this->addValueGetter($intelligence, $intelligenceValue);
        $this->addCodeGetter($intelligence, Intelligence::INTELLIGENCE);
        $professionLevel->shouldReceive('getCharismaIncrement')
            ->andReturn($charisma = $this->mockery(Charisma::class));
        $professionLevel->shouldReceive('getBasePropertyIncrement')
            ->with(Charisma::CHARISMA)
            ->andReturn($charisma);
        $this->addValueGetter($charisma, $charismaValue);
        $this->addCodeGetter($charisma, Charisma::CHARISMA);
    }

    private function addValueGetter(MockInterface $property, $value)
    {
        $property->shouldReceive('getValue')
            ->andReturn($value);
    }

    private function addCodeGetter(MockInterface $property, $code)
    {
        $property->shouldReceive('getCode')
            ->andReturn($code);
    }

    /**
     * @param string $professionCode
     *
     * @return ProfessionLevel|\Mockery\MockInterface
     */
    private function createFirstLevelProfession($professionCode)
    {
        return $this->createProfessionLevel($professionCode, 1);
    }

    private function createProfessionLevel($professionCode, $levelValue)
    {
        /** @var \Mockery\MockInterface|ProfessionLevel $professionLevel */
        $professionLevel = $this->mockery($this->getProfessionLevelClass($professionCode));
        $professionLevel->shouldReceive('getProfession')
            ->andReturn($profession = $this->mockery(Profession::class));
        $profession->shouldReceive('getValue')
            ->andReturn($professionCode);
        $this->addFirstLevelAnswer($professionLevel, $levelValue === 1);
        $this->addNextLevelAnswer($professionLevel, $levelValue > 1);
        $professionLevel->shouldReceive('getLevelRank')
            ->andReturn($levelRank = $this->mockery(LevelRank::class));
        $levelRank->shouldReceive('getValue')
            ->andReturn($levelValue);

        return $professionLevel;
    }

    /**
     * @param string $propertyName
     * @param string $professionCode
     *
     * @return bool
     */
    private function isPrimaryProperty($propertyName, $professionCode)
    {
        return in_array($propertyName, $this->getPrimaryProperties($professionCode));
    }

    private function getPrimaryProperties($professionCode)
    {
        switch ($professionCode) {
            case Fighter::FIGHTER :
                return [Strength::STRENGTH, Agility::AGILITY];
            case Priest::PRIEST :
                return [Will::WILL, Charisma::CHARISMA];
            case Ranger::RANGER :
                return [Strength::STRENGTH, Knack::KNACK];
            case Theurgist::THEURGIST :
                return [Intelligence::INTELLIGENCE, Charisma::CHARISMA];
            case Thief::THIEF :
                return [Knack::KNACK, Agility::AGILITY];
            case Wizard::WIZARD :
                return [Will::WILL, Intelligence::INTELLIGENCE];
            default :
                throw new \RuntimeException('Unknown profession name ' . var_export($professionCode, true));
        }
    }

    /**
     * @param string $professionCode
     * @param ProfessionLevel $professionLevel
     *
     * @return ProfessionLevels
     */
    private function createProfessionLevelsWith($professionCode, ProfessionLevel $professionLevel)
    {
        $professionLevels = new ProfessionLevels();
        $addProfessionLevel = 'add' . ucfirst($professionCode) . 'Level';
        $professionLevels->$addProfessionLevel($professionLevel);
        $this->assertSame($professionLevel, $professionLevels->getFirstLevel());
        $levelsGetter = 'get' . ucfirst($professionCode) . 'levels';
        $this->assertSame([$professionLevel], $professionLevels->$levelsGetter());
        $this->assertSame([$professionLevel], $professionLevels->getLevels());

        return $professionLevels;
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @return ProfessionLevels
     */
    private function missingLevelValueCauseException(ProfessionLevels $professionLevels)
    {
        $professionCode = $professionLevels->getFirstLevel()->getProfession()->getValue();
        /** @var \Mockery\MockInterface|ProfessionLevel $professionLevel */
        $professionLevel = $this->mockery($this->getProfessionLevelClass(
            $professionCode
        ));
        $professionLevel->shouldReceive('getProfession')
            ->andReturn($profession = $this->mockery(Profession::class));
        $profession->shouldReceive('getValue')
            ->andReturn($professionCode);
        $professionLevel->shouldReceive('getLevelRank')
            ->atLeast()->once()
            ->andReturn($levelRank = $this->mockery(LevelRank::class));
        $levelRank->shouldReceive('getValue')
            ->atLeast()->once()
            ->andReturn(0);
        $professionLevel->shouldReceive('getId')
            ->andReturn('foo');
        $addProfessionLevel = 'add' . ucfirst($professionCode) . 'Level';
        $professionLevels->$addProfessionLevel($professionLevel);
    }

    /**
     * @test
     * @dataProvider provideProfessionCode
     * @param string $professionCode
     *
     * @return ProfessionLevels
     */
    public function I_can_add_profession_level($professionCode)
    {
        $professionLevels = new ProfessionLevels();
        /** @var \Mockery\MockInterface|ProfessionLevel $professionLevel */
        $professionLevel = $this->mockery($this->getProfessionLevelClass($professionCode));
        $professionLevel->shouldReceive('getProfession')
            ->andReturn($profession = $this->mockery(Profession::class));
        $profession->shouldReceive('getValue')
            ->andReturn($professionCode);
        $professionLevel->shouldReceive('getLevelRank')
            ->atLeast()->once()
            ->andReturn($levelRank = $this->mockery(LevelRank::class));
        $levelRank->shouldReceive('getValue')
            ->atLeast()->once()
            ->andReturn(1);
        $this->addPropertyIncrementGetters(
            $professionLevel,
            $strength = $this->isPrimaryProperty(Strength::STRENGTH, $professionCode) ? 1 : 0,
            $agility = $this->isPrimaryProperty(Agility::AGILITY, $professionCode) ? 1 : 0,
            $knack = $this->isPrimaryProperty(Knack::KNACK, $professionCode) ? 1 : 0,
            $will = $this->isPrimaryProperty(Will::WILL, $professionCode) ? 1 : 0,
            $intelligence = $this->isPrimaryProperty(Intelligence::INTELLIGENCE, $professionCode) ? 1 : 0,
            $charisma = $this->isPrimaryProperty(Charisma::CHARISMA, $professionCode) ? 1 : 0
        );
        $this->addPrimaryPropertiesAnswer($professionLevel, $professionCode);
        $addProfessionLevel = 'add' . ucfirst($professionCode) . 'Level';
        $professionLevels->$addProfessionLevel($professionLevel);

        $this->assertSame($professionLevel, $professionLevels->getFirstLevel());
        $levelsGetter = 'get' . ucfirst($professionCode) . 'levels';
        $this->assertSame([$professionLevel], $professionLevels->$levelsGetter());
        $this->assertSame([$professionLevel], $professionLevels->getLevels());
        $this->assertEquals($levelRank, $professionLevels->getHighestLevelRank());

        $this->assertSame($strength, $professionLevels->getStrengthModifierSummary());
        $this->assertSame($agility, $professionLevels->getAgilityModifierSummary());
        $this->assertSame($knack, $professionLevels->getKnackModifierSummary());
        $this->assertSame($will, $professionLevels->getWillModifierSummary());
        $this->assertSame($intelligence, $professionLevels->getIntelligenceModifierSummary());
        $this->assertSame($charisma, $professionLevels->getCharismaModifierSummary());

        $this->assertSame($strength, $professionLevels->getStrengthModifierForFirstProfession());
        $this->assertSame($agility, $professionLevels->getAgilityModifierForFirstProfession());
        $this->assertSame($knack, $professionLevels->getKnackModifierForFirstProfession());
        $this->assertSame($will, $professionLevels->getWillModifierForFirstProfession());
        $this->assertSame($intelligence, $professionLevels->getIntelligenceModifierForFirstProfession());
        $this->assertSame($charisma, $professionLevels->getCharismaModifierForFirstProfession());

        $this->assertSame(0, $professionLevels->getNextLevelsStrengthModifier());
        $this->assertSame(0, $professionLevels->getNextLevelsAgilityModifier());
        $this->assertSame(0, $professionLevels->getNextLevelsKnackModifier());
        $this->assertSame(0, $professionLevels->getNextLevelsWillModifier());
        $this->assertSame(0, $professionLevels->getNextLevelsIntelligenceModifier());
        $this->assertSame(0, $professionLevels->getNextLevelsCharismaModifier());

        return $professionLevels;
    }

    private function getProfessionLevelClass($professionCode)
    {
        return '\DrdPlus\ProfessionLevels\\' . ucfirst($professionCode) . 'Level';
    }

    /*
     * MORE LEVELS
     */

    /**
     * @param string $professionCode
     * @return ProfessionLevels
     *
     * @test
     * @dataProvider provideProfessionCode
     */
    public function I_can_add_more_levels_of_same_profession($professionCode)
    {
        $firstLevel = $this->createFirstLevelProfession($professionCode);
        $this->addPrimaryPropertiesAnswer($firstLevel, $professionCode);
        $this->addFirstLevelAnswer($firstLevel, true);
        $this->addNextLevelAnswer($firstLevel, false);
        $this->addPropertyIncrementGetters(
            $firstLevel, $strength = 1, $agility = 2, $knack = 3, $will = 4, $intelligence = 5, $charisma = 6
        );
        $professionLevels = $this->createProfessionLevelsWith($professionCode, $firstLevel);

        $this->assertInstanceOf($this->geProfessionLevelClass($professionCode), $firstLevel);
        $this->assertSame(1, count($professionLevels->getLevels()));
        $this->assertSame([$firstLevel], $professionLevels->getLevels());
        $getProfessionLevels = 'get' . ucfirst($professionCode) . 'Levels';
        $this->assertSame([$firstLevel], $professionLevels->$getProfessionLevels());

        $propertiesSummary = $firstLevelProperties = [];
        foreach ($this->propertyCodes as $propertyName) {
            $firstLevelProperties[$propertyName] = $propertiesSummary[$propertyName] = $$propertyName;
        }
        $secondLevel = $this->createProfessionLevel($professionCode, 2);
        $this->addPropertyIncrementGetters(
            $secondLevel, $strength = 1, $agility = 2, $knack = 3, $will = 4, $intelligence = 5, $charisma = 6
        );
        $this->addPrimaryPropertiesAnswer($secondLevel, $professionCode);
        $this->addNextLevelAnswer($secondLevel, true);
        $nextLevelProperties = [];
        foreach ($this->propertyCodes as $propertyName) {
            $nextLevelProperties[$propertyName] = $$propertyName;
            $propertiesSummary[$propertyName] += $$propertyName;
        }
        $addProfessionLevel = 'add' . ucfirst($professionCode) . 'Level';
        $professionLevels->$addProfessionLevel($secondLevel);

        $thirdLevel = $this->createProfessionLevel($professionCode, 3);
        $this->addPropertyIncrementGetters(
            $thirdLevel,
            $strength = ($this->isPrimaryProperty(Strength::STRENGTH, $professionCode) ? 7 : 0),
            $agility = ($this->isPrimaryProperty(Agility::AGILITY, $professionCode) ? 8 : 0),
            $knack = ($this->isPrimaryProperty(Knack::KNACK, $professionCode) ? 9 : 0),
            $will = ($this->isPrimaryProperty(Will::WILL, $professionCode) ? 10 : 0),
            $intelligence = ($this->isPrimaryProperty(Intelligence::INTELLIGENCE, $professionCode) ? 11 : 0),
            $charisma = ($this->isPrimaryProperty(Charisma::CHARISMA, $professionCode) ? 12 : 0)
        );
        $this->addPrimaryPropertiesAnswer($thirdLevel, $professionCode);
        foreach ($this->propertyCodes as $propertyName) {
            $propertiesSummary[$propertyName] += $$propertyName;
            $nextLevelProperties[$propertyName] += $$propertyName;
        }
        $professionLevels->$addProfessionLevel($thirdLevel);

        $this->assertSame($firstLevel, $professionLevels->getFirstLevel(), 'After adding a new level the old one is no more the first.');
        $this->assertSame([$firstLevel, $secondLevel, $thirdLevel], $professionLevels->$getProfessionLevels());
        $this->assertSame([$firstLevel, $secondLevel, $thirdLevel], $professionLevels->getLevels());
        $this->assertSame([$secondLevel, $thirdLevel], $professionLevels->getNextLevels());

        $levelsArray = [];
        foreach ($professionLevels as $professionLevel) {
            $levelsArray[] = $professionLevel;
        }
        $this->assertEquals($professionLevels->getLevels(), $levelsArray);
        $this->assertSame($thirdLevel->getLevelRank(), $professionLevels->getHighestLevelRank());
        $this->assertEquals(count($levelsArray), $professionLevels->getHighestLevelRank()->getValue());

        $this->assertSame($propertiesSummary[Strength::STRENGTH], $professionLevels->getStrengthModifierSummary());
        $this->assertSame($propertiesSummary[Agility::AGILITY], $professionLevels->getAgilityModifierSummary());
        $this->assertSame($propertiesSummary[Knack::KNACK], $professionLevels->getKnackModifierSummary());
        $this->assertSame($propertiesSummary[Will::WILL], $professionLevels->getWillModifierSummary());
        $this->assertSame($propertiesSummary[Intelligence::INTELLIGENCE], $professionLevels->getIntelligenceModifierSummary());
        $this->assertSame($propertiesSummary[Charisma::CHARISMA], $professionLevels->getCharismaModifierSummary());

        $this->assertSame($firstLevelProperties[Strength::STRENGTH], $professionLevels->getStrengthModifierForFirstProfession());
        $this->assertSame($firstLevelProperties[Agility::AGILITY], $professionLevels->getAgilityModifierForFirstProfession());
        $this->assertSame($firstLevelProperties[Knack::KNACK], $professionLevels->getKnackModifierForFirstProfession());
        $this->assertSame($firstLevelProperties[Will::WILL], $professionLevels->getWillModifierForFirstProfession());
        $this->assertSame($firstLevelProperties[Intelligence::INTELLIGENCE], $professionLevels->getIntelligenceModifierForFirstProfession());
        $this->assertSame($firstLevelProperties[Charisma::CHARISMA], $professionLevels->getCharismaModifierForFirstProfession());

        $this->assertSame($nextLevelProperties[Strength::STRENGTH], $professionLevels->getNextLevelsStrengthModifier());
        $this->assertSame($nextLevelProperties[Agility::AGILITY], $professionLevels->getNextLevelsAgilityModifier());
        $this->assertSame($nextLevelProperties[Knack::KNACK], $professionLevels->getNextLevelsKnackModifier());
        $this->assertSame($nextLevelProperties[Will::WILL], $professionLevels->getNextLevelsWillModifier());
        $this->assertSame($nextLevelProperties[Intelligence::INTELLIGENCE], $professionLevels->getNextLevelsIntelligenceModifier());
        $this->assertSame($nextLevelProperties[Charisma::CHARISMA], $professionLevels->getNextLevelsCharismaModifier());

        return $professionLevels;
    }

    private function geProfessionLevelClass($professionCode)
    {
        $abstractClass = ProfessionLevel::class;

        return preg_replace('~ProfessionLevel$~', ucfirst($professionCode) . 'Level', $abstractClass);
    }

    /**
     * @param string $professionCode
     * @test
     * @dataProvider provideProfessionCode
     * @expectedException \LogicException
     */
    public function I_am_stopped_if_use_zero_level_rank($professionCode)
    {
        $professionLevels = new ProfessionLevels();
        /** @var \Mockery\MockInterface|ProfessionLevel $professionLevel */
        $professionLevel = $this->mockery($this->getProfessionLevelClass($professionCode));
        $professionLevel->shouldReceive('getProfession')
            ->andReturn($profession = $this->mockery(Profession::class));
        $profession->shouldReceive('getValue')
            ->andReturn($professionCode);
        $professionLevel->shouldReceive('getLevelRank')
            ->atLeast()->once()
            ->andReturn($levelRank = $this->mockery(LevelRank::class));
        $levelRank->shouldReceive('getValue')
            ->atLeast()->once()
            ->andReturn(1);
        $addProfessionLevel = 'add' . ucfirst($professionCode) . 'Level';
        $professionLevels->$addProfessionLevel($professionLevel);

        $this->missingLevelValueCauseException($professionLevels);
    }

    /**
     * @test
     * @dataProvider provideProfessionCode
     * @expectedException \LogicException
     * @param $professionCode
     */
    public function I_can_not_add_level_with_occupied_sequence($professionCode)
    {
        $professionLevels = $this->createProfessionLevelsForChangeResistTest($professionCode);

        $levelsCount = count($professionLevels->getLevels());
        $this->assertGreaterThan(1, $levelsCount /* already occupied level rank to achieve conflict */);

        $anotherLevel = $this->createProfessionLevel($professionCode, $levelsCount);

        $addProfessionLevel = 'add' . ucfirst($professionCode) . 'Level';
        $professionLevels->$addProfessionLevel($anotherLevel);
    }

    /**
     * @test
     * @expectedException \LogicException
     * @dataProvider provideProfessionCode
     * @param $professionCode
     */
    public function I_can_not_add_level_with_too_high_sequence($professionCode)
    {
        $professionLevels = $this->createProfessionLevelsForChangeResistTest($professionCode);
        $levelsCount = count($professionLevels->getLevels());
        $this->assertGreaterThan(1, $levelsCount);

        $professionCode = $professionLevels->getFirstLevel()->getProfession()->getValue();
        $anotherLevel = $this->createProfessionLevel($professionCode, $levelsCount + 2 /* skipping a rank by one */);

        $addProfessionLevel = 'add' . ucfirst($professionCode) . 'Level';
        $professionLevels->$addProfessionLevel($anotherLevel);
    }

    /**
     * @test
     * @expectedException \LogicException
     * @dataProvider provideProfessionCode
     * @param $professionCode
     */
    public function I_can_not_change_level_during_usage($professionCode)
    {
        $professionLevels = $this->createProfessionLevelsForChangeResistTest($professionCode);
        $levelsCount = count($professionLevels->getLevels());
        $this->assertGreaterThan(1, $levelsCount);

        /** @var FighterLevel|\Mockery\MockInterface $anotherLevel */
        $anotherLevel = $this->mockery($this->geProfessionLevelClass($professionCode));
        $anotherLevel->shouldReceive('getProfession')
            ->andReturn($profession = $this->mockery(Profession::class));
        $profession->shouldReceive('getValue')
            ->andReturn(Fighter::FIGHTER);
        $anotherLevel->shouldReceive('getLevelRank')
            ->andReturn($anotherLevelValue = $this->mockery(LevelRank::class));
        $rank = $levelsCount + 1;
        $anotherLevelValue->shouldReceive('getValue')
            ->andReturnUsing($rankGetter = function () use (&$rank) {
                return $rank;
            });

        $addProfessionLevel = 'add' . ucfirst($professionCode) . 'Level';
        $professionLevels->$addProfessionLevel($anotherLevel);
        /** @noinspection PhpUnusedLocalVariableInspection */
        $rank = 1; // changed rank to already occupied value (change propagated by reflection)

        $professionLevels->getFirstLevel();
    }

    private function createProfessionLevelsForChangeResistTest($professionCode)
    {
        $firstLevel = $this->createFirstLevelProfession($professionCode);
        $this->addPrimaryPropertiesAnswer($firstLevel, $professionCode);
        $this->addFirstLevelAnswer($firstLevel, true);
        $this->addNextLevelAnswer($firstLevel, false);
        $this->addPropertyIncrementGetters(
            $firstLevel, $strength = 1, $agility = 2, $knack = 3, $will = 4, $intelligence = 5, $charisma = 6
        );
        $professionLevels = $this->createProfessionLevelsWith($professionCode, $firstLevel);

        $this->assertInstanceOf($this->geProfessionLevelClass($professionCode), $firstLevel);
        $this->assertSame(1, count($professionLevels->getLevels()));
        $this->assertSame([$firstLevel], $professionLevels->getLevels());
        $getProfessionLevels = 'get' . ucfirst($professionCode) . 'Levels';
        $this->assertSame([$firstLevel], $professionLevels->$getProfessionLevels());

        $secondLevel = $this->createProfessionLevel($professionCode, 2);
        $this->addPropertyIncrementGetters(
            $secondLevel, $strength = 1, $agility = 2, $knack = 3, $will = 4, $intelligence = 5, $charisma = 6
        );
        $this->addPrimaryPropertiesAnswer($secondLevel, $professionCode);
        $this->addNextLevelAnswer($secondLevel, true);

        $addProfessionLevel = 'add' . ucfirst($professionCode) . 'Level';
        $professionLevels->$addProfessionLevel($secondLevel);

        $thirdLevel = $this->createProfessionLevel($professionCode, 3);
        $this->addPropertyIncrementGetters(
            $thirdLevel,
            $strength = ($this->isPrimaryProperty(Strength::STRENGTH, $professionCode) ? 7 : 0),
            $agility = ($this->isPrimaryProperty(Agility::AGILITY, $professionCode) ? 8 : 0),
            $knack = ($this->isPrimaryProperty(Knack::KNACK, $professionCode) ? 9 : 0),
            $will = ($this->isPrimaryProperty(Will::WILL, $professionCode) ? 10 : 0),
            $intelligence = ($this->isPrimaryProperty(Intelligence::INTELLIGENCE, $professionCode) ? 11 : 0),
            $charisma = ($this->isPrimaryProperty(Charisma::CHARISMA, $professionCode) ? 12 : 0)
        );
        $this->addPrimaryPropertiesAnswer($thirdLevel, $professionCode);
        $professionLevels->$addProfessionLevel($thirdLevel);

        return $professionLevels;
    }

    /**
     * @test
     * @expectedException \DrdPlus\ProfessionLevels\Exceptions\UnknownProfession
     */
    public function I_can_not_add_strange_profession()
    {
        $professionLevels = new ProfessionLevels();
        $strangeProfessionLevel = $this->mockery(ProfessionLevel::class);
        $profession = $this->mockery(Profession::class);
        $profession->shouldReceive('getValue')
            ->andReturn('Stranger in strange country');
        $strangeProfessionLevel->shouldReceive('getId')
            ->andReturn(null);
        $strangeProfessionLevel->shouldReceive('getProfession')
            ->andReturn($profession);

        /** @var ProfessionLevel $strangeProfessionLevel */
        $professionLevels->addLevel($strangeProfessionLevel);
    }

    /*
     * ONLY SINGLE PROFESSION IS ALLOWED
     */

    /**
     * @param string $mainProfessionCode
     * @test
     * @dataProvider provideProfessionCode
     */
    public function I_can_not_mix_professions($mainProfessionCode)
    {
        $professionLevels = $this->createProfessionLevelsForMixTest($mainProfessionCode);
        /** @var FighterLevel|\Mockery\MockInterface $firstLevel */
        $firstLevel = $professionLevels->getFirstLevel();
        $this->assertInstanceOf($this->getMultiProfessionTestLevelClass($mainProfessionCode), $firstLevel);

        $otherLevels = $this->getLevelsExcept($firstLevel);
        $this->assertNotEmpty($otherLevels);

        foreach ($otherLevels as $professionCode => $otherProfessionLevel) {
            $adder = 'add' . ucfirst($professionCode) . 'Level';
            try {
                $professionLevels->$adder($otherProfessionLevel);
                $this->fail(
                    "Adding $professionCode to levels already set to {$firstLevel->getProfession()->getValue()} should throw exception."
                );
            } catch (MultiProfessionsAreProhibited $exception) {
                $this->assertNotNull($exception);
            }
        }
    }

    private function createProfessionLevelsForMixTest($professionCode)
    {
        $professionLevels = new ProfessionLevels();
        /** @var \Mockery\MockInterface|ProfessionLevel $professionLevel */
        $professionLevel = $this->mockery($this->getProfessionLevelClass($professionCode));
        $professionLevel->shouldReceive('getProfession')
            ->andReturn($profession = $this->mockery(Profession::class));
        $profession->shouldReceive('getValue')
            ->andReturn($professionCode);
        $professionLevel->shouldReceive('getLevelRank')
            ->atLeast()->once()
            ->andReturn($levelRank = $this->mockery(LevelRank::class));
        $levelRank->shouldReceive('getValue')
            ->atLeast()->once()
            ->andReturn(1);
        $this->addPropertyIncrementGetters(
            $professionLevel,
            $strength = $this->isPrimaryProperty(Strength::STRENGTH, $professionCode) ? 1 : 0,
            $agility = $this->isPrimaryProperty(Agility::AGILITY, $professionCode) ? 1 : 0,
            $knack = $this->isPrimaryProperty(Knack::KNACK, $professionCode) ? 1 : 0,
            $will = $this->isPrimaryProperty(Will::WILL, $professionCode) ? 1 : 0,
            $intelligence = $this->isPrimaryProperty(Intelligence::INTELLIGENCE, $professionCode) ? 1 : 0,
            $charisma = $this->isPrimaryProperty(Charisma::CHARISMA, $professionCode) ? 1 : 0
        );
        $this->addPrimaryPropertiesAnswer($professionLevel, $professionCode);
        $addProfessionLevel = 'add' . ucfirst($professionCode) . 'Level';
        $professionLevels->$addProfessionLevel($professionLevel);

        return $professionLevels;
    }

    private function getMultiProfessionTestLevelClass($professionCode)
    {
        return '\DrdPlus\ProfessionLevels\\' . ucfirst($professionCode) . 'Level';
    }

    /**
     * @param ProfessionLevel $excludedProfession
     *
     * @return \Mockery\MockInterface[]|ProfessionLevel[]
     */
    private function getLevelsExcept(ProfessionLevel $excludedProfession)
    {
        $professionLevels = $this->buildProfessionLevels();

        return array_filter(
            $professionLevels,
            function (ProfessionLevel $level) use ($excludedProfession) {
                return $level->getProfession()->getValue() !== $excludedProfession->getProfession()->getValue();
            }
        );
    }

    private function buildProfessionLevels()
    {
        $professionLevels[$professionCode = Fighter::FIGHTER] = $level = $this->mockery(FighterLevel::class);
        $profession = $this->mockery(Fighter::class);
        $profession->shouldReceive('getValue')
            ->andReturn($professionCode);
        $level->shouldReceive('getProfession')
            ->andReturn($profession);

        $professionLevels[$professionCode = Priest::PRIEST] = $level = $this->mockery(PriestLevel::class);
        $profession = $this->mockery(Priest::class);
        $profession->shouldReceive('getValue')
            ->andReturn($professionCode);
        $level->shouldReceive('getProfession')
            ->andReturn($profession);

        $professionLevels[$professionCode = Ranger::RANGER] = $level = $this->mockery(RangerLevel::class);
        $profession = $this->mockery(Ranger::class);
        $profession->shouldReceive('getValue')
            ->andReturn($professionCode);
        $level->shouldReceive('getProfession')
            ->andReturn($profession);

        $professionLevels[$professionCode = Theurgist::THEURGIST] = $level = $this->mockery(TheurgistLevel::class);
        $profession = $this->mockery(Theurgist::class);
        $profession->shouldReceive('getValue')
            ->andReturn($professionCode);
        $level->shouldReceive('getProfession')
            ->andReturn($profession);

        $professionLevels[$professionCode = Thief::THIEF] = $level = $this->mockery(ThiefLevel::class);
        $profession = $this->mockery(Thief::class);
        $profession->shouldReceive('getValue')
            ->andReturn($professionCode);
        $level->shouldReceive('getProfession')
            ->andReturn($profession);

        $professionLevels[$professionCode = Wizard::WIZARD] = $level = $this->mockery(WizardLevel::class);
        $profession = $this->mockery(Wizard::class);
        $profession->shouldReceive('getValue')
            ->andReturn($professionCode);
        $level->shouldReceive('getProfession')
            ->andReturn($profession);

        return $professionLevels;
    }

    /*
     * SAME PROPERTY INCREMENT IN A ROW
     */

    /**
     * @param string $mainProfessionCode
     * @test
     * @dataProvider provideProfessionCode
     * @expectedException \DrdPlus\ProfessionLevels\Exceptions\TooHighPrimaryPropertyIncrease
     */
    public function I_can_not_increase_primary_property_three_times_in_a_row($mainProfessionCode)
    {
        $professionLevelsArray = $this->createProfessionLevelsForPrimaryPropertyThreeTimesTest($mainProfessionCode);
        $professionLevels = new ProfessionLevels();
        foreach ($professionLevelsArray as $professionLevel) {
            $professionLevels->addLevel($professionLevel);
        }
    }

    private function createProfessionLevelsForPrimaryPropertyThreeTimesTest($professionCode)
    {
        $professionLevelsArray = [];
        foreach ([1, 2, 3, 4] as $levelValue) {
            $professionLevel = $this->createProfessionLevel($professionCode, $levelValue);
            $this->addPropertyIncrementGetters(
                $professionLevel,
                $strength = $this->isPrimaryProperty(Strength::STRENGTH, $professionCode) ? 1 : 0,
                $agility = $this->isPrimaryProperty(Agility::AGILITY, $professionCode) ? 1 : 0,
                $knack = $this->isPrimaryProperty(Knack::KNACK, $professionCode) ? 1 : 0,
                $will = $this->isPrimaryProperty(Will::WILL, $professionCode) ? 1 : 0,
                $intelligence = $this->isPrimaryProperty(Intelligence::INTELLIGENCE, $professionCode) ? 1 : 0,
                $charisma = $this->isPrimaryProperty(Charisma::CHARISMA, $professionCode) ? 1 : 0
            );
            $this->addPrimaryPropertiesAnswer($professionLevel, $professionCode);
            $professionLevelsArray[] = $professionLevel;
        }

        return $professionLevelsArray;
    }
}
