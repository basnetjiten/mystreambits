$('.imepay').on('click', function () {
    const imePayData = document.querySelector('#imepay');
    let donatorId = imePayData.dataset.id;
    let amount = imePayData.dataset.amount;
    console.log(donatorId+amount);
    /*$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    }).*/$.ajax({
        type: "POST",
        url: "/payments/imepay/redirect",
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},

        data: {amount: amount, donatorId: donatorId},
        success: function (response) {
            this.imePayData = response.data;
            console.log(this.imePayData);

            //window.location.href = 'https://payment.imepay.com.np:7979/WebCheckout/Checkout?data=' + this.imePayData;
           // window.location.href = 'https://stg.imepay.com.np:7979/WebCheckout/Checkout?data=' + this.imePayData;

        }
    });
});
