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
 * Class AwbPrint
 */
class AwbPrint extends Action implements CsrfAwareActionInterface
{
    /**
     * AwbPrint constructor.
     *
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        parent::__construct($context);
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
        $barCodes = $this->getRequest()->getParam('bar_codes');
        $format = $this->getRequest()->getParam('format');

        if ($barCodes) {
            $urgentCargus = new UrgentCargus();
            $print = $urgentCargus->printAwb($barCodes, $format);

            header('Content-type:application/pdf');
            echo base64_decode($print);
            die();
        }
        $this->_redirect('cargus/order/index');
    }
}
