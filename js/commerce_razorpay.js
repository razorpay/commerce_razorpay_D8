
(function($) {
    Drupal.behaviors.commerce_razorpay = {
        attach: function(context, settings) {

            var amount = settings.commerce_razorpay.amount;
            var key = settings.commerce_razorpay.key;
            var logo = settings.commerce_razorpay.logo;
            var merchant_order_id = settings.commerce_razorpay.order_id;
            var commerce_order_id = settings.commerce_razorpay.commerce_order_id;
            var payment_id = '';
            var payment_settings = JSON.stringify(settings.commerce_razorpay.payment_settings);
            var billing_address = settings.commerce_razorpay.billing_address;
            var name = billing_address.first_name + " " + billing_address.last_name;
            var address = name + " " + billing_address.thoroughfare + " " + billing_address.locality + " " + billing_address.sub_administrative_area + " " + billing_address.country;

            var options = {
                "key": key,
                "amount": amount, // 100 paise = INR 1
                "name": "Merchant Name",
                "description": "Purchase Description",
                "image": logo,
                "order_id": merchant_order_id,
                //  Pass phone number.
                "handler": function(response) {

                    window.location = '/capture-payment?amount=' + amount + '&order_id=' + commerce_order_id + '&payment_settings=' + payment_settings + '&response=' + JSON.stringify(response);
                    $('razor-payment-id').val(response.razorpay_payment_id);
                },
                "prefill": {
                    "name": name,
                    "email": "neha.jyoti@yahoo.com"
                },
                "notes": {
                    "address": address
                },
                "theme": {
                    "color": "#F37254",
                    "emi_mode": true,

                }
            };
           
            console.log("options");
            console.log(options);

            var rzp1 = new Razorpay(options);
            rzp1.open();

            // document.getElementById("rzp-button1").onclick = function(e) {
            //   alert("on click");
            //   rzp1.open();
            //   e.preventDefault();
            // }

        }
    };
}(jQuery));
