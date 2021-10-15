$('.esewa').on('click', function () {
    const esewaData = document.querySelector('#esewa');
    let donatorId = esewaData.dataset.id;
    let amount = esewaData.dataset.amount;

    //console.log(donationAmount);
    //https://uat.esewa.com.np/epay/main
    //https://esewa.com.np/epay/main
    var path = "https://uat.esewa.com.np/epay/main";
    var params = {
        amt: 100,
        psc: 0,
        pdc: 0,
        txAmt: 0,
        tAmt: 100,
        pid: 'ee2c3ca1-696b-4cc5-a6be-2c40d929d453',
        scd: 'EPAYTEST'/*process.env.MIX_ESEWA_LIVE_PUBLIC_KEY*/,
        su: "https://streamersalert.com/esuccess?q=su",
        fu: "https://streamersalert.com/efailure?q=su"
    };

    function post(path, params) {
        //console.log(path);
        let form = document.createElement("form");
        form.setAttribute("method", "POST");
        form.setAttribute("action", path);

        for (let key in params) {
            let hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", params[key]);
            form.appendChild(hiddenField);
        }

        document.body.appendChild(form);
        form.submit();
    }

    post(path, params);


});