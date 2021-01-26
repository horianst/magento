<?php

namespace Urgent\Cargus\Controller\Adminhtml\order;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Urgent\Cargus\Model\UrgentCargus;

/**
 * Class Send
 */
class Send extends Action implements CsrfAwareActionInterface
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Send constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(Context $context, PageFactory $resultPageFactory)
    {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * @return Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();

        if ($this->getRequest()->getParam('submited')) {
            $post = $this->getRequest()->getPostValue();

            $date = explode('.', $post['date']);
            $from = $date[2] . '-' . $date[1] . '-' . $date[0] . ' ' . $post['hour_from'] . ':00';
            $to = $date[2] . '-' . $date[1] . '-' . $date[0] . ' ' . $post['hour_to'] . ':00';

            $urgentCargus = new UrgentCargus();
            $orderId = $urgentCargus->sendOrder($post['id'], $from, $to);

            $block = $resultPage->getLayout()->getBlock('urgent.cargus.order.send');
            $block->setData('tallySheet', true);
            $block->setData('orderId', $orderId);
        }

        $resultPage->getConfig()->getTitle()->prepend(__('Confirma comanda'));
        return $resultPage;
    }
}
