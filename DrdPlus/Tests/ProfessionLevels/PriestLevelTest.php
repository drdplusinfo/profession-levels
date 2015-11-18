<?php
namespace DrdPlus\ProfessionLevels;

use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Will;
use DrdPlus\Tests\ProfessionLevels\AbstractTestOfProfessionLevel;

class PriestLevelTest extends AbstractTestOfProfessionLevel
{

    /**
     * @return string[]
     */
    protected function getPrimaryProperties()
    {
        return [Will::WILL, Charisma::CHARISMA];
    }

}
