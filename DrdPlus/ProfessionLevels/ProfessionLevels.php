<?php
namespace DrdPlus\ProfessionLevels;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DrdPlus\Properties\Base\Agility;
use DrdPlus\Properties\Base\BaseProperty;
use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Intelligence;
use DrdPlus\Properties\Base\Knack;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Properties\Base\Will;
use \DrdPlus\Professions\Fighter;
use \DrdPlus\Professions\Priest;
use \DrdPlus\Professions\Ranger;
use \DrdPlus\Professions\Theurgist;
use \DrdPlus\Professions\Thief;
use \DrdPlus\Professions\Wizard;
use Granam\Strict\Object\StrictObject;

/**
 * ProfessionLevels
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class ProfessionLevels extends StrictObject implements \IteratorAggregate
{
    const PROPERTY_FIRST_LEVEL_MODIFIER = +1;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var FighterLevel[]
     *
     * @ORM\OneToMany(targetEntity="FighterLevel", mappedBy="professionLevels")
     */
    private $fighterLevels;

    /**
     * @var PriestLevel[]
     *
     * @ORM\OneToMany(targetEntity="PriestLevel", mappedBy="professionLevels")
     */
    private $priestLevels;

    /**
     * @var RangerLevel[]
     *
     * @ORM\OneToMany(targetEntity="RangerLevel", mappedBy="professionLevels")
     */
    private $rangerLevels;

    /**
     * @var TheurgistLevel[]
     *
     * @ORM\OneToMany(targetEntity="TheurgistLevel", mappedBy="professionLevels")
     */
    private $theurgistLevels;

    /**
     * @var ThiefLevel[]
     *
     * @ORM\OneToMany(targetEntity="ThiefLevel", mappedBy="professionLevels")
     */
    private $thiefLevels;

    /**
     * @var WizardLevel[]
     *
     * @ORM\OneToMany(targetEntity="WizardLevel", mappedBy="professionLevels")
     */
    private $wizardLevels;

    public function __construct()
    {
        // same profession levels are kept in collection only internally
        $this->fighterLevels = new ArrayCollection();
        $this->priestLevels = new ArrayCollection();
        $this->rangerLevels = new ArrayCollection();
        $this->theurgistLevels = new ArrayCollection();
        $this->thiefLevels = new ArrayCollection();
        $this->wizardLevels = new ArrayCollection();
    }

    /**
     * @return int
     */
    protected function getId()
    {
        return $this->id;
    }

    /**
     * @return FighterLevel[]|array
     */
    public function getFighterLevels()
    {
        return $this->fighterLevels->toArray();
    }

    /**
     * @return PriestLevel[]|array
     */
    public function getRangerLevels()
    {
        return $this->rangerLevels->toArray();
    }

    /**
     * @return TheurgistLevel[]|array
     */
    public function getTheurgistLevels()
    {
        return $this->theurgistLevels->toArray();
    }

    /**
     * @return ThiefLevel[]|array
     */
    public function getThiefLevels()
    {
        return $this->thiefLevels->toArray();
    }

    /**
     * @return WizardLevel[]|array
     */
    public function getWizardLevels()
    {
        return $this->wizardLevels->toArray();
    }

    /**
     * @return PriestLevel[]|array
     */
    public function getPriestLevels()
    {
        return $this->priestLevels->toArray();
    }

    /**
     * All levels, achieved at any profession
     *
     * @return ProfessionLevel[]
     */
    public function getLevels()
    {
        return $this->sortByLevelRank(array_merge(
            $this->getFighterLevels(),
            $this->getPriestLevels(),
            $this->getRangerLevels(),
            $this->getTheurgistLevels(),
            $this->getThiefLevels(),
            $this->getWizardLevels()
        ));
    }

    /**
     * @return ProfessionLevel[]
     */
    public function getIterator()
    {
        return new \ArrayObject($this->getLevels());
    }

    /**
     * @return ProfessionLevel|false
     */
    public function getFirstLevel()
    {
        $levels = $this->getLevels();
        if (count($levels) === 0) {
            return false;
        }

        return $this->sortByLevelRank($levels)[0];
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
            if ($difference === 0) {
                throw new \LogicException(
                    'Two profession levels of IDs' .
                    ' ' . var_export($aLevel->getId(), true) . ', ' . var_export($anotherLevel->getId(), true)
                    . ' have the same level rank.'
                );
            }

            return $difference > 0
                ? 1 // firstly given level is higher than second one
                : -1; // opposite
        });

        return $professionLevels;
    }

    /**
     * Sorted by level ascending
     *
     * @return ProfessionLevel[]
     */
    public function getNextLevels()
    {
        $levels = $this->getLevels();
        $firstLevel = array_shift($levels); // remote the fist level
        /** @var ProfessionLevel $firstLevel */
        if ($firstLevel && !$firstLevel->isFirstLevel()) {
            throw new \LogicException("The removed level should be the first one, removed {$firstLevel->getLevelRank()->getValue()}");
        }

        return $levels;
    }

    /**
     * @param FighterLevel $newFighterLevel
     */
    public function addFighterLevel(FighterLevel $newFighterLevel)
    {
        $this->addLevel($newFighterLevel);
    }

    /**
     * @param ProfessionLevel $newLevel
     */
    private function addLevel(ProfessionLevel $newLevel)
    {
        $previousLevels = $this->getPreviousProfessionLevels($newLevel);
        $this->checkProhibitedMultiProfession($previousLevels, $newLevel);
        $this->checkNewLevelSequence($previousLevels, $newLevel);
        $previousNextLevels = $this->filterOuFirstLevel($previousLevels);
        $this->checkPropertiesIncrementSequence($previousNextLevels, $newLevel);

        $previousLevels->add($newLevel);
    }

    /**
     * @param ProfessionLevel $professionLevel
     *
     * @return ProfessionLevel[]|ArrayCollection
     */
    private function getPreviousProfessionLevels(ProfessionLevel $professionLevel)
    {
        switch ($professionLevel->getProfession()->getCode()) {
            case Fighter::FIGHTER :
                return $this->fighterLevels;
            case Thief::THIEF :
                return $this->thiefLevels;
            case Wizard::WIZARD :
                return $this->wizardLevels;
            case Ranger::RANGER :
                return $this->rangerLevels;
            case Theurgist::THEURGIST :
                return $this->theurgistLevels;
            case Priest::PRIEST :
                return $this->priestLevels;
            default :
                throw new \LogicException;
        }
    }

    private function checkProhibitedMultiProfession(ArrayCollection $previousLevels, ProfessionLevel $newLevel)
    {
        if (count($previousLevels) !== count($this->getLevels())) {
            throw new \LogicException(
                'AbstractProfession levels of ID ' . var_export($this->id, true) . ' are already set for profession' .
                ' ' . $this->getAlreadySetProfessionCode() . ', given  ' . $newLevel->getProfession()->getCode()
                . ' . Multi-profession is not allowed.'
            );
        }
    }

    /** @return string */
    private function getAlreadySetProfessionCode()
    {
        return array_map(
            function (ProfessionLevel $level) {
                return $level->getProfession()->getCode();
            },
            $this->getLevels()
        )[0];
    }

    private function checkNewLevelSequence(ArrayCollection $previousProfessionLevels, ProfessionLevel $newLevel)
    {
        if (!$newLevel->getLevelRank()->getValue()) {
            throw new \LogicException(
                'Missing level value of given level of profession ' . $newLevel->getProfession()->getCode() . ' with ID ' . var_export($newLevel->getId(), true)
            );
        }

        if ($newLevel->getLevelRank()->getValue() !== ($previousProfessionLevels->count() + 1)) {
            throw new \LogicException(
                'Unexpected level of given profession level. Expected ' . ($previousProfessionLevels->count() + 1) . ', got ' . $newLevel->getLevelRank()->getValue()
            );
        }
    }

    /**
     * @param ArrayCollection $professionLevels
     *
     * @return ArrayCollection
     */
    private function filterOuFirstLevel(ArrayCollection $professionLevels)
    {
        return $professionLevels->filter(function (ProfessionLevel $level) {
            return $level->isNextLevel();
        });
    }

    private function checkPropertiesIncrementSequence(ArrayCollection $previousNextLevels, ProfessionLevel $newLevel)
    {
        $this->checkPropertyIncrementSequence($previousNextLevels, $newLevel, $newLevel->getStrengthIncrement());
        $this->checkPropertyIncrementSequence($previousNextLevels, $newLevel, $newLevel->getAgilityIncrement());
        $this->checkPropertyIncrementSequence($previousNextLevels, $newLevel, $newLevel->getKnackIncrement());
        $this->checkPropertyIncrementSequence($previousNextLevels, $newLevel, $newLevel->getWillIncrement());
        $this->checkPropertyIncrementSequence($previousNextLevels, $newLevel, $newLevel->getIntelligenceIncrement());
        $this->checkPropertyIncrementSequence($previousNextLevels, $newLevel, $newLevel->getCharismaIncrement());
    }

    private function checkPropertyIncrementSequence(
        ArrayCollection $previousNextLevels,
        ProfessionLevel $newLevel,
        BaseProperty $propertyIncrement
    )
    {
        if ($propertyIncrement->getValue() > 0) {
            if ($newLevel->isPrimaryProperty($propertyIncrement->getCode())) {
                $this->checkPrimaryPropertyIncrementInARow($previousNextLevels, $propertyIncrement);
            } else {
                $this->checkSecondaryPropertyIncrementInARow($previousNextLevels, $propertyIncrement);
            }
        }
    }

    private function checkPrimaryPropertyIncrementInARow(ArrayCollection $previousNextLevels, BaseProperty $propertyIncrement)
    {
        // main property can be increased only twice in sequence
        if ($previousNextLevels->count() < 2) {
            return true;
        }
        if (!$this->hasIncrementSameProperty($previousNextLevels->last(), $propertyIncrement)) {
            return true;
        }
        $lastButOne = $previousNextLevels[$previousNextLevels->count() - 2];
        if (!$this->hasIncrementSameProperty($lastButOne, $propertyIncrement)) {
            return true;
        }
        throw new \LogicException("Primary property can not be increased more then twice in a row, got {$propertyIncrement->getCode()} to increase.");
    }

    private function hasIncrementSameProperty(ProfessionLevel $testedProfessionLevel, BaseProperty $patternPropertyIncrement)
    {
        return $this->getSamePropertyIncrement($testedProfessionLevel, $patternPropertyIncrement)->getValue() > 0;
    }

    private function getSamePropertyIncrement(ProfessionLevel $searchedThroughProfessionLevel, BaseProperty $patternPropertyIncrement)
    {
        switch ($patternPropertyIncrement->getCode()) {
            case Strength::STRENGTH :
                return $searchedThroughProfessionLevel->getStrengthIncrement();
            case Agility::AGILITY :
                return $searchedThroughProfessionLevel->getAgilityIncrement();
            case Knack::KNACK :
                return $searchedThroughProfessionLevel->getKnackIncrement();
            case Will::WILL :
                return $searchedThroughProfessionLevel->getWillIncrement();
            case Intelligence::INTELLIGENCE :
                return $searchedThroughProfessionLevel->getIntelligenceIncrement();
            case Charisma::CHARISMA :
                return $searchedThroughProfessionLevel->getCharismaIncrement();
            default :
                throw new \LogicException;
        }
    }

    private function checkSecondaryPropertyIncrementInARow(ArrayCollection $previousNextLevels, BaseProperty $propertyIncrement)
    {
        // secondary property has to be increased at least alternately
        if ($previousNextLevels->count() === 0) {
            return true;
        }
        if (!$this->hasIncrementSameProperty($previousNextLevels->last(), $propertyIncrement)) {
            return true;
        }
        throw new \LogicException("Secondary property increase has to be at least alternately, got {$propertyIncrement->getCode()} again to increase.");
    }

    /**
     * @param PriestLevel $newPriestLevel
     */
    public function addPriestLevel(PriestLevel $newPriestLevel)
    {
        $this->addLevel($newPriestLevel);
    }

    /**
     * @param RangerLevel $newRangerLevel
     */
    public function addRangerLevel(RangerLevel $newRangerLevel)
    {
        $this->addLevel($newRangerLevel);
    }

    /**
     * @param TheurgistLevel $newTheurgistLevel
     */
    public function addTheurgistLevel(TheurgistLevel $newTheurgistLevel)
    {
        $this->addLevel($newTheurgistLevel);
    }

    /**
     * @param ThiefLevel $newThiefLevel
     */
    public function addThiefLevel(ThiefLevel $newThiefLevel)
    {
        $this->addLevel($newThiefLevel);
    }

    /**
     * @param WizardLevel $newWizardLevel
     */
    public function addWizardLevel(WizardLevel $newWizardLevel)
    {
        $this->addLevel($newWizardLevel);
    }

    /**
     * Get strength increment
     *
     * @return int
     */
    public function getStrengthModifierForFirstProfession()
    {
        return $this->getPropertyModifierForFirstProfession(Strength::STRENGTH);
    }

    private function getPropertyModifierForFirstProfession($propertyName)
    {
        if (!$this->hasFirstLevel()) {
            return 0;
        }
        $getPropertyIncrement = $this->composePropertyIncrementGetter($propertyName);

        return $this->getFirstLevel()->$getPropertyIncrement()->getValue();
    }

    private function composePropertyIncrementGetter($propertyName)
    {
        $propertyName = implode(
            array_map(
                function ($part) {
                    return ucfirst($part);
                },
                explode('_', $propertyName)
            )
        );

        // like "weight_in_kg" = getWeightInKg
        return "get{$propertyName}Increment";
    }

    /**
     * @return bool
     */
    private function hasFirstLevel()
    {
        return count($this->getLevels()) > 0;
    }

    /**
     * Get agility modifier
     *
     * @return int
     */
    public function getAgilityModifierForFirstProfession()
    {
        return $this->getPropertyModifierForFirstProfession(Agility::AGILITY);
    }

    /**
     * Get knack modifier
     *
     * @return int
     */
    public function getKnackModifierForFirstProfession()
    {
        return $this->getPropertyModifierForFirstProfession(Knack::KNACK);
    }

    /**
     * Get will modifier
     *
     * @return int
     */
    public function getWillModifierForFirstProfession()
    {
        return $this->getPropertyModifierForFirstProfession(Will::WILL);
    }

    /**
     * Get intelligence modifier
     *
     * @return int
     */
    public function getIntelligenceModifierForFirstProfession()
    {
        return $this->getPropertyModifierForFirstProfession(Intelligence::INTELLIGENCE);
    }

    /**
     * Get charisma modifier
     *
     * @return int
     */
    public function getCharismaModifierForFirstProfession()
    {
        return $this->getPropertyModifierForFirstProfession(Charisma::CHARISMA);
    }

    /**
     * Get weight modifier in kg
     *
     * @return int
     */
    public function getWeightKgModifierForFirstLevel()
    {
        return $this->hasFirstLevel()
            ? $this->getFirstLevel()->getWeightInKgIncrement()->getValue()
            : 0;
    }

    /**
     * Get strength modifier
     *
     * @return int
     */
    public function getStrengthModifierSummary()
    {
        return $this->getPropertyModifierSummary(Strength::STRENGTH);
    }

    /**
     * @param string $propertyName
     *
     * @return int
     */
    private function getPropertyModifierSummary($propertyName)
    {
        return $this->getPropertyModifierForFirstProfession($propertyName) + $this->sumNextLevelsProperty($propertyName);
    }

    /**
     * @param string $propertyName
     *
     * @return int
     */
    private function sumNextLevelsProperty($propertyName)
    {
        return (int)array_sum($this->getNextLevelsPropertyModifiers($propertyName));
    }

    /**
     * @param $propertyName
     *
     * @return int[]
     */
    private function getNextLevelsPropertyModifiers($propertyName)
    {
        return array_map(
            function (ProfessionLevel $professionLevel) use ($propertyName) {
                switch ($propertyName) {
                    case Strength::STRENGTH :
                        return $professionLevel->getStrengthIncrement()->getValue();
                    case Agility::AGILITY :
                        return $professionLevel->getAgilityIncrement()->getValue();
                    case Knack::KNACK :
                        return $professionLevel->getKnackIncrement()->getValue();
                    case Will::WILL :
                        return $professionLevel->getWillIncrement()->getValue();
                    case Intelligence::INTELLIGENCE :
                        return $professionLevel->getIntelligenceIncrement()->getValue();
                    case Charisma::CHARISMA :
                        return $professionLevel->getCharismaIncrement()->getValue();
                    default :
                        throw new \LogicException;
                }
            },
            $this->getNextLevels()
        );
    }

    /**
     * Get agility modifier
     *
     * @return int
     */
    public function getAgilityModifierSummary()
    {
        return $this->getPropertyModifierSummary(Agility::AGILITY);
    }

    /**
     * Get agility modifier
     *
     * @return int
     */
    public function getKnackModifierSummary()
    {
        return $this->getPropertyModifierSummary(Knack::KNACK);
    }

    /**
     * Get will modifier
     *
     * @return int
     */
    public function getWillModifierSummary()
    {
        return $this->getPropertyModifierSummary(Will::WILL);
    }

    /**
     * Get intelligence modifier
     *
     * @return int
     */
    public function getIntelligenceModifierSummary()
    {
        return $this->getPropertyModifierSummary(Intelligence::INTELLIGENCE);
    }

    /**
     * Get charisma modifier
     *
     * @return int
     */
    public function getCharismaModifierSummary()
    {
        return $this->getPropertyModifierSummary(Charisma::CHARISMA);
    }

    /**
     * @return int
     */
    public function getNextLevelsStrengthModifier()
    {
        return $this->sumNextLevelsProperty(Strength::STRENGTH);
    }

    /**
     * @return int
     */
    public function getNextLevelsAgilityModifier()
    {
        return $this->sumNextLevelsProperty(Agility::AGILITY);
    }

    /**
     * @return int
     */
    public function getNextLevelsKnackModifier()
    {
        return $this->sumNextLevelsProperty(Knack::KNACK);
    }

    /**
     * @return int
     */
    public function getNextLevelsWillModifier()
    {
        return $this->sumNextLevelsProperty(Will::WILL);
    }

    /**
     * @return int
     */
    public function getNextLevelsIntelligenceModifier()
    {
        return $this->sumNextLevelsProperty(Intelligence::INTELLIGENCE);
    }

    /**
     * @return int
     */
    public function getNextLevelsCharismaModifier()
    {
        return $this->sumNextLevelsProperty(Charisma::CHARISMA);
    }

    /**
     * @return LevelRank
     */
    public function getHighestLevelRank()
    {
        $levelRanks = $this->getLevelRanks(); // already sorted by level
        $highestLevelRank = array_pop($levelRanks);

        return $highestLevelRank;
    }

    /**
     * @return array|LevelRank
     */
    private function getLevelRanks()
    {
        return array_map(
            function (ProfessionLevel $professionLevel) {
                return $professionLevel->getLevelRank();
            },
            $this->getLevels()
        );
    }
}
