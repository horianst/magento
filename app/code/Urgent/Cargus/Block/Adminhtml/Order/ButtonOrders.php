<?php

namespace Urgent\Cargus\Block\Adminhtml\Order;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class ButtonOrders implements ButtonProviderInterface
{
    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @var Context
     */
    private $context;

    /**
     * ButtonOrders constructor.
     *
     * @param AuthorizationInterface $authorization
     * @param Context $context
     */
    public function __construct(
        AuthorizationInterface $authorization,
        Context $context
    ) {
        $this->authorization = $authorization;
        $this->context = $context;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        if (!$this->authorization->isAllowed('Magento_Cms::save')) {
            return [];
        }

        return [
            'label' => __('Adauga in lista de livrari Urgent Cargus'),
            'on_click' => 'addOrders()',
            'class' => 'primary',
            'sort_order' => 10
        ];
    }
}
