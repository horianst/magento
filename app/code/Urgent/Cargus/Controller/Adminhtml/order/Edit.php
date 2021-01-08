<?php

namespace Urgent\Cargus\Controller\Adminhtml\order;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Urgent\Cargus\Model\UrgentCargus;

/**
 * Class Index
 */
class Edit extends Action implements CsrfAwareActionInterface
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ResourceConnection $resource
     */
    public function __construct(Context $context, PageFactory $resultPageFactory, ResourceConnection $resource)
    {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
        $this->_resource = $resource;
    }

    public function createCsrfValidationException(RequestInterface $request): ? InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * Load the page defined in view/adminhtml/layout/order_index.xml
     *
     * @return Page
     */
    public function execute()
    {
        $post = $this->getRequest()->getPost();

        if(isset($post['order_id'])){
            $fields = [];
            foreach ($post as $key => $val) {
                if ($key == 'form_key' || $key == 'order_id'){
                    continue;
                }

                $fields[] = "`".$key."` = '".addslashes($val)."'";
            }

            $connection = $this->_resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
            $query = "UPDATE `awb_expeditii` SET ".implode(', ', $fields)." WHERE `order_id` = '".$post['order_id']."'";
            $connection->query($query);

            $this->messageManager->addNoticeMessage(__('Modificarile au fost salvate!'));
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Editeaza AWB'));

        return $resultPage;
    }
}
