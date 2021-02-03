<?php

namespace Urgent\Cargus\Block\Adminhtml\Order;

use Magento\Sales\Block\Adminhtml\Order\View as OrderView;

class ButtonOrder
{
    public function beforeSetLayout(OrderView $subject)
    {
        $subject->addButton(
            'order_custom_button',
            [
                'label' => __('Adauga in lista de livrari Cargus'),
                'class' => __('custom-button'),
                'id' => 'order-view-custom-button',
                'onclick' => 'addOrder()'
            ]
        );
    }
}
