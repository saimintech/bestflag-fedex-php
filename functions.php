<?php 

/*
Developer Test Key: iRCsW9FPq79OP3RS

Test Account Number: 510087720

Test Meter Number: 119218720

Test FedEx Office Integrator ID: 123

Test Client Product ID: TEST

Test Client Product Version: 9999*/

/*
Authentication Key: TYnSksEeFSnnnRwv
Meter Number: 252218807

Production URL: https://ws.fedex.com:443/web-services
Password: WEJqIYuyCBvOYicG1zStHfVFU
FedEx Shipping Account Number: 679167472
FedEx Web Services Meter Number: 252218807
*/

/*
Production URL: https://ws.fedex.com:443/web-services
Password: OSPrZgKivmeAloyHjxyrtcEKV
FedEx Web Services Meter Number: 253424974
Authentication Key: WfRD5PZFjpG0CtiT
*/


/*define("FEDEX_KEY", "TYnSksEeFSnnnRwv");
define("FEDEX_PASSWORD", "WEJqIYuyCBvOYicG1zStHfVFU");
define("FEDEX_ACCOUNT_NUMBER", "679167472");
define("FEDEX_METER_NUMBER", "252218807");
define("PRODUCTION_URL", "ws.fedex.com");*/
//define("API_KEY", "iRCsW9FPq79OP3RS");

/*define("FEDEX_KEY", "WfRD5PZFjpG0CtiT");
define("FEDEX_PASSWORD", "OSPrZgKivmeAloyHjxyrtcEKV");
define("FEDEX_ACCOUNT_NUMBER", "647310877");
define("FEDEX_METER_NUMBER", "253424974");
define("PRODUCTION_URL", "ws.fedex.com");*/

define("FEDEX_KEY", $_GET['FEDEX_KEY']);
define("FEDEX_PASSWORD", $_GET['FEDEX_PASSWORD']);
define("FEDEX_ACCOUNT_NUMBER", $_GET['FEDEX_ACCOUNT_NUMBER']);
define("FEDEX_METER_NUMBER", $_GET['FEDEX_METER_NUMBER']);
define("PRODUCTION_URL", "ws.fedex.com");