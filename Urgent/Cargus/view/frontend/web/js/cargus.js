require([ 'jquery', 'jquery/ui'], function($,url){
    $(function () {
        function do_replace(url) {
            var element = $("[name='city']");
            var value = element.val();

            var field_judet = $("select[name='region_id']");
            if (field_judet != null) {
                $.post(BASE_URL + 'cargus/get/cities?id=' + field_judet.val() + '&val=' + value, function (data) {
                    if (data != 'null')  {
                        var cagusElement = $("[name='cargus_select']");

                        if(cagusElement){
                            cagusElement.remove();
                        }

                        element.prop('type', 'hidden');
                        element.after('<select name="cargus_select" class="select" id="cargus_select">' + data + '</select>');
                        cagusElement = $("[name='cargus_select']");

                        cagusElement.on('change', function (){
                            var element = $("[name='city']");

                            element.val(cagusElement.val()).change();
                        });
                    }
                });
            }
        }

        $('input[name="city"]').each(function () {
            do_replace(url);
        });

        $(document).on('change', 'select[name="region_id"], select[name="country_id"]', function () {
            do_replace(url);
        });
    });
});