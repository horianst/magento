function addOrder() {
    require([ 'jquery', 'jquery/ui'], function($){
        let urlq = document.URL;
        let single_order_id = urlq.split('order_id/').pop().split('/key')[0];

        let soil = $('.page-title').html();
        soil = soil.replace('#', '');

        let ajaxRequest;

        ajaxRequest = $.ajax({
            async: false,
            url: "../../../../../../../cargus/order/addorder",
            type: 'POST',
            data: {
                id: single_order_id,
                id_long: soil,
                timestamp: new Date().getTime(),
                form_key:FORM_KEY
            },
        });

        ajaxRequest.done(function (response, textStatus, jqXHR) {
            if (response == 'ok') {
                alert('Comanda curenta a fost adaugata cu succes in lista pentru livrare.');
            } else if (response == 'old') {
                alert('Comanda curenta a fost livrata sau este deja in lista pentru livrare.');
            } else {
                alert('Comanda curenta nu a putut fi adaugata in lista pentru livrare.');
            }
        });
    });
}

function addOrders() {
    require([ 'jquery', 'jquery/ui'], function($){
        let status_final = '';
        let status_final_1 = '';
        let status_final_2 = '';
        let status_final_3 = '';
        let ajaxRequest;

        $("input:checkbox[id^=idscheck]:checked").each(function () {
            let id = $(this).val();
            var id_long = $(this).closest('td').next('td').find('div').text();

            ajaxRequest = $.ajax({
                async: false,
                url: "../../../../../cargus/order/addorder",
                type: 'POST',
                data: {
                    id: id,
                    id_long: id_long,
                    timestamp: new Date().getTime(),
                    form_key:FORM_KEY
                },
            });

            ajaxRequest.done(function (response, textStatus, jqXHR) {
                if (response == 'ok'){
                    status_final_1 = status_final_1 + " - " + id_long + "\n";
                } else if (response == 'old') {
                    status_final_2 = status_final_2 + " - " + id_long + "\n";
                } else {
                    status_final_3 = status_final_3 + " - " + id_long + "\n";
                }
            });
        });

        if (status_final_1) {
            status_final = status_final + "Comenzile de mai jos au fost adaugate cu succes in lista pentru livrare:\n" + status_final_1;
        }
        if (status_final_2) {
            status_final = status_final + "Comenzile de mai jos au fost livrate sau sunt deja in lista pentru livrare:\n" + status_final_2;
        }
        if (status_final_3) {
            status_final = status_final + "Comenzile de mai jos nu au putut fi adaugate in lista pentru livrare:\n" + status_final_3;
        }
        if (status_final) {
            alert(status_final);
        } else {
            alert('Nu ati selectat nicio comanda pentru a fi adaugata in lista de livrari Cargus!');
        }
    });
}

require([ 'jquery', 'jquery/ui'], function($,url){
    $(function () {
        function do_replace(url) {
            var element = $("[id='order-billing_address_city']");
            var value = element.val();

            var field_judet = $("select[id='order-billing_address_region_id']");
            if (field_judet != null) {
                $.post('../../../../../../cargus/get/cities?id=' + field_judet.val() + '&val=' + value, function (data) {
                    if (data != 'null')  {
                        var cagusElement = $("[name='cargus_select']");

                        if(cagusElement){
                            cagusElement.remove();
                        }

                        element.prop('type', 'hidden');
                        element.after('<select name="cargus_select" class="required-entry required-entry _required select admin__control-select" id="cargus_select">' + data + '</select>');
                        cagusElement = $("[name='cargus_select']");

                        cagusElement.on('change', function (){
                            var element = $("[id='order-billing_address_city']");

                            element.val(cagusElement.val()).change();
                        });
                    }
                });
            }
        }

        $('input[name="city"]').each(function () {
            do_replace(url);
        });

        $(document).on('change', 'select[id="order-billing_address_region_id"], select[id="order-billing_address_country_id"]', function () {
            do_replace(url);
        });
    });
});



