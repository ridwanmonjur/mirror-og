let intervalId;
let retryCount = 0; 

function checkPaymentStatus() {
  (async () => {
    try {
      const { paymentIntent } = await stripe.retrievePaymentIntent(clientSecret);
      if (paymentIntent && paymentIntent.status === 'failed') {
        console.log('Payment failed');
        retryCount++;
        if (retryCount >= 5) {
          clearInterval(intervalId);
          console.log('Reached maximum retries. Stopping interval.');
        }
      } else if (paymentIntent && paymentIntent.status === 'succeeded') {
        console.log('Payment succeeded');
        clearInterval(intervalId); 
      } else {
        console.log('Payment status:', paymentIntent.status);
      }
    } catch (error) {
      console.error('Error retrieving PaymentIntent:', error.message);
      clearInterval(intervalId); 
    }
  })();
}

intervalId = setInterval(checkPaymentStatus, 1000);
