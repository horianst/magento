requirejs([ 'jquery', 'jquery/ui', 'Magento_Checkout/js/model/quote', 'Magento_Checkout/js/model/shipping-rate-registry'], function($,url,mainQuote, rateReg){
    $(function () {
        function do_replace(url, judet = null, type = '') {

            if(judet) {
                var field_judet = judet;
            } else {
                var field_judet = $("select[name='region_id']").val();
            }

            switch (type) {
                case "new":
                    var element = $('#shipping-new-address-form').find("[name='city']");
                    break;
                case "load":
                    var element = $('#checkout-payment-method-load').find("[name='city']");
                    break;
                default:
                    var element = $("[name='city']");
            }

            var value = element.val();

            if (field_judet.toString() !== null && field_judet.toString() !== '' && field_judet.toString() !== 'undefined') {
                $.post(BASE_URL + 'cargus/get/cities?id=' + field_judet + '&val=' + value, function (data) {
                    if (data != 'null')  {
                        var cagusElement = $("[name='cargus_select']");

                        if(cagusElement){
                            cagusElement.remove();
                        }

                        element.prop('type', 'hidden');
                        element.after('<select name="cargus_select" class="select" id="cargus_select">' + data + '</select>');
                        cagusElement = $("[name='cargus_select']");

                        cagusElement.on('change',{element: element, type: type, field_judet: field_judet},  function(event) {
                            event.data.element.val(cagusElement.val()).change();

                            var address = mainQuote.shippingAddress();

                            address.city = cagusElement.val();
                            address.regionId = field_judet;
                            address.region = $('#cargus_select option[value="' + field_judet + '"]').html();

                            rateReg.set(address.getKey(), null);
                            rateReg.set(address.getCacheKey(), null);
                            mainQuote.shippingAddress(address);

                        });
                    }
                });
            }
        }

        $('input[name="city"]').each(function () {
            do_replace(url);
        });

        $(document).on('change', 'select[name="country_id"]', function () {
            do_replace(url);
        });

        $(document).on('change', 'select[name="region_id"]', function () {
            if ( $(this).parents('div#shipping-new-address-form').length ) {
                do_replace(url, $(this).val(), 'new');
            } else if ($(this).parents('div#checkout-payment-method-load').length){
                do_replace(url, $(this).val(), 'load');
            } else {
                do_replace(url, $(this).val());
            }
        });
    });
});