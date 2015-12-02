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

    private $propertyNames = [
        Strength::STRENGTH, Agility::AGILITY, Knack::KNACK, Will::WILL, Intelligence::INTELLIGENCE, Charisma::CHARISMA
    ];

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

        $this->assertSame(0, $professionLevels->getAgilityModifierForFirstProfession());
        $this->assertSame(0, $professionLevels->getPropertyModifierForFirstProfession(Agility::AGILITY));
        $this->assertSame(0, $professionLevels->getAgilityModifierSummary());

        $this->assertSame(0, $professionLevels->getKnackModifierForFirstProfession());
        $this->assertSame(0, $professionLevels->getPropertyModifierForFirstProfession(Knack::KNACK));
        $this->assertSame(0, $professionLevels->getKnackModifierSummary());

        $this->assertSame(0, $professionLevels->getWillModifierForFirstProfession());
        $this->assertSame(0, $professionLevels->getPropertyModifierForFirstProfession(Will::WILL));
        $this->assertSame(0, $professionLevels->getWillModifierSummary());

        $this->assertSame(0, $professionLevels->getIntelligenceModifierForFirstProfession());
        $this->assertSame(0, $professionLevels->getPropertyModifierForFirstProfession(Intelligence::INTELLIGENCE));
        $this->assertSame(0, $professionLevels->getIntelligenceModifierSummary());

        $this->assertSame(0, $professionLevels->getCharismaModifierForFirstProfession());
        $this->assertSame(0, $professionLevels->getPropertyModifierForFirstProfession(Charisma::CHARISMA));
        $this->assertSame(0, $professionLevels->getCharismaModifierSummary());

        $this->assertSame([], $professionLevels->getLevels());

        $this->assertFalse($professionLevels->getFirstLevel());

        foreach ($this->getProfessionCodes() as $professionCode) {
            $getProfessionLevels = 'get' . ucfirst($professionCode) . 'Levels';
            $levels = $professionLevels->$getProfessionLevels();
            $this->assertInternalType('array', $levels);
            $this->assertEmpty($levels);
        }
    }

    private function getProfessionCodes()
    {
        return [
            Fighter::FIGHTER, Wizard::WIZARD, Priest::PRIEST,
            Theurgist::THEURGIST, Thief::THIEF, Ranger::RANGER
        ];
    }

    /*
     * FIRST LEVELS
     */

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
        $professionLevel->shouldReceive('getBasePropertyIncrement')
            ->with(Strength::STRENGTH)
            ->andReturn($strength);
        $this->addValueGetter($strength, $strengthValue);
        $this->addNameGetter($strength, Strength::STRENGTH);
        $professionLevel->shouldReceive('getAgilityIncrement')
            ->atLeast()->once()
            ->andReturn($agility = $this->mockery(Agility::class));
        $professionLevel->shouldReceive('getBasePropertyIncrement')
            ->with(Agility::AGILITY)
            ->andReturn($agility);
        $this->addValueGetter($agility, $agilityValue);
        $this->addNameGetter($agility, Agility::AGILITY);
        $professionLevel->shouldReceive('getKnackIncrement')
            ->atLeast()->once()
            ->andReturn($knack = $this->mockery(Knack::class));
        $professionLevel->shouldReceive('getBasePropertyIncrement')
            ->with(Knack::KNACK)
            ->andReturn($knack);
        $this->addValueGetter($knack, $knackValue);
        $this->addNameGetter($knack, Knack::KNACK);
        $professionLevel->shouldReceive('getWillIncrement')
            ->atLeast()->once()
            ->andReturn($will = $this->mockery(Will::class));
        $professionLevel->shouldReceive('getBasePropertyIncrement')
            ->with(Will::WILL)
            ->andReturn($will);
        $this->addValueGetter($will, $willValue);
        $this->addNameGetter($will, Will::WILL);
        $professionLevel->shouldReceive('getIntelligenceIncrement')
            ->atLeast()->once()
            ->andReturn($intelligence = $this->mockery(Intelligence::class));
        $professionLevel->shouldReceive('getBasePropertyIncrement')
            ->with(Intelligence::INTELLIGENCE)
            ->andReturn($intelligence);
        $this->addValueGetter($intelligence, $intelligenceValue);
        $this->addNameGetter($intelligence, Intelligence::INTELLIGENCE);
        $professionLevel->shouldReceive('getCharismaIncrement')
            ->atLeast()->once()
            ->andReturn($charisma = $this->mockery(Charisma::class));
        $professionLevel->shouldReceive('getBasePropertyIncrement')
            ->with(Charisma::CHARISMA)
            ->andReturn($charisma);
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
            ->andReturn($profession = $this->mockery(Profession::class));
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
            ->andReturn($profession = $this->mockery(Profession::class));
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
     */
    public function I_can_add_fighter_level()
    {
        return $this->levelCanBeAdded(Fighter::FIGHTER);
    }

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
            ->andReturn($profession = $this->mockery(Profession::class));
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
        return '\DrdPlus\ProfessionLevels\\' . ucfirst($professionName) . 'Level';
    }

    /**
     * @test
     */
    public function I_can_add_priest_level()
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
     * @depends I_can_add_priest_level
     * @expectedException \LogicException
     */
    public function priest_missing_level_value_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->missingLevelValueCauseException(Priest::PRIEST, $professionLevels);
    }

    /**
     * @test
     * @depends I_can_create_it
     */
    public function I_can_add_ranger_level()
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
     * @depends I_can_add_ranger_level
     * @expectedException \LogicException
     */
    public function ranger_missing_level_value_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->missingLevelValueCauseException(Ranger::RANGER, $professionLevels);
    }

    /**
     * @test
     * @depends I_can_create_it
     */
    public function I_can_add_theurgist_level()
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
     * @depends I_can_add_theurgist_level
     * @expectedException \LogicException
     */
    public function theurgist_missing_level_value_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->missingLevelValueCauseException(Theurgist::THEURGIST, $professionLevels);
    }

    /**
     * @test
     * @depends I_can_create_it
     */
    public function I_can_add_thief_level()
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
     * @depends I_can_add_thief_level
     * @expectedException \LogicException
     */
    public function thief_missing_level_value_cause_exception(ProfessionLevels $professionLevels)
    {
        $this->missingLevelValueCauseException(Thief::THIEF, $professionLevels);
    }

    /**
     * @test
     * @depends I_can_create_it
     */
    public function I_can_add_wizard_level()
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
     * @depends I_can_add_wizard_level
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
     * @depends I_can_add_fighter_level
     * @test
     */
    public function I_can_add_more_fighter_levels()
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
     * @depends I_can_add_fighter_level
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
     * @depends I_can_add_more_fighter_levels
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
     * @depends I_can_add_more_fighter_levels
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
     * @depends I_can_add_more_fighter_levels
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
            ->andReturn($profession = $this->mockery(Profession::class));
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
     * @depends I_can_add_priest_level
     */
    public function I_can_add_more_priest_levels()
    {
        return $this->levelsCanBeAdded(Priest::PRIEST);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends I_can_add_more_priest_levels
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
     * @depends I_can_add_more_priest_levels
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
     * @depends I_can_add_more_priest_levels
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
     * @depends I_can_add_ranger_level
     */
    public function I_can_add_more_ranger_levels()
    {
        return $this->levelsCanBeAdded(Ranger::RANGER);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends I_can_add_more_ranger_levels
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
     * @depends I_can_add_more_ranger_levels
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
     * @depends I_can_add_more_ranger_levels
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
     * @depends I_can_add_theurgist_level
     */
    public function I_can_add_more_theurgist_levels()
    {
        return $this->levelsCanBeAdded(Theurgist::THEURGIST);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends I_can_add_more_theurgist_levels
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
     * @depends I_can_add_more_theurgist_levels
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
     * @depends I_can_add_more_theurgist_levels
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
     * @depends I_can_add_thief_level
     */
    public function I_can_add_more_thief_levels()
    {
        return $this->levelsCanBeAdded(Thief::THIEF);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends I_can_add_more_thief_levels
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
     * @depends I_can_add_more_thief_levels
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
     * @depends I_can_add_more_thief_levels
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
     * @depends I_can_add_wizard_level
     */
    public function I_can_add_more_wizard_levels()
    {
        return $this->levelsCanBeAdded(Wizard::WIZARD);
    }

    /**
     * @param ProfessionLevels $professionLevels
     *
     * @test
     * @depends I_can_add_more_wizard_levels
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
     * @depends I_can_add_more_wizard_levels
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
     * @depends I_can_add_more_wizard_levels
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
     * @test
     */
    public function I_can_not_mix_other_professions_with_fighter()
    {
        $this->otherProfessionsCauseException(Fighter::FIGHTER);
    }

    private function otherProfessionsCauseException($mainProfessionCode)
    {
        $professionLevels = $this->levelCanBeAdded($mainProfessionCode);
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
                    "Adding $professionCode to levels already set to {$firstLevel->getProfession()->getCode()} should throw exception."
                );
            } catch (MultiProfessionsAreProhibited $exception) {
                $this->assertNotNull($exception);
            }
        }
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
                return $level->getProfession()->getCode() !== $excludedProfession->getProfession()->getCode();
            }
        );
    }

    private function buildProfessionLevels()
    {
        $professionLevels[$professionCode = Fighter::FIGHTER] = $level = $this->mockery(FighterLevel::class);
        $profession = $this->mockery(Fighter::class);
        $profession->shouldReceive('getCode') // mock of static method
            ->andReturn($professionCode);
        $level->shouldReceive('getProfession')
            ->andReturn($profession);

        $professionLevels[$professionCode = Priest::PRIEST] = $level = $this->mockery(PriestLevel::class);
        $profession = $this->mockery(Priest::class);
        $profession->shouldReceive('getCode') // mock of static method
            ->andReturn($professionCode);
        $level->shouldReceive('getProfession')
            ->andReturn($profession);

        $professionLevels[$professionCode = Ranger::RANGER] = $level = $this->mockery(RangerLevel::class);
        $profession = $this->mockery(Ranger::class);
        $profession->shouldReceive('getCode') // mock of static method
            ->andReturn($professionCode);
        $level->shouldReceive('getProfession')
            ->andReturn($profession);

        $professionLevels[$professionCode = Theurgist::THEURGIST] = $level = $this->mockery(TheurgistLevel::class);
        $profession = $this->mockery(Theurgist::class);
        $profession->shouldReceive('getCode') // mock of static method
            ->andReturn($professionCode);
        $level->shouldReceive('getProfession')
            ->andReturn($profession);

        $professionLevels[$professionCode = Thief::THIEF] = $level = $this->mockery(ThiefLevel::class);
        $profession = $this->mockery(Thief::class);
        $profession->shouldReceive('getCode') // mock of static method
            ->andReturn($professionCode);
        $level->shouldReceive('getProfession')
            ->andReturn($profession);

        $professionLevels[$professionCode = Wizard::WIZARD] = $level = $this->mockery(WizardLevel::class);
        $profession = $this->mockery(Wizard::class);
        $profession->shouldReceive('getCode') // mock of static method
            ->andReturn($professionCode);
        $level->shouldReceive('getProfession')
            ->andReturn($profession);

        return $professionLevels;
    }

    /**
     * @test
     * @depends I_can_add_priest_level
     */
    public function I_can_not_mix_other_professions_with_priest()
    {
        $this->otherProfessionsCauseException(Priest::PRIEST);
    }

    /**
     * @test
     * @depends I_can_add_ranger_level
     */
    public function I_can_not_mix_other_professions_with_ranger()
    {
        $this->otherProfessionsCauseException(Ranger::RANGER);
    }

    /**
     * @test
     * @depends I_can_add_theurgist_level
     */
    public function I_can_not_mix_other_professions_with_theurgist()
    {
        $this->otherProfessionsCauseException(Theurgist::THEURGIST);
    }

    /**
     * @test
     * @depends I_can_add_thief_level
     */
    public function I_can_not_mix_other_professions_with_thief()
    {
        $this->otherProfessionsCauseException(Thief::THIEF);
    }

    /**
     * @test
     * @depends I_can_add_wizard_level
     */
    public function I_can_not_mix_other_professions_with_wizard()
    {
        $this->otherProfessionsCauseException(Wizard::WIZARD);
    }
}
