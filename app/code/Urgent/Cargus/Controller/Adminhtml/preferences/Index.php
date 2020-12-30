<?php

namespace Urgent\Cargus\Controller\Adminhtml\preferences;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Cache\Manager;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;

/**
 * Class Index
 */
class Index extends Action implements CsrfAwareActionInterface
{
    const MENU_ID = 'Urgent_Cargus::preferences';

    /**
     * @var PageFactory
     */
    protected PageFactory $resultPageFactory;

    /**
     * @var WriterInterface
     */
    private  $configWriter;
    /**
     * @var ScopeConfigInterface
     */
    private  $scopeConfig;
    private Manager $cacheManager;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param WriterInterface $configWriter
     * @param Manager $cacheManager
     */
    public function __construct(Context $context, PageFactory $resultPageFactory, ScopeConfigInterface $scopeConfig, WriterInterface $configWriter, Manager $cacheManager)
    {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
        $this->configWriter = $configWriter;
        $this->scopeConfig = $scopeConfig;
        $this->cacheManager = $cacheManager;
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
     * Load the page defined in view/adminhtml/layout/preferences_index.xml
     *
     * @return Page
     */
    public function execute()
    {
        if(!empty($this->getRequest()->getPostValue())){
            $this->cacheManager->flush(['config']);
            $values = $this->getRequest()->getPostValue();
            unset($values['form_key']);
            $this->configWriter->save('urgent/cargus/preferences', serialize($values));

            $this->_view->getLayout()->createBlock('Urgent\Cargus\Block\Preferences', 'Preferences', ['data' => ['message' => 'success']]);

            $this->messageManager->addNoticeMessage(__('Preferintele au fost salvate!'));
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(static::MENU_ID);
        $resultPage->getConfig()->getTitle()->prepend(__('Preferinte'));

        return $resultPage;
    }
}
