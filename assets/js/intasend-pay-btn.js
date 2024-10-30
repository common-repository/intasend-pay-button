jQuery(document).ready(function ($) {
    console.log(intBtnData.buttons)
    var btns = intBtnData.buttons
    for (i = 0; i < btns.length; i++) {

        var btn = $(`#isPayBtn-${btns[i].id}`);
        var paymentAmountInput = $(`#isPayAmount-${btns[i].id}`)
        var payment_amount = $(`#isPayAmount-${btns[i].id}`).val()

        // set amount
        btn.attr(`data-amount`, payment_amount);

        $(`#isPayAmount-${btns[i].id}`).on('input change focusout', function (e) {
            var btn_id = e.target.attributes.id.value.split('-')
            btn_id = btn_id[btn_id.length - 1]
            let paymentAmount = $(this).val();
            $(`#isPayBtn-${btn_id}`).attr('data-amount', paymentAmount);
        });

        function getPaymentAmount(val) {
            console.log(val)
            var payment_amount = $(`#${val}`).data("amount");
            if (payment_amount !== `undefined`) {
                payment_amount = parseInt(payment_amount);
            }
            if (payment_amount === 0) {
                payment_amount = $(`#isPayAmount-${btns[i].id}`).val();
            }
            return payment_amount;
        }

        var intaSendInstance = new window.IntaSend({
            publicAPIKey: intBtnData.publishable_key,
            live: intBtnData.live_key //set to true when going live
        })

        btn.on(`click`, function (e) {
            var btn = document.getElementById(e.target.attributes.id.value)
            var dataAttributes = btn.dataset
            let payload = {
                amount: getPaymentAmount(e.target.attributes.id.value),
                card_tarrif: dataAttributes.card_tarrif,
                mobile_tarrif: dataAttributes.mobile_tarrif,
                currency: dataAttributes.currency,
                redirect_url: dataAttributes.redirect_url,
                api_ref: `Wordpress-${e.target.attributes.id.value}`
            }
            intaSendInstance.run(payload, dataAttributes)
            .on("COMPLETE", (results) => {
                window.location.href = dataAttributes.redirect_url;
            })
            .on("FAILED", (results) => {
                console.log("Do something on failure", results)
            })
            .on("IN-PROGRESS", (results) => {
                console.log("Payment in progress status", results)
            })
        })
    }
})