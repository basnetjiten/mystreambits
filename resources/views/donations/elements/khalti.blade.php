<a class="khalti list-group-item" id ="khalti"
   data-id="XX"
   data-amount="XXX">
    <b>Khalti</b>
    <img src="{{ asset('assets/img/khalti.png') }}">
</a>



{{--@push('footer-scripts')
    <script>
        $(document).ready(function () {
            // Add a 'click' event instead of an invalid 'submit' event.
            $('#deleteItem').on('click', function (e) {
                // Prevent the button from submitting the form
                e.preventDefault();
                console.log("test")

                // The rest of your code
            });
        });
        /* document.querySelector('.list-group-item').addEventListener('click', function(ev) {
             console.log("hellll runner");
             // e.preventDefault();
             //e.stopPropagation();

             //var data = $(this).data('href');
             //e.preventDefault();
             // let donatorId = $(this).data('value');

             //takes user input and performs khalti chekout
             //let donationAmount = this.donatedAmount * 100;
             //let checkout = new KhaltiCheckout(config);
             //checkout.show({amount: donationAmount});
         });*/
    </script>


    });

@endpush--}}

