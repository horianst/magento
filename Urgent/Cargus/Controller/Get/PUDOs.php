<?php
namespace Urgent\Cargus\Controller\Get;

use Magento\Framework\App\Action\Action;
use Urgent\Cargus\Model\UrgentCargus;
use Magento\Framework\App\ResourceConnection;

class PUDOs extends Action
{
    protected $resource;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        ResourceConnection $resource
    ) {
        $this->resource = $resource;
        return parent::__construct($context);
    }

    public function execute()
    {
        $urgentCargus = new UrgentCargus();
        $pudos = $urgentCargus->getPudoPoints();

        //Frontend if needed

        return null;
    }
}