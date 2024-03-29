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
 * Class Cities
 */
class Cities extends Action implements CsrfAwareActionInterface
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Cities constructor.
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
        $countyCode = $this->getRequest()->getParam('code');

        $urgentCargus = new UrgentCargus();
        $countiesList = $urgentCargus->getCounties();

        $counties = [];

        foreach ($countiesList as $county) {
            $counties[$county->Abbreviation] = $county->CountyId;
        }

        $cities = $urgentCargus->getCities($counties[$countyCode]);

        $response = '';

        foreach ($cities as $city) {
            $response = $response . '<option km="' . $city->ExtraKm . '">' . $city->Name . "</option>\n";
        }

        echo $response;
    }
}
