<?php

namespace Urgent\Cargus\Controller\Adminhtml\order;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Urgent\Cargus\Model\UrgentCargus;

/**
 * Class Index
 */
class Index extends Action implements HttpGetActionInterface
{
    const MENU_ID = 'Urgent_Cargus::order';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(Context $context, PageFactory $resultPageFactory)
    {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
    }

    public static function getPickupPoints()
    {
        $urgentCargus = new UrgentCargus();
        return $urgentCargus->execute();
    }

    /**
     * @return Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(static::MENU_ID);
        $resultPage->getConfig()->getTitle()->prepend(__('Comanda'));

        return $resultPage;
    }
}
