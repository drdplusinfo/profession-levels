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
use Granam\Scalar\Tools\ValueDescriber;
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
    public function getId()
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
     * All levels, achieved at any profession, sorted ascending by level rank
     *
     * @return array|ProfessionLevel[]
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

        return $levels[0];
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
     * Sorted by level ascending
     *
     * @return ProfessionLevel[]
     */
    public function getNextLevels()
    {
        $levels = $this->getLevels();
        array_shift($levels); // remote the fist level

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
    public function addLevel(ProfessionLevel $newLevel)
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
        switch ($professionLevel->getProfession()->getValue()) {
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
                throw new Exceptions\UnknownProfession(
                    "Profession level of ID {$professionLevel->getId()} is of unknown profession "
                    . $professionLevel->getProfession()->getValue()
                );
        }
    }

    private function checkProhibitedMultiProfession(ArrayCollection $previousLevels, ProfessionLevel $newLevel)
    {
        if (count($previousLevels) !== count($this->getLevels())) {
            throw new Exceptions\MultiProfessionsAreProhibited(
                'Profession levels of ID ' . ValueDescriber::describe($this->id)
                . " are already set for profession {$this->getAlreadySetProfessionCode()}"
                . ', given  ' . $newLevel->getProfession()->getValue()
            );
        }
    }

    /** @return string */
    private function getAlreadySetProfessionCode()
    {
        return array_map(
            function (ProfessionLevel $level) {
                return $level->getProfession()->getValue();
            },
            $this->getLevels()
        )[0];
    }

    private function checkNewLevelSequence(ArrayCollection $previousProfessionLevels, ProfessionLevel $newLevel)
    {
        if ($newLevel->getLevelRank()->getValue() !== ($previousProfessionLevels->count() + 1)) {
            throw new \LogicException(
                'Unexpected level of given profession level. Expected ' . ($previousProfessionLevels->count() + 1)
                . ', got ' . $newLevel->getLevelRank()->getValue()
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

    /**
     * @param ArrayCollection|ProfessionLevel[] $previousNextLevels
     * @param BaseProperty $propertyIncrement
     * @return bool
     */
    private function checkPrimaryPropertyIncrementInARow(ArrayCollection $previousNextLevels, BaseProperty $propertyIncrement)
    {
        // main property can be increased only twice in sequence
        if ($previousNextLevels->count() < 2) {
            return true;
        }
        $lastPrevious = $previousNextLevels->last();
        if (!$this->hasIncrementSameProperty($lastPrevious, $propertyIncrement)) {
            return true;
        }
        /** @var ProfessionLevel $lastPrevious */
        $lastButOnePrevious = $previousNextLevels->get($lastPrevious->getLevelRank()->getValue() - 1);
        if (!$this->hasIncrementSameProperty($lastButOnePrevious, $propertyIncrement)) {
            return true;
        }
        throw new Exceptions\TooHighPrimaryPropertyIncrease(
            "Primary property can not be increased more then twice in a row, got {$propertyIncrement->getCode()} to increase."
        );
    }

    private function hasIncrementSameProperty(ProfessionLevel $testedProfessionLevel, BaseProperty $patternPropertyIncrement)
    {
        return $this->getSamePropertyIncrement($testedProfessionLevel, $patternPropertyIncrement)->getValue() > 0;
    }

    private function getSamePropertyIncrement(ProfessionLevel $searchedThroughProfessionLevel, BaseProperty $patternPropertyIncrement)
    {
        return $searchedThroughProfessionLevel->getBasePropertyIncrement($patternPropertyIncrement->getCode());
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
        throw new \LogicException(
            "Secondary property increase has to be at least alternately, got {$propertyIncrement->getCode()} again to increase."
        );
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

    /**
     * @param $propertyCode
     * @return int
     */
    public function getPropertyModifierForFirstProfession($propertyCode)
    {
        if (!$this->hasFirstLevel()) {
            return 0;
        }

        return $this->getFirstLevel()->getBasePropertyIncrement($propertyCode)->getValue();
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
     * Get strength modifier
     *
     * @return int
     */
    public function getStrengthModifierSummary()
    {
        return $this->getPropertyModifierSummary(Strength::STRENGTH);
    }

    /**
     * @param string $propertyCode
     *
     * @return int
     */
    public function getPropertyModifierSummary($propertyCode)
    {
        return array_sum($this->getLevelsPropertyModifiers($propertyCode));
    }

    /**
     * @param $propertyCode
     *
     * @return int[]
     */
    private function getLevelsPropertyModifiers($propertyCode)
    {
        return array_map(
            function (ProfessionLevel $professionLevel) use ($propertyCode) {
                return $professionLevel->getBasePropertyIncrement($propertyCode)->getValue();
            },
            $this->getLevels()
        );
    }

    /**
     * @param string $propertyCode
     *
     * @return int
     */
    private function sumNextLevelsProperty($propertyCode)
    {
        return array_sum($this->getNextLevelsPropertyModifiers($propertyCode));
    }

    /**
     * @param $propertyCode
     *
     * @return int[]
     */
    private function getNextLevelsPropertyModifiers($propertyCode)
    {
        return array_map(
            function (ProfessionLevel $professionLevel) use ($propertyCode) {
                return $professionLevel->getBasePropertyIncrement($propertyCode)->getValue();
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
            $this->getLevels() // already sorted by level
        );
    }
}
