<?php
/*
 * WellCommerce Open-Source E-Commerce Platform
 * 
 * This file is part of the WellCommerce package.
 *
 * (c) Adam Piotrowski <adam@wellcommerce.org>
 * 
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */

namespace WellCommerce\Bundle\GeneratorBundle\Dumper;

use Wingu\OctopusCore\CodeGenerator\PHP\OOP\ClassGenerator;

/**
 * Interface EntityDumperInterface
 *
 * @author  Adam Piotrowski <adam@wellcommerce.org>
 */
interface EntityDumperInterface
{
    /**
     * Dumps extended entity to file
     *
     * @param string         $targetPath
     * @param ClassGenerator $generator
     */
    public function dump($targetPath, ClassGenerator $generator);
}