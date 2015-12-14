<?php
namespace DrdPlus\Tests\Person\ProfessionLevels;

use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Will;

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
