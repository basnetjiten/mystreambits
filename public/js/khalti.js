$('.khalti').on('click', function () {


    const khaltiData = document.querySelector('#khalti');
    let donatorId = khaltiData.dataset.id;
    let amount = khaltiData.dataset.amount;

    let config = {
        // replace this key with yours
        /* "publicKey": "test_public_key_dc74e0fd57cb46cd93832aee0a390234"/!*process.env.MIX_KHALTI_LIVE_PUBLIC_KEY*!/,
         "productIdentity": donatorId,
         "productName": "OPSTREAMERS_SUPPORTER",
         "productUrl": "https://opstreamers.com/",*/
        "publicKey": "test_public_key_dc74e0fd57cb46cd93832aee0a390234",
        "productIdentity": "1234567890",
        "productName": "Drogon",
        "productUrl": "http://gameofthrones.com/buy/Dragons",
        // "merchant_extra": this.form.message,
        "eventHandler": {
            onSuccess(payload) {
                //on check out success
                // hit merchant api for initiating verification
                console.log(payload);
                let amount = payload.amount;
                let token = payload.token;

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).ajax({
                    type: "POST",
                    url: '{{route("payments.khalti.redirect")}}',
                    data: {amount: amount, token: token, donatorId: donatorId},
                    success: function (res) {
                        $('#warung-plain').load("/warung_plain/{category}");
                    }
                });

                /* axios.post('/khaltiClient', {token: token, amount: amount})
                     .then(() => {
                         toast({
                             type: 'success',
                             title: 'Payment successful'
                         });
                         //window.location.href = 'https://streamersalert.com';

                         Swal.fire({

                             title: 'Payment Successful!',
                             text: 'Thank you for your Support',
                             type: 'success',
                             allowOutsideClick: false,
                         }).then(() => {
                             window.location.href = 'https://streamersalert.com'
                         });


                     })
                     .catch((error) => {
                         console.log(error)
                     })*/

            },
            // onError handler is optional
            onError(error) {
                // handle errors
                console.log(error);
            },
            onClose() {
                console.log('widget is closing');
            }
        }
    };

    //takes user input and performs khalti chekout
    let donationAmount = amount * 100;
    let checkout = new KhaltiCheckout(config);


    // minimum transaction amount must be 10, i.e 1000 in paisa.
    checkout.show({amount: donationAmount});


});




