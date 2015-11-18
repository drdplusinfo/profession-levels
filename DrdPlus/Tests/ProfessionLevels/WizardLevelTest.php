<?php
namespace DrdPlus\ProfessionLevels;

use DrdPlus\Properties\Base\Intelligence;
use DrdPlus\Properties\Base\Will;
use DrdPlus\Tests\ProfessionLevels\AbstractTestOfProfessionLevel;

class WizardLevelTest extends AbstractTestOfProfessionLevel
{

    /**
     * @return string[]
     */
    protected function getPrimaryProperties()
    {
        return [Will::WILL, Intelligence::INTELLIGENCE];
    }

}
