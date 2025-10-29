$(document).ready(function () {
    processBarcode();
    $("#barcode").on("keyup", function () {
        processBarcode();
    });

    $("#print_btn").on("click", function () {
        // select print button with class "print," then on click run callback function
        $("#print_section").print(/*options*/);
    });


    function processBarcode() {
        const inputVal = $("#barcode").val();
        if(!inputVal) {
            return;
        }else{
            JsBarcode("#b-image-show", $("#barcode").val());
            $("#barcode-value").val($("#b-image-show").attr("src"));
        }
    }

    $(".generate-barcode").click(function (e) {
        processBarcode();
    });

    if ($(".summernote").length > 0) {
        $('.summernote').summernote();
    }

    $(document).on('click','.list_payment_btn', function(e){
        e.preventDefault();

        let id = $(this).data('id')
        let url = `/order/${id}/payment-list`
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                $('.loader').show();
            },
            success: function(response) {
                $('.loader').hide();
                if(response){

                    $("#my-modal").html(response.html);
                    var myModal = new bootstrap.Modal(
                        document.getElementById("add_payment_modal"), {
                            keyboard: false,
                        }
                    );
                    myModal.show();


                }

                // console.log(response)
            },
            error: function() {
                $('.loader').hide();
                alert('Failed to fetch data. Please try again.');
            }
        });

    })

    $(document).on('click', '#paginate_section .pagination a',function(event)
    {

        event.preventDefault();

        var url = $(this).attr('href');


        $.ajax({
                url: url+'&isPaginate=true',
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    $('.loader').show();
                },
                success: function(response) {
                    $('.loader').hide();
                    if(response){
                        $("#transaction_list").html(response.html);

                    }

                    // console.log(response)
                },
                error: function() {
                    $('.loader').hide();
                    alert('Failed to fetch data. Please try again.');
                }
            });

    });

    if($(window).width() < 576){
        $("body").removeClass("sidebar-enable");
        $("body").removeClass("vertical-collpsed");
    }


});


