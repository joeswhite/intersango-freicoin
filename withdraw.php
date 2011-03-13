<?php
require 'util.php';

function save_details($amount, $curr_type)
{
    if ($curr_type == 'GBP') {
        $is_international = post('is_international') == 'true';
        if (!$is_international) {
            $name = post('name_holder');
            $bank = post('name_bank');
            $acc_num = post('account_number');
            $sort_code = post('sort_code');
            echo "<p>$name $bank $acc_num $sort_code local</p>";
            return true;
        }
        else {
            $iban = post('iban');
            $swift = post('swift');
            echo "<p>$iban $swift non local</p>";
            return true;
        }
    }
    else if ($curr_type == 'BTC') {
        return true;
    }
    else {
        throw Error('Invalid currency', 'You cannot withdraw a currency that does not exist.');
    }
    # should never happen!
    return false;
}

if (isset($_POST['amount']) && isset($_POST['curr_type'])) {
    $uid = user_id();
    $amount_disp = post('amount');
    $curr_type = post('curr_type');
    $amount = numstr_to_internal($amount_disp);

    curr_supported_check($curr_type);
    order_worthwhile_check($amount);
    enough_money_check($amount, $curr_type);

    if (!save_details($amount, $curr_type))
        throw Error('We had to admit it sometime...', 'Stop trading on thie site. Contact the admin FAST.');
    # actually take the money now
    deduct_funds($amount, $curr_type);

    echo "<div class='content_box'>\n";
    echo "<h3>Withdraw $curr_type</h3>\n";
    echo "<p>Your request to withdraw $amount_disp $curr_type has been submitted. Visit your <a href='?page=profile'>profile</a> to check on the status of your request.</p>\n";
    echo "</div>\n";
}
else {
?>
    <div class='content_box'>
    <h3>Withdraw GBP (UK residents)</h3>
    <p>Enter an amount below to submit a withdrawal request.</p>
    <p>
        <form action='' class='indent_form' method='post'>
            <label for='input_name_holder'>Name of account holder</label>
            <input type='text' id='input_name_holder' name='name_holder' />

            <label for='input_name_bank'>Name of the bank</label>
            <input type='text' id='input_name_bank' name='name_bank' />

            <div id='acc_details'>
                <div id='acc_num'>
                    <label for='input_account_number'>Account number</label>
                    <input type='text' class='input_no_block' id='input_account_number' name='account_number' />
                </div>
                <div id='acc_sort'>
                    <label for='input_sort_code'>Sort code</label>
                    <input type='text' id='input_sort_code' name='sort_code' />
                </div>
            </div>

            <label for='input_amount'>Amount</label>
            <input type='text' id='input_amount' name='amount' />

            <input type='hidden' name='curr_type' value='GBP' />
            <input type='hidden' name='is_international' value='false' />
            <input type='submit' value='Submit' />
        </form>
    </p>
    </div>

    <div class='content_box'>
    <h3>Withdraw GBP (international)</h3>
    <p>Enter an amount below to submit a withdrawal request. A fee of 20 GBP for amounts below 5000 GBP and 35 GBP otherwise, applies. Your bank may charge an additional processing fee on their end.</p>
    <p>
        <form action='' class='indent_form' method='post'>
            <div id='acc_details'>
                <div id='acc_num'>
                    <label for='input_account_number'>IBAN</label>
                    <input type='text' class='input_no_block' id='input_account_number' name='iban' />
                </div>
                <div id='acc_sort'>
                    <label for='input_sort_code'>BIC/SWIFT</label>
                    <input type='text' id='input_sort_code' name='swift' />
                </div>
            </div>

            <label for='input_amount'>Amount</label>
            <input type='text' id='input_amount' name='amount' />

            <input type='hidden' name='curr_type' value='GBP' />
            <input type='hidden' name='is_international' value='true' />
            <input type='submit' value='Submit' />
        </form>
    </p>
    </div>

    <div class='content_box'>
    <h3>Withdraw BTC</h3>
    <p>Enter an amount below to withdraw.</p>
    <p>
        <form action='' class='indent_form' method='post'>
            <label for='input_amount'>Amount</label>
            <input type='text' id='input_amount' name='amount' />

            <label for='input_address'>Address</label>
            <input type='text' id='input_address' name='address' />
            <input type='hidden' name='curr_type' value='BTC' />
            <input type='submit' value='Submit' />
        </form>
    </p>
    </div>
<?php
}
?>
