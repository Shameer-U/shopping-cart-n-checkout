<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>Accept a payment</title>
    <meta name="description" content="A demo of a payment on Stripe" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    {{-- <link rel="stylesheet" href="{{ URL::to('stripe/checkout.css') }}" /> --}}
  </head>
  <body>
    <!-- Display a payment form -->
    <form id="payment-form" action="{{ url('/checkout-cashier') }}" method="post">

        <input id="card-holder-name" type="text">

        <div id="card-element"></div>

        <input id="card-holder-name" type="hidden">

        <input id="payment-method-id" name="paymentMethodId" type="hidden">

        <button id="card-button">
            Process Payment
        </button>
        
    </form>

    <script src="https://js.stripe.com/v3/"></script>
    <script src="{{ asset('src/jquery/jquery-3.6.0.min.js') }}"></script>
    {{-- <script src="{{ URL::to('stripe/checkout.js') }}"></script> --}}
    <script>
        // This is your test publishable API key.
        const stripe = Stripe("{{ env('STRIPE_KEY') }}");
        elements = stripe.elements();
        const cardElement = elements.create("card",{hidePostalCode: true});
        cardElement.mount("#card-element");

        const cardHolderName = document.getElementById('card-holder-name');
        const cardButton = document.getElementById('card-button');

        cardButton.addEventListener('click', async (e) => {
            e.preventDefault();

            const { paymentMethod, error } = await stripe.createPaymentMethod(
                'card', cardElement, {
                    billing_details: { name: cardHolderName.value }
                }
            );

            if (error) {
                // Display "error.message" to the user...
                alert(error.message);
            }
            else {
                alert('Card verified successfully');
                console.log(paymentMethod.id);
                document.getElementById('payment-method-id').setAttribute('value', paymentMethod.id);
                document.getElementById('payment-form').submit();

                // $.ajax({ 
                //     method:'post',
                //     url: "{{ url('/checkout-stripe') }}",
                //     data: { _token:'{{ csrf_token() }}', paymentMethodId:paymentMethod.id}
                // }).always(function (data) {
                
                // }).done(function (data) {
                    
                // }).fail(function (data) {
                //     alert('Some error occured');
                // });
            }

        });

    </script>
  </body>
</html>