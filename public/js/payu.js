$('.payu').on('click', function () {
    const payuData = document.querySelector('#payu');
    let donatorId = payuData.dataset.id;
    let amount = payuData.dataset.amount;

    $.ajax({
        type: "POST",
        url: "/payments/payu/redirect",
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},

        data: {amount: amount, donatorId: donatorId},
        success: function (response) {

            console.log(response);


            var options = {
                "access_key": "access_key_mZR7lyzeQw6Lbv9p", // Enter the Key ID generated from the Dashboard
                "order_id": order_id, // Enter the order_id from the create-order api
                "callback_handler": function (response) {
                    let orderId = response["nimbbl_order_id"];
                    let transactionId = response["nimbbl_transaction_id"];
                    let nimbblSignature = response["nimbbl_signature"];
                    let status = response["status"];

                    $.ajax({
                        type: "POST",
                        url: "/payments/verify/payu/redirect",
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},

                        data: {
                            donatorId: donatorId,
                            orderId: orderId,
                            nimbblSignature: nimbblSignature,
                            transactionId: transactionId
                        },
                        success: function (response) {

                        }
                    });


                },
                "custom": {
                    "key_1": "val_1",
                    "key_2": "val_2"
                },


            };

            var checkout = new NimbblCheckout(options);

            checkout.open(order_id);


        }
    });


});