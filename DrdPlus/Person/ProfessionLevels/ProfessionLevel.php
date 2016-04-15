<?php
namespace DrdPlus\Person\ProfessionLevels;

use Doctrine\ORM\Mapping as ORM;
use Doctrineum\Entity\Entity;
use DrdPlus\Properties\Base\Agility;
use DrdPlus\Properties\Base\BaseProperty;
use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Intelligence;
use DrdPlus\Properties\Base\Knack;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Properties\Base\Will;
use DrdPlus\Professions\Profession;
use Granam\Strict\Object\StrictObject;
use Granam\Tools\ValueDescriber;

/**
 * @ORM\MappedSuperclass()
 */
abstract class ProfessionLevel extends StrictObject implements Entity
{

    /**
     * @var integer
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue()
     */
    private $id;

    /**
     * @var Profession
     * @ORM\Column(type="profession")
     */
    private $profession;

    /**
     * @var LevelRank
     * @ORM\Column(type="level_rank")
     */
    private $levelRank;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $levelUpAt;

    /**
     * @var Strength
     * @ORM\Column(type="strength")
     */
    private $strengthIncrement;

    /**
     * @var Agility
     * @ORM\Column(type="agility")
     */
    private $agilityIncrement;

    /**
     * @var Knack
     * @ORM\Column(type="knack")
     */
    private $knackIncrement;

    /**
     * @var Will
     * @ORM\Column(type="will")
     */
    private $willIncrement;

    /**
     * @var Intelligence
     * @ORM\Column(type="intelligence")
     */
    private $intelligenceIncrement;

    /**
     * @var Charisma
     * @ORM\Column(type="charisma")
     */
    private $charismaIncrement;

    public function __construct(
        Profession $profession,
        LevelRank $levelRank,
        Strength $strengthIncrement,
        Agility $agilityIncrement,
        Knack $knackIncrement,
        Will $willIncrement,
        Intelligence $intelligenceIncrement,
        Charisma $charismaIncrement,
        \DateTimeImmutable $levelUpAt = null
    )
    {
        $this->checkLevelRank($levelRank);
        $this->checkPropertyIncrement($strengthIncrement, $profession);
        $this->checkPropertyIncrement($agilityIncrement, $profession);
        $this->checkPropertyIncrement($knackIncrement, $profession);
        $this->checkPropertyIncrement($willIncrement, $profession);
        $this->checkPropertyIncrement($intelligenceIncrement, $profession);
        $this->checkPropertyIncrement($charismaIncrement, $profession);
        $this->checkPropertySumIncrement(
            $levelRank,
            $strengthIncrement,
            $agilityIncrement,
            $knackIncrement,
            $willIncrement,
            $intelligenceIncrement,
            $charismaIncrement
        );

        $this->profession = $profession;
        $this->levelRank = $levelRank;
        $this->strengthIncrement = $strengthIncrement;
        $this->agilityIncrement = $agilityIncrement;
        $this->knackIncrement = $knackIncrement;
        $this->willIncrement = $willIncrement;
        $this->intelligenceIncrement = $intelligenceIncrement;
        $this->charismaIncrement = $charismaIncrement;
        $this->levelUpAt = $levelUpAt ?: new \DateTimeImmutable();
    }

    abstract protected function checkLevelRank(LevelRank $levelRank);

    private function checkPropertySumIncrement(
        LevelRank $levelRank,
        Strength $strength,
        Agility $agility,
        Knack $knack,
        Will $will,
        Intelligence $intelligence,
        Charisma $charisma
    )
    {
        if ($levelRank->isNextLevel()) { // note: first level properties are covered by one-by-one tests
            $sumOfProperties = $this->sumProperties($strength, $agility, $knack, $will, $intelligence, $charisma);
            if ($sumOfProperties !== $this->getExpectedSumOfNextLevelProperties()) {
                throw new Exceptions\InvalidNextLevelPropertiesSum(
                    "Sum of {$levelRank->getValue()}. level properties should be "
                    . $this->getExpectedSumOfNextLevelProperties()
                    . ', got ' . $sumOfProperties
                );
            }
        }
    }

    const PRIMARY_PROPERTY_NEXT_LEVEL_INCREMENT_SUM = 1;
    const SECONDARY_PROPERTY_NEXT_LEVEL_INCREMENT_SUM = 1;

    private function getExpectedSumOfNextLevelProperties()
    {
        return static::PRIMARY_PROPERTY_NEXT_LEVEL_INCREMENT_SUM + static::SECONDARY_PROPERTY_NEXT_LEVEL_INCREMENT_SUM;
    }

    private function sumProperties(
        Strength $strengthIncrement,
        Agility $agilityIncrement,
        Knack $knackIncrement,
        Will $willIncrement,
        Intelligence $intelligenceIncrement,
        Charisma $charismaIncrement
    )
    {
        return $strengthIncrement->getValue() + $agilityIncrement->getValue() + $knackIncrement->getValue()
        + $willIncrement->getValue() + $intelligenceIncrement->getValue() + $charismaIncrement->getValue();
    }

    abstract protected function checkPropertyIncrement(BaseProperty $property, Profession $profession);

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getLevelUpAt()
    {
        return $this->levelUpAt;
    }

    /**
     * @return LevelRank
     */
    public function getLevelRank()
    {
        return $this->levelRank;
    }

    /** @return bool */
    public function isFirstLevel()
    {
        return $this->getLevelRank()->getValue() === 1;
    }

    public function isNextLevel()
    {
        return $this->getLevelRank()->getValue() > 1;
    }

    /**
     * @param string $propertyCode
     *
     * @return bool
     */
    public function isPrimaryProperty($propertyCode)
    {
        return static::isProfessionPrimaryProperty($this->getProfession(), $propertyCode);
    }

    protected static function isProfessionPrimaryProperty(Profession $profession, $propertyCode)
    {
        return $profession->isPrimaryProperty($propertyCode);
    }

    /**
     * @return Strength
     */
    public function getStrengthIncrement()
    {
        return $this->strengthIncrement;
    }

    /**
     * @return Agility
     */
    public function getAgilityIncrement()
    {
        return $this->agilityIncrement;
    }

    /**
     * @return Knack
     */
    public function getKnackIncrement()
    {
        return $this->knackIncrement;
    }

    /**
     * @return Will
     */
    public function getWillIncrement()
    {
        return $this->willIncrement;
    }

    /**
     * @return Intelligence
     */
    public function getIntelligenceIncrement()
    {
        return $this->intelligenceIncrement;
    }

    /**
     * @return Charisma
     */
    public function getCharismaIncrement()
    {
        return $this->charismaIncrement;
    }

    public function getBasePropertyIncrement($propertyCode)
    {
        switch ($propertyCode) {
            case Strength::STRENGTH :
                return $this->getStrengthIncrement();
            case Agility::AGILITY :
                return $this->getAgilityIncrement();
            case Knack::KNACK :
                return $this->getKnackIncrement();
            case Will::WILL :
                return $this->getWillIncrement();
            case Intelligence::INTELLIGENCE :
                return $this->getIntelligenceIncrement();
            case Charisma::CHARISMA :
                return $this->getCharismaIncrement();
            default :
                throw new Exceptions\UnknownBaseProperty(
                    'Unknown property ' . ValueDescriber::describe($propertyCode)
                );
        }
    }

    /**
     * @return Profession
     */
    public function getProfession()
    {
        return $this->profession;
    }
}
