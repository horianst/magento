function addOrder() {
    require([ 'jquery', 'jquery/ui'], function($){
        let urlq = document.URL;
        let urlvar = urlq.split('/');
        let single_order_id = urlvar[8];

        let soil = $('.page-title').html();
        soil = soil.replace('# ', '');

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
            alert('Nu ati selectat nicio comanda pentru a fi adaugata in lista de livrari Urgent Cargus!');
        }
    });
}



