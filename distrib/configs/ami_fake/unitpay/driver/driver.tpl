%%include_language "_local/eshop/pay_drivers/unitpay/driver.lng"%%

<!--#set var="settings_form" value="
    <tr>
        <td>%%DOMAIN%%:</td>
        <td>
            <input type="text" name="DOMAIN" class="field" value="##DOMAIN##" size="40">
        </td>
    </tr>
    <tr>
        <td>%%PUBLIC_KEY%%:</td>
        <td>
            <input type="text" name="PUBLIC_KEY" class="field" value="##PUBLIC_KEY##" size="40">
        </td>
    </tr>
    <tr>
        <td>%%SECRET_KEY%%:</td>
        <td>
            <input type="text" name="SECRET_KEY" class="field" value="##SECRET_KEY##" size="40">
        </td>
    </tr>
"-->

<!--#set var="checkout_form" value="
    <form name="unitpay" action="##process_url##" method="POST">
    ##hiddens##
    </form>
"-->

<!--#set var="pay_form" value="
    <form name="paymentform" action="##payment_url##" method="POST">
    </form>
    <script type="text/javascript">
        document.paymentform.submit();
    </script>
"-->


