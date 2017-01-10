<?php
namespace DrdPlus\Person\ProfessionLevels;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrineum\Entity\Entity;
use DrdPlus\Codes\PropertyCode;
use DrdPlus\Properties\Base\BaseProperty;
use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Intelligence;
use DrdPlus\Properties\Base\Knack;
use DrdPlus\Properties\Base\Will;
use Granam\Strict\Object\StrictObject;
use Granam\Tools\ValueDescriber;

/**
 * @ORM\Entity()
 */
class ProfessionLevels extends StrictObject implements Entity, \IteratorAggregate
{
    /**
     * @var integer
     *
     * @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var ProfessionZeroLevel
     * @ORM\OneToOne(targetEntity="ProfessionZeroLevel", cascade={"persist"})
     */
    private $professionZeroLevel;

    /**
     * @var ProfessionFirstLevel
     * @ORM\OneToOne(targetEntity="ProfessionFirstLevel", cascade={"persist"})
     */
    private $professionFirstLevel;

    /**
     * @var ProfessionNextLevel[]
     * @ORM\OneToMany(targetEntity="ProfessionNextLevel", cascade={"persist"}, mappedBy="professionLevels",
     *     fetch="EAGER")
     */
    private $professionNextLevels;

    /**
     * @param ProfessionZeroLevel $professionZeroLevel
     * @param ProfessionFirstLevel $professionFirstLevel
     * @param array $professionNextLevels
     * @return static|ProfessionLevels
     * @throws Exceptions\MultiProfessionsAreProhibited
     */
    public static function createIt(
        ProfessionZeroLevel $professionZeroLevel,
        ProfessionFirstLevel $professionFirstLevel,
        array $professionNextLevels = []
    )
    {
        $professionLevels = new static($professionZeroLevel, $professionFirstLevel);
        foreach ($professionNextLevels as $professionNextLevel) {
            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            $professionLevels->addLevel($professionNextLevel);
        }

        return $professionLevels;
    }

    /**
     * @param ProfessionZeroLevel $professionZeroLevel
     * @param ProfessionFirstLevel $professionFirstLevel
     */
    public function __construct(ProfessionZeroLevel $professionZeroLevel, ProfessionFirstLevel $professionFirstLevel)
    {
        $this->professionZeroLevel = $professionZeroLevel;
        $this->professionFirstLevel = $professionFirstLevel;
        $this->professionNextLevels = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * All levels, achieved at any profession, unsorted
     *
     * @return Collection|ProfessionLevel[]
     */
    public function getProfessionNextLevels()
    {
        return $this->professionNextLevels;
    }

    /**
     * @return \ArrayObject|ProfessionLevel[]
     */
    public function getIterator()
    {
        return new \ArrayObject($this->getSortedProfessionLevels());
    }

    /**
     * @return array|ProfessionLevel[]
     */
    public function getSortedProfessionLevels()
    {
        $levels = $this->getProfessionNextLevels()->toArray();
        $levels = $this->sortByLevelRank($levels);
        array_unshift($levels, $this->getFirstLevel());
        array_unshift($levels, $this->getZeroLevel());

        return $levels;
    }

    /**
     * @param array|ProfessionLevel[] $professionLevels
     *
     * @return array
     */
    private function sortByLevelRank(array $professionLevels)
    {
        usort($professionLevels, function (ProfessionLevel $aLevel, ProfessionLevel $anotherLevel) {
            $difference = $aLevel->getLevelRank()->getValue() - $anotherLevel->getLevelRank()->getValue();

            return $difference > 0
                ? 1 // firstly given level is higher than second one
                : -1; // opposite
        });

        return $professionLevels;
    }

    /**
     * @return ProfessionZeroLevel
     */
    public function getZeroLevel()
    {
        return $this->professionZeroLevel;
    }

    /**
     * @return ProfessionFirstLevel
     */
    public function getFirstLevel()
    {
        return $this->professionFirstLevel;
    }

    /**
     * @param ProfessionNextLevel $newLevel
     * @throws Exceptions\MultiProfessionsAreProhibited
     * @throws Exceptions\InvalidLevelRank
     * @throws Exceptions\TooHighPrimaryPropertyIncrease
     * @throws Exceptions\TooHighSecondaryPropertyIncrease
     */
    public function addLevel(ProfessionNextLevel $newLevel)
    {
        $this->checkProhibitedMultiProfession($newLevel);
        $this->checkNewLevelSequence($newLevel);
        $this->checkPropertiesIncrementSequence($newLevel);

        $this->getProfessionNextLevels()->add($newLevel);
        $newLevel->setProfessionLevels($this);
    }

    /**
     * @param ProfessionLevel $newLevel
     * @throws Exceptions\MultiProfessionsAreProhibited
     */
    private function checkProhibitedMultiProfession(ProfessionLevel $newLevel)
    {
        // zero level is not checked - you could be anything before heroic live. cook, bartender, beggar ...
        if ($newLevel->getProfession()->getValue() !== $this->getFirstLevel()->getProfession()->getValue()) {
            throw new Exceptions\MultiProfessionsAreProhibited(
                'New level has to be of same profession as first level.'
                . ' Expected ' . ValueDescriber::describe($this->getFirstLevel()->getProfession()->getValue())
                . ', got ' . ValueDescriber::describe($newLevel->getProfession()->getValue())
            );
        }
    }

    /**
     * @param ProfessionLevel $newLevel
     * @throws Exceptions\InvalidLevelRank
     */
    private function checkNewLevelSequence(ProfessionLevel $newLevel)
    {
        if ($newLevel->getLevelRank()->getValue() !== $this->getCurrentLevel()->getLevelRank()->getValue() + 1) {
            throw new Exceptions\InvalidLevelRank(
                'Unexpected rank of given profession level.'
                . ' Expected ' . ($this->getCurrentLevel()->getLevelRank()->getValue() + 1)
                . ', got ' . $newLevel->getLevelRank()->getValue()
            );
        }
    }

    /**
     * @param ProfessionLevel $newLevel
     * @throws Exceptions\TooHighPrimaryPropertyIncrease
     * @throws Exceptions\TooHighSecondaryPropertyIncrease
     */
    private function checkPropertiesIncrementSequence(ProfessionLevel $newLevel)
    {
        $this->checkPropertyIncrementSequence($newLevel, $newLevel->getStrengthIncrement());
        $this->checkPropertyIncrementSequence($newLevel, $newLevel->getAgilityIncrement());
        $this->checkPropertyIncrementSequence($newLevel, $newLevel->getKnackIncrement());
        $this->checkPropertyIncrementSequence($newLevel, $newLevel->getWillIncrement());
        $this->checkPropertyIncrementSequence($newLevel, $newLevel->getIntelligenceIncrement());
        $this->checkPropertyIncrementSequence($newLevel, $newLevel->getCharismaIncrement());
    }

    /**
     * @param ProfessionLevel $newLevel
     * @param BaseProperty $propertyIncrement
     * @throws Exceptions\TooHighPrimaryPropertyIncrease
     * @throws Exceptions\TooHighSecondaryPropertyIncrease
     */
    private function checkPropertyIncrementSequence(ProfessionLevel $newLevel, BaseProperty $propertyIncrement)
    {
        if ($propertyIncrement->getValue() > 0) {
            if ($newLevel->isPrimaryProperty($propertyIncrement->getCode())) {
                $this->checkPrimaryPropertyIncrementInARow($propertyIncrement);
            } else {
                $this->checkSecondaryPropertyIncrementInARow($propertyIncrement);
            }
        }
    }

    /**
     * @param BaseProperty $propertyIncrement
     * @return bool
     * @throws Exceptions\TooHighPrimaryPropertyIncrease
     */
    private function checkPrimaryPropertyIncrementInARow(BaseProperty $propertyIncrement)
    {
        $previousLevels = $this->getProfessionNextLevels();
        $previousNextLevelsCount = count($previousLevels);
        // main property can be increased twice in a row
        if ($previousNextLevelsCount < 2) {
            return true;
        }
        $lastPrevious = $previousLevels->last();
        if (!$this->hasIncrementSameProperty($lastPrevious, $propertyIncrement)) {
            return true;
        }
        $lastButOnePreviousKey = $previousLevels->getKeys()[$previousNextLevelsCount - 2];
        /** @var ProfessionLevel $lastPrevious */
        $lastButOnePrevious = $previousLevels->get($lastButOnePreviousKey);
        if (!$this->hasIncrementSameProperty($lastButOnePrevious, $propertyIncrement)) {
            return true;
        }
        throw new Exceptions\TooHighPrimaryPropertyIncrease(
            'Primary property can not be increased more than twice in a row'
            . ", got {$propertyIncrement->getCode()} to increase."
        );
    }

    /**
     * @param ProfessionLevel $testedProfessionLevel
     * @param BaseProperty $patternPropertyIncrement
     * @return bool
     */
    private function hasIncrementSameProperty(ProfessionLevel $testedProfessionLevel, BaseProperty $patternPropertyIncrement)
    {
        return $this->getSamePropertyIncrement($testedProfessionLevel, $patternPropertyIncrement)->getValue() > 0;
    }

    /**
     * @param ProfessionLevel $searchedThroughProfessionLevel
     * @param BaseProperty $patternPropertyIncrement
     * @return Charisma|Intelligence|Knack|Will
     */
    private function getSamePropertyIncrement(ProfessionLevel $searchedThroughProfessionLevel, BaseProperty $patternPropertyIncrement)
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return $searchedThroughProfessionLevel->getBasePropertyIncrement(
            PropertyCode::getIt($patternPropertyIncrement->getCode())
        );
    }

    /**
     * @param BaseProperty $propertyIncrement
     * @return bool
     * @throws Exceptions\TooHighSecondaryPropertyIncrease
     */
    private function checkSecondaryPropertyIncrementInARow(BaseProperty $propertyIncrement)
    {
        $nextLevels = $this->getProfessionNextLevels();
        // secondary property has to be increased at least alternately
        if (count($nextLevels) === 0) {
            return true;
        }
        if (!$this->hasIncrementSameProperty($nextLevels->last(), $propertyIncrement)) {
            return true;
        }
        throw new Exceptions\TooHighSecondaryPropertyIncrease(
            'Secondary property increase has to be at least alternately'
            . ", got {$propertyIncrement->getCode()} again to increase."
        );
    }

    /**
     * @return int
     */
    public function getFirstLevelStrengthModifier()
    {
        return $this->getFirstLevelPropertyModifier(PropertyCode::getIt(PropertyCode::STRENGTH));
    }

    /**
     * @param PropertyCode $propertyCode
     * @return int
     */
    public function getFirstLevelPropertyModifier(PropertyCode $propertyCode)
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return $this->getFirstLevel()->getBasePropertyIncrement($propertyCode)->getValue();
    }

    /**
     * @return int
     */
    public function getFirstLevelAgilityModifier()
    {
        return $this->getFirstLevelPropertyModifier(PropertyCode::getIt(PropertyCode::AGILITY));
    }

    /**
     * @return int
     */
    public function getFirstLevelKnackModifier()
    {
        return $this->getFirstLevelPropertyModifier(PropertyCode::getIt(PropertyCode::KNACK));
    }

    /**
     * @return int
     */
    public function getFirstLevelWillModifier()
    {
        return $this->getFirstLevelPropertyModifier(PropertyCode::getIt(PropertyCode::WILL));
    }

    /**
     * @return int
     */
    public function getFirstLevelIntelligenceModifier()
    {
        return $this->getFirstLevelPropertyModifier(PropertyCode::getIt(PropertyCode::INTELLIGENCE));
    }

    /**
     * @return int
     */
    public function getFirstLevelCharismaModifier()
    {
        return $this->getFirstLevelPropertyModifier(PropertyCode::getIt(PropertyCode::CHARISMA));
    }

    /**
     * @return int
     */
    public function getStrengthModifierSummary()
    {
        return $this->getPropertyModifierSummary(PropertyCode::getIt(PropertyCode::STRENGTH));
    }

    /**
     * @param PropertyCode $propertyCode
     *
     * @return int
     */
    public function getPropertyModifierSummary(PropertyCode $propertyCode)
    {
        return array_sum($this->getLevelsPropertyModifiers($propertyCode));
    }

    /**
     * @param PropertyCode $propertyCode
     *
     * @return int[]
     */
    private function getLevelsPropertyModifiers(PropertyCode $propertyCode)
    {
        return array_map(
            function (ProfessionLevel $professionLevel) use ($propertyCode) {
                /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
                return $professionLevel->getBasePropertyIncrement($propertyCode)->getValue();
            },
            $this->getSortedProfessionLevels()
        );
    }

    /**
     * @param PropertyCode $propertyCode
     *
     * @return int
     */
    public function getNextLevelsPropertyModifier(PropertyCode $propertyCode)
    {
        return array_sum($this->getNextLevelsPropertyModifiers($propertyCode));
    }

    /**
     * @param PropertyCode $propertyCode
     *
     * @return int[]
     */
    private function getNextLevelsPropertyModifiers(PropertyCode $propertyCode)
    {
        return array_map(
            function (ProfessionLevel $professionLevel) use ($propertyCode) {
                /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
                return $professionLevel->getBasePropertyIncrement($propertyCode)->getValue();
            },
            $this->getProfessionNextLevels()->toArray()
        );
    }

    /**
     * @return int
     */
    public function getAgilityModifierSummary()
    {
        return $this->getPropertyModifierSummary(PropertyCode::getIt(PropertyCode::AGILITY));
    }

    /**
     * @return int
     */
    public function getKnackModifierSummary()
    {
        return $this->getPropertyModifierSummary(PropertyCode::getIt(PropertyCode::KNACK));
    }

    /**
     * @return int
     */
    public function getWillModifierSummary()
    {
        return $this->getPropertyModifierSummary(PropertyCode::getIt(PropertyCode::WILL));
    }

    /**
     * @return int
     */
    public function getIntelligenceModifierSummary()
    {
        return $this->getPropertyModifierSummary(PropertyCode::getIt(PropertyCode::INTELLIGENCE));
    }

    /**
     * @return int
     */
    public function getCharismaModifierSummary()
    {
        return $this->getPropertyModifierSummary(PropertyCode::getIt(PropertyCode::CHARISMA));
    }

    /**
     * @return int
     */
    public function getNextLevelsStrengthModifier()
    {
        return $this->getNextLevelsPropertyModifier(PropertyCode::getIt(PropertyCode::STRENGTH));
    }

    /**
     * @return int
     */
    public function getNextLevelsAgilityModifier()
    {
        return $this->getNextLevelsPropertyModifier(PropertyCode::getIt(PropertyCode::AGILITY));
    }

    /**
     * @return int
     */
    public function getNextLevelsKnackModifier()
    {
        return $this->getNextLevelsPropertyModifier(PropertyCode::getIt(PropertyCode::KNACK));
    }

    /**
     * @return int
     */
    public function getNextLevelsWillModifier()
    {
        return $this->getNextLevelsPropertyModifier(PropertyCode::getIt(PropertyCode::WILL));
    }

    /**
     * @return int
     */
    public function getNextLevelsIntelligenceModifier()
    {
        return $this->getNextLevelsPropertyModifier(PropertyCode::getIt(PropertyCode::INTELLIGENCE));
    }

    /**
     * @return int
     */
    public function getNextLevelsCharismaModifier()
    {
        return $this->getNextLevelsPropertyModifier(PropertyCode::getIt(PropertyCode::CHARISMA));
    }

    /**
     * @return ProfessionLevel
     */
    public function getCurrentLevel()
    {
        $sortedLevels = $this->getSortedProfessionLevels();

        return end($sortedLevels);
    }

}