<?php

namespace Urgent\Cargus\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Urgent\Cargus\Api\Data\AWBExpeditiiInterface;

/**
 * Class File
 * @package Urgent\Cargus\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AWBExpeditii extends AbstractModel implements AWBExpeditiiInterface, IdentityInterface
{
    /**
     * Cache tag
     */
    const CACHE_TAG = 'awb_expeditii';

    /**
     * Return identities
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::POST_ID, $id);
    }

    /**
     * Post Initialization
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Urgent\Cargus\Model\ResourceModel\AWBExpeditii');
    }
}
