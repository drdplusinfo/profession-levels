<?php
namespace DrdPlus\Person\ProfessionLevels;

use Doctrine\ORM\Mapping as ORM;
use DrdPlus\Properties\Base\Agility;
use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Intelligence;
use DrdPlus\Properties\Base\Knack;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Properties\Base\Will;
use \DrdPlus\Professions\Wizard;

/**
 * Wizard
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class WizardLevel extends ProfessionLevel
{
    /**
     * Inner link, used by Doctrine only
     * @var ProfessionLevels
     *
     * @ORM\ManyToOne(targetEntity="ProfessionLevels", inversedBy="wizardLevels")
     */
    protected $professionLevels;

    public static function createFirstLevel(Wizard $wizard, \DateTimeImmutable $levelUpAt = null)
    {
        return parent::createFirstLevelFor($wizard, $levelUpAt);
    }

    public function __construct(
        Wizard $wizard,
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
        parent::__construct(
            $wizard,
            $levelRank,
            $strengthIncrement,
            $agilityIncrement,
            $knackIncrement,
            $willIncrement,
            $intelligenceIncrement,
            $charismaIncrement,
            $levelUpAt
        );
    }

}
