<?php
/**
 * WHMCS Jazzcash Bank Transfer Payment Gateway Module
 *
 * Payment Gateway modules allow you to integrate payment solutions with the
 * WHMCS platform.
 *
 * This sample file demonstrates how a payment gateway module for WHMCS should
 * be structured and all supported functionality it can contain.
 *
 * Within the module itself, all functions must be prefixed with the module
 * filename, followed by an underscore, and then the function name. For this
 * example file, the filename is "jazzcash" and therefore all functions
 * begin "jazzcash_".
 *
 * If your module or third party API does not support a given function, you
 * should not define that function within your module. Only the _config
 * function is required.
 *
 * For more information, please refer to the online documentation.
 *
 * @see https://developers.whmcs.com/payment-gateways/
 *
 * @copyright Copyright (c) WHMCS Limited 2017
 * @license http://www.whmcs.com/license/ WHMCS Eula
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
 * Define module related meta data.
 *
 * Values returned here are used to determine module related capabilities and
 * settings.
 *
 * @see https://developers.whmcs.com/payment-gateways/meta-data-params/
 *
 * @return array
 */
function jazzcards_MetaData()
{
    return array(
        'DisplayName' => 'JazzCash CC Payment Gateway Module',
        'APIVersion' => '1.1', // Use API Version 1.1
        'DisableLocalCredtCardInput' => false,
        'TokenisedStorage' => false,
    );
}

/**
 * Define gateway configuration options.
 *
 * The fields you define here determine the configuration options that are
 * presented to administrator users when activating and configuring your
 * payment gateway module for use.
 *
 * Supported field types include:
 * * text
 * * password
 * * yesno
 * * dropdown
 * * radio
 * * textarea
 *
 * Examples of each field type and their possible configuration parameters are
 * provided in the sample function below.
 *
 * @return array
 */
function jazzcards_config()
{
    return array(
        // the friendly display name for a payment gateway should be
        // defined here for backwards compatibility
         'FriendlyName' => array(
            'Type' => 'System',
            'Value' => 'Debit/Credit Cards',
        ),
        // a text field type allows for single line text input
        'merchantID' => array(
            'FriendlyName' => 'Merchant ID',
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Enter your Merchant ID here',
        ),
        // a password field type allows for masked text input
        'salt' => array(
            'FriendlyName' => 'Integerity Salt',
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Enter Integerity Salt key here',
        ),
		'password' => array(
            'FriendlyName' => 'Integerity Password',
            'Type' => 'password',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Enter Integerity Password here',
        ),
		/*'securehash' => array(
            'FriendlyName' => 'SecureHash',
            'Type' => 'text',
            'Size' => '255',
            'Default' => '',
            'Description' => 'Enter SecureHash here',
        ),*/
    );
}

/**
 * Payment link.
 *
 * Required by third party payment gateway modules only.
 *
 * Defines the HTML output displayed on an invoice. Typically consists of an
 * HTML form that will take the user to the payment gateway endpoint.
 *
 * @param array $params Payment Gateway Module Parameters
 *
 * @see https://developers.whmcs.com/payment-gateways/third-party-gateway/
 *
 * @return string
 */
function jazzcards_link($params)
{
	
    // Gateway Configuration Parameters
    $merchantID = $params['merchantID'];
    $moduleDisplayName = $params['name'];
    $password = $params['password'];
    $salt = $params['salt'];
    //$securehash = $params['securehash'];

    // Invoice Parameters
    $invoiceId = $params['invoiceid'];
	$invoicenum = $params['invoicenum'];
	$dueDate = $params['dueDate'];
    $description = $params["description"];
    $amount = $params['amount'];
    $currencyCode = $params['currency'];


    // System Parameters
    $companyName = $params['companyname'];
    $systemUrl = $params['systemurl'];
    $returnUrl = $params['returnurl'];
    $langPayNow = $params['langpaynow'];
    $moduleName = $params['paymentmethod'];
    $whmcsVersion = $params['whmcsVersion'];

    $url = 'https://sandbox.jazzcash.com.pk/PayaxisCustomerPortal/transactionmanagement/merchantform';
	
	$postfields = array();
    $postfields['pp_Version'] = '1.1';
	
	//DD -Direct Debit
	//MIGS - Card Payment
	//MWALLET - Mobile Voucher
	//OTC - Voucher Payment
	
    $postfields['pp_TxnType'] = 'MIGS';
    $postfields['pp_Language'] = 'EN';
    $postfields['pp_MerchantID'] = $merchantID;
    $postfields['pp_SubMerchantID'] = '';
    $postfields['pp_Password'] = $password;
    $postfields['pp_BankID'] = '';
    $postfields['pp_ProductID'] = '';
	
	//generate the referenec number
    $postfields['pp_TxnRefNo'] = 'T'. $invoicenum . 'x' . rand(1111111111 , 9999999999) ;
	
    $postfields['pp_Amount'] = str_replace('.' , '' ,$amount);
    $postfields['pp_TxnCurrency'] = 'PKR';
	
	//Generate it Like 20180523111537 - yyyyMMddHHmmss
    $postfields['pp_TxnDateTime'] = date('YmdHis');
	
    $postfields['pp_BillReference'] = $invoiceId;
    $postfields['pp_Description'] = $description;
	
	//Generate The Time Like: 20180524111537
    $postfields['pp_TxnExpiryDateTime'] = date('YmdHis' , strtotime(date('YmdHis'). ' + 2 days'));
	
	//$postfields['callback_url'] = $systemUrl . '/modules/gateways/callback/' . $moduleName . '.php';
    $postfields['pp_ReturnURL'] = $returnUrl;
	//$postfields['pp_ReturnURL'] = urlencode('http://hosting.kayecommerce.net/viewinvoice.php');
	
	
	//pp_SecureHash
	//$securehash;
    $postfields['pp_SecureHash'] = '';
    $postfields['ppmpf_1'] = '1';
    $postfields['ppmpf_2'] = '2';
    $postfields['ppmpf_3'] = '3';
    $postfields['ppmpf_4'] = '4';
    $postfields['ppmpf_5'] = '5';
	
	
	
	$htmlOutput .= '<style>
						body {
							background: #fff;
						}
					
						form {
							margin: 0;
							padding: 0;
						}
					
						.jsformWrapper {
							border: 1px solid rgba(196, 21, 28, 0.50);
							padding: 2rem;
							margin: 0 auto;
							border-radius: 2px;
							margin-top: 2rem;
							box-shadow: 0 7px 5px #eee;
						}
				
				
						.jsformWrapper button {
							background: rgba(196, 21, 28, 1);
							border: none;
							color: #fff;
							width: 120px;
							height: 40px;
							line-height: 25px;
							font-size: 16px;
							font-family: sans-serif;
							text-transform: uppercase;
							border-radius: 2px;
							cursor: pointer;
						}
				
					h3 {
						text-align: center;
						margin-top: 3rem;
						color: rgba(196, 21, 28, 1);
					}
				</style>
				<script>
					function submitForm() {
				
						var IntegritySalt = document.getElementById("salt").innerText;
				
						var hash = CryptoJS.HmacSHA256(document.getElementById("hashValuesString").value, IntegritySalt);
				
						document.getElementsByName("pp_SecureHash")[0].value = hash + \'\';
				
						document.jsform.submit();
					}
				</script>
				<script src="https://sandbox.jazzcash.com.pk/Sandbox/Scripts/hmac-sha256.js"></script>
				
				<!--<h3>JazzCash HTTP POST (Page Redirection) Testing</h3>-->
				<div class="jsformWrapper text-center">';


	$htmlOutput .= '<form name="jsform" method="post" action="' . $url . '">' ;
	
    foreach ($postfields as $k => $v) {
        $htmlOutput .= '<input type="hidden" name="' . $k . '" value="' . ($v) . '" />' ;
    }
    $htmlOutput .= '<button type="button" class="text-center" onclick="submitForm()">' . $langPayNow . '</button><!--<input type="submit" onclick="submitForm()" value="' . $langPayNow . '" />-->';
    $htmlOutput .= '</form>';


	$htmlOutput .= '<label id="salt" style="display:none;">' . $salt . '</label>
					<!--<br><br>
					<div class="formFielWrapper">
						<label class="active">Hash values string: </label>-->
						<input type="hidden" id="hashValuesString" value="">
						<!--<br><br>
					</div>-->
				
				</div>
				
				<script>
				
					var IntegritySalt = document.getElementById("salt").innerText;
					hashString = \'\';
				
					hashString += IntegritySalt + \'&\';
				
					if (document.getElementsByName("pp_Amount")[0].value != \'\') {
						hashString += document.getElementsByName("pp_Amount")[0].value + \'&\';
					}
					if (document.getElementsByName("pp_BankID")[0].value != \'\') {
						hashString += document.getElementsByName("pp_BankID")[0].value + \'&\';
					}
					if (document.getElementsByName("pp_BillReference")[0].value != \'\') {
						hashString += document.getElementsByName("pp_BillReference")[0].value + \'&\';
					}
					if (document.getElementsByName("pp_Description")[0].value != \'\') {
						hashString += document.getElementsByName("pp_Description")[0].value + \'&\';
					}
					if (document.getElementsByName("pp_Language")[0].value != \'\') {
						hashString += document.getElementsByName("pp_Language")[0].value + \'&\';
					}
					if (document.getElementsByName("pp_MerchantID")[0].value != \'\') {
						hashString += document.getElementsByName("pp_MerchantID")[0].value + \'&\';
					}
					if (document.getElementsByName("pp_Password")[0].value != \'\') {
						hashString += document.getElementsByName("pp_Password")[0].value + \'&\';
					}
					if (document.getElementsByName("pp_ProductID")[0].value != \'\') {
						hashString += document.getElementsByName("pp_ProductID")[0].value + \'&\';
					}
					if (document.getElementsByName("pp_ReturnURL")[0].value != \'\') {
						hashString += document.getElementsByName("pp_ReturnURL")[0].value + \'&\';
					}
					if (document.getElementsByName("pp_SubMerchantID")[0].value != \'\') {
						hashString += document.getElementsByName("pp_SubMerchantID")[0].value + \'&\';
					}
					if (document.getElementsByName("pp_TxnCurrency")[0].value != \'\') {
						hashString += document.getElementsByName("pp_TxnCurrency")[0].value + \'&\';
					}
					if (document.getElementsByName("pp_TxnDateTime")[0].value != \'\') {
						hashString += document.getElementsByName("pp_TxnDateTime")[0].value + \'&\';
					}
					if (document.getElementsByName("pp_TxnExpiryDateTime")[0].value != \'\') {
						hashString += document.getElementsByName("pp_TxnExpiryDateTime")[0].value + \'&\';
					}
					if (document.getElementsByName("pp_TxnRefNo")[0].value != \'\') {
						hashString += document.getElementsByName("pp_TxnRefNo")[0].value + \'&\';
					}
					if (document.getElementsByName("pp_TxnType")[0].value != \'\') {
						hashString += document.getElementsByName("pp_TxnType")[0].value + \'&\';
					}
					if (document.getElementsByName("pp_Version")[0].value != \'\') {
						hashString += document.getElementsByName("pp_Version")[0].value + \'&\';
					}
					if (document.getElementsByName("ppmpf_1")[0].value != \'\') {
						hashString += document.getElementsByName("ppmpf_1")[0].value + \'&\';
					}
					if (document.getElementsByName("ppmpf_2")[0].value != \'\') {
						hashString += document.getElementsByName("ppmpf_2")[0].value + \'&\';
					}
					if (document.getElementsByName("ppmpf_3")[0].value != \'\') {
						hashString += document.getElementsByName("ppmpf_3")[0].value + \'&\';
					}
					if (document.getElementsByName("ppmpf_4")[0].value != \'\') {
						hashString += document.getElementsByName("ppmpf_4")[0].value + \'&\';
					}
					if (document.getElementsByName("ppmpf_5")[0].value != \'\') {
						hashString += document.getElementsByName("ppmpf_5")[0].value + \'&\';
					}
				
					hashString = hashString.slice(0, -1);
				
					var hash = CryptoJS.HmacSHA256(hashString, IntegritySalt);
				
					document.getElementsByName("pp_SecureHash")[0].value = hash + \'\';
				
					console.log(\'string: \' + hashString);
					console.log(\'hash: \' + document.getElementsByName("pp_SecureHash")[0].value);
				
					document.getElementById("hashValuesString").value = hashString;
				
				</script>';

	
    return $htmlOutput;
}

/**
 * Refund transaction.
 *
 * Called when a refund is requested for a previously successful transaction.
 *
 * @param array $params Payment Gateway Module Parameters
 *
 * @see https://developers.whmcs.com/payment-gateways/refunds/
 *
 * @return array Transaction response status
 */
function jazzcards_refund($params)
{
   return true;
}

/**
 * Cancel subscription.
 *
 * If the payment gateway creates subscriptions and stores the subscription
 * ID in tblhosting.subscriptionid, this function is called upon cancellation
 * or request by an admin user.
 *
 * @param array $params Payment Gateway Module Parameters
 *
 * @see https://developers.whmcs.com/payment-gateways/subscription-management/
 *
 * @return array Transaction response status
 */
function jazzcards_cancelSubscription($params)
{
    return true;
}
