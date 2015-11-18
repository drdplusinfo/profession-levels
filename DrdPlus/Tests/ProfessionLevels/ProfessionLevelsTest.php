<?php
namespace DrdPlus\ProfessionLevels;

use DrdPlus\Properties\Base\Agility;
use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Intelligence;
use DrdPlus\Properties\Base\Knack;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Properties\Base\Will;
use \DrdPlus\Professions\Fighter;
use \DrdPlus\Professions\Priest;
use \DrdPlus\Professions\AbstractProfession;
use \DrdPlus\Professions\Ranger;
use \DrdPlus\Professions\Theurgist;
use \DrdPlus\Professions\Thief;
use \DrdPlus\Professions\Wizard;
use DrdPlus\Tools\Tests\TestWithMockery;
use Mockery\MockInterface;

class ProfessionLevelsTest extends TestWithMockery
{

    private $propertyNames = [Strength::STRENGTH, Agility::AGILITY, Knack::KNACK, Will::WILL, Intelligence::INTELLIGENCE, Charisma::CHARISMA];

    /**
     * @return ProfessionLevels
     *
     * @test
     */
    public function can_create_instance()
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
     * @depends can_create_instance
     */
    public function new_levels_gives_zero_first_level_strength_increment(ProfessionLevels $professionLevels)
    {
        /** @var ProfessionLevelsTest $this */
        $this->assertSame(0, $professionLevels->getStrengthModifierForFirstProfession());
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends can_create_instance
     */
    public function new_levels_gives_zero_strength_increment_summary(ProfessionLevels $professionLevels)
    {
        /** @var ProfessionLevelsTest $this */
        $this->assertSame(0, $professionLevels->getStrengthModifierSummary());
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends can_create_instance
     */
    public function new_levels_gives_zero_first_level_agility_increment(ProfessionLevels $professionLevels)
    {
        /** @var ProfessionLevelsTest $this */
        $this->assertSame(0, $professionLevels->getAgilityModifierForFirstProfession());
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends can_create_instance
     */
    public function new_levels_gives_zero_agility_increment_summary(ProfessionLevels $professionLevels)
    {
        /** @var ProfessionLevelsTest $this */
        $this->assertSame(0, $professionLevels->getAgilityModifierSummary());
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends can_create_instance
     */
    public function new_levels_gives_zero_first_level_knack_increment(ProfessionLevels $professionLevels)
    {
        /** @var ProfessionLevelsTest $this */
        $this->assertSame(0, $professionLevels->getKnackModifierForFirstProfession());
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends can_create_instance
     */
    public function new_levels_gives_zero_knack_increment_summary(ProfessionLevels $professionLevels)
    {
        /** @var ProfessionLevelsTest $this */
        $this->assertSame(0, $professionLevels->getKnackModifierSummary());
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends can_create_instance
     */
    public function new_levels_gives_zero_first_level_will_increment(ProfessionLevels $professionLevels)
    {
        /** @var ProfessionLevelsTest $this */
        $this->assertSame(0, $professionLevels->getWillModifierForFirstProfession());
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends can_create_instance
     */
    public function new_levels_gives_zero_will_increment_summary(ProfessionLevels $professionLevels)
    {
        /** @var ProfessionLevelsTest $this */
        $this->assertSame(0, $professionLevels->getWillModifierSummary());
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends can_create_instance
     */
    public function new_levels_gives_zero_first_level_intelligence_increment(ProfessionLevels $professionLevels)
    {
        /** @var ProfessionLevelsTest $this */
        $this->assertSame(0, $professionLevels->getIntelligenceModifierForFirstProfession());
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends can_create_instance
     */
    public function new_levels_gives_zero_intelligence_increment_summary(ProfessionLevels $professionLevels)
    {
        /** @var ProfessionLevelsTest $this */
        $this->assertSame(0, $professionLevels->getIntelligenceModifierSummary());
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends can_create_instance
     */
    public function new_levels_gives_zero_first_level_charisma_increment(ProfessionLevels $professionLevels)
    {
        /** @var ProfessionLevelsTest $this */
        $this->assertSame(0, $professionLevels->getCharismaModifierForFirstProfession());
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends can_create_instance
     */
    public function new_levels_gives_zero_charisma_increment_summary(ProfessionLevels $professionLevels)
    {
        /** @var ProfessionLevelsTest $this */
        $this->assertSame(0, $professionLevels->getCharismaModifierSummary());
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends can_create_instance
     */
    public function new_levels_gives_empty_array_as_levels(ProfessionLevels $professionLevels)
    {
        /** @var ProfessionLevelsTest $this */
        $this->assertSame([], $professionLevels->getLevels());
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends can_create_instance
     */
    public function new_levels_gives_false_as_first_level(ProfessionLevels $professionLevels)
    {
        /** @var ProfessionLevelsTest $this */
        $this->assertFalse($professionLevels->getFirstLevel());
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends can_create_instance
     */
    public function new_levels_gives_empty_array_as_fighter_levels(ProfessionLevels $professionLevels)
    {
        $this->newLevelsGivesEmptyArrayAsSpecificProfessionLevels($professionLevels, Fighter::FIGHTER);
    }

    private function newLevelsGivesEmptyArrayAsSpecificProfessionLevels(ProfessionLevels $professionLevels, $professionName)
    {
        $getProfessionLevels = 'get' . ucfirst($professionName) . 'Levels';
        $levels = $professionLevels->$getProfessionLevels();
        $this->assertInternalType('array', $levels);
        $this->isEmpty($levels);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends can_create_instance
     */
    public function new_levels_gives_empty_array_as_priest_levels(ProfessionLevels $professionLevels)
    {
        $this->newLevelsGivesEmptyArrayAsSpecificProfessionLevels($professionLevels, Priest::PRIEST);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends can_create_instance
     */
    public function new_levels_gives_empty_array_as_ranger_levels(ProfessionLevels $professionLevels)
    {
        $this->newLevelsGivesEmptyArrayAsSpecificProfessionLevels($professionLevels, Ranger::RANGER);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends can_create_instance
     */
    public function new_levels_gives_empty_array_as_theurgist_levels(ProfessionLevels $professionLevels)
    {
        $this->newLevelsGivesEmptyArrayAsSpecificProfessionLevels($professionLevels, Theurgist::THEURGIST);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends can_create_instance
     */
    public function new_levels_gives_empty_array_as_thief_levels(ProfessionLevels $professionLevels)
    {
        $this->newLevelsGivesEmptyArrayAsSpecificProfessionLevels($professionLevels, Thief::THIEF);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends can_create_instance
     */
    public function new_levels_gives_empty_array_as_wizard_levels(ProfessionLevels $professionLevels)
    {
        $this->newLevelsGivesEmptyArrayAsSpecificProfessionLevels($professionLevels, Wizard::WIZARD);
    }

    /*
     * FIRST LEVELS
     */

    /**
     * @param string $professionName
     *
     * @return ProfessionLevels
     */
    private function levelCanBeAdded($professionName)
    {
        $professionLevels = new ProfessionLevels();
        /** @var \Mockery\MockInterface|ProfessionLevel $professionLevel */
        $professionLevel = $this->mockery($this->getFirstLevelsProfessionLevelClass($professionName));
        $professionLevel->shouldReceive('getProfession')
            ->andReturn($profession = $this->mockery(AbstractProfession::class));
        $profession->shouldReceive('getCode')
            ->andReturn($professionName);
        $professionLevel->shouldReceive('getLevelRank')
            ->atLeast()->once()
            ->andReturn($levelRank = $this->mockery(LevelRank::class));
        $levelRank->shouldReceive('getValue')
            ->atLeast()->once()
            ->andReturn(1);
        $this->addPropertyIncrementGetters($professionLevel);
        $addProfessionLevel = 'add' . ucfirst($professionName) . 'Level';
        $professionLevels->$addProfessionLevel($professionLevel);
        $this->assertSame($professionLevel, $professionLevels->getFirstLevel());
        $levelsGetter = 'get' . ucfirst($professionName) . 'levels';
        $this->assertSame([$professionLevel], $professionLevels->$levelsGetter());
        $this->assertSame([$professionLevel], $professionLevels->getLevels());

        return $professionLevels;
    }

    private function getFirstLevelsProfessionLevelClass($professionName)
    {
        return '\DrdPlus\ProfessionLevels\\'
        . ucfirst($professionName) . 'Level';
    }

    private function addFirstLevelPropertyIncrementGetters(MockInterface $professionLevel, $professionName)
    {
        $modifiers = [];
        foreach ($this->propertyNames as $propertyName) {
            $modifiers[$propertyName] = $this->isPrimaryProperty($propertyName, $professionName) ? 1 : 0;
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
            ->atLeast()->once()
            ->andReturn($strength = $this->mockery(Strength::class));
        $this->addValueGetter($strength, $strengthValue);
        $this->addNameGetter($strength, Strength::STRENGTH);
        $professionLevel->shouldReceive('getAgilityIncrement')
            ->atLeast()->once()
            ->andReturn($agility = $this->mockery(Agility::class));
        $this->addValueGetter($agility, $agilityValue);
        $this->addNameGetter($agility, Agility::AGILITY);
        $professionLevel->shouldReceive('getKnackIncrement')
            ->atLeast()->once()
            ->andReturn($knack = $this->mockery(Knack::class));
        $this->addValueGetter($knack, $knackValue);
        $this->addNameGetter($knack, Knack::KNACK);
        $professionLevel->shouldReceive('getWillIncrement')
            ->atLeast()->once()
            ->andReturn($will = $this->mockery(Will::class));
        $this->addValueGetter($will, $willValue);
        $this->addNameGetter($will, Will::WILL);
        $professionLevel->shouldReceive('getIntelligenceIncrement')
            ->atLeast()->once()
            ->andReturn($intelligence = $this->mockery(Intelligence::class));
        $this->addValueGetter($intelligence, $intelligenceValue);
        $this->addNameGetter($intelligence, Intelligence::INTELLIGENCE);
        $professionLevel->shouldReceive('getCharismaIncrement')
            ->atLeast()->once()
            ->andReturn($charisma = $this->mockery(Charisma::class));
        $this->addValueGetter($charisma, $charismaValue);
        $this->addNameGetter($charisma, Charisma::CHARISMA);
    }

    private function addValueGetter(MockInterface $property, $value)
    {
        $property->shouldReceive('getValue')
            ->atLeast()->once()
            ->andReturn($value);
    }

    private function addNameGetter(MockInterface $property, $name)
    {
        $property->shouldReceive('getCode')
            ->andReturn($name);
    }

    private function addPrimaryPropertiesAnswer(MockInterface $professionLevel, $professionName)
    {
        $modifiers = [];
        foreach ($this->propertyNames as $propertyName) {
            $modifiers[$propertyName] = $this->isPrimaryProperty($propertyName, $professionName) ? 1 : 0;
        }
        $primaryProperties = array_keys(array_filter($modifiers));

        foreach ($this->propertyNames as $propertyName) {
            $professionLevel->shouldReceive('isPrimaryProperty')
                ->with($propertyName)
                ->andReturn(in_array($propertyName, $primaryProperties));
        }
    }

    private function addFirstLevelAnswer(MockInterface $professionLevel, $isFirstLevel)
    {
        $professionLevel->shouldReceive('isFirstLevel')
            ->atLeast()->once()
            ->andReturn($isFirstLevel);
    }

    private function addNextLevelAnswer(MockInterface $professionLevel, $isNextLevel)
    {
        $professionLevel->shouldReceive('isNextLevel')
            ->atLeast()->once()
            ->andReturn($isNextLevel);
    }

    /**
     * @test
     *
     * @return ProfessionLevels
     */
    public function fighter_at_first_level_has_strength_and_agility_increment()
    {
        return $this->askFirstLevelForPrimaryPropertiesIncrement(Fighter::FIGHTER);
    }

    private function askFirstLevelForPrimaryPropertiesIncrement($professionName)
    {
        $firstLevel = $this->createFirstLevelProfession($professionName);
        $this->assertInstanceOf($this->getFirstLevelsProfessionLevelClass($professionName), $firstLevel);
        $this->addFirstLevelPropertyIncrementGetters($firstLevel, $professionName);
        $this->addPrimaryPropertiesAnswer($firstLevel, $professionName);
        $professionLevels = $this->createProfessionLevelsWith($professionName, $firstLevel);
        $this->assertSame(
            $this->isPrimaryProperty(Strength::STRENGTH, $professionName) ? 1 : 0,
            $professionLevels->getStrengthModifierForFirstProfession()
        );
        $this->assertSame(
            $this->isPrimaryProperty(Agility::AGILITY, $professionName) ? 1 : 0,
            $professionLevels->getAgilityModifierForFirstProfession()
        );
        $this->assertSame(
            $this->isPrimaryProperty(Knack::KNACK, $professionName) ? 1 : 0,
            $professionLevels->getKnackModifierForFirstProfession()
        );
        $this->assertSame(
            $this->isPrimaryProperty(Will::WILL, $professionName) ? 1 : 0,
            $professionLevels->getWillModifierForFirstProfession()
        );
        $this->assertSame(
            $this->isPrimaryProperty(Intelligence::INTELLIGENCE, $professionName) ? 1 : 0,
            $professionLevels->getIntelligenceModifierForFirstProfession()
        );
        $this->assertSame(
            $this->isPrimaryProperty(Charisma::CHARISMA, $professionName) ? 1 : 0,
            $professionLevels->getCharismaModifierForFirstProfession()
        );

        return $professionLevels;
    }

    /**
     * @param string $professionName
     *
     * @return ProfessionLevel|\Mockery\MockInterface
     */
    private function createFirstLevelProfession($professionName)
    {
        return $this->createProfessionLevel($professionName, 1);
    }

    private function createProfessionLevel($professionName, $levelValue)
    {
        /** @var \Mockery\MockInterface|ProfessionLevel $professionLevel */
        $professionLevel = $this->mockery($this->getFirstLevelsProfessionLevelClass($professionName));
        $professionLevel->shouldReceive('getProfession')
            ->andReturn($profession = $this->mockery(AbstractProfession::class));
        $profession->shouldReceive('getCode')
            ->andReturn($professionName);
        $professionLevel->shouldReceive('getLevelRank')
            ->atLeast()->once()
            ->andReturn($levelRank = $this->mockery(LevelRank::class));
        $levelRank->shouldReceive('getValue')
            ->atLeast()->once()
            ->andReturn($levelValue);

        return $professionLevel;
    }

    /**
     * @param string $propertyName
     * @param string $professionName
     *
     * @return bool
     */
    public function isPrimaryProperty($propertyName, $professionName)
    {
        return in_array($propertyName, $this->getPrimaryProperties($professionName));
    }

    private function getPrimaryProperties($professionName)
    {
        switch ($professionName) {
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
                throw new \RuntimeException('Unknown profession name ' . var_export($professionName, true));
        }
    }

    /**
     * @param string $professionName
     * @param ProfessionLevel $professionLevel
     *
     * @return ProfessionLevels
     */
    private function createProfessionLevelsWith($professionName, ProfessionLevel $professionLevel)
    {
        $professionLevels = new ProfessionLevels();
        $addProfessionLevel = 'add' . ucfirst($professionName) . 'Level';
        $professionLevels->$addProfessionLevel($professionLevel);
        $this->assertSame($professionLevel, $professionLevels->getFirstLevel());
        $levelsGetter = 'get' . ucfirst($professionName) . 'levels';
        $this->assertSame([$professionLevel], $professionLevels->$levelsGetter());
        $this->assertSame([$professionLevel], $professionLevels->getLevels());

        return $professionLevels;
    }

    /**
     * @param string $professionName
     * @param ProfessionLevels $professionLevels
     *
     * @return ProfessionLevels
     */
    private function missingLevelValueCauseException($professionName, ProfessionLevels $professionLevels)
    {
        /** @var \Mockery\MockInterface|ProfessionLevel $professionLevel */
        $professionLevel = $this->mockery($this->getFirstLevelsProfessionLevelClass($professionName));
        $professionLevel->shouldReceive('getProfession')
            ->andReturn($profession = $this->mockery(AbstractProfession::class));
        $profession->shouldReceive('getCode')
            ->andReturn($professionName);
        $professionLevel->shouldReceive('getLevelRank')
            ->atLeast()->once()
            ->andReturn($levelRank = $this->mockery(LevelRank::class));
        $levelRank->shouldReceive('getValue')
            ->atLeast()->once()
            ->andReturn(0);
        $professionLevel->shouldReceive('getId')
            ->andReturn('foo');
        $adder = 'add' . ucfirst($professionName) . 'Level';
        $professionLevels->$adder($professionLevel);
    }

    /**
     * @test
     * @depends can_create_instance
     */
    public function priest_level_can_be_added()
    {
        return $this->levelCanBeAdded(Priest::PRIEST);
    }

    /**
     * @test
     */
    public function priest_at_first_level_has_will_and_charisma_increment()
    {
        $this->askFirstLevelForPrimaryPropertiesIncrement(Priest::PRIEST);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends priest_level_can_be_added
     * @expectedException \LogicException
     */
    public function priest_missing_level_value_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->missingLevelValueCauseException(Priest::PRIEST, $professionLevels);
    }

    /**
     * @test
     * @depends can_create_instance
     */
    public function ranger_level_can_be_added()
    {
        return $this->levelCanBeAdded(Ranger::RANGER);
    }

    /**
     * @test
     */
    public function ranger_at_first_level_has_strength_and_knack_increment()
    {
        $this->askFirstLevelForPrimaryPropertiesIncrement(Ranger::RANGER);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends ranger_level_can_be_added
     * @expectedException \LogicException
     */
    public function ranger_missing_level_value_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->missingLevelValueCauseException(Ranger::RANGER, $professionLevels);
    }

    /**
     * @test
     * @depends can_create_instance
     */
    public function theurgist_level_can_be_added()
    {
        return $this->levelCanBeAdded(Theurgist::THEURGIST);
    }

    /**
     * @test
     */
    public function theurgist_at_first_level_has_intelligence_and_charisma_increment()
    {
        $this->askFirstLevelForPrimaryPropertiesIncrement(Theurgist::THEURGIST);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends theurgist_level_can_be_added
     * @expectedException \LogicException
     */
    public function theurgist_missing_level_value_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->missingLevelValueCauseException(Theurgist::THEURGIST, $professionLevels);
    }

    /**
     * @test
     * @depends can_create_instance
     */
    public function thief_level_can_be_added()
    {
        return $this->levelCanBeAdded(Thief::THIEF);
    }

    /**
     * @test
     */
    public function thief_at_first_level_has_agility_and_knack_increment()
    {
        $this->askFirstLevelForPrimaryPropertiesIncrement(Thief::THIEF);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends thief_level_can_be_added
     * @expectedException \LogicException
     */
    public function thief_missing_level_value_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->missingLevelValueCauseException(Thief::THIEF, $professionLevels);
    }

    /**
     * @test
     * @depends can_create_instance
     */
    public function wizard_level_can_be_added()
    {
        return $this->levelCanBeAdded(Wizard::WIZARD);
    }

    /**
     * @test
     */
    public function wizard_at_first_level_has_will_and_intelligence_increment()
    {
        $this->askFirstLevelForPrimaryPropertiesIncrement(Wizard::WIZARD);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends wizard_level_can_be_added
     * @expectedException \LogicException
     */
    public function wizard_missing_level_value_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->missingLevelValueCauseException(Wizard::WIZARD, $professionLevels);
    }

    /*
     * MORE LEVELS
     */


    /**
     * @return ProfessionLevels
     *
     * @test
     */
    public function fighter_levels_can_be_added()
    {
        return $this->levelsCanBeAdded(Fighter::FIGHTER);
    }

    private function levelsCanBeAdded($professionName)
    {
        $firstLevel = $this->createFirstLevelProfession($professionName);
        $this->addFirstLevelPropertyIncrementGetters($firstLevel, $professionName);
        $this->addPrimaryPropertiesAnswer($firstLevel, $professionName);
        $this->addFirstLevelAnswer($firstLevel, true);
        $this->addNextLevelAnswer($firstLevel, false);
        $professionLevels = $this->createProfessionLevelsWith($professionName, $firstLevel);

        $this->assertInstanceOf($this->geProfessionLevelClass($professionName), $firstLevel);
        $this->assertSame(1, count($professionLevels->getLevels()));
        $this->assertSame([$firstLevel], $professionLevels->getLevels());
        $getProfessionLevels = 'get' . ucfirst($professionName) . 'Levels';
        $this->assertSame([$firstLevel], $professionLevels->$getProfessionLevels());

        $propertiesSummary = [];
        foreach ($this->propertyNames as $propertyName) {
            $propertiesSummary[$propertyName] = 0;
        }
        $secondLevel = $this->createProfessionLevel($professionName, 2);
        $this->addPropertyIncrementGetters($secondLevel, $strength = 1, $agility = 2, $knack = 3, $will = 4, $intelligence = 5, $charisma = 6);
        $this->addPrimaryPropertiesAnswer($secondLevel, $professionName);
        $this->addNextLevelAnswer($secondLevel, true);
        foreach ($this->propertyNames as $propertyName) {
            $propertiesSummary[$propertyName] += $$propertyName;
        }
        $addProfessionLevel = 'add' . ucfirst($professionName) . 'Level';
        $professionLevels->$addProfessionLevel($secondLevel);

        $thirdLevel = $this->createProfessionLevel($professionName, 3);
        $this->addPropertyIncrementGetters(
            $thirdLevel,
            $strength = ($this->isPrimaryProperty(Strength::STRENGTH, $professionName) ? 7 : 0),
            $agility = ($this->isPrimaryProperty(Agility::AGILITY, $professionName) ? 8 : 0),
            $knack = ($this->isPrimaryProperty(Knack::KNACK, $professionName) ? 9 : 0),
            $will = ($this->isPrimaryProperty(Will::WILL, $professionName) ? 10 : 0),
            $intelligence = ($this->isPrimaryProperty(Intelligence::INTELLIGENCE, $professionName) ? 11 : 0),
            $charisma = ($this->isPrimaryProperty(Charisma::CHARISMA, $professionName) ? 12 : 0)
        );
        $this->addPrimaryPropertiesAnswer($thirdLevel, $professionName);
        foreach ($this->propertyNames as $propertyName) {
            $propertiesSummary[$propertyName] += $$propertyName;
        }
        $professionLevels->$addProfessionLevel($thirdLevel);

        $this->assertSame($firstLevel, $professionLevels->getFirstLevel(), 'After adding a new level the old one is no more the first.');
        $this->assertSame([$firstLevel, $secondLevel, $thirdLevel], $professionLevels->$getProfessionLevels());
        $this->assertSame([$firstLevel, $secondLevel, $thirdLevel], $professionLevels->getLevels());
        $this->assertSame([$secondLevel, $thirdLevel], $professionLevels->getNextLevels());

        foreach ($this->propertyNames as $propertyName) {
            $getPropertyModifierSummary = 'get' . ucfirst($propertyName) . 'ModifierSummary';
            $this->assertSame(
                ($this->isPrimaryProperty($propertyName, $professionName) ? 1 : 0) + $propertiesSummary[$propertyName],
                $professionLevels->$getPropertyModifierSummary(),
                "The modifier summary of property $propertyName should be "
                . (($this->isPrimaryProperty($propertyName, $professionName) ? 1 : 0) + $propertiesSummary[$propertyName])
                . " for $professionName, got " . $professionLevels->$getPropertyModifierSummary()
            );
        }

        return $professionLevels;
    }

    private function geProfessionLevelClass($professionName)
    {
        $abstractClass = ProfessionLevel::class;

        return preg_replace('~ProfessionLevel$~', ucfirst($professionName) . 'Level', $abstractClass);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends fighter_levels_can_be_added
     * @expectedException \LogicException
     */
    public function fighter_missing_level_value_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->missingLevelValueCauseException(Fighter::FIGHTER, $professionLevels);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends fighter_levels_can_be_added
     * @expectedException \LogicException
     */
    public function adding_fighter_level_with_occupied_sequence_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->addingLevelWithOccupiedSequenceCauseException(Fighter::FIGHTER, $professionLevels);
    }

    private function addingLevelWithOccupiedSequenceCauseException(
        $professionName,
        ProfessionLevels $professionLevels
    )
    {
        $levelsCount = count($professionLevels->getLevels());
        $this->assertGreaterThan(1, $levelsCount /* already occupied level rank to achieve conflict */);

        $anotherLevel = $this->createProfessionLevel($professionName, $levelsCount);

        $addProfessionLevel = 'add' . ucfirst($professionName) . 'Level';
        $professionLevels->$addProfessionLevel($anotherLevel);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends fighter_levels_can_be_added
     * @expectedException \LogicException
     */
    public function adding_fighter_level_with_too_high_sequence_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->addingLevelWithTooHighSequenceCauseException(Fighter::FIGHTER, $professionLevels);
    }

    private function addingLevelWithTooHighSequenceCauseException(
        $professionName,
        ProfessionLevels $professionLevels
    )
    {
        $levelsCount = count($professionLevels->getLevels());
        $this->assertGreaterThan(1, $levelsCount);

        $anotherLevel = $this->createProfessionLevel($professionName, $levelsCount + 2 /* skipping a rank by one */);

        $addProfessionLevel = 'add' . ucfirst($professionName) . 'Level';
        $professionLevels->$addProfessionLevel($anotherLevel);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends fighter_levels_can_be_added
     * @expectedException \LogicException
     */
    public function changed_fighter_level_during_usage_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->changedLevelDuringUsageCauseException(Fighter::FIGHTER, $professionLevels);
    }

    private function changedLevelDuringUsageCauseException(
        $professionName,
        ProfessionLevels $professionLevels
    )
    {
        $levelsCount = count($professionLevels->getLevels());
        $this->assertGreaterThan(1, $levelsCount);

        /** @var FighterLevel|\Mockery\MockInterface $anotherLevel */
        $anotherLevel = $this->mockery($this->geProfessionLevelClass($professionName));
        $anotherLevel->shouldReceive('getProfession')
            ->andReturn($profession = $this->mockery(AbstractProfession::class));
        $profession->shouldReceive('getCode')
            ->andReturn(Fighter::FIGHTER);
        $anotherLevel->shouldReceive('getLevelRank')
            ->andReturn($anotherLevelValue = $this->mockery(LevelRank::class));
        $rank = $levelsCount + 1;
        $anotherLevelValue->shouldReceive('getValue')
            ->andReturnUsing($rankGetter = function () use (&$rank) {
                return $rank;
            });

        $addProfessionLevel = 'add' . ucfirst($professionName) . 'Level';
        $professionLevels->$addProfessionLevel($anotherLevel);
        /** @noinspection PhpUnusedLocalVariableInspection */
        $rank = 1; // changed rank to already occupied value (change propagated by reflection)

        $professionLevels->getFirstLevel();
    }

    /**
     * @return ProfessionLevels
     *
     * @test
     * @depends priest_level_can_be_added
     */
    public function priest_levels_can_be_added()
    {
        return $this->levelsCanBeAdded(Priest::PRIEST);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends priest_levels_can_be_added
     * @expectedException \LogicException
     */
    public function adding_priest_level_with_occupied_sequence_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->addingLevelWithOccupiedSequenceCauseException(Priest::PRIEST, $professionLevels);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends priest_levels_can_be_added
     * @expectedException \LogicException
     */
    public function adding_priest_level_with_too_high_sequence_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->addingLevelWithTooHighSequenceCauseException(Priest::PRIEST, $professionLevels);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends priest_levels_can_be_added
     * @expectedException \LogicException
     */
    public function changed_priest_level_during_usage_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->changedLevelDuringUsageCauseException(Priest::PRIEST, $professionLevels);
    }

    /**
     * @return ProfessionLevels
     *
     * @test
     * @depends ranger_level_can_be_added
     */
    public function ranger_levels_can_be_added()
    {
        return $this->levelsCanBeAdded(Ranger::RANGER);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends ranger_levels_can_be_added
     * @expectedException \LogicException
     */
    public function adding_ranger_level_with_occupied_sequence_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->addingLevelWithOccupiedSequenceCauseException(Ranger::RANGER, $professionLevels);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends ranger_levels_can_be_added
     * @expectedException \LogicException
     */
    public function adding_ranger_level_with_too_high_sequence_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->addingLevelWithTooHighSequenceCauseException(Ranger::RANGER, $professionLevels);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends ranger_levels_can_be_added
     * @expectedException \LogicException
     */
    public function changed_ranger_level_during_usage_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->changedLevelDuringUsageCauseException(Ranger::RANGER, $professionLevels);
    }

    /**
     * @return ProfessionLevels
     *
     * @test
     * @depends theurgist_level_can_be_added
     */
    public function theurgist_levels_can_be_added()
    {
        return $this->levelsCanBeAdded(Theurgist::THEURGIST);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends theurgist_levels_can_be_added
     * @expectedException \LogicException
     */
    public function adding_theurgist_level_with_occupied_sequence_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->addingLevelWithOccupiedSequenceCauseException(Theurgist::THEURGIST, $professionLevels);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends theurgist_levels_can_be_added
     * @expectedException \LogicException
     */
    public function adding_theurgist_level_with_too_high_sequence_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->addingLevelWithTooHighSequenceCauseException(Theurgist::THEURGIST, $professionLevels);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends theurgist_levels_can_be_added
     * @expectedException \LogicException
     */
    public function changed_theurgist_level_during_usage_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->changedLevelDuringUsageCauseException(Theurgist::THEURGIST, $professionLevels);
    }

    /**
     * @return ProfessionLevels
     *
     * @test
     * @depends thief_level_can_be_added
     */
    public function thief_levels_can_be_added()
    {
        return $this->levelsCanBeAdded(Thief::THIEF);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends thief_levels_can_be_added
     * @expectedException \LogicException
     */
    public function adding_thief_level_with_occupied_sequence_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->addingLevelWithOccupiedSequenceCauseException(Thief::THIEF, $professionLevels);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends thief_levels_can_be_added
     * @expectedException \LogicException
     */
    public function adding_thief_level_with_too_high_sequence_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->addingLevelWithTooHighSequenceCauseException(Thief::THIEF, $professionLevels);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends thief_levels_can_be_added
     * @expectedException \LogicException
     */
    public function changed_thief_level_during_usage_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->changedLevelDuringUsageCauseException(Thief::THIEF, $professionLevels);
    }

    /**
     * @return ProfessionLevels
     *
     * @test
     * @depends wizard_level_can_be_added
     */
    public function wizard_levels_can_be_added()
    {
        return $this->levelsCanBeAdded(Wizard::WIZARD);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends wizard_levels_can_be_added
     * @expectedException \LogicException
     */
    public function adding_wizard_level_with_occupied_sequence_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->addingLevelWithOccupiedSequenceCauseException(Wizard::WIZARD, $professionLevels);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends wizard_levels_can_be_added
     * @expectedException \LogicException
     */
    public function adding_wizard_level_with_too_high_sequence_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->addingLevelWithTooHighSequenceCauseException(Wizard::WIZARD, $professionLevels);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends wizard_levels_can_be_added
     * @expectedException \LogicException
     */
    public function changed_wizard_level_during_usage_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->changedLevelDuringUsageCauseException(Wizard::WIZARD, $professionLevels);
    }

    /*
     * ONLY SINGLE PROFESSION IS ALLOWED
     */


    /**
     * @var \Mockery\MockInterface[]|ProfessionLevel[]|array $levels
     */
    private $professionLevels = [];

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends fighter_levels_can_be_added
     * @expectedException \LogicException
     */
    public function other_professions_to_fighter_levels_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->otherProfessionsCauseException(Fighter::FIGHTER, $professionLevels);
    }

    private function otherProfessionsCauseException(
        $professionName,
        ProfessionLevels $professionLevels
    )
    {
        /** @var FighterLevel|\Mockery\MockInterface $firstLevel */
        $firstLevel = $professionLevels->getFirstLevel();
        $this->assertInstanceOf($this->getMultiProfessionTestLevelClass($professionName), $firstLevel);

        $exception = new \Exception('No other professions than ' . $firstLevel->getProfession()->getCode() . '?');
        foreach ($this->getLevelsExcept($firstLevel) as $professionCode => $otherProfessionLevel) {
            $adder = 'add' . ucfirst($professionCode) . 'Level';
            try {
                $professionLevels->$adder($otherProfessionLevel);
                $this->fail(
                    "Adding $professionCode to levels already set to {$firstLevel->getProfession()->getCode()} should throw exception."
                );
            } catch (\LogicException $exception) {
                $this->assertNotNull($exception);
            }
        }

        throw $exception;
    }

    private function getMultiProfessionTestLevelClass($professionName)
    {
        return '\DrdPlus\ProfessionLevels\\'
        . ucfirst($professionName) . 'Level';
    }

    /**
     * @param ProfessionLevel $excludedProfession
     *
     * @return \Mockery\MockInterface[]|ProfessionLevel[]
     */
    private function getLevelsExcept(ProfessionLevel $excludedProfession)
    {
        if (empty($this->professionLevels)) {
            $this->buildProfessionLevels();
        }

        return array_filter(
            $this->professionLevels,
            function (ProfessionLevel $level) use ($excludedProfession) {
                return $level->getProfession()->getCode() !== $excludedProfession->getProfession()->getCode();
            }
        );
    }

    private function buildProfessionLevels()
    {
        $this->professionLevels[Fighter::FIGHTER] = $this->mockery(FighterLevel::class);
        $this->professionLevels[Priest::PRIEST] = $this->mockery(PriestLevel::class);
        $this->professionLevels[Ranger::RANGER] = $this->mockery(RangerLevel::class);
        $this->professionLevels[Theurgist::THEURGIST] = $this->mockery(TheurgistLevel::class);
        $this->professionLevels[Thief::THIEF] = $this->mockery(ThiefLevel::class);
        $this->professionLevels[Wizard::WIZARD] = $this->mockery(WizardLevel::class);
        foreach ($this->professionLevels as $professionCode => $level) {
            $level->shouldReceive('getProfession')
                ->andReturn($profession = $this->mockery(AbstractProfession::class));
            $profession->shouldReceive('getCode')
                ->andReturn($professionCode);
        }
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends priest_level_can_be_added
     * @expectedException \LogicException
     */
    public function other_professions_to_priest_levels_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->otherProfessionsCauseException(Priest::PRIEST, $professionLevels);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends ranger_level_can_be_added
     * @expectedException \LogicException
     */
    public function other_professions_to_ranger_levels_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->otherProfessionsCauseException(Ranger::RANGER, $professionLevels);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends theurgist_level_can_be_added
     * @expectedException \LogicException
     */
    public function other_professions_to_theurgist_levels_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->otherProfessionsCauseException(Theurgist::THEURGIST, $professionLevels);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends thief_level_can_be_added
     * @expectedException \LogicException
     */
    public function other_professions_to_thief_levels_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->otherProfessionsCauseException(Thief::THIEF, $professionLevels);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends wizard_level_can_be_added
     * @expectedException \LogicException
     */
    public function other_professions_to_wizard_levels_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->otherProfessionsCauseException(Wizard::WIZARD, $professionLevels);
    }

}
