<?php
namespace DrdPlus\ProfessionLevels;

use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Intelligence;
use DrdPlus\Tests\ProfessionLevels\AbstractTestOfProfessionLevel;

class TheurgistLevelTest extends AbstractTestOfProfessionLevel
{

    /**
     * @return string[]
     */
    protected function getPrimaryProperties()
    {
        return [Intelligence::INTELLIGENCE, Charisma::CHARISMA];
    }

}
