<?php

namespace Urgent\Cargus\Controller\Adminhtml\order;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;

/**
 * Class WaitDelete
 */
class WaitDelete extends Action implements CsrfAwareActionInterface
{
    /**
     * @var ResourceConnection
     */
    private $_resource;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param ResourceConnection $resource
     */
    public function __construct(Context $context, ResourceConnection $resource)
    {
        parent::__construct($context);
        $this->_resource = $resource;
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    public function execute()
    {
        $awbs = $this->getRequest()->getParam('awb');

        if ($awbs) {
            $connection = $this->_resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
            $awbExpeditii = $connection->getTableName('awb_expeditii');

            $query = "DELETE FROM `" . $awbExpeditii . "` WHERE order_id IN (" . implode(',', $awbs) . ")";
            $connection->query($query);

            $this->messageManager->addNoticeMessage(__('AWB-urile selectate au fost sterse!'));
        }
        $this->_redirect('cargus/order/index');
    }
}
