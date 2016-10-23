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

namespace WellCommerce\Bundle\ProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use WellCommerce\Bundle\AppBundle\Entity\DiscountablePrice;
use WellCommerce\Bundle\AppBundle\Entity\HierarchyAwareTrait;
use WellCommerce\Bundle\AvailabilityBundle\Entity\AvailabilityAwareTrait;
use WellCommerce\Bundle\CoreBundle\Behaviours\Enableable\EnableableTrait;
use WellCommerce\Bundle\CoreBundle\Entity\IdentifiableTrait;
use WellCommerce\Bundle\MediaBundle\Entity\MediaAwareTrait;
use WellCommerce\Bundle\ProductBundle\Entity\Extra\VariantExtraTrait;

/**
 * Class Variant
 *
 * @author  Adam Piotrowski <adam@wellcommerce.org>
 */
class Variant implements VariantInterface
{
    use IdentifiableTrait;
    use Timestampable;
    use HierarchyAwareTrait;
    use MediaAwareTrait;
    use AvailabilityAwareTrait;
    use ProductAwareTrait;
    use EnableableTrait;
    use VariantExtraTrait;
    
    /**
     * @var Collection
     */
    protected $options;
    
    /**
     * @var DiscountablePrice
     */
    protected $sellPrice;
    
    /**
     * @var float
     */
    protected $weight;
    
    /**
     * @var string
     */
    protected $symbol;
    
    /**
     * @var int
     */
    protected $stock;
    
    /**
     * @var string
     */
    protected $modifierType;
    
    /**
     * @var float
     */
    protected $modifierValue;
    
    public function __construct()
    {
        $this->options = new ArrayCollection();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getOptions() : Collection
    {
        return $this->options;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setOptions(Collection $options)
    {
        if ($this->options instanceof Collection) {
            $this->synchronizeOptions($options);
        }
        
        $this->options = $options;
    }
    
    protected function synchronizeOptions(Collection $options)
    {
        $this->options->map(function (VariantOptionInterface $option) use ($options) {
            if (false === $options->contains($option)) {
                $this->options->removeElement($option);
            }
        });
    }
    
    /**
     * {@inheritdoc}
     */
    public function getWeight() : float
    {
        return $this->weight;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setWeight(float $weight)
    {
        $this->weight = $weight;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getSymbol() : string
    {
        return $this->symbol;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setSymbol(string $symbol)
    {
        $this->symbol = $symbol;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getStock() : int
    {
        return $this->stock;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setStock(int $stock)
    {
        $this->stock = $stock;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getSellPrice() : DiscountablePrice
    {
        return $this->sellPrice;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setSellPrice(DiscountablePrice $sellPrice)
    {
        $this->sellPrice = $sellPrice;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getModifierValue() : float
    {
        return $this->modifierValue;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setModifierValue(float $modifierValue)
    {
        $this->modifierValue = $modifierValue;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getModifierType() : string
    {
        return $this->modifierType;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setModifierType(string $modifierType)
    {
        $this->modifierType = $modifierType;
    }
}
