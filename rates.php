<?php 
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);



require_once "functions.php";
require_once "vendor/autoload.php";

//recipient
$shipToAddress = array(trim($_POST['shipToaddress1']), trim($_POST['shipToaddress2']));
$shipToAddress = array_filter($shipToAddress);
$shipToCity    = trim($_POST['shipTocity']);
$shipToState   = trim($_POST['shipTostate']);
$shipToZip     = (int)trim($_POST['shipTozip']);
$shipToCountry = trim($_POST['shipTocountry']);
$shipToDate    = trim($_POST['shipTodate']);
$is_residential = trim($_POST['shipToresidential']);

//shipper
$shipFromAddress = array(trim($_POST['shipFromaddress1']), trim($_POST['shipFromaddress2']));
$shipFromAddress = array_filter($shipFromAddress);
$shipFromCity    = trim($_POST['shipFromcity']);
$shipFromState   = trim($_POST['shipFromstate']);
$shipFromZip     = (int)trim($_POST['shipFromzip']);
$shipFromCountry = trim($_POST['shipFromcountry']);

//item specifications
$packaging     = trim($_POST['packaging']);
$weight     = (int)trim($_POST['weight']);
$length     = (int)trim($_POST['length']);
$width      = (int)trim($_POST['width']);
$height     = (int)trim($_POST['height']);

use FedEx\RateService\Request;
use FedEx\RateService\ComplexType;
use FedEx\RateService\SimpleType;

$rateRequest = new ComplexType\RateRequest();

//authentication & client details
$rateRequest->WebAuthenticationDetail->UserCredential->Key = FEDEX_KEY;
$rateRequest->WebAuthenticationDetail->UserCredential->Password = FEDEX_PASSWORD;
$rateRequest->ClientDetail->AccountNumber = FEDEX_ACCOUNT_NUMBER;
$rateRequest->ClientDetail->MeterNumber = FEDEX_METER_NUMBER;

//$rateRequest->TransactionDetail->CustomerTransactionId = 'testing rate service request';
$rateRequest->TransactionDetail->CustomerTransactionId = md5(date("Y-m-d h:i:s"));

//version
$rateRequest->Version->ServiceId = 'crs';
$rateRequest->Version->Major = 28;
$rateRequest->Version->Minor = 0;
$rateRequest->Version->Intermediate = 0;

$rateRequest->ReturnTransitAndCommit = true;
$rateRequest->RequestedShipment->PreferredCurrency = 'USD';

//shipper
$rateRequest->RequestedShipment->Shipper->Address->StreetLines = $shipFromAddress;
$rateRequest->RequestedShipment->Shipper->Address->City = $shipFromCity;
$rateRequest->RequestedShipment->Shipper->Address->StateOrProvinceCode = $shipFromState;
$rateRequest->RequestedShipment->Shipper->Address->PostalCode = $shipFromZip;
$rateRequest->RequestedShipment->Shipper->Address->CountryCode = 'US';

//recipient
$rateRequest->RequestedShipment->Recipient->Address->StreetLines = $shipToAddress;
$rateRequest->RequestedShipment->Recipient->Address->City = $shipToCity;
$rateRequest->RequestedShipment->Recipient->Address->StateOrProvinceCode = $shipToState;
$rateRequest->RequestedShipment->Recipient->Address->PostalCode = $shipToZip;
$rateRequest->RequestedShipment->Recipient->Address->CountryCode = 'US';

if($is_residential == "false")

$rateRequest->RequestedShipment->Recipient->Address->Residential = 0;
elseif($is_residential == "true")

$rateRequest->RequestedShipment->Recipient->Address->Residential = 1;


//shipping charges payment
$rateRequest->RequestedShipment->ShippingChargesPayment->PaymentType = SimpleType\PaymentType::_SENDER;

//rate request types
$rateRequest->RequestedShipment->RateRequestTypes = [SimpleType\RateRequestType::_PREFERRED, SimpleType\RateRequestType::_LIST];
//$rateRequest->RequestedShipment->RateRequestTypes = [SimpleType\RateRequestType::_ACCOUNT];
$rateRequest->RequestedShipment->PackageCount = 1;

//$rateRequest->RequestedShipment->ServiceType = "FEDEX_EXPRESS_SAVER";


//create package line items
//$rateRequest->RequestedShipment->RequestedPackageLineItems = [new ComplexType\RequestedPackageLineItem(), new ComplexType\RequestedPackageLineItem()]; //2 packages
$rateRequest->RequestedShipment->RequestedPackageLineItems = [new ComplexType\RequestedPackageLineItem()]; //1 package


//package 1
$rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Weight->Value = $weight;
$rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Weight->Units = SimpleType\WeightUnits::_LB;
$rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Dimensions->Length = $length;
$rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Dimensions->Width = $width;
$rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Dimensions->Height = $height;
$rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Dimensions->Units = SimpleType\LinearUnits::_IN;
$rateRequest->RequestedShipment->RequestedPackageLineItems[0]->GroupPackageCount = 1;

//package 2
/*$rateRequest->RequestedShipment->RequestedPackageLineItems[1]->Weight->Value = 20;
$rateRequest->RequestedShipment->RequestedPackageLineItems[1]->Weight->Units = SimpleType\WeightUnits::_LB;
$rateRequest->RequestedShipment->RequestedPackageLineItems[1]->Dimensions->Length = 40;
$rateRequest->RequestedShipment->RequestedPackageLineItems[1]->Dimensions->Width = 10;
$rateRequest->RequestedShipment->RequestedPackageLineItems[1]->Dimensions->Height = 15;
$rateRequest->RequestedShipment->RequestedPackageLineItems[1]->Dimensions->Units = SimpleType\LinearUnits::_IN;
$rateRequest->RequestedShipment->RequestedPackageLineItems[1]->GroupPackageCount = 1;*/


$rateServiceRequest = new Request();
$rateServiceRequest->getSoapClient()->__setLocation(Request::PRODUCTION_URL); //use production URL

$rateReply = $rateServiceRequest->getGetRatesReply($rateRequest); // send true as the 2nd argument to return the SoapClient's stdClass response.

$count = 0;
$result = array();

if (!empty($rateReply->RateReplyDetails)) {
    $result['err'] = false;
    
    foreach ($rateReply->RateReplyDetails as $rateReplyDetail) {
        
        $result[$count]['ServiceType'] = $rateReplyDetail->ServiceType;
        $result[$count]['PackagingType'] = $rateReplyDetail->PackagingType;
        //$result[$count]['DeliveryStation'] = $rateReplyDetail->DeliveryStation;
        $result[$count]['DeliveryDayOfWeek'] = $rateReplyDetail->DeliveryDayOfWeek;
        $result[$count]['DeliveryTimestamp'] = $rateReplyDetail->DeliveryTimestamp;
        $result[$count]['ShipmentDetails'] = array();
        
        if (!empty($rateReplyDetail->RatedShipmentDetails)) {
            foreach ($rateReplyDetail->RatedShipmentDetails as $i=>$ratedShipmentDetail) {
                
                $result[$count]['ShipmentDetails'][$i]['RateType'] = $ratedShipmentDetail->ShipmentRateDetail->RateType;
                $result[$count]['ShipmentDetails'][$i]['TotalNetCharge'] = $ratedShipmentDetail->ShipmentRateDetail->TotalNetCharge->Amount;
                
            }
        }
        $count++;
    }
}

if(count($result) > 0) {
    echo json_encode($result);
} else {
    
    echo json_encode(array("err" => true));
    
}


