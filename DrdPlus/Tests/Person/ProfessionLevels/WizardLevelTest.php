<?php
namespace DrdPlus\Tests\Person\ProfessionLevels;

use DrdPlus\Properties\Base\Intelligence;
use DrdPlus\Properties\Base\Will;


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
