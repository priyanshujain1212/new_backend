$(document).ready(function () {
    'use strict';
    $(document).on('click', '.quantity-change-btn', function () {
        let newQuantity = 0;
        let quantity = parseInt($(this).parent().parent().children().eq(1).val());
        let rowId = $(this).parent().parent().children().eq(1).attr('id');

        if ($(this).attr('id') == 'button-plus') {
            newQuantity = quantity + 1;
        } else if ($(this).attr('id') == 'button-minus') {
            if ((quantity - 1) == 0) {
                newQuantity = 1;
            } else {
                newQuantity = quantity - 1;
            }
        }
        quantityUpdate(newQuantity, rowId);
    });

    let oldQuantity = 0;
    $(document).on('click', '.quantity-change', function () {
        let rowId = $(this).attr('id');
        oldQuantity = $('#' + rowId).val();
    });

    $(document).on('change', '.quantity-change', function () {
        let newQuantity = $(this).val();
        let rowId = $(this).attr('id');
        quantityUpdate(newQuantity, rowId, oldQuantity);
    });

    function quantityUpdate(newQuantity, rowId, oldQuantity = null) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'html',
            data: {"rowId": rowId, "quantity": newQuantity, "deliveryCharge": deliveryCharge},
            success: function (data) {
                let response = JSON.parse(data);
                if (response.status) {
                    $('.price-' + rowId).text(response.price);
                    $('.total-price-js').text(response.totalPrice);
                    $('.total-js').text(response.total);
                    $('#' + rowId).val(newQuantity);
                } else {
                    alert(response.message);
                    if (oldQuantity) {
                        $('#' + rowId).val(oldQuantity);
                    }
                }
            }
        });
    }
});






