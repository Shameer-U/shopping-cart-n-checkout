<!DOCTYPE html>
<html lang="en">
<head>
  <title>Stripe Payment</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    
    <div class="container" style="margin-top:10%;margin-bottom:10%">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <form action="{{ url('/post-checkout-stripe') }}"  method="post" id="payment-form">
                        @csrf 
                        <div>
                            <div class="card-header">
                                <label for="card-element">
                                    Enter your details
                                </label>
                            </div>
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label for="name">Name</label>
                                        <input type="text" id="name" class="form-control" required name="name">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="address">Address</label>
                                        <input type="text" id="address" class="form-control"  required name="address">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="card-name">Card Holder Name</label>
                                        <input type="text" id="card-name" class="form-control" required> 
                                    </div> 
                                </div> 
                            </div>  
                        </div>              
                        <div>
                            <div class="card-header">
                                <label for="card-element">
                                    Enter your credit card information
                                </label>
                            </div>
                            <div class="card-body">
                                <div id="card-element">
                                    <!-- A Stripe Element will be inserted here. -->
                                </div>
                                <!-- Used to display form errors. -->
                                <div id="card-errors" role="alert"></div>
                                <input type="hidden" name="plan" value="" />
                            </div>
                        </div>
                        <input type="hidden" id="payment-id" name="payment-id" value="" />
                        <div class="card-footer">
                          <button id="card-button" class="btn btn-dark" type="submit" data-secret="{{ $intent }}"> Pay </button>
                        </div>
                  </form>
              </div>
          </div>
      </div>
  </div>
  <script src="https://js.stripe.com/v3/"></script>
  <script>
        // Custom styling can be passed to options when creating an Element.
        var style = {
            base: {
                color: '#32325d',
                lineHeight: '18px',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };
        
        const stripe = Stripe("{{ env('STRIPE_KEY') }}"); // Create a Stripe client.
        const elements = stripe.elements(); // Create an instance of Elements.
        const cardElement = elements.create('card', { hidePostalCode: true, style: style }); // Create an instance of the card Element.
        const cardButton = document.getElementById('card-button');
        const clientSecret = cardButton.dataset.secret;
        
        cardElement.mount('#card-element'); // Add an instance of the card Element into the `card-element` <div>.
        
        // Handle real-time validation errors from the card Element.
        cardElement.addEventListener('change', function(event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });
        
        // Handle form submission.
        var form = document.getElementById('payment-form');
        
        /*form.addEventListener('submit', function(event) {
            event.preventDefault();

            // Method 1 (working - deprecated)
            stripe.handleCardPayment(clientSecret, cardElement, {
                payment_method_data: {
                    billing_details: { name: 'shameer' }
                }
            })
            .then(function(result) {
                console.log(result);
                if (result.error) {
                    // Inform the user if there was an error.
                    var errorElement = document.getElementById('card-errors');
                    errorElement.textContent = result.error.message;
                    alert(result.error.message);
                } else {
                    if (result.paymentIntent.status === 'succeeded') {
                        alert("Payment successfully completed (Method 1)")
                        form.submit();
                    }
                }
            });
        });*/


        form.addEventListener('submit', function(e) {
            e.preventDefault();

            //Method 2 (working)
            stripe.confirmCardPayment(clientSecret, {
                payment_method: {
                    card: cardElement,
                    billing_details: {
                        name: '{{ auth()->user()->name }}',
                        email: '{{ auth()->user()->email }}'
                    }
                },
                setup_future_usage: 'off_session'
            }).then(function(result) {
                console.log(result);
                if (result.error) {
                    // Show error to your customer (e.g., insufficient funds)
                    alert(result.error.message);
                } else {
                    // The payment has been processed!
                    if (result.paymentIntent.status === 'succeeded') {
                        document.getElementById('payment-id').value = result.paymentIntent.id;
                        alert("Payment successfully completed (Method 2)");
                        form.submit();
                    }
                }
            });
        });

    </script>
</body>
</html>