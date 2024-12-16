<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stripe Payment</title>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
    @if (session('success_message'))
        <div style="color: green;">
            {{ session('success_message') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="color: red;">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('payment.process') }}" method="POST" id="payment-form">
        @csrf
        <label for="amount">Amount (INR): </label>
        <input type="number" name="amount_inr" id="amount_inr" placeholder="Enter amount in INR" required>

        <div id="card-element"></div>
        <button id="submit" type="submit">Pay</button>
    </form>

    <div id="payment-message"></div>

    <script>
        var stripe = Stripe('{{ env('STRIPE_KEY') }}');
        var elements = stripe.elements();
        var card = elements.create('card');
        card.mount('#card-element');

        var form = document.getElementById('payment-form');
        var paymentMessage = document.getElementById('payment-message');

        form.addEventListener('submit', async function(event) {
            event.preventDefault();

            let amountInInr = document.getElementById('amount_inr').value;
            let amountInUsd = amountInInr; 

            let response = await fetch("{{ route('payment.process') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ amount: amountInUsd }) 
            });
            let { clientSecret } = await response.json();
            stripe.confirmCardPayment(clientSecret, {
                payment_method: {
                    card: card,
                }
            }).then(function(result) {
                if (result.error) {
                    paymentMessage.textContent = result.error.message;
                    paymentMessage.style.color = 'red';
                } else {
                    if (result.paymentIntent.status === 'succeeded') {
                        paymentMessage.textContent = 'Payment successful!';
                        paymentMessage.style.color = 'green';
                    }
                }
            });
        });
    </script>
</body>
</html>
