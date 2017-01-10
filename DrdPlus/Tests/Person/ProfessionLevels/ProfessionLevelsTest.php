<?php
namespace DrdPlus\Tests\Person\ProfessionLevels;

use DrdPlus\Codes\ProfessionCode;
use DrdPlus\Codes\PropertyCode;
use DrdPlus\Person\ProfessionLevels\Exceptions\MultiProfessionsAreProhibited;
use DrdPlus\Person\ProfessionLevels\LevelRank;
use DrdPlus\Person\ProfessionLevels\ProfessionFirstLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionLevels;
use DrdPlus\Person\ProfessionLevels\ProfessionNextLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionZeroLevel;
use DrdPlus\Properties\Base\Agility;
use DrdPlus\Properties\Base\BaseProperty;
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
use Granam\Tests\Tools\TestWithMockery;
use Mockery\MockInterface;

class ProfessionLevelsTest extends TestWithMockery
{

    /**
     * @test
     */
    public function I_can_create_it()
    {
        $zeroLevel = $this->createZeroLevel();
        $firstLevel = $this->createFirstLevel('fighter');
        $withFirstLevelOnly = new ProfessionLevels($zeroLevel, $firstLevel);
        self::assertNotNull($withFirstLevelOnly);

        $anotherInstance = ProfessionLevels::createIt($zeroLevel, $firstLevel);
        self::assertEquals($withFirstLevelOnly, $anotherInstance);

        $yetAnotherInstance = ProfessionLevels::createIt($zeroLevel, $firstLevel);
        self::assertNotSame($anotherInstance, $yetAnotherInstance);

        $withExplicitlyEmptyNextLevels = ProfessionLevels::createIt($zeroLevel, $firstLevel, []);
        self::assertEquals($withFirstLevelOnly, $withExplicitlyEmptyNextLevels);

        $withNextLevels = ProfessionLevels::createIt(
            $zeroLevel,
            $firstLevel,
            $nextLevels = [
                ProfessionNextLevel::createNextLevel(
                    Fighter::getIt(),
                    LevelRank::getIt(2),
                    Strength::getIt(1),
                    Agility::getIt(0),
                    Knack::getIt(0),
                    Will::getIt(0),
                    Intelligence::getIt(1),
                    Charisma::getIt(0)
                ),
            ]
        );
        self::assertNotEquals($withFirstLevelOnly, $withNextLevels);
    }

    /**
     * @return MockInterface|ProfessionZeroLevel
     */
    private function createZeroLevel()
    {
        $professionZeroLevel = $this->mockery(ProfessionZeroLevel::class);
        $professionZeroLevel->shouldReceive('getBasePropertyIncrement')
            ->with($this->type(PropertyCode::class))
            ->andReturn($baseProperty = $this->mockery(BaseProperty::class));
        $baseProperty->shouldReceive('getValue')
            ->andReturn(0);

        return $professionZeroLevel;
    }

    /**
     * @param string $professionCode
     * @return ProfessionFirstLevel|\Mockery\MockInterface
     */
    private function createFirstLevel($professionCode)
    {
        $firstLevel = $this->mockery(ProfessionFirstLevel::class);
        $this->addProfessionGetter($firstLevel, $professionCode);
        $firstLevel->shouldReceive('getLevelRank')
            ->andReturn($levelRank = $this->mockery(LevelRank::class));
        $levelRank->shouldReceive('getValue')
            ->andReturn(1);
        $this->addPropertyIncrementGetters(
            $firstLevel,
            $strength = $this->isPrimaryProperty(Strength::STRENGTH, $professionCode) ? 1 : 0,
            $agility = $this->isPrimaryProperty(Agility::AGILITY, $professionCode) ? 1 : 0,
            $knack = $this->isPrimaryProperty(Knack::KNACK, $professionCode) ? 1 : 0,
            $will = $this->isPrimaryProperty(Will::WILL, $professionCode) ? 1 : 0,
            $intelligence = $this->isPrimaryProperty(Intelligence::INTELLIGENCE, $professionCode) ? 1 : 0,
            $charisma = $this->isPrimaryProperty(Charisma::CHARISMA, $professionCode) ? 1 : 0
        );
        $this->addPrimaryPropertiesAnswer($firstLevel, $professionCode);

        return $firstLevel;
    }

    private function addProfessionGetter(MockInterface $professionLevel, $professionCode)
    {
        $professionLevel->shouldReceive('getProfession')
            ->andReturn($profession = $this->mockery(Profession::class));
        $profession->shouldReceive('getValue')
            ->andReturn($professionCode);
    }

    /*
     * EMPTY AFTER INITIALIZATION
     */

    /**
     * @test
     */
    public function I_got_everything_empty_or_zeroed_from_empty_new_levels()
    {
        $professionLevels = new ProfessionLevels(
            $zeroLevel = $this->createZeroLevel(),
            $firstLevel = $this->createFirstLevel(Fighter::FIGHTER)
        );

        self::assertSame(0, $professionLevels->getNextLevelsStrengthModifier());
        self::assertSame(0, $professionLevels->getNextLevelsPropertyModifier(PropertyCode::getIt(PropertyCode::STRENGTH)));
        self::assertSame(0, $professionLevels->getNextLevelsAgilityModifier());
        self::assertSame(0, $professionLevels->getNextLevelsPropertyModifier(PropertyCode::getIt(PropertyCode::AGILITY)));
        self::assertSame(0, $professionLevels->getNextLevelsKnackModifier());
        self::assertSame(0, $professionLevels->getNextLevelsPropertyModifier(PropertyCode::getIt(PropertyCode::KNACK)));
        self::assertSame(0, $professionLevels->getNextLevelsWillModifier());
        self::assertSame(0, $professionLevels->getNextLevelsPropertyModifier(PropertyCode::getIt(PropertyCode::WILL)));
        self::assertSame(0, $professionLevels->getNextLevelsIntelligenceModifier());
        self::assertSame(0, $professionLevels->getNextLevelsPropertyModifier(PropertyCode::getIt(PropertyCode::INTELLIGENCE)));
        self::assertSame(0, $professionLevels->getNextLevelsCharismaModifier());
        self::assertSame(0, $professionLevels->getNextLevelsPropertyModifier(PropertyCode::getIt(PropertyCode::CHARISMA)));

        self::assertCount(0, $professionLevels->getProfessionNextLevels());
        self::assertEquals([$zeroLevel, $firstLevel], $professionLevels->getSortedProfessionLevels());
        $levelsFromIteration = [];
        foreach ($professionLevels as $professionLevel) {
            $levelsFromIteration[] = $professionLevel;
        }
        self::assertEquals($levelsFromIteration, $professionLevels->getSortedProfessionLevels());
        self::assertNull($professionLevels->getId());
    }

    /*
     * FIRST LEVELS
     */

    /**
     * @test
     */
    public function I_will_get_proper_value_of_first_level_properties()
    {
        $firstLevel = $this->createProfessionFirstLevel(Fighter::FIGHTER);
        $this->addFirstLevelPropertyIncrementGetters($firstLevel, Fighter::FIGHTER);
        $this->addPrimaryPropertiesAnswer($firstLevel, Fighter::FIGHTER);
        $zeroLevel = $this->createZeroLevel();
        $professionLevels = $this->createProfessionLevelsWith($zeroLevel, $firstLevel);
        self::assertSame(
            $this->isPrimaryProperty(Strength::STRENGTH, Fighter::FIGHTER) ? 1 : 0,
            $professionLevels->getFirstLevelStrengthModifier()
        );
        self::assertSame(
            $this->isPrimaryProperty(Agility::AGILITY, Fighter::FIGHTER) ? 1 : 0,
            $professionLevels->getFirstLevelAgilityModifier()
        );
        self::assertSame(
            $this->isPrimaryProperty(Knack::KNACK, Fighter::FIGHTER) ? 1 : 0,
            $professionLevels->getFirstLevelKnackModifier()
        );
        self::assertSame(
            $this->isPrimaryProperty(Will::WILL, Fighter::FIGHTER) ? 1 : 0,
            $professionLevels->getFirstLevelWillModifier()
        );
        self::assertSame(
            $this->isPrimaryProperty(Intelligence::INTELLIGENCE, Fighter::FIGHTER) ? 1 : 0,
            $professionLevels->getFirstLevelIntelligenceModifier()
        );
        self::assertSame(
            $this->isPrimaryProperty(Charisma::CHARISMA, Fighter::FIGHTER) ? 1 : 0,
            $professionLevels->getFirstLevelCharismaModifier()
        );
    }

    private function addFirstLevelPropertyIncrementGetters(MockInterface $professionLevel, $professionCode)
    {
        $modifiers = [];
        foreach ($this->getPropertyCodes() as $propertyName) {
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
            ->with(PropertyCode::getIt(PropertyCode::STRENGTH))
            ->andReturn($strength);
        $this->addValueGetter($strength, $strengthValue);
        $this->addCodeGetter($strength, Strength::STRENGTH);
        $professionLevel->shouldReceive('getAgilityIncrement')
            ->andReturn($agility = $this->mockery(Agility::class));
        $professionLevel->shouldReceive('getBasePropertyIncrement')
            ->with(PropertyCode::getIt(PropertyCode::AGILITY))
            ->andReturn($agility);
        $this->addValueGetter($agility, $agilityValue);
        $this->addCodeGetter($agility, Agility::AGILITY);
        $professionLevel->shouldReceive('getKnackIncrement')
            ->andReturn($knack = $this->mockery(Knack::class));
        $professionLevel->shouldReceive('getBasePropertyIncrement')
            ->with(PropertyCode::getIt(PropertyCode::KNACK))
            ->andReturn($knack);
        $this->addValueGetter($knack, $knackValue);
        $this->addCodeGetter($knack, Knack::KNACK);
        $professionLevel->shouldReceive('getWillIncrement')
            ->andReturn($will = $this->mockery(Will::class));
        $professionLevel->shouldReceive('getBasePropertyIncrement')
            ->with(PropertyCode::getIt(PropertyCode::WILL))
            ->andReturn($will);
        $this->addValueGetter($will, $willValue);
        $this->addCodeGetter($will, Will::WILL);
        $professionLevel->shouldReceive('getIntelligenceIncrement')
            ->andReturn($intelligence = $this->mockery(Intelligence::class));
        $professionLevel->shouldReceive('getBasePropertyIncrement')
            ->with(PropertyCode::getIt(PropertyCode::INTELLIGENCE))
            ->andReturn($intelligence);
        $this->addValueGetter($intelligence, $intelligenceValue);
        $this->addCodeGetter($intelligence, Intelligence::INTELLIGENCE);
        $professionLevel->shouldReceive('getCharismaIncrement')
            ->andReturn($charisma = $this->mockery(Charisma::class));
        $professionLevel->shouldReceive('getBasePropertyIncrement')
            ->with(PropertyCode::getIt(PropertyCode::CHARISMA))
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

    private function addPrimaryPropertiesAnswer(MockInterface $professionLevel, $professionCode)
    {
        $modifiers = [];
        foreach ($this->getPropertyCodes() as $propertyName) {
            $modifiers[$propertyName] = $this->isPrimaryProperty($propertyName, $professionCode) ? 1 : 0;
        }
        $primaryProperties = array_keys(array_filter($modifiers));

        foreach ($this->getPropertyCodes() as $propertyName) {
            $professionLevel->shouldReceive('isPrimaryProperty')
                ->with($propertyName)
                ->andReturn(in_array($propertyName, $primaryProperties, true));
        }
    }

    private function getPropertyCodes()
    {
        return [
            Strength::STRENGTH, Agility::AGILITY, Knack::KNACK,
            Will::WILL, Intelligence::INTELLIGENCE, Charisma::CHARISMA,
        ];
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

    /**
     * @param string $professionCode
     *
     * @return ProfessionFirstLevel|ProfessionNextLevel|\Mockery\MockInterface
     */
    private function createProfessionFirstLevel($professionCode)
    {
        return $this->createProfessionLevel($professionCode, 1);
    }

    /**
     * @param string $professionCode
     * @param int $levelValue
     * @param ProfessionLevels|null $professionLevels
     * @return ProfessionFirstLevel|ProfessionNextLevel|MockInterface
     */
    private function createProfessionLevel($professionCode, $levelValue, ProfessionLevels $professionLevels = null)
    {
        /** @var \Mockery\MockInterface|ProfessionLevel $professionLevel */
        $professionLevel = $this->mockery($this->getProfessionLevelClass($levelValue));
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
        if ($professionLevels) {
            $professionLevel->shouldReceive('setProfessionLevels')
                ->with($professionLevels)
                ->atMost()
                ->once();
        }

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
        return in_array($propertyName, $this->getPrimaryProperties($professionCode), true);
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
     * @param ProfessionZeroLevel $zeroLevel
     * @param ProfessionFirstLevel $firstLevel
     *
     * @return ProfessionLevels
     */
    private function createProfessionLevelsWith(ProfessionZeroLevel $zeroLevel, ProfessionFirstLevel $firstLevel)
    {
        $professionLevels = new ProfessionLevels($zeroLevel, $firstLevel);
        self::assertSame($firstLevel, $professionLevels->getFirstLevel());
        self::assertEquals([$zeroLevel, $firstLevel], $professionLevels->getSortedProfessionLevels());

        return $professionLevels;
    }

    /**
     * @test
     */
    public function I_can_add_profession_level()
    {
        $professionLevels = new ProfessionLevels(
            $zeroLevel = $this->createZeroLevel(),
            $firstLevel = $this->createFirstLevel(Fighter::FIGHTER)
        );
        $nextLevel = $this->createProfessionLevel(Fighter::FIGHTER, $levelValue = 2, $professionLevels);
        $this->addPropertyIncrementGetters(
            $nextLevel,
            $strength = $this->isPrimaryProperty(Strength::STRENGTH, Fighter::FIGHTER) ? 1 : 0,
            $agility = $this->isPrimaryProperty(Agility::AGILITY, Fighter::FIGHTER) ? 1 : 0,
            $knack = $this->isPrimaryProperty(Knack::KNACK, Fighter::FIGHTER) ? 1 : 0,
            $will = $this->isPrimaryProperty(Will::WILL, Fighter::FIGHTER) ? 1 : 0,
            $intelligence = $this->isPrimaryProperty(Intelligence::INTELLIGENCE, Fighter::FIGHTER) ? 1 : 0,
            $charisma = $this->isPrimaryProperty(Charisma::CHARISMA, Fighter::FIGHTER) ? 1 : 0
        );
        $this->addPrimaryPropertiesAnswer($nextLevel, Fighter::FIGHTER);
        $professionLevels->addLevel($nextLevel);

        $strength += $firstLevel->getStrengthIncrement()->getValue();
        $agility += $firstLevel->getAgilityIncrement()->getValue();
        $knack += $firstLevel->getKnackIncrement()->getValue();
        $will += $firstLevel->getWillIncrement()->getValue();
        $intelligence += $firstLevel->getIntelligenceIncrement()->getValue();
        $charisma += $firstLevel->getCharismaIncrement()->getValue();

        self::assertSame($zeroLevel, $professionLevels->getZeroLevel());
        self::assertSame($firstLevel, $professionLevels->getFirstLevel());
        self::assertSame([$nextLevel], $professionLevels->getProfessionNextLevels()->toArray());
        self::assertEquals([$zeroLevel, $firstLevel, $nextLevel], $professionLevels->getSortedProfessionLevels());
        self::assertEquals($nextLevel, $professionLevels->getCurrentLevel());

        self::assertSame($strength, $professionLevels->getStrengthModifierSummary());
        self::assertSame($strength, $professionLevels->getPropertyModifierSummary(PropertyCode::getIt(PropertyCode::STRENGTH)));
        self::assertSame($agility, $professionLevels->getAgilityModifierSummary());
        self::assertSame($agility, $professionLevels->getPropertyModifierSummary(PropertyCode::getIt(PropertyCode::AGILITY)));
        self::assertSame($knack, $professionLevels->getKnackModifierSummary());
        self::assertSame($knack, $professionLevels->getPropertyModifierSummary(PropertyCode::getIt(PropertyCode::KNACK)));
        self::assertSame($will, $professionLevels->getWillModifierSummary());
        self::assertSame($will, $professionLevels->getPropertyModifierSummary(PropertyCode::getIt(PropertyCode::WILL)));
        self::assertSame($intelligence, $professionLevels->getIntelligenceModifierSummary());
        self::assertSame($intelligence, $professionLevels->getPropertyModifierSummary(PropertyCode::getIt(PropertyCode::INTELLIGENCE)));
        self::assertSame($charisma, $professionLevels->getCharismaModifierSummary());
        self::assertSame($charisma, $professionLevels->getPropertyModifierSummary(PropertyCode::getIt(PropertyCode::CHARISMA)));

        self::assertSame($firstLevel->getStrengthIncrement()->getValue(), $professionLevels->getFirstLevelStrengthModifier());
        self::assertSame($firstLevel->getAgilityIncrement()->getValue(), $professionLevels->getFirstLevelAgilityModifier());
        self::assertSame($firstLevel->getKnackIncrement()->getValue(), $professionLevels->getFirstLevelKnackModifier());
        self::assertSame($firstLevel->getWillIncrement()->getValue(), $professionLevels->getFirstLevelWillModifier());
        self::assertSame($firstLevel->getIntelligenceIncrement()->getValue(), $professionLevels->getFirstLevelIntelligenceModifier());
        self::assertSame($firstLevel->getCharismaIncrement()->getValue(), $professionLevels->getFirstLevelCharismaModifier());

        self::assertSame($nextLevel->getStrengthIncrement()->getValue(), $professionLevels->getNextLevelsStrengthModifier());
        self::assertSame($nextLevel->getAgilityIncrement()->getValue(), $professionLevels->getNextLevelsAgilityModifier());
        self::assertSame($nextLevel->getKnackIncrement()->getValue(), $professionLevels->getNextLevelsKnackModifier());
        self::assertSame($nextLevel->getWillIncrement()->getValue(), $professionLevels->getNextLevelsWillModifier());
        self::assertSame($nextLevel->getIntelligenceIncrement()->getValue(), $professionLevels->getNextLevelsIntelligenceModifier());
        self::assertSame($nextLevel->getCharismaIncrement()->getValue(), $professionLevels->getNextLevelsCharismaModifier());
    }

    /**
     * @param int $levelValue
     * @return string
     */
    private function getProfessionLevelClass($levelValue)
    {
        return (int)$levelValue === 1
            ? ProfessionFirstLevel::class
            : ProfessionNextLevel::class;
    }

    /*
     * MORE LEVELS
     */

    /**
     * @test
     */
    public function I_can_add_more_levels_of_same_profession()
    {
        $firstLevel = $this->createProfessionFirstLevel(Fighter::FIGHTER);
        $this->addPrimaryPropertiesAnswer($firstLevel, Fighter::FIGHTER);
        $this->addFirstLevelAnswer($firstLevel, true);
        $this->addNextLevelAnswer($firstLevel, false);
        $this->addPropertyIncrementGetters(
            $firstLevel, $strength = 1, $agility = 2, $knack = 3, $will = 4, $intelligence = 5, $charisma = 6
        );
        $zeroLevel = $this->createZeroLevel();
        $professionLevels = $this->createProfessionLevelsWith($zeroLevel, $firstLevel);

        self::assertCount(2, $professionLevels->getSortedProfessionLevels());
        self::assertSame($zeroLevel, $professionLevels->getZeroLevel());
        self::assertSame($firstLevel, $professionLevels->getFirstLevel());
        self::assertSame([$zeroLevel, $firstLevel], $professionLevels->getSortedProfessionLevels());

        $propertiesSummary = $firstLevelProperties = [];
        foreach ($this->getPropertyCodes() as $propertyName) {
            $firstLevelProperties[$propertyName] = $propertiesSummary[$propertyName] = $$propertyName;
        }
        $secondLevel = $this->createProfessionLevel(Fighter::FIGHTER, 2, $professionLevels);
        $this->addPropertyIncrementGetters(
            $secondLevel, $strength = 1, $agility = 2, $knack = 3, $will = 4, $intelligence = 5, $charisma = 6
        );
        $this->addPrimaryPropertiesAnswer($secondLevel, Fighter::FIGHTER);
        $this->addNextLevelAnswer($secondLevel, true);
        $nextLevelProperties = [];
        foreach ($this->getPropertyCodes() as $propertyName) {
            $nextLevelProperties[$propertyName] = $$propertyName;
            $propertiesSummary[$propertyName] += $$propertyName;
        }
        $professionLevels->addLevel($secondLevel);

        $thirdLevel = $this->createProfessionLevel(Fighter::FIGHTER, 3, $professionLevels);
        $this->addPropertyIncrementGetters(
            $thirdLevel,
            $strength = ($this->isPrimaryProperty(Strength::STRENGTH, Fighter::FIGHTER) ? 7 : 0),
            $agility = ($this->isPrimaryProperty(Agility::AGILITY, Fighter::FIGHTER) ? 8 : 0),
            $knack = ($this->isPrimaryProperty(Knack::KNACK, Fighter::FIGHTER) ? 9 : 0),
            $will = ($this->isPrimaryProperty(Will::WILL, Fighter::FIGHTER) ? 10 : 0),
            $intelligence = ($this->isPrimaryProperty(Intelligence::INTELLIGENCE, Fighter::FIGHTER) ? 11 : 0),
            $charisma = ($this->isPrimaryProperty(Charisma::CHARISMA, Fighter::FIGHTER) ? 12 : 0)
        );
        $this->addPrimaryPropertiesAnswer($thirdLevel, Fighter::FIGHTER);
        foreach ($this->getPropertyCodes() as $propertyName) {
            $propertiesSummary[$propertyName] += $$propertyName;
            $nextLevelProperties[$propertyName] += $$propertyName;
        }
        $professionLevels->addLevel($thirdLevel);

        self::assertSame($zeroLevel, $professionLevels->getZeroLevel());
        self::assertSame($firstLevel, $professionLevels->getFirstLevel(), 'After adding a new level the old one is no more the first.');
        self::assertSame([$zeroLevel, $firstLevel, $secondLevel, $thirdLevel], $professionLevels->getSortedProfessionLevels());
        self::assertSame([$secondLevel, $thirdLevel], $professionLevels->getProfessionNextLevels()->toArray());

        $levelsArray = [];
        foreach ($professionLevels as $professionLevel) {
            $levelsArray[] = $professionLevel;
        }
        self::assertEquals($professionLevels->getSortedProfessionLevels(), $levelsArray);
        self::assertSame($thirdLevel, $professionLevels->getCurrentLevel());
        self::assertSame(count($levelsArray), $professionLevels->getCurrentLevel()->getLevelRank()->getValue() + 1 /* zero level */);

        self::assertSame($propertiesSummary[Strength::STRENGTH], $professionLevels->getStrengthModifierSummary());
        self::assertSame($propertiesSummary[Agility::AGILITY], $professionLevels->getAgilityModifierSummary());
        self::assertSame($propertiesSummary[Knack::KNACK], $professionLevels->getKnackModifierSummary());
        self::assertSame($propertiesSummary[Will::WILL], $professionLevels->getWillModifierSummary());
        self::assertSame($propertiesSummary[Intelligence::INTELLIGENCE], $professionLevels->getIntelligenceModifierSummary());
        self::assertSame($propertiesSummary[Charisma::CHARISMA], $professionLevels->getCharismaModifierSummary());

        self::assertSame($firstLevelProperties[Strength::STRENGTH], $professionLevels->getFirstLevelStrengthModifier());
        self::assertSame($firstLevelProperties[Agility::AGILITY], $professionLevels->getFirstLevelAgilityModifier());
        self::assertSame($firstLevelProperties[Knack::KNACK], $professionLevels->getFirstLevelKnackModifier());
        self::assertSame($firstLevelProperties[Will::WILL], $professionLevels->getFirstLevelWillModifier());
        self::assertSame($firstLevelProperties[Intelligence::INTELLIGENCE], $professionLevels->getFirstLevelIntelligenceModifier());
        self::assertSame($firstLevelProperties[Charisma::CHARISMA], $professionLevels->getFirstLevelCharismaModifier());

        self::assertSame($nextLevelProperties[Strength::STRENGTH], $professionLevels->getNextLevelsStrengthModifier());
        self::assertSame($nextLevelProperties[Agility::AGILITY], $professionLevels->getNextLevelsAgilityModifier());
        self::assertSame($nextLevelProperties[Knack::KNACK], $professionLevels->getNextLevelsKnackModifier());
        self::assertSame($nextLevelProperties[Will::WILL], $professionLevels->getNextLevelsWillModifier());
        self::assertSame($nextLevelProperties[Intelligence::INTELLIGENCE], $professionLevels->getNextLevelsIntelligenceModifier());
        self::assertSame($nextLevelProperties[Charisma::CHARISMA], $professionLevels->getNextLevelsCharismaModifier());
    }

    /**
     * @test
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidLevelRank
     */
    public function I_can_not_add_level_with_occupied_sequence()
    {
        $professionLevels = $this->createProfessionLevelsForChangeResistTest(Fighter::FIGHTER);

        $levelsCount = count($professionLevels->getSortedProfessionLevels());
        self::assertGreaterThan(2, $levelsCount /* already occupied level rank to achieve conflict */);

        $anotherLevel = $this->createProfessionLevel(Fighter::FIGHTER, $levelsCount - 1 /* zero level */);

        $professionLevels->addLevel($anotherLevel);
    }

    /**
     * @test
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidLevelRank
     */
    public function I_can_not_add_level_with_out_of_sequence_rank()
    {
        $professionLevels = $this->createProfessionLevelsForChangeResistTest(Fighter::FIGHTER);
        $levelsCount = count($professionLevels->getSortedProfessionLevels());
        self::assertGreaterThan(1, $levelsCount);

        $anotherLevel = $this->createProfessionLevel(Fighter::FIGHTER, $levelsCount + 2 /* skipping a rank by one */, $professionLevels);

        $professionLevels->addLevel($anotherLevel);
    }

    private function createProfessionLevelsForChangeResistTest($professionCode)
    {
        $firstLevel = $this->createProfessionFirstLevel($professionCode);
        $this->addPrimaryPropertiesAnswer($firstLevel, $professionCode);
        $this->addFirstLevelAnswer($firstLevel, true);
        $this->addNextLevelAnswer($firstLevel, false);
        $this->addPropertyIncrementGetters(
            $firstLevel, $strength = 1, $agility = 2, $knack = 3, $will = 4, $intelligence = 5, $charisma = 6
        );
        $zeroLevel = $this->createZeroLevel();
        $professionLevels = $this->createProfessionLevelsWith($zeroLevel, $firstLevel);

        self::assertCount(2, $professionLevels->getSortedProfessionLevels());
        self::assertSame($zeroLevel, $professionLevels->getZeroLevel());
        self::assertSame($firstLevel, $professionLevels->getFirstLevel());
        self::assertEquals([$zeroLevel, $firstLevel], $professionLevels->getSortedProfessionLevels());

        $secondLevel = $this->createProfessionLevel($professionCode, 2, $professionLevels);
        $this->addPropertyIncrementGetters(
            $secondLevel, $strength = 1, $agility = 2, $knack = 3, $will = 4, $intelligence = 5, $charisma = 6
        );
        $this->addPrimaryPropertiesAnswer($secondLevel, $professionCode);
        $this->addNextLevelAnswer($secondLevel, true);

        $professionLevels->addLevel($secondLevel);

        $thirdLevel = $this->createProfessionLevel($professionCode, 3, $professionLevels);
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
        $professionLevels->addLevel($thirdLevel);

        return $professionLevels;
    }

    /*
     * ONLY SINGLE PROFESSION IS ALLOWED
     */

    /**
     * @test
     */
    public function I_can_not_mix_professions()
    {
        $professionLevels = $this->createProfessionLevelsForMixTest(Fighter::FIGHTER);
        /** @var ProfessionFirstLevel|\Mockery\MockInterface $firstLevel */
        $firstLevel = $professionLevels->getFirstLevel();
        self::assertInstanceOf(ProfessionFirstLevel::class, $firstLevel);

        $otherLevels = $this->getLevelsExcept($firstLevel);
        self::assertNotEmpty($otherLevels);

        foreach ($otherLevels as $professionCode => $otherProfessionLevel) {
            try {
                $professionLevels->addLevel($otherProfessionLevel);
                self::fail(
                    "Adding $professionCode to levels already set to {$firstLevel->getProfession()->getValue()} should throw exception."
                );
            } catch (MultiProfessionsAreProhibited $exception) {
                self::assertNotNull($exception);
            }
        }
    }

    /**
     * @param $professionCode
     * @return ProfessionLevels
     */
    private function createProfessionLevelsForMixTest($professionCode)
    {
        $professionLevels = new ProfessionLevels(
            $this->createZeroLevel(),
            $firstLevel = $this->createFirstLevel($professionCode)
        );

        return $professionLevels;
    }

    /**
     * @param ProfessionLevel $excludedProfession
     *
     * @return \Mockery\MockInterface[]|ProfessionFirstLevel[]|ProfessionNextLevel[]
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

    /**
     * @return array|ProfessionNextLevel[]
     */
    private function buildProfessionLevels()
    {
        $professions = [
            ProfessionCode::FIGHTER => Fighter::class,
            ProfessionCode::PRIEST => Priest::class,
            ProfessionCode::RANGER => Ranger::class,
            ProfessionCode::THEURGIST => Theurgist::class,
            ProfessionCode::THIEF => Thief::class,
            ProfessionCode::WIZARD => Wizard::class,
        ];
        $professionLevels = [];
        foreach ($professions as $professionCode => $professionClass) {
            $professionLevels[$professionCode] = $level = $this->mockery(ProfessionNextLevel::class);
            $profession = $this->mockery($professionClass);
            $profession->shouldReceive('getValue')
                ->andReturn($professionCode);
            $level->shouldReceive('getProfession')
                ->andReturn($profession);
        }

        return $professionLevels;
    }

    /*
     * SAME PROPERTY INCREMENT IN A ROW
     */

    /**
     * @test
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\TooHighPrimaryPropertyIncrease
     */
    public function I_can_not_increase_primary_property_three_times_in_a_row()
    {
        try {
            $firstLevel = $this->createProfessionLevelWithPrimaryPropertiesIncreased(
                Fighter::FIGHTER,
                1
            );
            $zeroLevel = $this->createZeroLevel();
            // the first level does not come to property increment check
            $professionLevels = new ProfessionLevels($zeroLevel, $firstLevel);

            // the second level will be taken into account on check of fourth level
            $secondLevel = $this->createProfessionLevelWithPrimaryPropertiesIncreased(
                Fighter::FIGHTER,
                $professionLevels->getCurrentLevel()->getLevelRank()->getValue() + 1,
                true, // first primary property increment
                false, // no second primary property increment
                $professionLevels
            );
            $professionLevels->addLevel($secondLevel);

            // with both primary properties increased
            $thirdLevel = $this->createProfessionLevelWithPrimaryPropertiesIncreased(
                Fighter::FIGHTER,
                $professionLevels->getCurrentLevel()->getLevelRank()->getValue() + 1,
                false, // no first primary property increment
                true, // second primary property increment
                $professionLevels
            );
            $professionLevels->addLevel($thirdLevel);

            // again with both primary properties increased
            $fourthLevel = $this->createProfessionLevelWithPrimaryPropertiesIncreased(
                Fighter::FIGHTER,
                $professionLevels->getCurrentLevel()->getLevelRank()->getValue() + 1,
                true, // first primary property increment
                true, // second primary property increment
                $professionLevels
            );
            $professionLevels->addLevel($fourthLevel); //should pass
        } catch (\Exception $exception) {
            self::fail(
                'No exception should happen this far: ' . $exception->getMessage()
                . ' (' . $exception->getTraceAsString() . ')'
            );

            return;
        }

        // and again with both primary properties increased
        $fifthLevel = $this->createProfessionLevelWithPrimaryPropertiesIncreased(
            Fighter::FIGHTER,
            $professionLevels->getCurrentLevel()->getLevelRank()->getValue() + 1
        );
        $professionLevels->addLevel($fifthLevel); // should fail
    }

    private function createProfessionLevelWithPrimaryPropertiesIncreased(
        $professionCode,
        $levelValue,
        $increaseFirstPrimaryProperty = true,
        $increaseSecondPrimaryProperty = true,
        ProfessionLevels $professionLevels = null
    )
    {
        $professionLevel = $this->createProfessionLevel($professionCode, $levelValue, $professionLevels);
        $propertyIncrements = [];
        $isFirst = true;
        foreach ($this->getPropertyCodes() as $propertyCode) {
            $increment = $this->isPrimaryProperty($propertyCode, $professionCode) ? 1 : 0;
            if ($increment) {
                if ($isFirst) {
                    $isFirst = false;
                    $increment &= $increaseFirstPrimaryProperty;
                } else {
                    $increment &= $increaseSecondPrimaryProperty;
                }
            }
            $propertyIncrements[$propertyCode] = $increment;
        }
        $this->addPropertyIncrementGetters(
            $professionLevel,
            $propertyIncrements[Strength::STRENGTH],
            $propertyIncrements[Agility::AGILITY],
            $propertyIncrements[Knack::KNACK],
            $propertyIncrements[Will::WILL],
            $propertyIncrements[Intelligence::INTELLIGENCE],
            $propertyIncrements[Charisma::CHARISMA]
        );
        $this->addPrimaryPropertiesAnswer($professionLevel, $professionCode);

        return $professionLevel;
    }

    /**
     * @param string $professionCode
     * @test
     * dataProvider provideProfessionCode
     * @expectedException \DrdPlus\Person\ProfessionLevels\Exceptions\TooHighSecondaryPropertyIncrease
     */
    public function I_can_not_increase_secondary_property_two_times_in_a_row($professionCode = 'fighter')
    {
        try {
            $firstLevel = $this->createProfessionLevelWithSecondaryPropertiesIncreased($professionCode, 1);
            // the first level does not come to property increment check
            $zeroLevel = $this->createZeroLevel();
            $professionLevels = new ProfessionLevels($zeroLevel, $firstLevel);

            // the second level will be taken into account on check of third level
            $secondLevel = $this->createProfessionLevelWithSecondaryPropertiesIncreased(
                $professionCode,
                $professionLevels->getCurrentLevel()->getLevelRank()->getValue() + 1,
                false, // without secondary properties increment
                $professionLevels
            );
            $professionLevels->addLevel($secondLevel);

            $thirdLevel = $this->createProfessionLevelWithSecondaryPropertiesIncreased(
                $professionCode,
                $professionLevels->getCurrentLevel()->getLevelRank()->getValue() + 1,
                true, // with secondary properties increment
                $professionLevels
            );
            $professionLevels->addLevel($thirdLevel); // should pass
        } catch (\Exception $exception) {
            self::fail(
                "No exception should happen this far: {$exception->getMessage()} ({$exception->getTraceAsString()})"
            );

            return;
        }
        $fourthLevel = $this->createProfessionLevelWithSecondaryPropertiesIncreased(
            $professionCode,
            $professionLevels->getCurrentLevel()->getLevelRank()->getValue() + 1,
            true, // with secondary properties increment
            $professionLevels
        );
        $professionLevels->addLevel($fourthLevel); // should fail
    }

    private function createProfessionLevelWithSecondaryPropertiesIncreased(
        $professionCode,
        $levelValue,
        $increaseSecondaryProperties = true,
        ProfessionLevels $professionLevels = null
    )
    {
        $professionLevel = $this->createProfessionLevel($professionCode, $levelValue, $professionLevels);
        $propertyIncrements = [];
        foreach ($this->getPropertyCodes() as $propertyCode) {
            $increment = $increaseSecondaryProperties
            && !$this->isPrimaryProperty($propertyCode, $professionCode) ? 1 : 0;
            $propertyIncrements[$propertyCode] = $increment;
        }
        $this->addPropertyIncrementGetters(
            $professionLevel,
            $propertyIncrements[Strength::STRENGTH],
            $propertyIncrements[Agility::AGILITY],
            $propertyIncrements[Knack::KNACK],
            $propertyIncrements[Will::WILL],
            $propertyIncrements[Intelligence::INTELLIGENCE],
            $propertyIncrements[Charisma::CHARISMA]
        );
        $this->addPrimaryPropertiesAnswer($professionLevel, $professionCode);

        return $professionLevel;
    }
}