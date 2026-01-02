<?php
include_once('../common.php');

if($MODULES_OBJ->isEnableAppHomeScreenLayoutV4()) {
    if(strtoupper($APP_TYPE) == "RIDE") {
        include_once 'manage_app_home_screen_ride.php';
        exit;
    } elseif (strtoupper($APP_TYPE) == "DELIVERY") {
        include_once 'manage_app_home_screen_delivery.php';
        exit;
    } elseif (strtoupper($APP_TYPE) == "RIDE-DELIVERY") {
        include_once 'manage_app_home_screen_ride_delivery.php';
        exit;
    } elseif (strtoupper($APP_TYPE) == "UBERX") {
        include_once 'manage_app_home_screen_sp.php';
        exit;
    } elseif (strtoupper(IS_CUBEX_APP) == "YES") {
        include_once 'manage_app_home_screen_cubex.php';
        exit;
    } elseif (strtoupper(ONLYDELIVERALL) == "YES") {
        include_once 'manage_app_home_screen_deliverall.php';
        exit;
    } elseif (strtoupper(ONLY_MEDICAL_SERVICE) == "YES") {
        include_once 'manage_app_home_screen_ms.php';
        exit;
    } elseif ($MODULES_OBJ->isCubeXGcApp()) {
        include_once 'manage_app_home_screen_cubexgc.php';
        exit;
    } else {
        include_once 'manage_app_home_screen_v4.php';
        exit;
    }
} else {
    if(strtoupper($APP_TYPE) == "RIDE") {
        include_once 'manage_app_home_screen_ride.php';
        exit;
    } elseif (strtoupper($APP_TYPE) == "DELIVERY") {
        include_once 'manage_app_home_screen_delivery.php';
        exit;
    } elseif (strtoupper($APP_TYPE) == "RIDE-DELIVERY") {
        include_once 'manage_app_home_screen_ride_delivery.php';
        exit;
    } elseif (strtoupper($APP_TYPE) == "UBERX") {
        include_once 'manage_app_home_screen_sp.php';
        exit;
    } elseif (strtoupper(IS_CUBEX_APP) == "YES") {
        include_once 'manage_app_home_screen_cubex.php';
        exit;
    } elseif (strtoupper(IS_TAXI_SP) == "YES") {
        include_once 'manage_app_home_screen_ride_sp.php';
        exit;
    } elseif (strtoupper(ONLYDELIVERALL) == "YES") {
        include_once 'manage_app_home_screen_deliverall.php';
        exit;
    } elseif (strtoupper(ONLY_ENABLE_BUY_SELL_RENT_PRO) == "YES") {
        include_once 'manage_app_home_screen_bsr.php';
        exit;
    } elseif (strtoupper(IS_DELIVERYKING_APP) == "YES") {
        include_once 'manage_app_home_screen_dk.php';
        exit;
    } elseif (strtoupper(ONLY_MEDICAL_SERVICE) == "YES") {
        include_once 'manage_app_home_screen_ms.php';
        exit;
    }
}


if (!$userObj->hasPermission('manage-app-home-screen-view')) {
    $userObj->redirect();
}

$default_lang = $LANG_OBJ->FetchSystemDefaultLang();
$script = 'ManageAppHomePage';
$tbl_name = "app_home_screen_view";
$db_master = $obj->MySQLSelect("SELECT * FROM `language_master` ORDER BY `iDispOrder`");
$count_all = scount($db_master);

$EN_available = $LANG_OBJ->checkLanguageExist();
$db_master = $LANG_OBJ->getLangDataDefaultFirst($db_master);

$sql_vehicle_category_table_name = getVehicleCategoryTblName();

$master_service_categories = $obj->MySQLSelect("SELECT vCategoryName, JSON_UNQUOTE(JSON_VALUE(vCategoryName, '$.vCategoryName_" . $default_lang . "')) as vMasterCategoryName, eType, iMasterServiceCategoryId, vIconImage1, tCategoryDetails, vTextColor, vBgColor FROM $master_service_category_tbl WHERE eStatus = 'Active'");
$MasterCategoryArr = array();
foreach ($master_service_categories as $mCategory) {
    $MasterCategoryArr[$mCategory['eType']] = $mCategory;
}

$userEditDataArr = $db_data_arr = array();
$db_data = $obj->MySQLSelect("SELECT * FROM $tbl_name");
foreach ($db_data as $db_value) {
    $ViewType = !empty($db_value['eServiceType']) ? $db_value['eServiceType'] : $db_value['eViewType'];
    $db_data_arr[$ViewType] = $db_value;
}
/* Intro */
$vIntroTitleArr = json_decode($db_data_arr['TitleView']['vTitle'], true);
foreach ($vIntroTitleArr as $key => $value) {
    $key = str_replace('vTitle_', 'vIntroTitle_', $key);
    $userEditDataArr[$key] = $value;
}
$vIntroSubTitleArr = json_decode($db_data_arr['TitleView']['vSubtitle'], true);
foreach ($vIntroSubTitleArr as $key => $value) {
    $key = str_replace('vSubtitle_', 'vIntroSubTitle_', $key);
    $userEditDataArr[$key] = $value;
}

/* General Banners */
$bannerData = $obj->MySQLSelect("SELECT * FROM banners WHERE iServiceId = 0 AND vCode = '$default_lang' AND eType = 'General' AND eFor = 'General' AND eStatus = 'Active' ORDER BY iDisplayOrder LIMIT 0,3");

/* Taxi Booking, DeliverAll, Delivery */
if ($MODULES_OBJ->isRideFeatureAvailable()) {
    $vTaxiBookingTitleArr = json_decode($MasterCategoryArr['Ride']['vCategoryName'], true);
    foreach ($vTaxiBookingTitleArr as $key => $value) {
        $key = str_replace('vCategoryName_', 'vTaxiBookingTitle_', $key);
        $userEditDataArr[$key] = $value;
    }
    $vImageOldTaxiBooking = $MasterCategoryArr['Ride']['vIconImage1'];
}

if ($MODULES_OBJ->isDeliverAllFeatureAvailable()) {
    $vDeliverAllTitleArr = json_decode($MasterCategoryArr['DeliverAll']['vCategoryName'], true);
    foreach ($vDeliverAllTitleArr as $key => $value) {
        $key = str_replace('vCategoryName_', 'vDeliverAllTitle_', $key);
        $userEditDataArr[$key] = $value;
    }
    $vImageOldDeliverAll = $MasterCategoryArr['DeliverAll']['vIconImage1'];
}

if ($MODULES_OBJ->isDeliveryFeatureAvailable()) {
    $vDeliveryTitleArr = json_decode($MasterCategoryArr['Deliver']['vCategoryName'], true);
    foreach ($vDeliveryTitleArr as $key => $value) {
        $key = str_replace('vCategoryName_', 'vDeliveryTitle_', $key);
        $userEditDataArr[$key] = $value;
    }
    $vImageOldDelivery = $MasterCategoryArr['Deliver']['vIconImage1'];
}

/* Taxi Bid */
if ($MODULES_OBJ->isEnableTaxiBidFeature()) {
    $vTaxiBidTitleArr = json_decode($db_data_arr['TaxiBid']['vTitle'], true);
    foreach ($vTaxiBidTitleArr as $key => $value) {
        $key = str_replace('vTitle_', 'vTaxiBidTitle_', $key);
        $userEditDataArr[$key] = $value;
    }
    $vBtnTxtArr = json_decode($db_data_arr['TaxiBid']['vBtnTxt'], true);
    foreach ($vBtnTxtArr as $key => $value) {
        $userEditDataArr[$key] = $value;
    }
    $vImageOldTaxiBid = $db_data_arr['TaxiBid']['vImage'];
    
    $TaxiBidLayoutDetails = json_decode($db_data_arr['TaxiBid']['tLayoutDetails'], true);
    $vTitleColorTaxiBid = $TaxiBidLayoutDetails['vTxtTitleColor']; 
    $vBgColorTaxiBid = $TaxiBidLayoutDetails['vBgColor']; 

    $labelsTaxiBid = $obj->MySQLSelect("SELECT vCode, vLabel, vValue FROM language_label WHERE vLabel IN ('LBL_TAXI_BID_PAGE_TITLE', 'LBL_TAXI_BID_PAGE_DESC') ");

    foreach ($labelsTaxiBid as $label) {
        if ($label['vLabel'] == 'LBL_TAXI_BID_PAGE_TITLE') {
            $userEditDataArr['ServicePageTitle_' . $label['vCode']] = $label['vValue'];

        } elseif ($label['vLabel'] == 'LBL_TAXI_BID_PAGE_DESC') {
            $userEditDataArr['ServicePageDesc_' . $label['vCode']] = $label['vValue'];

        }
    }

    $tServiceDetailsArr = json_decode($db_data_arr['TaxiBid']['tServiceDetails'], true);
    $vInfoImageOld['TaxiBid'] = $tServiceDetailsArr['TaxiBid']['vInfoImage'];
}

/* Delivery Genie / Runner */
if ($MODULES_OBJ->isEnableAnywhereDeliveryFeature('No', 'Genie')) {
    $vGenieTitleArr = json_decode($db_data_arr['Genie']['vTitle'], true);
    foreach ($vGenieTitleArr as $key => $value) {
        $key = str_replace('vTitle_', 'vGenieTitle_', $key);
        $userEditDataArr[$key] = $value;
    }
    $vGenieSubTitleArr = json_decode($db_data_arr['Genie']['vSubtitle'], true);
    foreach ($vGenieSubTitleArr as $key => $value) {
        $key = str_replace('vSubtitle_', 'vGenieSubTitle_', $key);
        $userEditDataArr[$key] = $value;
    }
    $vImageOldGenie = $db_data_arr['Genie']['vImage'];

    $GenieLayoutDetails = json_decode($db_data_arr['Genie']['tLayoutDetails'], true);
    $vTitleColorGenie = $GenieLayoutDetails['vTxtTitleColor']; 
    $vSubTitleColorGenie = $GenieLayoutDetails['vTxtSubTitleColor']; 
    $vBgColorGenie = $GenieLayoutDetails['vBgColor']; 
}

/* Video Consult */
if ($MODULES_OBJ->isEnableVideoConsultingService()) {
    $vVideoConsultTitleArr = json_decode($db_data_arr['VideoConsult']['vTitle'], true);
    foreach ($vVideoConsultTitleArr as $key => $value) {
        $key = str_replace('vTitle_', 'vVideoConsultTitle_', $key);
        $userEditDataArr[$key] = $value;
    }
    $vVideoConsultSubTitleArr = json_decode($db_data_arr['VideoConsult']['vSubtitle'], true);
    foreach ($vVideoConsultSubTitleArr as $key => $value) {
        $key = str_replace('vSubtitle_', 'vVideoConsultSubTitle_', $key);
        $userEditDataArr[$key] = $value;
    }
    $vImageOldVideoConsult = $db_data_arr['VideoConsult']['vImage'];

    $VideoConsultLayoutDetails = json_decode($db_data_arr['VideoConsult']['tLayoutDetails'], true);
    $vTitleColorVideoConsult = $VideoConsultLayoutDetails['vTxtTitleColor']; 
    $vSubTitleColorVideoConsult = $VideoConsultLayoutDetails['vTxtSubTitleColor']; 
    $vBgColorVideoConsult = $VideoConsultLayoutDetails['vBgColor'];
}

/* Service Bid */
if ($MODULES_OBJ->isEnableBiddingServices()) {
    $vBiddingTitleArr = json_decode($db_data_arr['Bidding']['vTitle'], true);
    foreach ($vBiddingTitleArr as $key => $value) {
        $key = str_replace('vTitle_', 'vBiddingTitle_', $key);
        $userEditDataArr[$key] = $value;
    }
    $vBiddingSubTitleArr = json_decode($db_data_arr['Bidding']['vSubtitle'], true);
    foreach ($vBiddingSubTitleArr as $key => $value) {
        $key = str_replace('vSubtitle_', 'vBiddingSubTitle_', $key);
        $userEditDataArr[$key] = $value;
    }
    $vImageOldBidding = $db_data_arr['Bidding']['vImage'];

    $BiddingLayoutDetails = json_decode($db_data_arr['Bidding']['tLayoutDetails'], true);
    $vTitleColorBidding = $BiddingLayoutDetails['vTxtTitleColor']; 
    $vSubTitleColorBidding = $BiddingLayoutDetails['vTxtSubTitleColor']; 
    $vBgColorBidding = $BiddingLayoutDetails['vBgColor'];
}

/* On-Demand Services */
if ($MODULES_OBJ->isUberXFeatureAvailable()) {
    $vOnDemandServiceTitleArr = json_decode($db_data_arr['UberX']['vTitle'], true);
    foreach ($vOnDemandServiceTitleArr as $key => $value) {
        $key = str_replace('vTitle_', 'vOnDemandServiceTitle_', $key);
        $userEditDataArr[$key] = $value;
    }
    $vOnDemandServiceSubTitleArr = json_decode($db_data_arr['UberX']['vSubtitle'], true);
    foreach ($vOnDemandServiceSubTitleArr as $key => $value) {
        $key = str_replace('vSubtitle_', 'vOnDemandServiceSubTitle_', $key);
        $userEditDataArr[$key] = $value;
    }

    $tServiceDetails = $db_data_arr['UberX']['tServiceDetails'];
    $tServiceDetailsArr = array();
    if (!empty($tServiceDetails)) {
        $tServiceDetailsArr = json_decode($tServiceDetails, true);
    }
    $ufxData = $obj->MySQLSelect("SELECT iVehicleCategoryId, vCategory_$default_lang as vCategoryName FROM " . $sql_vehicle_category_table_name . " WHERE eCatType = 'ServiceProvider' AND eVideoConsultEnable = 'No' AND iParentId='0' AND eStatus = 'Active' ORDER BY vCategoryName");
}

/* Promotional Banner */
$promotionalBanner = $obj->MySQLSelect("SELECT iVehicleCategoryId FROM " . $sql_vehicle_category_table_name . " WHERE ePromoteBanner = 'Yes' AND eStatus = 'Active' ");
if (!empty($promotionalBanner) && scount($promotionalBanner) > 0) {
    $promotionalCategoryId = $promotionalBanner[0]['iVehicleCategoryId'];
} else {
    $promotionalBanner = $obj->MySQLSelect("SELECT iVehicleCategoryId FROM " . $sql_vehicle_category_table_name . " AND eStatus = 'Active' ORDER BY iDisplayOrder LIMIT 1");
    $promotionalCategoryId = $promotionalBanner[0]['iVehicleCategoryId'];
}

$promotional_banner_data = $obj->MySQLSelect("SELECT vImage FROM banners WHERE vCode = '$default_lang' AND iVehicleCategoryId = '" . $promotionalCategoryId . "' AND eType = 'Promotion'");

/* Buy, Sell & Rent */
if ($MODULES_OBJ->isEnableRentEstateService()) {
    $vRentEstateTitleArr = json_decode($MasterCategoryArr['RentEstate']['vCategoryName'], true);
    foreach ($vRentEstateTitleArr as $key => $value) {
        $key = str_replace('vCategoryName_', 'vRentEstateTitle_', $key);
        $userEditDataArr[$key] = $value;
    }
    $vImageOldRentEstate = $MasterCategoryArr['RentEstate']['vIconImage1'];

    $vTitleColorRentEstate = $MasterCategoryArr['RentEstate']['vTextColor']; 
    $vBgColorRentEstate = $MasterCategoryArr['RentEstate']['vBgColor']; 
}

if ($MODULES_OBJ->isEnableRentCarsService()) {
    $vRentCarsTitleArr = json_decode($MasterCategoryArr['RentCars']['vCategoryName'], true);
    foreach ($vRentCarsTitleArr as $key => $value) {
        $key = str_replace('vCategoryName_', 'vRentCarsTitle_', $key);
        $userEditDataArr[$key] = $value;
    }
    $vImageOldRentCars = $MasterCategoryArr['RentCars']['vIconImage1'];

    $vTitleColorRentCars = $MasterCategoryArr['RentCars']['vTextColor']; 
    $vBgColorRentCars = $MasterCategoryArr['RentCars']['vBgColor']; 
}

if ($MODULES_OBJ->isEnableRentItemService()) {
    $vRentItemTitleArr = json_decode($MasterCategoryArr['RentItem']['vCategoryName'], true);
    foreach ($vRentItemTitleArr as $key => $value) {
        $key = str_replace('vCategoryName_', 'vRentItemTitle_', $key);
        $userEditDataArr[$key] = $value;
    }
    $vImageOldRentItem = $MasterCategoryArr['RentItem']['vIconImage1'];

    $vTitleColorRentItem = $MasterCategoryArr['RentItem']['vTextColor']; 
    $vBgColorRentItem = $MasterCategoryArr['RentItem']['vBgColor'];
}

/* Ride Share */
if ($MODULES_OBJ->isEnableRideShareService()) {
    $labelsTaxiRideShare = $obj->MySQLSelect("SELECT vCode, vLabel, vValue FROM language_label WHERE vLabel IN ('LBL_RIDE_SHARE_PUBLISH_TXT', 'LBL_RIDE_SHARE_BOOK_TXT','LBL_RIDE_SHARE_MY_RIDES_TXT') ");

    foreach ($labelsTaxiRideShare as $label) {
        if ($label['vLabel'] == 'LBL_RIDE_SHARE_PUBLISH_TXT') {
            $userEditDataArr['RideSharePublishTitle_' . $label['vCode']] = $label['vValue'];
        } elseif ($label['vLabel'] == 'LBL_RIDE_SHARE_BOOK_TXT') {
            $userEditDataArr['RideShareBookTitle_' . $label['vCode']] = $label['vValue'];
        } elseif ($label['vLabel'] == 'LBL_RIDE_SHARE_MY_RIDES_TXT') {
            $userEditDataArr['RideShareMyRideTitle_' . $label['vCode']] = $label['vValue'];
        }
    }

    $service_details = $obj->MySQLSelect("SELECT tCategoryDetails FROM $master_service_category_tbl WHERE eType = 'RideShare' ");
    $tCategoryDetails = json_decode($service_details[0]['tCategoryDetails'], true);
    $vImageOldRideSharePublish = $tCategoryDetails['RideSharePublish']['vImage']; //$tconfig["tsite_upload_app_home_screen_images"]
    $vImageOldRideShareBook = $tCategoryDetails['RideShareBook']['vImage'];
    $vImageOldRideShareMyRides = $tCategoryDetails['RideShareMyRides']['vImage'];

    $vRideShareTitleArr = json_decode($db_data_arr['RideShare']['vTitle'], true);
    foreach ($vRideShareTitleArr as $key => $value) {
        $key = str_replace('vTitle_', 'vRideShareTitle_', $key);
        $userEditDataArr[$key] = $value;
    }
    $vRideShareSubTitleArr = json_decode($db_data_arr['RideShare']['vSubtitle'], true);
    foreach ($vRideShareSubTitleArr as $key => $value) {
        $key = str_replace('vSubtitle_', 'vRideShareSubTitle_', $key);
        $userEditDataArr[$key] = $value;
    }
    $vImageOldRideShare = $db_data_arr['RideShare']['vImage'];

    $RideShareLayoutDetails = json_decode($db_data_arr['RideShare']['tLayoutDetails'], true);
    $vTitleColorRideShare = $RideShareLayoutDetails['vTxtTitleColor']; 
    $vSubTitleColorRideShare = $RideShareLayoutDetails['vTxtSubTitleColor']; 
    $vBgColorRideShare = $RideShareLayoutDetails['vBgColor'];
}

/* Medical Services */
if ($MODULES_OBJ->isEnableMedicalServices()) {
    $vMSTitleArr = json_decode($db_data_arr['MedicalServices']['vTitle'], true);
    foreach ($vMSTitleArr as $key => $value) {
        $key = str_replace('vTitle_', 'vMSTitle_', $key);
        $userEditDataArr[$key] = $value;
    }

    $labelsMS = $obj->MySQLSelect("SELECT vCode, vLabel, vValue FROM language_label WHERE vLabel IN ('LBL_ON_DEMAND_MEDICAL_SERVICES_TITLE', 'LBL_ON_DEMAND_MEDICAL_SERVICES_DESC', 'LBL_VIDEO_CONSULT_MEDICAL_SERVICES_TITLE', 'LBL_VIDEO_CONSULT_MEDICAL_SERVICES_DESC', 'LBL_MEDICAL_MORE_SERVICES_TITLE', 'LBL_MEDICAL_MORE_SERVICES_DESC') ");

    $BookServiceMSTitleArr = $BookServiceMSSubTitleArr = $VideoConsultMSTitleArr = $VideoConsultMSSubTitleArr = $MoreServiceMSTitleArr = $MoreServiceMSSubTitleArr = array();
    foreach ($labelsMS as $label) {
        if($label['vLabel'] == 'LBL_ON_DEMAND_MEDICAL_SERVICES_TITLE') {
            $BookServiceMSTitleArr[$label['vCode']] = $label['vValue'];

        } elseif ($label['vLabel'] == 'LBL_ON_DEMAND_MEDICAL_SERVICES_DESC') {
            $BookServiceMSSubTitleArr[$label['vCode']] = $label['vValue'];

        } elseif ($label['vLabel'] == 'LBL_VIDEO_CONSULT_MEDICAL_SERVICES_TITLE') {
            $VideoConsultMSTitleArr[$label['vCode']] = $label['vValue'];

        } elseif ($label['vLabel'] == 'LBL_VIDEO_CONSULT_MEDICAL_SERVICES_DESC') {
            $VideoConsultMSSubTitleArr[$label['vCode']] = $label['vValue'];

        } elseif ($label['vLabel'] == 'LBL_MEDICAL_MORE_SERVICES_TITLE') {
            $MoreServiceMSTitleArr[$label['vCode']] = $label['vValue'];

        } elseif ($label['vLabel'] == 'LBL_MEDICAL_MORE_SERVICES_DESC') {
            $MoreServiceMSSubTitleArr[$label['vCode']] = $label['vValue'];
        }
    }

    $medicalServiceDataArr = $obj->MySQLSelect("SELECT vc.iParentId,vc.iVehicleCategoryId,vc.vCategory_$default_lang as vCategoryName, vc.eStatus, vc.iDisplayOrder,vc.eCatType,vc.eForMedicalService, vc.eVideoConsultEnable, vc.tMedicalServiceInfo, (select count(iVehicleCategoryId) FROM " . $sql_vehicle_category_table_name . " WHERE vc.iParentId = vc.iVehicleCategoryId AND eStatus = 'Active') as SubCategories FROM " . $sql_vehicle_category_table_name . " as vc WHERE eStatus = 'Active' AND (vc.iParentId='0' OR vc.iParentId = '3') AND eForMedicalService = 'Yes' AND iVehicleCategoryId != 297 ORDER BY iDisplayOrder ASC");
    $OnDemandServicesArr = $VideoConsultServicesArr = $MoreServicesArr = array();
    foreach ($medicalServiceDataArr as $medicalService) {
        if (!empty($medicalService['tMedicalServiceInfo'])) {
            $tMedicalServiceInfoArr = json_decode($medicalService['tMedicalServiceInfo'], true);
            $medicalServiceData = $medicalService;
            if ($tMedicalServiceInfoArr['BookService'] == "Yes") {
                $medicalServiceData['ms_display_order'] = $tMedicalServiceInfoArr['iDisplayOrderBS'];
                $medicalServiceDataBS = array();
                $medicalServiceDataBS = $medicalServiceData;
                $medicalServiceDataBS['eVideoConsultEnable'] = "No";
                $OnDemandServicesArr[] = $medicalServiceDataBS;
            }
            if ($medicalService['eVideoConsultEnable'] == "Yes" && $tMedicalServiceInfoArr['VideoConsult'] == "Yes") {
                $medicalServiceData['ms_display_order'] = $tMedicalServiceInfoArr['iDisplayOrderVC'];
                $VideoConsultServicesArr[] = $medicalServiceData;
            }
            if ($tMedicalServiceInfoArr['MoreService'] == "Yes") {
                $medicalServiceData['ms_display_order'] = $tMedicalServiceInfoArr['iDisplayOrderMS'];
                $medicalServiceDataMS = array();
                $medicalServiceDataMS = $medicalServiceData;
                $medicalServiceDataMS['eVideoConsultEnable'] = "No";
                $MoreServicesArr[] = $medicalServiceDataMS;
            }
        }
    }
    $ms_display_order = array_column($OnDemandServicesArr, 'ms_display_order');
    array_multisort($ms_display_order, SORT_ASC, $OnDemandServicesArr);
    $ms_display_order = array_column($VideoConsultServicesArr, 'ms_display_order');
    array_multisort($ms_display_order, SORT_ASC, $VideoConsultServicesArr);
    $ms_display_order = array_column($MoreServicesArr, 'ms_display_order');
    array_multisort($ms_display_order, SORT_ASC, $MoreServicesArr);

    $tServiceDetailsMS = $db_data_arr['MedicalServices']['tServiceDetails'];
    $tServiceDetailsMSArr = array();
    if (!empty($tServiceDetailsMS)) {
        $tServiceDetailsMSArr = json_decode($tServiceDetailsMS, true);
    }

    $MEDICAL_SERVICES_ARR = array(
        array(
            'ServiceTitle' => $BookServiceMSTitleArr, 
            'ServiceDesc' => $BookServiceMSSubTitleArr, 
            'ManageServiceKey' => 'BookService', 
            'ManageServiceSuffix' => 'BS', 
            'HiddenInput' => 'saveBookServiceMS', 
            'ServicesArr' => $OnDemandServicesArr

        ), 
        array(
            'ServiceTitle' => $VideoConsultMSTitleArr, 
            'ServiceDesc' => $VideoConsultMSSubTitleArr, 
            'ManageServiceKey' => 'VideoConsult', 
            'ManageServiceSuffix' => 'VC',  
            'HiddenInput' => 'saveVideoConsultMS', 
            'ServicesArr' => $VideoConsultServicesArr
        ),

        array(
            'ServiceTitle' => $MoreServiceMSTitleArr, 
            'ServiceDesc' => $MoreServiceMSSubTitleArr, 
            'ManageServiceKey' => 'MoreService', 
            'ManageServiceSuffix' => 'MS', 
            'HiddenInput' => 'saveMoreServiceMS', 
            'ServicesArr' => $MoreServicesArr
        )
    );

    $TextColorMS['BookService'] = $TextColorMS['VideoConsult'] = $TextColorMS['MoreService'] = "#000000";
    $BgColorMS['BookService'] = $BgColorMS['VideoConsult'] = $BgColorMS['MoreService'] = "#ffffff";
    $vImageOldMS['BookService'] = $vImageOldMS['VideoConsult'] = $vImageOldMS['MoreService'] = "";

    $tCategoryDetailsMS = $MasterCategoryArr['MedicalServices']['tCategoryDetails'];
    if (!empty($tCategoryDetailsMS)) {
        $tCategoryDetails = $tCategoryDetailsMS;
        if (!empty($tCategoryDetails)) {
            $tCategoryDetails = json_decode($tCategoryDetails, true);
            $TextColorMS['BookService'] = $tCategoryDetails['BookService']['vTextColor'];
            $BgColorMS['BookService'] = $tCategoryDetails['BookService']['vBgColor'];
            $vImageOldMS['BookService'] = $tCategoryDetails['BookService']['vImage'];
            $TextColorMS['VideoConsult'] = $tCategoryDetails['VideoConsult']['vTextColor'];
            $BgColorMS['VideoConsult'] = $tCategoryDetails['VideoConsult']['vBgColor'];
            $vImageOldMS['VideoConsult'] = $tCategoryDetails['VideoConsult']['vImage'];
            $TextColorMS['MoreService'] = $tCategoryDetails['MoreService']['vTextColor'];
            $BgColorMS['MoreService'] = $tCategoryDetails['MoreService']['vBgColor'];
            $vImageOldMS['MoreService'] = $tCategoryDetails['MoreService']['vImage'];
        }
    }
}

/* Track Service */
if ($MODULES_OBJ->isEnableTrackAnyServiceFeature()) {
    $vTrackServiceTitleArr = json_decode($db_data_arr['TrackAnyService']['vTitle'], true);
    foreach ($vTrackServiceTitleArr as $key => $value) {
        $key = str_replace('vTitle_', 'vTrackServiceTitle_', $key);
        $userEditDataArr[$key] = $value;
    }
    $vTrackServiceSubTitleArr = json_decode($db_data_arr['TrackAnyService']['vSubtitle'], true);

    foreach ($vTrackServiceSubTitleArr as $key => $value) {
        $key = str_replace('vSubtitle_', 'vTrackServiceSubTitle_', $key);
        $userEditDataArr[$key] = $value;
    }
    $vImageOldTrackService = $db_data_arr['TrackAnyService']['vImage'];

    $TrackServiceLayoutDetails = json_decode($db_data_arr['TrackAnyService']['tLayoutDetails'], true);
    $vTitleColorTrackService = $TrackServiceLayoutDetails['vTxtTitleColor']; 
    $vSubTitleColorTrackService = $TrackServiceLayoutDetails['vTxtSubTitleColor']; 
    $vBgColorTrackService = $TrackServiceLayoutDetails['vBgColor'];


    $trackServiceCategory = $obj->MySQLSelect("SELECT * FROM track_service_category WHERE eStatus = 'Active'");
    $userEditDataArrNew = $vTrackServiceCategoryArr =  array();
    foreach ($trackServiceCategory as $trackkey => $trackvalue) {
        $userEditDataArrNew[$trackkey]['iTrackServiceCategoryId'] = $trackvalue['iTrackServiceCategoryId'];
        $userEditDataArrNew[$trackkey]['vImage'] = $trackvalue['vImage'];
        $vTrackServiceCategoryArr[] = json_decode($trackvalue['vCategoryName'], true);
        foreach ($vTrackServiceCategoryArr as $tkey => $tvalue) {
            foreach ($tvalue as $tk => $tval) {
                $tk = str_replace('vCategoryName_', 'vTrackServiceCategory_', $tk);
                $userEditDataArrNew[$tkey][$tk] = $tval;
            }
        }
    }
  // echo"<pre>";print_r($userEditDataArrNew);die;
}

/* Nearby Services */
if ($MODULES_OBJ->isEnableNearByService()) {
    $vNearbyServiceTitleArr = json_decode($db_data_arr['NearBy']['vTitle'], true);
    foreach ($vNearbyServiceTitleArr as $key => $value) {
        $key = str_replace('vTitle_', 'vNearbyServiceTitle_', $key);
        $userEditDataArr[$key] = $value;
    }

    $tServiceDetailsNearby = $db_data_arr['NearBy']['tServiceDetails'];
    $tServiceDetailsNearbyArr = array();
    if (!empty($tServiceDetailsNearby)) {
        $tServiceDetailsNearbyArr = json_decode($tServiceDetailsNearby, true);
    }
    $nearbyData = $NEARBY_OBJ->getNearByCategory('webservice', '', '', '', $default_lang);
}

?>
<!DOCTYPE html>
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD-->
<head>
    <meta charset="UTF-8"/>
    <title>Admin | Manage App Home Screen</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <?php include_once('global_files.php'); ?>
    <link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css"/>
    <link href="../assets/css/jquery-ui.css" rel="stylesheet"/>
    <link rel="stylesheet" href="css/fancybox.css"/>
    <link rel="stylesheet" href="../assets/css/modal_alert.css"/>
    <style>
        .section-title {
            font-size: 24px;
            font-weight: 600;
        }

        .underline-section-title {
            display: block;
            border-top: 5px solid #799FCB;
            width: 75px;
            margin: 0 0 15px 0;
        }

        .save-section-btn {
            background-color: #000000;
            border-color: #000000;
            font-size: 18px;
            min-width: 120px;
            outline: none !important;
        }

        .save-section-btn:hover, .save-section-btn:focus, .save-section-btn:active, .save-section-btn:disabled {
            background-color: #000000;
            border-color: #000000;
        }

        .paddingbottom-10 {
            padding-bottom: 10px !important;
        }

        .paddingbottom-0 {
            padding-bottom: 0 !important;   
        }

        .promo-banner .banner-img-block {
            justify-content: center;
            grid-template-columns: auto;
        }

        /* Style the tab */
        .tab {
            overflow: hidden;
            border: 1px solid #ccc;
            background-color: #f1f1f1;
        }

        /* Style the buttons that are used to open the tab content */
        .tab button {
            background-color: inherit;
            float: left;
            border: none;
            outline: none;
            cursor: pointer;
            padding: 14px 16px;
            transition: 0.3s;
            font-weight: 500;
        }

        /* Change background color of buttons on hover */
        .tab button:hover {
            background-color: #dddddd;
        }

        /* Create an active/current tablink class */
        .tab button.active {
            background-color: #cccccc;
        }

        /* Style the tab content */
        .tabcontent {
            display: none;
            padding-top: 15px;
        }

        .display-tab-content {
            display: block;
        }

        .manage-banner-section .service-img-block {
            display: inline-block;
            justify-content: center;
            background-color: #ffffff;
            padding: 15px 0 10px 15px;
            margin-bottom: 15px;
        }

        .service-preview-img {
            width: auto;
            display: inline-block;
            margin-right: 15px;
            vertical-align: top;
        }

        .manage-banner-section .manage-icon-btn {
            display: block;
            margin: auto;
        }

        .service-img-title {
            font-size: 12px;
            font-weight: 600;
            word-break: break-word;
            width: 60px;
            margin-top: 5px;
        }

        .manage-banner-section .manage-banner-btn {
            margin-top: 10px;
        }

        .img-note {
            display: block;
            margin-top: 10px;
            width: max-content;
        }
    </style>
</head>
<!-- END  HEAD-->
<!-- BEGIN BODY-->
<body class="padTop53 ">
<!-- MAIN WRAPPER -->
<div id="wrap">
    <? include_once('header.php'); ?>
    <? include_once('left_menu.php'); ?>
    <!--PAGE CONTENT -->
    <div id="content">
        <div class="inner">
            <div class="row">
                <div class="col-lg-12">
                    <h2>Manage App Home Screen</h2>
                </div>
            </div>
            <hr/>
            <?php include('valid_msg.php'); ?>
            <div class="body-div">
                <div class="form-group">
                    <div class="show-help-section section-title">Introduction Section</div>
                    <div class="underline-section-title"></div>
                    <?php if (scount($db_master) > 1) { ?>
                        <div class="row">
                            <div class="col-lg-12">
                                <label>Title</label>
                            </div>
                            <div class="col-md-4 col-sm-4">
                                <input type="text" class="form-control" id="vIntroTitle_Default"
                                       name="vIntroTitle_Default"
                                       value="<?= $userEditDataArr['vIntroTitle_' . $default_lang]; ?>"
                                       data-originalvalue="<?= $userEditDataArr['vIntroTitle_' . $default_lang]; ?>"
                                       readonly="readonly" required>
                            </div>
                            <div class="col-lg-2">
                                <button type="button" class="btn btn-info" data-toggle="tooltip"
                                        data-original-title="Edit" onclick="editIntroTitle('Edit')">
                                    <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>
                        <div class="modal fade" id="IntroTitle_Modal" tabindex="-1" role="dialog" aria-hidden="true"
                             data-backdrop="static" data-keyboard="false">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content nimot-class">
                                    <div class="modal-header">
                                        <h4>
                                            <span id="intro_title_modal_action"></span>
                                            Title
                                            <button type="button" class="close" data-dismiss="modal"
                                                    onclick="resetToOriginalValue(this, 'vIntroTitle_')">x
                                            </button>
                                        </h4>
                                    </div>
                                    <div class="modal-body">
                                        <?php
                                        for ($i = 0; $i < $count_all; $i++) {
                                            $vCode = $db_master[$i]['vCode'];
                                            $vTitle = $db_master[$i]['vTitle'];
                                            $eDefault = $db_master[$i]['eDefault'];
                                            $vValue = 'vIntroTitle_' . $vCode;
                                            $$vValue = $userEditDataArr[$vValue];
                                            $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                            ?>
                                            <?php
                                            $page_title_class = 'col-lg-12';
                                            if (scount($db_master) > 1) {
                                                if ($EN_available) {
                                                    if ($vCode == "EN") {
                                                        $page_title_class = 'col-md-9 col-sm-9';
                                                    }
                                                } else {
                                                    if ($vCode == $default_lang) {
                                                        $page_title_class = 'col-md-9 col-sm-9';
                                                    }
                                                }
                                            }
                                            ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Title (<?= $vTitle; ?>
                                                        ) <?php echo $required_msg; ?></label>
                                                </div>
                                                <div class="<?= $page_title_class ?>">
                                                    <input type="text" class="form-control" name="<?= $vValue; ?>"
                                                           id="<?= $vValue; ?>" value="<?= $$vValue; ?>"
                                                           data-originalvalue="<?= $$vValue; ?>"
                                                           placeholder="<?= $vTitle; ?> Value">
                                                    <div class="text-danger" id="<?= $vValue . '_error'; ?>"
                                                         style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                </div>
                                                <?php
                                                if (scount($db_master) > 1) {
                                                    if ($EN_available) {
                                                        if ($vCode == "EN") { ?>
                                                            <div class="col-md-3 col-sm-3">
                                                                <button type="button" name="allLanguage"
                                                                        id="allLanguage" class="btn btn-primary"
                                                                        onClick="getAllLanguageCode('vIntroTitle_', 'EN');">
                                                                    Convert To All Language
                                                                </button>
                                                            </div>
                                                        <?php }
                                                    } else {
                                                        if ($vCode == $default_lang) { ?>
                                                            <div class="col-md-3 col-sm-3">
                                                                <button type="button" name="allLanguage"
                                                                        id="allLanguage" class="btn btn-primary"
                                                                        onClick="getAllLanguageCode('vIntroTitle_', '<?= $default_lang ?>');">
                                                                    Convert To All Language
                                                                </button>
                                                            </div>
                                                        <?php }
                                                    }
                                                }
                                                ?>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <div class="modal-footer" style="margin-top: 0">
                                        <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                            <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                            </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                        <div class="nimot-class-but" style="margin-bottom: 0">
                                            <button type="button" class="save" style="margin-left: 0 !important"
                                                    onclick="saveIntroTitle()"><?= $langage_lbl['LBL_Save']; ?></button>
                                            <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                    onclick="resetToOriginalValue(this, 'vIntroTitle_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                        </div>
                                    </div>
                                    <div style="clear:both;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <label>Subtitle</label>
                            </div>
                            <div class="col-md-4 col-sm-4">
                                <input type="text" class="form-control" id="vIntroSubTitle_Default"
                                       name="vIntroSubTitle_Default"
                                       value="<?= $userEditDataArr['vIntroSubTitle_' . $default_lang]; ?>"
                                       data-originalvalue="<?= $userEditDataArr['vIntroSubTitle_' . $default_lang]; ?>"
                                       readonly="readonly" required>
                            </div>
                            <div class="col-lg-2">
                                <button type="button" class="btn btn-info" data-toggle="tooltip"
                                        data-original-title="Edit" onclick="editIntroSubTitle('Edit')">
                                    <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>
                        <div class="modal fade" id="IntroSubTitle_Modal" tabindex="-1" role="dialog"
                             aria-hidden="true" data-backdrop="static" data-keyboard="false">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content nimot-class">
                                    <div class="modal-header">
                                        <h4>
                                            <span id="intro_subtitle_modal_action"></span>
                                            Title
                                            <button type="button" class="close" data-dismiss="modal"
                                                    onclick="resetToOriginalValue(this, 'vIntroSubTitle_')">x
                                            </button>
                                        </h4>
                                    </div>
                                    <div class="modal-body">
                                        <?php
                                        for ($i = 0; $i < $count_all; $i++) {
                                            $vCode = $db_master[$i]['vCode'];
                                            $vTitle = $db_master[$i]['vTitle'];
                                            $eDefault = $db_master[$i]['eDefault'];
                                            $vValue = 'vIntroSubTitle_' . $vCode;
                                            $$vValue = $userEditDataArr[$vValue];
                                            $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                            ?>
                                            <?php
                                            $page_title_class = 'col-lg-12';
                                            if (scount($db_master) > 1) {
                                                if ($EN_available) {
                                                    if ($vCode == "EN") {
                                                        $page_title_class = 'col-md-9 col-sm-9';
                                                    }
                                                } else {
                                                    if ($vCode == $default_lang) {
                                                        $page_title_class = 'col-md-9 col-sm-9';
                                                    }
                                                }
                                            }
                                            ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Subtitle (<?= $vTitle; ?>
                                                        ) <?php echo $required_msg; ?></label>
                                                </div>
                                                <div class="<?= $page_title_class ?>">
                                                    <input type="text" class="form-control" name="<?= $vValue; ?>"
                                                           id="<?= $vValue; ?>" value="<?= $$vValue; ?>"
                                                           data-originalvalue="<?= $$vValue; ?>"
                                                           placeholder="<?= $vTitle; ?> Value">
                                                    <div class="text-danger" id="<?= $vValue . '_error'; ?>"
                                                         style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                </div>
                                                <?php
                                                if (scount($db_master) > 1) {
                                                    if ($EN_available) {
                                                        if ($vCode == "EN") { ?>
                                                            <div class="col-md-3 col-sm-3">
                                                                <button type="button" name="allLanguage"
                                                                        id="allLanguage" class="btn btn-primary"
                                                                        onClick="getAllLanguageCode('vIntroSubTitle_', 'EN');">
                                                                    Convert To All Language
                                                                </button>
                                                            </div>
                                                        <?php }
                                                    } else {
                                                        if ($vCode == $default_lang) { ?>
                                                            <div class="col-md-3 col-sm-3">
                                                                <button type="button" name="allLanguage"
                                                                        id="allLanguage" class="btn btn-primary"
                                                                        onClick="getAllLanguageCode('vIntroSubTitle_', '<?= $default_lang ?>');">
                                                                    Convert To All Language
                                                                </button>
                                                            </div>
                                                        <?php }
                                                    }
                                                }
                                                ?>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <div class="modal-footer" style="margin-top: 0">
                                        <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                            <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                            </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                        <div class="nimot-class-but" style="margin-bottom: 0">
                                            <button type="button" class="save" style="margin-left: 0 !important"
                                                    onclick="saveIntroSubTitle()"><?= $langage_lbl['LBL_Save']; ?></button>
                                            <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                    onclick="resetToOriginalValue(this, 'vIntroSubTitle_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                        </div>
                                    </div>
                                    <div style="clear:both;"></div>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="row">
                            <div class="col-lg-12">
                                <label>Title</label>
                            </div>
                            <div class="col-md-4 col-sm-4">
                                <input type="text" class="form-control" id="vIntroTitle_<?= $default_lang ?>"
                                       name="vIntroTitle_<?= $default_lang ?>"
                                       value="<?= $userEditDataArr['vIntroTitle_' . $default_lang]; ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <label>Subtitle</label>
                            </div>
                            <div class="col-md-4 col-sm-4">
                                <input type="text" class="form-control" id="vIntroSubTitle_<?= $default_lang ?>"
                                       name="vIntroSubTitle_<?= $default_lang ?>"
                                       value="<?= $userEditDataArr['vIntroSubTitle_' . $default_lang]; ?>">
                            </div>
                        </div>
                    <?php } ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <button type="button" class="btn btn-primary save-section-btn" id="saveIntroSection">Save</button>
                        </div>
                    </div>
                
                    <hr />
                    <div class="show-help-section section-title">General Banners</div>
                    <div class="underline-section-title"></div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="manage-banner-section">
                                <?php if (scount($bannerData) > 0) { ?>
                                    <div class="banner-img-block">
                                        <?php foreach ($bannerData as $app_banner_img) { ?>
                                            <div class="banner-img">
                                                <img src="<?= $tconfig["tsite_url"] . 'resizeImg.php?w=400&src=' . $tconfig['tsite_upload_images'] . $app_banner_img['vImage']; ?>">
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } else { ?>
                                    <div class="no-banner">
                                        No Banner Found.
                                    </div>
                                <?php } ?>
                                <a href="<?= $tconfig['tsite_url_main_admin'] ?>banner.php" class="manage-banner-btn" target="_blank">Manage Banners for App Home Screen</a>
                            </div>
                        </div>
                    </div>

                    <?php if ($MODULES_OBJ->isRideFeatureAvailable() || $MODULES_OBJ->isDeliveryFeatureAvailable() || $MODULES_OBJ->isDeliverAllFeatureAvailable()) {
                        $GridViewTxtArr = array();
                        if($MODULES_OBJ->isRideFeatureAvailable()) {
                            $GridViewTxtArr[] = 'Taxi Services';
                        }
                        if($MODULES_OBJ->isDeliverAllFeatureAvailable()) {
                            $GridViewTxtArr[] = 'Food, Grocery, Store Deliveries';
                        }
                        if($MODULES_OBJ->isDeliveryFeatureAvailable()) {
                            $GridViewTxtArr[] = 'Delivery Anything Services';
                        }
                        $GridViewTxt = implode(" | ", $GridViewTxtArr);

                        $show_ride_tab = $show_deliverall_tab = $show_delivery_tab = "";
                        $show_ride_content = $show_deliverall_content = $show_delivery_content = "";
                    ?>
                        <hr />
                        <div class="show-help-section section-title"><?= $GridViewTxt ?></div>
                        <div class="underline-section-title"></div>

                        <div class="row paddingbottom-0">
                            <div class="col-lg-12">
                                <div class="tab">
                                    <?php if ($MODULES_OBJ->isRideFeatureAvailable()) { $show_ride_tab = "active"; $show_ride_content = "display-tab-content"; ?>
                                    <button class="tablinks manage-rideservice-tab <?= $show_ride_tab ?>" onclick="openTabContent(event, 'manage-rideservice-content', 'tabcontent-mainservice')"> Taxi Services
                                    </button>
                                    <?php } if($MODULES_OBJ->isDeliverAllFeatureAvailable()) {
                                        if(empty($show_ride_tab)) {
                                            $show_deliverall_tab = "active";
                                            $show_deliverall_content = "display-tab-content";
                                        }
                                        ?>
                                    <button class="tablinks manage-deliverallservice-tab <?= $show_deliverall_tab ?>" onclick="openTabContent(event, 'manage-deliverall-content', 'tabcontent-mainservice')"> Food, Grocery, Store Deliveries
                                    </button>
                                    <?php } if($MODULES_OBJ->isDeliveryFeatureAvailable()) {
                                        if(empty($show_ride_tab) && empty($show_deliverall_tab)) {
                                            $show_delivery_tab = "active";
                                            $show_delivery_content = "display-tab-content";
                                        }
                                        ?>
                                    <button class="tablinks manage-deliveryservice-tab <?= $show_delivery_tab ?>" onclick="openTabContent(event, 'manage-delivery-content', 'tabcontent-mainservice')"> Delivery Anything Services
                                    </button>
                                    <?php } ?>
                                </div>
                                <?php if ($MODULES_OBJ->isRideFeatureAvailable()) { ?>
                                <div class="tabcontent tabcontent-mainservice <?= $show_ride_content ?>" id="manage-rideservice-content">
                                    <div class="col-lg-12">
                                        <?php if (scount($db_master) > 1) { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Title</label>
                                                </div>
                                                <div class="col-md-4 col-sm-4">
                                                    <input type="text" class="form-control" id="vTaxiBookingTitle_Default"
                                                           name="vTaxiBookingTitle_Default"
                                                           value="<?= $userEditDataArr['vTaxiBookingTitle_' . $default_lang]; ?>"
                                                           data-originalvalue="<?= $userEditDataArr['vTaxiBookingTitle_' . $default_lang]; ?>"
                                                           readonly="readonly" required>
                                                </div>
                                                <div class="col-lg-2">
                                                    <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                            data-original-title="Edit" onclick="editTaxiBookingTitle('Edit')">
                                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="TaxiBookingTitle_Modal" tabindex="-1" role="dialog" aria-hidden="true"
                                                 data-backdrop="static" data-keyboard="false">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content nimot-class">
                                                        <div class="modal-header">
                                                            <h4>
                                                                <span id="taxibooking_title_modal_action"></span>
                                                                Title
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                        onclick="resetToOriginalValue(this, 'vTaxiBookingTitle_')">x
                                                                </button>
                                                            </h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <?php
                                                            for ($i = 0; $i < $count_all; $i++) {
                                                                $vCode = $db_master[$i]['vCode'];
                                                                $vTitle = $db_master[$i]['vTitle'];
                                                                $eDefault = $db_master[$i]['eDefault'];
                                                                $vValue = 'vTaxiBookingTitle_' . $vCode;
                                                                $$vValue = $userEditDataArr[$vValue];
                                                                $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                                ?>
                                                                <?php
                                                                $page_title_class = 'col-lg-12';
                                                                if (scount($db_master) > 1) {
                                                                    if ($EN_available) {
                                                                        if ($vCode == "EN") {
                                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                                        }
                                                                    } else {
                                                                        if ($vCode == $default_lang) {
                                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                                        }
                                                                    }
                                                                }
                                                                ?>
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <label>Title (<?= $vTitle; ?>
                                                                            ) <?php echo $required_msg; ?></label>
                                                                    </div>
                                                                    <div class="<?= $page_title_class ?>">
                                                                        <input type="text" class="form-control" name="<?= $vValue; ?>"
                                                                               id="<?= $vValue; ?>" value="<?= $$vValue; ?>"
                                                                               data-originalvalue="<?= $$vValue; ?>"
                                                                               placeholder="<?= $vTitle; ?> Value">
                                                                        <div class="text-danger" id="<?= $vValue . '_error'; ?>"
                                                                             style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                                    </div>
                                                                    <?php
                                                                    if (scount($db_master) > 1) {
                                                                        if ($EN_available) {
                                                                            if ($vCode == "EN") { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type="button" name="allLanguage"
                                                                                            id="allLanguage" class="btn btn-primary"
                                                                                            onClick="getAllLanguageCode('vTaxiBookingTitle_', 'EN');">
                                                                                        Convert To All Language
                                                                                    </button>
                                                                                </div>
                                                                            <?php }
                                                                        } else {
                                                                            if ($vCode == $default_lang) { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type="button" name="allLanguage"
                                                                                            id="allLanguage" class="btn btn-primary"
                                                                                            onClick="getAllLanguageCode('vTaxiBookingTitle_', '<?= $default_lang ?>');">
                                                                                        Convert To All Language
                                                                                    </button>
                                                                                </div>
                                                                            <?php }
                                                                        }
                                                                    }
                                                                    ?>
                                                                </div>
                                                                <?php
                                                            }
                                                            ?>
                                                        </div>
                                                        <div class="modal-footer" style="margin-top: 0">
                                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                                <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                                                </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                                <button type="button" class="save" style="margin-left: 0 !important"
                                                                        onclick="saveTaxiBookingTitle()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                                        onclick="resetToOriginalValue(this, 'vTaxiBookingTitle_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                            </div>
                                                        </div>
                                                        <div style="clear:both;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Title</label>
                                                </div>
                                                <div class="col-md-4 col-sm-4">
                                                    <input type="text" class="form-control" id="vTaxiBookingTitle_<?= $default_lang ?>" name="vTaxiBookingTitle_<?= $default_lang ?>" value="<?= $userEditDataArr['vTaxiBookingTitle_' . $default_lang]; ?>">
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="row pb-10">
                                            <div class="col-lg-12">
                                                <label>Image</label>
                                            </div>
                                            <div class="col-md-4 col-sm-4 marginbottom-10">
                                                <?php if(!empty($vImageOldTaxiBooking)) { ?>
                                                <div class="marginbottom-10">
                                                    <img src="<?=$tconfig["tsite_url"].'resizeImg.php?h=100&src=' . $tconfig['tsite_upload_app_home_screen_images'] . $vImageOldTaxiBooking; ?>" id="taxibooking_img">
                                                </div>
                                                <?php } ?>
                                                <input type="file" class="form-control" name="vImageTaxiBooking" id="vImageTaxiBooking" onchange="previewImage(this, event);" data-img="taxibooking_img">
                                                <input type="hidden" class="form-control" name="vImageOldTaxiBooking" id="vImageOldTaxiBooking" value="<?= $vImageOldTaxiBooking ?>">
                                                <strong class="img-note">Note: Upload only png image size of 512px X 512px. <br> <?= IMAGE_INSTRUCTION_NOTES ?> </strong>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <button type="button" class="btn btn-primary save-section-btn" id="saveTaxiBookingSection">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>                            
                                <?php } ?>

                                <?php if ($MODULES_OBJ->isDeliverAllFeatureAvailable()) { ?>
                                <div class="tabcontent tabcontent-mainservice <?= $show_deliverall_content ?>" id="manage-deliverall-content">
                                    <div class="col-lg-12">
                                        <?php if (scount($db_master) > 1) { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Title</label>
                                                </div>
                                                <div class="col-md-4 col-sm-4">
                                                    <input type="text" class="form-control" id="vDeliverAllTitle_Default" name="vDeliverAllTitle_Default" value="<?= $userEditDataArr['vDeliverAllTitle_' . $default_lang]; ?>" data-originalvalue="<?= $userEditDataArr['vDeliverAllTitle_' . $default_lang]; ?>" readonly="readonly" required>
                                                </div>
                                                <div class="col-lg-2">
                                                    <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                            data-original-title="Edit" onclick="editDeliverAllTitle('Edit')">
                                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="DeliverAllTitle_Modal" tabindex="-1" role="dialog" aria-hidden="true"
                                                 data-backdrop="static" data-keyboard="false">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content nimot-class">
                                                        <div class="modal-header">
                                                            <h4>
                                                                <span id="deliverall_title_modal_action"></span>
                                                                Title
                                                                <button type="button" class="close" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vDeliverAllTitle_')">x
                                                                </button>
                                                            </h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <?php
                                                            for ($i = 0; $i < $count_all; $i++) {
                                                                $vCode = $db_master[$i]['vCode'];
                                                                $vTitle = $db_master[$i]['vTitle'];
                                                                $eDefault = $db_master[$i]['eDefault'];
                                                                $vValue = 'vDeliverAllTitle_' . $vCode;
                                                                $$vValue = $userEditDataArr[$vValue];
                                                                $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                                ?>
                                                                <?php
                                                                $page_title_class = 'col-lg-12';
                                                                if (scount($db_master) > 1) {
                                                                    if ($EN_available) {
                                                                        if ($vCode == "EN") {
                                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                                        }
                                                                    } else {
                                                                        if ($vCode == $default_lang) {
                                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                                        }
                                                                    }
                                                                }
                                                                ?>
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <label>Title (<?= $vTitle; ?>
                                                                            ) <?php echo $required_msg; ?></label>
                                                                    </div>
                                                                    <div class="<?= $page_title_class ?>">
                                                                        <input type="text" class="form-control" name="<?= $vValue; ?>"
                                                                               id="<?= $vValue; ?>" value="<?= $$vValue; ?>"
                                                                               data-originalvalue="<?= $$vValue; ?>"
                                                                               placeholder="<?= $vTitle; ?> Value">
                                                                        <div class="text-danger" id="<?= $vValue . '_error'; ?>"
                                                                             style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                                    </div>
                                                                    <?php
                                                                    if (scount($db_master) > 1) {
                                                                        if ($EN_available) {
                                                                            if ($vCode == "EN") { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type="button" name="allLanguage"
                                                                                            id="allLanguage" class="btn btn-primary"
                                                                                            onClick="getAllLanguageCode('vDeliverAllTitle_', 'EN');">
                                                                                        Convert To All Language
                                                                                    </button>
                                                                                </div>
                                                                            <?php }
                                                                        } else {
                                                                            if ($vCode == $default_lang) { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type="button" name="allLanguage"
                                                                                            id="allLanguage" class="btn btn-primary"
                                                                                            onClick="getAllLanguageCode('vDeliverAllTitle_', '<?= $default_lang ?>');">
                                                                                        Convert To All Language
                                                                                    </button>
                                                                                </div>
                                                                            <?php }
                                                                        }
                                                                    }
                                                                    ?>
                                                                </div>
                                                                <?php
                                                            }
                                                            ?>
                                                        </div>
                                                        <div class="modal-footer" style="margin-top: 0">
                                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                                <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                                                </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                                <button type="button" class="save" style="margin-left: 0 !important"
                                                                        onclick="saveDeliverAllTitle()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                                        onclick="resetToOriginalValue(this, 'vDeliverAllTitle_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                            </div>
                                                        </div>
                                                        <div style="clear:both;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Title</label>
                                                </div>
                                                <div class="col-md-4 col-sm-4">
                                                    <input type="text" class="form-control" id="vDeliverAllTitle_<?= $default_lang ?>" name="vDeliverAllTitle_<?= $default_lang ?>" value="<?= $userEditDataArr['vDeliverAllTitle_' . $default_lang]; ?>">
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="row pb-10">
                                            <div class="col-lg-12">
                                                <label>Image</label>
                                            </div>
                                            <div class="col-md-4 col-sm-4 marginbottom-10">
                                                <?php if(!empty($vImageOldDeliverAll)) { ?>
                                                <div class="marginbottom-10">
                                                    <img src="<?=$tconfig["tsite_url"].'resizeImg.php?h=100&src=' . $tconfig['tsite_upload_app_home_screen_images'] . $vImageOldDeliverAll; ?>" id="deliverall_img">
                                                </div>
                                                <?php } ?>
                                                <input type="file" class="form-control" name="vImageDeliverAll" id="vImageDeliverAll" onchange="previewImage(this, event);" data-img="deliverall_img">
                                                <input type="hidden" class="form-control" name="vImageOldDeliverAll" id="vImageOldDeliverAll" value="<?= $vImageOldDeliverAll ?>">
                                                <strong class="img-note">Note: Upload only png image size of 512px X 512px. <br> <?= IMAGE_INSTRUCTION_NOTES ?> </strong>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <button type="button" class="btn btn-primary save-section-btn" id="saveDeliverAllSection">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>

                                <?php if ($MODULES_OBJ->isDeliveryFeatureAvailable()) { ?>
                                <div class="tabcontent tabcontent-mainservice <?= $show_delivery_content ?>" id="manage-delivery-content">
                                    <div class="col-lg-12">
                                        <?php if (scount($db_master) > 1) { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Title</label>
                                                </div>
                                                <div class="col-md-4 col-sm-4">
                                                    <input type="text" class="form-control" id="vDeliveryTitle_Default"
                                                           name="vDeliveryTitle_Default"
                                                           value="<?= $userEditDataArr['vDeliveryTitle_' . $default_lang]; ?>"
                                                           data-originalvalue="<?= $userEditDataArr['vDeliveryTitle_' . $default_lang]; ?>"
                                                           readonly="readonly" required>
                                                </div>
                                                <div class="col-lg-2">
                                                    <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                            data-original-title="Edit" onclick="editDeliveryTitle('Edit')">
                                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="DeliveryTitle_Modal" tabindex="-1" role="dialog" aria-hidden="true"
                                                 data-backdrop="static" data-keyboard="false">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content nimot-class">
                                                        <div class="modal-header">
                                                            <h4>
                                                                <span id="delivery_title_modal_action"></span>
                                                                Title
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                        onclick="resetToOriginalValue(this, 'vDeliveryTitle_')">x
                                                                </button>
                                                            </h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <?php
                                                            for ($i = 0; $i < $count_all; $i++) {
                                                                $vCode = $db_master[$i]['vCode'];
                                                                $vTitle = $db_master[$i]['vTitle'];
                                                                $eDefault = $db_master[$i]['eDefault'];
                                                                $vValue = 'vDeliveryTitle_' . $vCode;
                                                                $$vValue = $userEditDataArr[$vValue];
                                                                $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                                ?>
                                                                <?php
                                                                $page_title_class = 'col-lg-12';
                                                                if (scount($db_master) > 1) {
                                                                    if ($EN_available) {
                                                                        if ($vCode == "EN") {
                                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                                        }
                                                                    } else {
                                                                        if ($vCode == $default_lang) {
                                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                                        }
                                                                    }
                                                                }
                                                                ?>
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <label>Title (<?= $vTitle; ?>
                                                                            ) <?php echo $required_msg; ?></label>
                                                                    </div>
                                                                    <div class="<?= $page_title_class ?>">
                                                                        <input type="text" class="form-control" name="<?= $vValue; ?>"
                                                                               id="<?= $vValue; ?>" value="<?= $$vValue; ?>"
                                                                               data-originalvalue="<?= $$vValue; ?>"
                                                                               placeholder="<?= $vTitle; ?> Value">
                                                                        <div class="text-danger" id="<?= $vValue . '_error'; ?>"
                                                                             style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                                    </div>
                                                                    <?php
                                                                    if (scount($db_master) > 1) {
                                                                        if ($EN_available) {
                                                                            if ($vCode == "EN") { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type="button" name="allLanguage"
                                                                                            id="allLanguage" class="btn btn-primary"
                                                                                            onClick="getAllLanguageCode('vDeliveryTitle_', 'EN');">
                                                                                        Convert To All Language
                                                                                    </button>
                                                                                </div>
                                                                            <?php }
                                                                        } else {
                                                                            if ($vCode == $default_lang) { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type="button" name="allLanguage"
                                                                                            id="allLanguage" class="btn btn-primary"
                                                                                            onClick="getAllLanguageCode('vDeliveryTitle_', '<?= $default_lang ?>');">
                                                                                        Convert To All Language
                                                                                    </button>
                                                                                </div>
                                                                            <?php }
                                                                        }
                                                                    }
                                                                    ?>
                                                                </div>
                                                                <?php
                                                            }
                                                            ?>
                                                        </div>
                                                        <div class="modal-footer" style="margin-top: 0">
                                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                                <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                                                </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                                <button type="button" class="save" style="margin-left: 0 !important"
                                                                        onclick="saveDeliveryTitle()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                                        onclick="resetToOriginalValue(this, 'vDeliveryTitle_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                            </div>
                                                        </div>
                                                        <div style="clear:both;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Title</label>
                                                </div>
                                                <div class="col-md-4 col-sm-4">
                                                    <input type="text" class="form-control" id="vDeliveryTitle_<?= $default_lang ?>" name="vDeliveryTitle_<?= $default_lang ?>" value="<?= $userEditDataArr['vDeliveryTitle_' . $default_lang]; ?>">
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="row pb-10">
                                            <div class="col-lg-12">
                                                <label>Image</label>
                                            </div>
                                            <div class="col-md-4 col-sm-4 marginbottom-10">
                                                <?php if(!empty($vImageOldDelivery)) { ?>
                                                <div class="marginbottom-10">
                                                    <img src="<?=$tconfig["tsite_url"].'resizeImg.php?h=100&src=' . $tconfig['tsite_upload_app_home_screen_images'] . $vImageOldDelivery; ?>" id="delivery_img">
                                                </div>
                                                <?php } ?>
                                                <input type="file" class="form-control" name="vImageDelivery" id="vImageDelivery" onchange="previewImage(this, event);" data-img="delivery_img">
                                                <input type="hidden" class="form-control" name="vImageOldDelivery" id="vImageOldDelivery" value="<?= $vImageOldDelivery ?>">
                                                <strong class="img-note">Note: Upload only png image size of 512px X 512px. <br> <?= IMAGE_INSTRUCTION_NOTES ?> </strong>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <button type="button" class="btn btn-primary save-section-btn" id="saveDeliverySection">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($MODULES_OBJ->isEnableTaxiBidFeature()) { ?>
                        <hr />
                        <div class="show-help-section section-title">Bid for Taxi</div>
                        <div class="underline-section-title"></div>

                        <div class="row paddingbottom-0">
                            <div class="col-lg-12">
                                <div class="tab">
                                    <button class="tablinks manage-taxibid-home-screen-tab active" onclick="openTabContent(event, 'manage-taxibid-home-screen-content', 'tabcontent-taxibid')">Home Screen</button>
                                    <button class="tablinks manage-taxibid-info-screen-tab" onclick="openTabContent(event, 'manage-taxibid-info-screen-content', 'tabcontent-taxibid')">Info Screen</button>
                                </div>
                            </div>
                        </div>

                        <div class="tabcontent tabcontent-taxibid display-tab-content" id="manage-taxibid-home-screen-content">
                            <?php if (scount($db_master) > 1) { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Title</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control" id="vTaxiBidTitle_Default"
                                               name="vTaxiBidTitle_Default"
                                               value="<?= $userEditDataArr['vTaxiBidTitle_' . $default_lang]; ?>"
                                               data-originalvalue="<?= $userEditDataArr['vTaxiBidTitle_' . $default_lang]; ?>"
                                               readonly="readonly" required>
                                    </div>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                data-original-title="Edit" onclick="editTaxiBidTitle('Edit')">
                                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="modal fade" id="TaxiBidTitle_Modal" tabindex="-1" role="dialog" aria-hidden="true"
                                     data-backdrop="static" data-keyboard="false">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content nimot-class">
                                            <div class="modal-header">
                                                <h4>
                                                    <span id="taxibid_title_modal_action"></span>
                                                    Title
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'vTaxiBidTitle_')">x
                                                    </button>
                                                </h4>
                                            </div>
                                            <div class="modal-body">
                                                <?php
                                                for ($i = 0; $i < $count_all; $i++) {
                                                    $vCode = $db_master[$i]['vCode'];
                                                    $vTitle = $db_master[$i]['vTitle'];
                                                    $eDefault = $db_master[$i]['eDefault'];
                                                    $vValue = 'vTaxiBidTitle_' . $vCode;
                                                    $$vValue = $userEditDataArr[$vValue];
                                                    $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                    ?>
                                                    <?php
                                                    $page_title_class = 'col-lg-12';
                                                    if (scount($db_master) > 1) {
                                                        if ($EN_available) {
                                                            if ($vCode == "EN") {
                                                                $page_title_class = 'col-md-9 col-sm-9';
                                                            }
                                                        } else {
                                                            if ($vCode == $default_lang) {
                                                                $page_title_class = 'col-md-9 col-sm-9';
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label>Title (<?= $vTitle; ?>
                                                                ) <?php echo $required_msg; ?></label>
                                                        </div>
                                                        <div class="<?= $page_title_class ?>">
                                                            <input type="text" class="form-control" name="<?= $vValue; ?>"
                                                                   id="<?= $vValue; ?>" value="<?= $$vValue; ?>"
                                                                   data-originalvalue="<?= $$vValue; ?>"
                                                                   placeholder="<?= $vTitle; ?> Value">
                                                            <div class="text-danger" id="<?= $vValue . '_error'; ?>"
                                                                 style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                        </div>
                                                        <?php
                                                        if (scount($db_master) > 1) {
                                                            if ($EN_available) {
                                                                if ($vCode == "EN") { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vTaxiBidTitle_', 'EN');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                            } else {
                                                                if ($vCode == $default_lang) { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vTaxiBidTitle_', '<?= $default_lang ?>');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <div class="modal-footer" style="margin-top: 0">
                                                <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                    <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                                    </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                <div class="nimot-class-but" style="margin-bottom: 0">
                                                    <button type="button" class="save" style="margin-left: 0 !important"
                                                            onclick="saveTaxiBidTitle()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                    <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'vTaxiBidTitle_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                </div>
                                            </div>
                                            <div style="clear:both;"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Button Text</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control" id="vBtnTxt_Default" name="vBtnTxt_Default" value="<?= $userEditDataArr['vBtnTxt_' . $default_lang]; ?>" data-originalvalue="<?= $userEditDataArr['vBtnTxt_' . $default_lang]; ?>" readonly="readonly" required>
                                    </div>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editBtnTxt('Edit')">
                                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="modal fade" id="BtnTxt_Modal" tabindex="-1" role="dialog" aria-hidden="true"
                                     data-backdrop="static" data-keyboard="false">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content nimot-class">
                                            <div class="modal-header">
                                                <h4>
                                                    <span id="btntxt_modal_action"></span>
                                                    Button Text
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'vBtnTxt_')">x
                                                    </button>
                                                </h4>
                                            </div>
                                            <div class="modal-body">
                                                <?php
                                                for ($i = 0; $i < $count_all; $i++) {
                                                    $vCode = $db_master[$i]['vCode'];
                                                    $vTitle = $db_master[$i]['vTitle'];
                                                    $eDefault = $db_master[$i]['eDefault'];
                                                    $vValue = 'vBtnTxt_' . $vCode;
                                                    $$vValue = $userEditDataArr[$vValue];
                                                    $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                    ?>
                                                    <?php
                                                    $page_title_class = 'col-lg-12';
                                                    if (scount($db_master) > 1) {
                                                        if ($EN_available) {
                                                            if ($vCode == "EN") {
                                                                $page_title_class = 'col-md-9 col-sm-9';
                                                            }
                                                        } else {
                                                            if ($vCode == $default_lang) {
                                                                $page_title_class = 'col-md-9 col-sm-9';
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label>Button Text (<?= $vTitle; ?>
                                                                ) <?php echo $required_msg; ?></label>
                                                        </div>
                                                        <div class="<?= $page_title_class ?>">
                                                            <input type="text" class="form-control" name="<?= $vValue; ?>"
                                                                   id="<?= $vValue; ?>" value="<?= $$vValue; ?>"
                                                                   data-originalvalue="<?= $$vValue; ?>"
                                                                   placeholder="<?= $vTitle; ?> Value">
                                                            <div class="text-danger" id="<?= $vValue . '_error'; ?>"
                                                                 style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                        </div>
                                                        <?php
                                                        if (scount($db_master) > 1) {
                                                            if ($EN_available) {
                                                                if ($vCode == "EN") { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vBtnTxt_', 'EN');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                            } else {
                                                                if ($vCode == $default_lang) { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vBtnTxt_', '<?= $default_lang ?>');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <div class="modal-footer" style="margin-top: 0">
                                                <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                    <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                                    </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                <div class="nimot-class-but" style="margin-bottom: 0">
                                                    <button type="button" class="save" style="margin-left: 0 !important" onclick="saveBtnTxt()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                    <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vBtnTxt_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                </div>
                                            </div>
                                            <div style="clear:both;"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Title</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control" id="vTaxiBidTitle_<?= $default_lang ?>" name="vTaxiBidTitle_<?= $default_lang ?>" value="<?= $userEditDataArr['vTaxiBidTitle_' . $default_lang]; ?>">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Button Text</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control" id="vBtnTxt_<?= $default_lang ?>" name="vBtnTxt_<?= $default_lang ?>" value="<?= $userEditDataArr['vBtnTxt_' . $default_lang]; ?>">
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Title Color</label>
                                </div>
                                <div class="col-md-1 col-sm-1">
                                    <input type="color" data-id="vTitleColorTaxiBid" class="form-control txt-color" value="<?= $vTitleColorTaxiBid ?>"/>
                                    <input type="hidden" name="vTitleColorTaxiBid" id="vTitleColorTaxiBid" value="<?= $vTitleColorTaxiBid ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Background Color</label>
                                </div>
                                <div class="col-md-1 col-sm-1">
                                    <input type="color" data-id="vBgColorTaxiBid" class="form-control bg-color" value="<?= $vBgColorTaxiBid ?>"/>
                                    <input type="hidden" name="vBgColorTaxiBid" id="vBgColorTaxiBid" value="<?= $vBgColorTaxiBid ?>">
                                </div>
                            </div>

                            <div class="row pb-10">
                                <div class="col-lg-12">
                                    <label>Image</label>
                                </div>
                                <div class="col-md-4 col-sm-4 marginbottom-10">
                                    <?php if(!empty($vImageOldTaxiBid)) { ?>
                                    <div class="marginbottom-10">
                                        <img src="<?=$tconfig["tsite_url"].'resizeImg.php?h=100&src=' . $tconfig['tsite_upload_app_home_screen_images'] . 'AppHomeScreen/' . $vImageOldTaxiBid; ?>" id="taxibid_img">
                                    </div>
                                    <?php } ?>
                                    <input type="file" class="form-control" name="vImageTaxiBid" id="vImageTaxiBid" onchange="previewImage(this, event);" data-img="taxibid_img">
                                    <input type="hidden" class="form-control" name="vImageOldTaxiBid" id="vImageOldTaxiBid" value="<?= $vImageOldTaxiBid ?>">
                                    <strong class="img-note">Note: Upload only png image size of 865px X 763px. <br> <?= IMAGE_INSTRUCTION_NOTES ?> </strong>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <button type="button" class="btn btn-primary save-section-btn" id="saveTaxiBidSection">Save</button>
                                </div>
                            </div>
                        </div>

                        <div class="tabcontent tabcontent-taxibid" id="manage-taxibid-info-screen-content">
                            <?php if (scount($db_master) > 1) { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Info Title</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control" id="vTaxiBidInfoTitle_Default" name="vTaxiBidInfoTitle_Default" value="<?= $userEditDataArr['ServicePageTitle_' . $default_lang]; ?>" data-originalvalue="<?= $userEditDataArr['ServicePageTitle_' . $default_lang]; ?>" readonly="readonly" required>
                                    </div>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editTaxiBidInfoTitle('Edit')">
                                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="modal fade" id="TaxiBidInfoTitle_Modal" tabindex="-1" role="dialog" aria-hidden="true"
                                     data-backdrop="static" data-keyboard="false">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content nimot-class">
                                            <div class="modal-header">
                                                <h4>
                                                    <span id="taxibid_infotitle_modal_action"></span>
                                                    Info Title
                                                    <button type="button" class="close" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vTaxiBidInfoTitle_')">x
                                                    </button>
                                                </h4>
                                            </div>
                                            <div class="modal-body">
                                                <?php
                                                for ($i = 0; $i < $count_all; $i++) {
                                                    $vCode = $db_master[$i]['vCode'];
                                                    $vTitle = $db_master[$i]['vTitle'];
                                                    $eDefault = $db_master[$i]['eDefault'];
                                                    $vValue = 'vTaxiBidInfoTitle_' . $vCode;
                                                    $$vValue = $userEditDataArr['ServicePageTitle_' . $vCode];
                                                    $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                    ?>
                                                    <?php
                                                    $page_title_class = 'col-lg-12';
                                                    if (scount($db_master) > 1) {
                                                        if ($EN_available) {
                                                            if ($vCode == "EN") {
                                                                $page_title_class = 'col-md-9 col-sm-9';
                                                            }
                                                        } else {
                                                            if ($vCode == $default_lang) {
                                                                $page_title_class = 'col-md-9 col-sm-9';
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label>Title (<?= $vTitle; ?>
                                                                ) <?php echo $required_msg; ?></label>
                                                        </div>
                                                        <div class="<?= $page_title_class ?>">
                                                            <input type="text" class="form-control" name="<?= $vValue; ?>"
                                                                   id="<?= $vValue; ?>" value="<?= $$vValue; ?>"
                                                                   data-originalvalue="<?= $$vValue; ?>"
                                                                   placeholder="<?= $vTitle; ?> Value">
                                                            <div class="text-danger" id="<?= $vValue . '_error'; ?>"
                                                                 style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                        </div>
                                                        <?php
                                                        if (scount($db_master) > 1) {
                                                            if ($EN_available) {
                                                                if ($vCode == "EN") { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vTaxiBidInfoTitle_', 'EN');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                            } else {
                                                                if ($vCode == $default_lang) { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vTaxiBidInfoTitle_', '<?= $default_lang ?>');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <div class="modal-footer" style="margin-top: 0">
                                                <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                    <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                                    </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                <div class="nimot-class-but" style="margin-bottom: 0">
                                                    <button type="button" class="save" style="margin-left: 0 !important"
                                                            onclick="saveTaxiBidInfoTitle()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                    <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vTaxiBidInfoTitle_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                </div>
                                            </div>
                                            <div style="clear:both;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Info Description</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <textarea class="form-control ckeditor" rows="10" id="vTaxiBidInfoSubTitle_Default" name="vTaxiBidInfoSubTitle_Default" data-originalvalue="<?= $userEditDataArr['ServicePageDesc_' . $default_lang] ?>" readonly="readonly"><?= $userEditDataArr['ServicePageDesc_' . $default_lang] ?></textarea>
                                    </div>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                data-original-title="Edit" onclick="editTaxiBidInfoSubTitle('Edit')">
                                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="modal fade" id="TaxiBidInfoSubTitle_Modal" tabindex="-1" role="dialog"
                                     aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content nimot-class">
                                            <div class="modal-header">
                                                <h4>
                                                    <span id="taxibid_infosubtitle_modal_action"></span>
                                                    Description
                                                    <button type="button" class="close" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vTaxiBidInfoSubTitle_')">x
                                                    </button>
                                                </h4>
                                            </div>
                                            <div class="modal-body">
                                                <?php
                                                for ($i = 0; $i < $count_all; $i++) {
                                                    $vCode = $db_master[$i]['vCode'];
                                                    $vTitle = $db_master[$i]['vTitle'];
                                                    $eDefault = $db_master[$i]['eDefault'];
                                                    $vValue = 'vTaxiBidInfoSubTitle_' . $vCode;
                                                    $$vValue = $userEditDataArr['ServicePageDesc_' . $vCode];
                                                    $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                    ?>
                                                    <?php
                                                    $page_title_class = 'col-lg-12';
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label>Description (<?= $vTitle; ?>) <?php echo $required_msg; ?></label>
                                                        </div>
                                                        <div class="<?= $page_title_class ?>">
                                                            <textarea class="form-control ckeditor" rows="10" name="<?= $vValue; ?>" id="<?= $vValue; ?>" data-originalvalue="<?= $$vValue; ?>"><?= $$vValue; ?></textarea>
                                                            <div class="text-danger" id="<?= $vValue . '_error'; ?>" style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <div class="modal-footer" style="margin-top: 0">
                                                <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                    <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                                    </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                <div class="nimot-class-but" style="margin-bottom: 0">
                                                    <button type="button" class="save" style="margin-left: 0 !important" onclick="saveTaxiBidInfoSubTitle('vTaxiBidInfoSubTitle_', 'TaxiBidInfoSubTitle_Modal')"><?= $langage_lbl['LBL_Save']; ?></button>
                                                    <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vTaxiBidInfoSubTitle_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                </div>
                                            </div>
                                            <div style="clear:both;"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Info Title</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control" id="vTaxiBidInfoTitle_<?= $default_lang ?>" name="vTaxiBidInfoTitle_<?= $default_lang ?>"
                                               value="<?= $userEditDataArr['ServicePageTitle_' . $default_lang] ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Info Subtitle</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <textarea class="form-control ckeditor" rows="10" id="vTaxiBidInfoSubTitle_<?= $default_lang ?>" name="vTaxiBidInfoSubTitle_<?= $default_lang ?>"> <?= $userEditDataArr['ServicePageDesc_' . $default_lang] ?></textarea>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="row pb-10">
                                <div class="col-lg-12">
                                    <label>Image</label>
                                </div>
                                <div class="col-lg-12 marginbottom-10">
                                    <?php if(!empty($vInfoImageOld['TaxiBid'])) { ?>
                                    <div class="marginbottom-10">
                                        <img src="<?=$tconfig["tsite_url"].'resizeImg.php?h=100&src=' . $tconfig['tsite_upload_app_home_screen_images'] . 'AppHomeScreen/' . $vInfoImageOld['TaxiBid']; ?>" id="taxibidinfo_img">
                                    </div>
                                    <?php } ?>
                                    <input type="file" class="form-control" name="vImageTaxiBidInfo" id="vImageTaxiBidInfo" onchange="previewImage(this, event);" data-img="taxibidinfo_img">
                                    <input type="hidden" class="form-control" name="vImageOldTaxiBidInfo" id="vImageOldTaxiBidInfo" value="<?= $vInfoImageOld['TaxiBid'] ?>">
                                </div>
                                <div class="col-lg-12">
                                    <strong>Note: Upload only png image size of 512px X 512px. <br> <?= IMAGE_INSTRUCTION_NOTES ?> </strong>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <button type="button" class="btn btn-primary save-section-btn" id="saveTaxiBidInfoSection">Save</button>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($MODULES_OBJ->isEnableAnywhereDeliveryFeature('No', 'Genie')) { ?>
                        <hr />
                        <div class="show-help-section section-title">Delivery Genie / Runner</div>
                        <div class="underline-section-title"></div>
                        <?php if (scount($db_master) > 1) { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Title</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="vGenieTitle_Default"
                                           name="vGenieTitle_Default"
                                           value="<?= $userEditDataArr['vGenieTitle_' . $default_lang]; ?>"
                                           data-originalvalue="<?= $userEditDataArr['vGenieTitle_' . $default_lang]; ?>"
                                           readonly="readonly" required>
                                </div>
                                <div class="col-lg-2">
                                    <button type="button" class="btn btn-info" data-toggle="tooltip"
                                            data-original-title="Edit" onclick="editGenieTitle('Edit')">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>
                            <div class="modal fade" id="GenieTitle_Modal" tabindex="-1" role="dialog" aria-hidden="true"
                                 data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content nimot-class">
                                        <div class="modal-header">
                                            <h4>
                                                <span id="genie_title_modal_action"></span>
                                                Title
                                                <button type="button" class="close" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vGenieTitle_')">x
                                                </button>
                                            </h4>
                                        </div>
                                        <div class="modal-body">
                                            <?php
                                            for ($i = 0; $i < $count_all; $i++) {
                                                $vCode = $db_master[$i]['vCode'];
                                                $vTitle = $db_master[$i]['vTitle'];
                                                $eDefault = $db_master[$i]['eDefault'];
                                                $vValue = 'vGenieTitle_' . $vCode;
                                                $$vValue = $userEditDataArr[$vValue];
                                                $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                ?>
                                                <?php
                                                $page_title_class = 'col-lg-12';
                                                if (scount($db_master) > 1) {
                                                    if ($EN_available) {
                                                        if ($vCode == "EN") {
                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                        }
                                                    } else {
                                                        if ($vCode == $default_lang) {
                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                        }
                                                    }
                                                }
                                                ?>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <label>Title (<?= $vTitle; ?>
                                                            ) <?php echo $required_msg; ?></label>
                                                    </div>
                                                    <div class="<?= $page_title_class ?>">
                                                        <input type="text" class="form-control" name="<?= $vValue; ?>"
                                                               id="<?= $vValue; ?>" value="<?= $$vValue; ?>"
                                                               data-originalvalue="<?= $$vValue; ?>"
                                                               placeholder="<?= $vTitle; ?> Value">
                                                        <div class="text-danger" id="<?= $vValue . '_error'; ?>"
                                                             style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                    </div>
                                                    <?php
                                                    if (scount($db_master) > 1) {
                                                        if ($EN_available) {
                                                            if ($vCode == "EN") { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vGenieTitle_', 'EN');">
                                                                        Convert To All Language
                                                                    </button>
                                                                </div>
                                                            <?php }
                                                        } else {
                                                            if ($vCode == $default_lang) { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vGenieTitle_', '<?= $default_lang ?>');">
                                                                        Convert To All Language
                                                                    </button>
                                                                </div>
                                                            <?php }
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <div class="modal-footer" style="margin-top: 0">
                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                                </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                <button type="button" class="save" style="margin-left: 0 !important"
                                                        onclick="saveGenieTitle()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vGenieTitle_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                            </div>
                                        </div>
                                        <div style="clear:both;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Subtitle</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="vGenieSubTitle_Default"
                                           name="vGenieSubTitle_Default"
                                           value="<?= $userEditDataArr['vGenieSubTitle_' . $default_lang]; ?>"
                                           data-originalvalue="<?= $userEditDataArr['vGenieSubTitle_' . $default_lang]; ?>"
                                           readonly="readonly" required>
                                </div>
                                <div class="col-lg-2">
                                    <button type="button" class="btn btn-info" data-toggle="tooltip"
                                            data-original-title="Edit" onclick="editGenieSubTitle('Edit')">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>
                            <div class="modal fade" id="GenieSubTitle_Modal" tabindex="-1" role="dialog"
                                 aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content nimot-class">
                                        <div class="modal-header">
                                            <h4>
                                                <span id="genie_subtitle_modal_action"></span>
                                                Title
                                                <button type="button" class="close" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vGenieSubTitle_')">x
                                                </button>
                                            </h4>
                                        </div>
                                        <div class="modal-body">
                                            <?php
                                            for ($i = 0; $i < $count_all; $i++) {
                                                $vCode = $db_master[$i]['vCode'];
                                                $vTitle = $db_master[$i]['vTitle'];
                                                $eDefault = $db_master[$i]['eDefault'];
                                                $vValue = 'vGenieSubTitle_' . $vCode;
                                                $$vValue = $userEditDataArr[$vValue];
                                                $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                ?>
                                                <?php
                                                $page_title_class = 'col-lg-12';
                                                if (scount($db_master) > 1) {
                                                    if ($EN_available) {
                                                        if ($vCode == "EN") {
                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                        }
                                                    } else {
                                                        if ($vCode == $default_lang) {
                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                        }
                                                    }
                                                }
                                                ?>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <label>Subtitle (<?= $vTitle; ?>
                                                            ) <?php echo $required_msg; ?></label>
                                                    </div>
                                                    <div class="<?= $page_title_class ?>">
                                                        <input type="text" class="form-control" name="<?= $vValue; ?>"
                                                               id="<?= $vValue; ?>" value="<?= $$vValue; ?>"
                                                               data-originalvalue="<?= $$vValue; ?>"
                                                               placeholder="<?= $vTitle; ?> Value">
                                                        <div class="text-danger" id="<?= $vValue . '_error'; ?>"
                                                             style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                    </div>
                                                    <?php
                                                    if (scount($db_master) > 1) {
                                                        if ($EN_available) {
                                                            if ($vCode == "EN") { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vGenieSubTitle_', 'EN');">
                                                                        Convert To All Language
                                                                    </button>
                                                                </div>
                                                            <?php }
                                                        } else {
                                                            if ($vCode == $default_lang) { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vGenieSubTitle_', '<?= $default_lang ?>');">
                                                                        Convert To All Language
                                                                    </button>
                                                                </div>
                                                            <?php }
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <div class="modal-footer" style="margin-top: 0">
                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                                </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                <button type="button" class="save" style="margin-left: 0 !important"
                                                        onclick="saveGenieSubTitle()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vGenieSubTitle_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                            </div>
                                        </div>
                                        <div style="clear:both;"></div>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Title</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="vGenieTitle_<?= $default_lang ?>"
                                           name="vGenieTitle_<?= $default_lang ?>"
                                           value="<?= $userEditDataArr['vGenieTitle_' . $default_lang]; ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Subtitle</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="vGenieSubTitle_<?= $default_lang ?>"
                                           name="vGenieSubTitle_<?= $default_lang ?>"
                                           value="<?= $userEditDataArr['vGenieSubTitle_' . $default_lang]; ?>">
                                </div>
                            </div>
                        <?php } ?>

                        <div class="row">
                            <div class="col-lg-12">
                                <label>Title Color</label>
                            </div>
                            <div class="col-md-1 col-sm-1">
                                <input type="color" data-id="vTitleColorGenie" class="form-control txt-color" value="<?= $vTitleColorGenie ?>"/>
                                <input type="hidden" name="vTitleColorGenie" id="vTitleColorGenie" value="<?= $vTitleColorGenie ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <label>Subtitle Color</label>
                            </div>
                            <div class="col-md-1 col-sm-1">
                                <input type="color" data-id="vSubTitleColorGenie" class="form-control txt-color" value="<?= $vSubTitleColorGenie ?>"/>
                                <input type="hidden" name="vSubTitleColorGenie" id="vSubTitleColorGenie" value="<?= $vSubTitleColorGenie ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <label>Background Color</label>
                            </div>
                            <div class="col-md-1 col-sm-1">
                                <input type="color" data-id="vBgColorGenie" class="form-control bg-color" value="<?= $vBgColorGenie ?>"/>
                                <input type="hidden" name="vBgColorGenie" id="vBgColorGenie" value="<?= $vBgColorGenie ?>">
                            </div>
                        </div>

                        <div class="row pb-10">
                            <div class="col-lg-12">
                                <label>Image</label>
                            </div>
                            <div class="col-md-4 col-sm-4 marginbottom-10">
                                <?php if(!empty($vImageOldGenie)) { ?>
                                <div class="marginbottom-10">
                                    <img src="<?=$tconfig["tsite_url"].'resizeImg.php?h=100&src=' . $tconfig['tsite_upload_app_home_screen_images'] . 'AppHomeScreen/' . $vImageOldGenie; ?>" id="genie_img">
                                </div>
                                <?php } ?>
                                <input type="file" class="form-control" name="vImageGenie" id="vImageGenie" onchange="previewImage(this, event);" data-img="genie_img">
                                <input type="hidden" class="form-control" name="vImageOldGenie" id="vImageOldGenie" value="<?= $vImageOldGenie ?>">
                                <strong class="img-note">Note: Upload only png image size of 512px X 750px. <br> <?= IMAGE_INSTRUCTION_NOTES ?> </strong>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <button type="button" class="btn btn-primary save-section-btn" id="saveGenieSection">Save</button>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($MODULES_OBJ->isEnableVideoConsultingService()) { ?>
                        <hr />
                        <div class="show-help-section section-title">Video Consultation</div>
                        <div class="underline-section-title"></div>
                        <?php if (scount($db_master) > 1) { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Title</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="vVideoConsultTitle_Default"
                                           name="vVideoConsultTitle_Default"
                                           value="<?= $userEditDataArr['vVideoConsultTitle_' . $default_lang]; ?>"
                                           data-originalvalue="<?= $userEditDataArr['vVideoConsultTitle_' . $default_lang]; ?>"
                                           readonly="readonly" required>
                                </div>
                                <div class="col-lg-2">
                                    <button type="button" class="btn btn-info" data-toggle="tooltip"
                                            data-original-title="Edit" onclick="editVideoConsultTitle('Edit')">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>
                            <div class="modal fade" id="VideoConsultTitle_Modal" tabindex="-1" role="dialog" aria-hidden="true"
                                 data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content nimot-class">
                                        <div class="modal-header">
                                            <h4>
                                                <span id="videoconsult_title_modal_action"></span>
                                                Title
                                                <button type="button" class="close" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vVideoConsultTitle_')">x
                                                </button>
                                            </h4>
                                        </div>
                                        <div class="modal-body">
                                            <?php
                                            for ($i = 0; $i < $count_all; $i++) {
                                                $vCode = $db_master[$i]['vCode'];
                                                $vTitle = $db_master[$i]['vTitle'];
                                                $eDefault = $db_master[$i]['eDefault'];
                                                $vValue = 'vVideoConsultTitle_' . $vCode;
                                                $$vValue = $userEditDataArr[$vValue];
                                                $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                ?>
                                                <?php
                                                $page_title_class = 'col-lg-12';
                                                if (scount($db_master) > 1) {
                                                    if ($EN_available) {
                                                        if ($vCode == "EN") {
                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                        }
                                                    } else {
                                                        if ($vCode == $default_lang) {
                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                        }
                                                    }
                                                }
                                                ?>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <label>Title (<?= $vTitle; ?>
                                                            ) <?php echo $required_msg; ?></label>
                                                    </div>
                                                    <div class="<?= $page_title_class ?>">
                                                        <input type="text" class="form-control" name="<?= $vValue; ?>"
                                                               id="<?= $vValue; ?>" value="<?= $$vValue; ?>"
                                                               data-originalvalue="<?= $$vValue; ?>"
                                                               placeholder="<?= $vTitle; ?> Value">
                                                        <div class="text-danger" id="<?= $vValue . '_error'; ?>"
                                                             style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                    </div>
                                                    <?php
                                                    if (scount($db_master) > 1) {
                                                        if ($EN_available) {
                                                            if ($vCode == "EN") { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vVideoConsultTitle_', 'EN');">
                                                                        Convert To All Language
                                                                    </button>
                                                                </div>
                                                            <?php }
                                                        } else {
                                                            if ($vCode == $default_lang) { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vVideoConsultTitle_', '<?= $default_lang ?>');">
                                                                        Convert To All Language
                                                                    </button>
                                                                </div>
                                                            <?php }
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <div class="modal-footer" style="margin-top: 0">
                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                                </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                <button type="button" class="save" style="margin-left: 0 !important"
                                                        onclick="saveVideoConsultTitle()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vVideoConsultTitle_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                            </div>
                                        </div>
                                        <div style="clear:both;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Subtitle</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="vVideoConsultSubTitle_Default"
                                           name="vVideoConsultSubTitle_Default"
                                           value="<?= $userEditDataArr['vVideoConsultSubTitle_' . $default_lang]; ?>"
                                           data-originalvalue="<?= $userEditDataArr['vVideoConsultSubTitle_' . $default_lang]; ?>"
                                           readonly="readonly" required>
                                </div>
                                <div class="col-lg-2">
                                    <button type="button" class="btn btn-info" data-toggle="tooltip"
                                            data-original-title="Edit" onclick="editVideoConsultSubTitle('Edit')">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>
                            <div class="modal fade" id="VideoConsultSubTitle_Modal" tabindex="-1" role="dialog"
                                 aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content nimot-class">
                                        <div class="modal-header">
                                            <h4>
                                                <span id="videoconsult_subtitle_modal_action"></span>
                                                Title
                                                <button type="button" class="close" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vVideoConsultSubTitle_')">x
                                                </button>
                                            </h4>
                                        </div>
                                        <div class="modal-body">
                                            <?php
                                            for ($i = 0; $i < $count_all; $i++) {
                                                $vCode = $db_master[$i]['vCode'];
                                                $vTitle = $db_master[$i]['vTitle'];
                                                $eDefault = $db_master[$i]['eDefault'];
                                                $vValue = 'vVideoConsultSubTitle_' . $vCode;
                                                $$vValue = $userEditDataArr[$vValue];
                                                $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                ?>
                                                <?php
                                                $page_title_class = 'col-lg-12';
                                                if (scount($db_master) > 1) {
                                                    if ($EN_available) {
                                                        if ($vCode == "EN") {
                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                        }
                                                    } else {
                                                        if ($vCode == $default_lang) {
                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                        }
                                                    }
                                                }
                                                ?>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <label>Subtitle (<?= $vTitle; ?>
                                                            ) <?php echo $required_msg; ?></label>
                                                    </div>
                                                    <div class="<?= $page_title_class ?>">
                                                        <input type="text" class="form-control" name="<?= $vValue; ?>"
                                                               id="<?= $vValue; ?>" value="<?= $$vValue; ?>"
                                                               data-originalvalue="<?= $$vValue; ?>"
                                                               placeholder="<?= $vTitle; ?> Value">
                                                        <div class="text-danger" id="<?= $vValue . '_error'; ?>"
                                                             style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                    </div>
                                                    <?php
                                                    if (scount($db_master) > 1) {
                                                        if ($EN_available) {
                                                            if ($vCode == "EN") { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vVideoConsultSubTitle_', 'EN');">
                                                                        Convert To All Language
                                                                    </button>
                                                                </div>
                                                            <?php }
                                                        } else {
                                                            if ($vCode == $default_lang) { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vVideoConsultSubTitle_', '<?= $default_lang ?>');">
                                                                        Convert To All Language
                                                                    </button>
                                                                </div>
                                                            <?php }
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <div class="modal-footer" style="margin-top: 0">
                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                                </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                <button type="button" class="save" style="margin-left: 0 !important"
                                                        onclick="saveVideoConsultSubTitle()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vVideoConsultSubTitle_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                            </div>
                                        </div>
                                        <div style="clear:both;"></div>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Title</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="vVideoConsultTitle_<?= $default_lang ?>"
                                           name="vVideoConsultTitle_<?= $default_lang ?>"
                                           value="<?= $userEditDataArr['vVideoConsultTitle_' . $default_lang]; ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Subtitle</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="vVideoConsultSubTitle_<?= $default_lang ?>"
                                           name="vVideoConsultSubTitle_<?= $default_lang ?>"
                                           value="<?= $userEditDataArr['vVideoConsultSubTitle_' . $default_lang]; ?>">
                                </div>
                            </div>
                        <?php } ?>

                        <div class="row">
                            <div class="col-lg-12">
                                <label>Title Color</label>
                            </div>
                            <div class="col-md-1 col-sm-1">
                                <input type="color" data-id="vTitleColorVideoConsult" class="form-control txt-color" value="<?= $vTitleColorVideoConsult ?>"/>
                                <input type="hidden" name="vTitleColorVideoConsult" id="vTitleColorVideoConsult" value="<?= $vTitleColorVideoConsult ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <label>Subtitle Color</label>
                            </div>
                            <div class="col-md-1 col-sm-1">
                                <input type="color" data-id="vSubTitleColorVideoConsult" class="form-control txt-color" value="<?= $vSubTitleColorVideoConsult ?>"/>
                                <input type="hidden" name="vSubTitleColorVideoConsult" id="vSubTitleColorVideoConsult" value="<?= $vSubTitleColorVideoConsult ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <label>Background Color</label>
                            </div>
                            <div class="col-md-1 col-sm-1">
                                <input type="color" data-id="vBgColorVideoConsult" class="form-control bg-color" value="<?= $vBgColorVideoConsult ?>"/>
                                <input type="hidden" name="vBgColorVideoConsult" id="vBgColorVideoConsult" value="<?= $vBgColorVideoConsult ?>">
                            </div>
                        </div>

                        <div class="row pb-10">
                            <div class="col-lg-12">
                                <label>Image</label>
                            </div>
                            <div class="col-md-4 col-sm-4 marginbottom-10">
                                <?php if(!empty($vImageOldVideoConsult)) { ?>
                                <div class="marginbottom-10">
                                    <img src="<?=$tconfig["tsite_url"].'resizeImg.php?h=100&src=' . $tconfig['tsite_upload_app_home_screen_images'] . 'AppHomeScreen/' . $vImageOldVideoConsult; ?>" id="videoconsult_img">
                                </div>
                                <?php } ?>
                                <input type="file" class="form-control" name="vImageVideoConsult" id="vImageVideoConsult" onchange="previewImage(this, event);" data-img="videoconsult_img">
                                <input type="hidden" class="form-control" name="vImageOldVideoConsult" id="vImageOldVideoConsult" value="<?= $vImageOldVideoConsult ?>">
                                <strong class="img-note">Note: Upload only png image size of 700px X 800px. <br> <?= IMAGE_INSTRUCTION_NOTES ?> </strong>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <button type="button" class="btn btn-primary save-section-btn" id="saveVideoConsultSection">Save</button>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($MODULES_OBJ->isEnableBiddingServices()) { ?>
                        <hr />
                        <div class="show-help-section section-title">Service Bid</div>
                        <div class="underline-section-title"></div>
                        <?php if (scount($db_master) > 1) { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Title</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="vBiddingTitle_Default"
                                           name="vBiddingTitle_Default"
                                           value="<?= $userEditDataArr['vBiddingTitle_' . $default_lang]; ?>"
                                           data-originalvalue="<?= $userEditDataArr['vBiddingTitle_' . $default_lang]; ?>"
                                           readonly="readonly" required>
                                </div>
                                <div class="col-lg-2">
                                    <button type="button" class="btn btn-info" data-toggle="tooltip"
                                            data-original-title="Edit" onclick="editBiddingTitle('Edit')">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>
                            <div class="modal fade" id="BiddingTitle_Modal" tabindex="-1" role="dialog" aria-hidden="true"
                                 data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content nimot-class">
                                        <div class="modal-header">
                                            <h4>
                                                <span id="bidding_title_modal_action"></span>
                                                Title
                                                <button type="button" class="close" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vBiddingTitle_')">x
                                                </button>
                                            </h4>
                                        </div>
                                        <div class="modal-body">
                                            <?php
                                            for ($i = 0; $i < $count_all; $i++) {
                                                $vCode = $db_master[$i]['vCode'];
                                                $vTitle = $db_master[$i]['vTitle'];
                                                $eDefault = $db_master[$i]['eDefault'];
                                                $vValue = 'vBiddingTitle_' . $vCode;
                                                $$vValue = $userEditDataArr[$vValue];
                                                $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                ?>
                                                <?php
                                                $page_title_class = 'col-lg-12';
                                                if (scount($db_master) > 1) {
                                                    if ($EN_available) {
                                                        if ($vCode == "EN") {
                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                        }
                                                    } else {
                                                        if ($vCode == $default_lang) {
                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                        }
                                                    }
                                                }
                                                ?>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <label>Title (<?= $vTitle; ?>
                                                            ) <?php echo $required_msg; ?></label>
                                                    </div>
                                                    <div class="<?= $page_title_class ?>">
                                                        <input type="text" class="form-control" name="<?= $vValue; ?>"
                                                               id="<?= $vValue; ?>" value="<?= $$vValue; ?>"
                                                               data-originalvalue="<?= $$vValue; ?>"
                                                               placeholder="<?= $vTitle; ?> Value">
                                                        <div class="text-danger" id="<?= $vValue . '_error'; ?>"
                                                             style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                    </div>
                                                    <?php
                                                    if (scount($db_master) > 1) {
                                                        if ($EN_available) {
                                                            if ($vCode == "EN") { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vBiddingTitle_', 'EN');">
                                                                        Convert To All Language
                                                                    </button>
                                                                </div>
                                                            <?php }
                                                        } else {
                                                            if ($vCode == $default_lang) { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vBiddingTitle_', '<?= $default_lang ?>');">
                                                                        Convert To All Language
                                                                    </button>
                                                                </div>
                                                            <?php }
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <div class="modal-footer" style="margin-top: 0">
                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                                </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                <button type="button" class="save" style="margin-left: 0 !important"
                                                        onclick="saveBiddingTitle()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vBiddingTitle_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                            </div>
                                        </div>
                                        <div style="clear:both;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Subtitle</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="vBiddingSubTitle_Default"
                                           name="vBiddingSubTitle_Default"
                                           value="<?= $userEditDataArr['vBiddingSubTitle_' . $default_lang]; ?>"
                                           data-originalvalue="<?= $userEditDataArr['vBiddingSubTitle_' . $default_lang]; ?>"
                                           readonly="readonly" required>
                                </div>
                                <div class="col-lg-2">
                                    <button type="button" class="btn btn-info" data-toggle="tooltip"
                                            data-original-title="Edit" onclick="editBiddingSubTitle('Edit')">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>
                            <div class="modal fade" id="BiddingSubTitle_Modal" tabindex="-1" role="dialog"
                                 aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content nimot-class">
                                        <div class="modal-header">
                                            <h4>
                                                <span id="bidding_subtitle_modal_action"></span>
                                                Title
                                                <button type="button" class="close" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vBiddingSubTitle_')">x
                                                </button>
                                            </h4>
                                        </div>
                                        <div class="modal-body">
                                            <?php
                                            for ($i = 0; $i < $count_all; $i++) {
                                                $vCode = $db_master[$i]['vCode'];
                                                $vTitle = $db_master[$i]['vTitle'];
                                                $eDefault = $db_master[$i]['eDefault'];
                                                $vValue = 'vBiddingSubTitle_' . $vCode;
                                                $$vValue = $userEditDataArr[$vValue];
                                                $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                ?>
                                                <?php
                                                $page_title_class = 'col-lg-12';
                                                if (scount($db_master) > 1) {
                                                    if ($EN_available) {
                                                        if ($vCode == "EN") {
                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                        }
                                                    } else {
                                                        if ($vCode == $default_lang) {
                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                        }
                                                    }
                                                }
                                                ?>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <label>Subtitle (<?= $vTitle; ?>
                                                            ) <?php echo $required_msg; ?></label>
                                                    </div>
                                                    <div class="<?= $page_title_class ?>">
                                                        <input type="text" class="form-control" name="<?= $vValue; ?>"
                                                               id="<?= $vValue; ?>" value="<?= $$vValue; ?>"
                                                               data-originalvalue="<?= $$vValue; ?>"
                                                               placeholder="<?= $vTitle; ?> Value">
                                                        <div class="text-danger" id="<?= $vValue . '_error'; ?>"
                                                             style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                    </div>
                                                    <?php
                                                    if (scount($db_master) > 1) {
                                                        if ($EN_available) {
                                                            if ($vCode == "EN") { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vBiddingSubTitle_', 'EN');">
                                                                        Convert To All Language
                                                                    </button>
                                                                </div>
                                                            <?php }
                                                        } else {
                                                            if ($vCode == $default_lang) { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vBiddingSubTitle_', '<?= $default_lang ?>');">
                                                                        Convert To All Language
                                                                    </button>
                                                                </div>
                                                            <?php }
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <div class="modal-footer" style="margin-top: 0">
                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                                </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                <button type="button" class="save" style="margin-left: 0 !important"
                                                        onclick="saveBiddingSubTitle()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vBiddingSubTitle_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                            </div>
                                        </div>
                                        <div style="clear:both;"></div>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Title</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="vBiddingTitle_<?= $default_lang ?>"
                                           name="vBiddingTitle_<?= $default_lang ?>"
                                           value="<?= $userEditDataArr['vBiddingTitle_' . $default_lang]; ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Subtitle</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="vBiddingSubTitle_<?= $default_lang ?>"
                                           name="vBiddingSubTitle_<?= $default_lang ?>"
                                           value="<?= $userEditDataArr['vBiddingSubTitle_' . $default_lang]; ?>">
                                </div>
                            </div>
                        <?php } ?>

                        <div class="row">
                            <div class="col-lg-12">
                                <label>Title Color</label>
                            </div>
                            <div class="col-md-1 col-sm-1">
                                <input type="color" data-id="vTitleColorBidding" class="form-control txt-color" value="<?= $vTitleColorBidding ?>"/>
                                <input type="hidden" name="vTitleColorBidding" id="vTitleColorBidding" value="<?= $vTitleColorBidding ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <label>Subtitle Color</label>
                            </div>
                            <div class="col-md-1 col-sm-1">
                                <input type="color" data-id="vSubTitleColorBidding" class="form-control txt-color" value="<?= $vSubTitleColorBidding ?>"/>
                                <input type="hidden" name="vSubTitleColorBidding" id="vSubTitleColorBidding" value="<?= $vSubTitleColorBidding ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <label>Background Color</label>
                            </div>
                            <div class="col-md-1 col-sm-1">
                                <input type="color" data-id="vBgColorBidding" class="form-control bg-color" value="<?= $vBgColorBidding ?>"/>
                                <input type="hidden" name="vBgColorBidding" id="vBgColorBidding" value="<?= $vBgColorBidding ?>">
                            </div>
                        </div>

                        <div class="row pb-10">
                            <div class="col-lg-12">
                                <label>Image</label>
                            </div>
                            <div class="col-md-4 col-sm-4 marginbottom-10">
                                <?php if(!empty($vImageOldBidding)) { ?>
                                <div class="marginbottom-10">
                                    <img src="<?=$tconfig["tsite_url"].'resizeImg.php?h=100&src=' . $tconfig['tsite_upload_app_home_screen_images'] . 'AppHomeScreen/' . $vImageOldBidding; ?>" id="bidding_img">
                                </div>
                                <?php } ?>
                                <input type="file" class="form-control" name="vImageBidding" id="vImageBidding" onchange="previewImage(this, event);" data-img="bidding_img">
                                <input type="hidden" class="form-control" name="vImageOldBidding" id="vImageOldBidding" value="<?= $vImageOldBidding ?>">
                                <strong class="img-note">Note: Upload only png image size of 740px X 993px. <br> <?= IMAGE_INSTRUCTION_NOTES ?> </strong>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <button type="button" class="btn btn-primary save-section-btn" id="saveBiddingSection">Save</button>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($MODULES_OBJ->isUberXFeatureAvailable()) { ?>
                        <hr />
                        <div class="show-help-section section-title">On-Demand Services</div>
                        <div class="underline-section-title"></div>
                        <?php if (scount($db_master) > 1) { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Title</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="vOnDemandServiceTitle_Default"
                                           name="vOnDemandServiceTitle_Default"
                                           value="<?= $userEditDataArr['vOnDemandServiceTitle_' . $default_lang]; ?>"
                                           data-originalvalue="<?= $userEditDataArr['vOnDemandServiceTitle_' . $default_lang]; ?>"
                                           readonly="readonly" required>
                                </div>
                                <div class="col-lg-2">
                                    <button type="button" class="btn btn-info" data-toggle="tooltip"
                                            data-original-title="Edit" onclick="editOnDemandServiceTitle('Edit')">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>
                            <div class="modal fade" id="OnDemandServiceTitle_Modal" tabindex="-1" role="dialog" aria-hidden="true"
                                 data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content nimot-class">
                                        <div class="modal-header">
                                            <h4>
                                                <span id="ondemandservice_title_modal_action"></span>
                                                Title
                                                <button type="button" class="close" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vOnDemandServiceTitle_')">x
                                                </button>
                                            </h4>
                                        </div>
                                        <div class="modal-body">
                                            <?php
                                            for ($i = 0; $i < $count_all; $i++) {
                                                $vCode = $db_master[$i]['vCode'];
                                                $vTitle = $db_master[$i]['vTitle'];
                                                $eDefault = $db_master[$i]['eDefault'];
                                                $vValue = 'vOnDemandServiceTitle_' . $vCode;
                                                $$vValue = $userEditDataArr[$vValue];
                                                $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                ?>
                                                <?php
                                                $page_title_class = 'col-lg-12';
                                                if (scount($db_master) > 1) {
                                                    if ($EN_available) {
                                                        if ($vCode == "EN") {
                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                        }
                                                    } else {
                                                        if ($vCode == $default_lang) {
                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                        }
                                                    }
                                                }
                                                ?>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <label>Title (<?= $vTitle; ?>
                                                            ) <?php echo $required_msg; ?></label>
                                                    </div>
                                                    <div class="<?= $page_title_class ?>">
                                                        <input type="text" class="form-control" name="<?= $vValue; ?>"
                                                               id="<?= $vValue; ?>" value="<?= $$vValue; ?>"
                                                               data-originalvalue="<?= $$vValue; ?>"
                                                               placeholder="<?= $vTitle; ?> Value">
                                                        <div class="text-danger" id="<?= $vValue . '_error'; ?>"
                                                             style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                    </div>
                                                    <?php
                                                    if (scount($db_master) > 1) {
                                                        if ($EN_available) {
                                                            if ($vCode == "EN") { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vOnDemandServiceTitle_', 'EN');">
                                                                        Convert To All Language
                                                                    </button>
                                                                </div>
                                                            <?php }
                                                        } else {
                                                            if ($vCode == $default_lang) { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vOnDemandServiceTitle_', '<?= $default_lang ?>');">
                                                                        Convert To All Language
                                                                    </button>
                                                                </div>
                                                            <?php }
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <div class="modal-footer" style="margin-top: 0">
                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                                </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                <button type="button" class="save" style="margin-left: 0 !important"
                                                        onclick="saveOnDemandServiceTitle()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vOnDemandServiceTitle_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                            </div>
                                        </div>
                                        <div style="clear:both;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Subtitle</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="vOnDemandServiceSubTitle_Default"
                                           name="vOnDemandServiceSubTitle_Default"
                                           value="<?= $userEditDataArr['vOnDemandServiceSubTitle_' . $default_lang]; ?>"
                                           data-originalvalue="<?= $userEditDataArr['vOnDemandServiceSubTitle_' . $default_lang]; ?>"
                                           readonly="readonly" required>
                                </div>
                                <div class="col-lg-2">
                                    <button type="button" class="btn btn-info" data-toggle="tooltip"
                                            data-original-title="Edit" onclick="editOnDemandServiceSubTitle('Edit')">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>
                            <div class="modal fade" id="OnDemandServiceSubTitle_Modal" tabindex="-1" role="dialog"
                                 aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content nimot-class">
                                        <div class="modal-header">
                                            <h4>
                                                <span id="ondemandservice_subtitle_modal_action"></span>
                                                Title
                                                <button type="button" class="close" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vOnDemandServiceSubTitle_')">x
                                                </button>
                                            </h4>
                                        </div>
                                        <div class="modal-body">
                                            <?php
                                            for ($i = 0; $i < $count_all; $i++) {
                                                $vCode = $db_master[$i]['vCode'];
                                                $vTitle = $db_master[$i]['vTitle'];
                                                $eDefault = $db_master[$i]['eDefault'];
                                                $vValue = 'vOnDemandServiceSubTitle_' . $vCode;
                                                $$vValue = $userEditDataArr[$vValue];
                                                $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                ?>
                                                <?php
                                                $page_title_class = 'col-lg-12';
                                                if (scount($db_master) > 1) {
                                                    if ($EN_available) {
                                                        if ($vCode == "EN") {
                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                        }
                                                    } else {
                                                        if ($vCode == $default_lang) {
                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                        }
                                                    }
                                                }
                                                ?>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <label>Subtitle (<?= $vTitle; ?>
                                                            ) <?php echo $required_msg; ?></label>
                                                    </div>
                                                    <div class="<?= $page_title_class ?>">
                                                        <input type="text" class="form-control" name="<?= $vValue; ?>"
                                                               id="<?= $vValue; ?>" value="<?= $$vValue; ?>"
                                                               data-originalvalue="<?= $$vValue; ?>"
                                                               placeholder="<?= $vTitle; ?> Value">
                                                        <div class="text-danger" id="<?= $vValue . '_error'; ?>"
                                                             style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                    </div>
                                                    <?php
                                                    if (scount($db_master) > 1) {
                                                        if ($EN_available) {
                                                            if ($vCode == "EN") { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vOnDemandServiceSubTitle_', 'EN');">
                                                                        Convert To All Language
                                                                    </button>
                                                                </div>
                                                            <?php }
                                                        } else {
                                                            if ($vCode == $default_lang) { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vOnDemandServiceSubTitle_', '<?= $default_lang ?>');">
                                                                        Convert To All Language
                                                                    </button>
                                                                </div>
                                                            <?php }
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <div class="modal-footer" style="margin-top: 0">
                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                                </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                <button type="button" class="save" style="margin-left: 0 !important"
                                                        onclick="saveOnDemandServiceSubTitle()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vOnDemandServiceSubTitle_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                            </div>
                                        </div>
                                        <div style="clear:both;"></div>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Title</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="vOnDemandServiceTitle_<?= $default_lang ?>"
                                           name="vOnDemandServiceTitle_<?= $default_lang ?>"
                                           value="<?= $userEditDataArr['vOnDemandServiceTitle_' . $default_lang]; ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Subtitle</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="vOnDemandServiceSubTitle_<?= $default_lang ?>" name="vOnDemandServiceSubTitle_<?= $default_lang ?>" value="<?= $userEditDataArr['vOnDemandServiceSubTitle_' . $default_lang]; ?>">
                                </div>
                            </div>
                        <?php } ?>

                        <div class="row">
                            <div class="col-lg-12">
                                <label>Services</label>
                            </div>
                            <div class="col-lg-6">
                                <div class="manage-banner-section">
                                    <div class="service-img-block">
                                    <?php foreach ($ufxData as $ufxService) {
                                        if (isset($tServiceDetailsArr['iVehicleCategoryId_' . $ufxService['iVehicleCategoryId']])) {
                                            $tServiceDetails = $tServiceDetailsArr['iVehicleCategoryId_' . $ufxService['iVehicleCategoryId']];
                                            if (!empty($tServiceDetails['vImage'])) {
                                                $vServiceImg = $tconfig['tsite_url'] . 'resizeImg.php?w=60&src=' . $tconfig["tsite_upload_app_home_screen_images"] . 'AppHomeScreen/' . $tServiceDetails['vImage'];
                                            }
                                            $vServiceImgOld = $tServiceDetails['vImage'];
                                            if ($tServiceDetails['eStatus'] == "Active") { 
                                        
                                    ?>
                                        <div class="service-preview-img">
                                            <img src="<?= $vServiceImg ?>">
                                            <div class="service-img-title"><?= $ufxService['vCategoryName'] ?></div>
                                        </div>
                                    
                                    <?php }} } ?>
                                    <div class="service-preview-img">
                                        <img src="<?= $tconfig['tsite_url'] . 'resizeImg.php?w=60&src=' . $tconfig["tsite_url"] . "webimages/icons/DefaultImg/ic_more_other_services.png" ?>">
                                        <div class="service-img-title">50+ More Services</div>
                                    </div>
                                    </div>
                                    <button type="button" class="manage-banner-btn manage-icon-btn" data-toggle="modal" data-target="#ondemanservices_modal">Manage Services for App Home Screen</button>
                                </div>
                            </div>                            
                        </div>

                        <div class="modal fade" id="ondemanservices_modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content nimot-class">
                                    <div class="modal-header">
                                        <h4>
                                            On-Demand Services
                                            <button type="button" class="close" data-dismiss="modal">x</button>
                                        </h4>
                                    </div>
                                    <div class="modal-body">
                                        <p>
                                            <strong>Note:</strong>
                                            Enable any 3 service categories from below list to be shown on App
                                            home screen. All other service categories will be shown under more.
                                            <br>
                                            Icons uploaded will only be shown on App home screen and not under
                                            more section.
                                            <br><br>
                                            <strong>Upload only png image size of 512px X 512px. <br> <?= IMAGE_INSTRUCTION_NOTES ?></strong>
                                        </p>
                                        <input type="hidden" name="saveOnDemandDisplay" id="saveOnDemandDisplay" value="No">
                                        <table class="table table-striped table-bordered table-hover">
                                            <thead>
                                            <tr>
                                                <th style="text-align: center;">Icon</th>
                                                <th>Service Category</th>
                                                <th>Display Order</th>
                                                <th>Upload Icon</th>
                                                <th>Status</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            foreach ($ufxData as $ufxService) {
                                                $vServiceImg = "";
                                                $vServiceStatus = "";
                                                $vServiceImgOld = "";
                                                $vServiceDisplay = 'style="display: none"';
                                                $vServiceDisplayOrder = "1";
                                                if (isset($tServiceDetailsArr['iVehicleCategoryId_' . $ufxService['iVehicleCategoryId']])) {
                                                    $tServiceDetails = $tServiceDetailsArr['iVehicleCategoryId_' . $ufxService['iVehicleCategoryId']];
                                                    if (!empty($tServiceDetails['vImage'])) {
                                                        $vServiceImg = $tconfig['tsite_url'] . 'resizeImg.php?w=50&src=' . $tconfig["tsite_upload_app_home_screen_images"] . 'AppHomeScreen/' . $tServiceDetails['vImage'];
                                                    }
                                                    $vServiceImgOld = $tServiceDetails['vImage'];
                                                    if ($tServiceDetails['eStatus'] == "Active") {
                                                        $vServiceStatus = "checked";
                                                        $vServiceDisplay = "";
                                                        $vServiceDisplayOrder = $tServiceDetails['iDisplayOrder'];
                                                    }
                                                }
                                                ?>
                                                <tr>
                                                    <td style="text-align: center; vertical-align: middle;">
                                                        <?php if (!empty($vServiceImg)) { ?>
                                                            <img src="<?= $vServiceImg ?>">
                                                        <?php } else { ?>
                                                            --
                                                        <?php } ?>
                                                    </td>
                                                    <td style="vertical-align: middle;"><?= $ufxService['vCategoryName'] ?></td>
                                                    <td>
                                                        <select class="form-control" name="iDisplayOrderOnDemandServiceArr[]" <?= $vServiceDisplay ?>>
                                                            <?php for ($disp_order = 1; $disp_order <= scount($ufxData); $disp_order++) { ?>
                                                                <option value="<?= $disp_order ?>" <?= $vServiceDisplayOrder == $disp_order ? 'selected' : '' ?>><?= $disp_order ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="file" class="form-control" name="vOnDemandServiceImage[]" <?= $vServiceDisplay ?>>
                                                        <input type="hidden" class="form-control" name="vOnDemandServiceImageOld[]" value="<?= $vServiceImgOld ?>">
                                                    </td>
                                                    <td>
                                                        <div class="make-switch" data-on="success" data-off="warning">
                                                            <input type="checkbox" name="iVehicleCategoryId[]" value="<?= $ufxService['iVehicleCategoryId'] ?>" <?= $vServiceStatus ?> />
                                                        </div>
                                                        <input type="hidden" name="iVehicleCategoryIdVal[]" value="<?= $ufxService['iVehicleCategoryId'] ?>">
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="modal-footer" style="text-align: left">
                                        <button type="button" class="btn btn-default"
                                                onclick="saveOnDemandServices('Yes')">Save
                                        </button>
                                        <button type="button" class="btn btn-default"
                                                onclick="saveOnDemandServices('No')">Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-lg-12">
                                <button type="button" class="btn btn-primary save-section-btn" id="saveOnDemandServiceSection">Save</button>
                            </div>
                        </div>
                    <?php } ?>

                    <hr/>
                    <div class="show-help-section section-title">Promotional Banner</div>
                    <div class="underline-section-title"></div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="manage-banner-section promo-banner">
                                <?php if (!empty($promotional_banner_data)) { ?>
                                    <div class="banner-img-block">
                                        <?php foreach ($promotional_banner_data as $app_promot_banner) { ?>
                                            <div class="banner-img">
                                                <img src="<?= $tconfig["tsite_url"] . 'resizeImg.php?w=400&src=' . $tconfig['tsite_upload_images'] . $app_promot_banner['vImage']; ?>">
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                                <a href="<?= $tconfig['tsite_url_main_admin'] ?>app_banner.php?iVehicleCategoryId=<?= $promotionalCategoryId ?>&eFor=Promotion" class="manage-banner-btn" target="_blank">Manage Promotional Banner for App Home Screen</a>
                            </div>
                        </div>
                    </div>

                    <?php if ($MODULES_OBJ->isEnableRentEstateService() || $MODULES_OBJ->isEnableRentCarsService() || $MODULES_OBJ->isEnableRentItemService()) { 
                        $show_rentestate_tab = $show_rentcars_tab = $show_rentitem_tab = "";
                        $show_rentestate_content = $show_rentcars_content = $show_rentitem_content = "";
                        ?>
                        <hr/>
                        <div class="show-help-section section-title">Buy, Sell & Rent</div>
                        <div class="underline-section-title"></div>

                        <div class="row paddingbottom-0">
                            <div class="col-lg-12">
                                <div class="tab">
                                    <?php if ($MODULES_OBJ->isEnableRentEstateService()) { $show_rentestate_tab = "active"; $show_rentestate_content = "display-tab-content"; ?>
                                    <button class="tablinks manage-rentestate-tab <?= $show_rentestate_tab ?>" onclick="openTabContent(event, 'manage-rentestate-content', 'tabcontent-buysellrent')"> Buy, Sell & Rent Real Estate
                                    </button>
                                    <?php } if($MODULES_OBJ->isEnableRentCarsService()) {
                                        if(empty($show_rentestate_tab)) {
                                            $show_rentcars_tab = "active";
                                            $show_rentcars_content = "display-tab-content";
                                        }
                                        ?>
                                    <button class="tablinks manage-rentcars-tab <?= $show_rentcars_tab ?>" onclick="openTabContent(event, 'manage-rentcars-content', 'tabcontent-buysellrent')"> Buy, Sell & Rent Cars
                                    </button>
                                    <?php } if($MODULES_OBJ->isEnableRentItemService()) {
                                        if(empty($show_rentestate_tab) && empty($show_rentcars_tab)) {
                                            $show_rentitem_tab = "active";
                                            $show_rentitem_content = "display-tab-content";
                                        }
                                        ?>
                                    <button class="tablinks manage-rentitem-tab <?= $show_rentitem_tab ?>" onclick="openTabContent(event, 'manage-rentitem-content', 'tabcontent-buysellrent')"> Buy, Sell & Rent General Items
                                    </button>
                                    <?php } ?>
                                </div>

                                <?php if($MODULES_OBJ->isEnableRentEstateService()) { ?>
                                <div class="tabcontent tabcontent-buysellrent <?= $show_rentestate_content ?>" id="manage-rentestate-content">
                                    <div class="col-lg-12">
                                        <?php if (scount($db_master) > 1) { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Title</label>
                                                </div>
                                                <div class="col-md-4 col-sm-4">
                                                    <input type="text" class="form-control" id="vRentEstateTitle_Default"
                                                           name="vRentEstateTitle_Default"
                                                           value="<?= $userEditDataArr['vRentEstateTitle_' . $default_lang]; ?>"
                                                           data-originalvalue="<?= $userEditDataArr['vRentEstateTitle_' . $default_lang]; ?>"
                                                           readonly="readonly" required>
                                                </div>
                                                <div class="col-lg-2">
                                                    <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                            data-original-title="Edit" onclick="editRentEstateTitle('Edit')">
                                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="RentEstateTitle_Modal" tabindex="-1" role="dialog" aria-hidden="true"
                                                 data-backdrop="static" data-keyboard="false">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content nimot-class">
                                                        <div class="modal-header">
                                                            <h4>
                                                                <span id="rentestate_title_modal_action"></span>
                                                                Title
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                        onclick="resetToOriginalValue(this, 'vRentEstateTitle_')">x
                                                                </button>
                                                            </h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <?php
                                                            for ($i = 0; $i < $count_all; $i++) {
                                                                $vCode = $db_master[$i]['vCode'];
                                                                $vTitle = $db_master[$i]['vTitle'];
                                                                $eDefault = $db_master[$i]['eDefault'];
                                                                $vValue = 'vRentEstateTitle_' . $vCode;
                                                                $$vValue = $userEditDataArr[$vValue];
                                                                $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                                ?>
                                                                <?php
                                                                $page_title_class = 'col-lg-12';
                                                                if (scount($db_master) > 1) {
                                                                    if ($EN_available) {
                                                                        if ($vCode == "EN") {
                                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                                        }
                                                                    } else {
                                                                        if ($vCode == $default_lang) {
                                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                                        }
                                                                    }
                                                                }
                                                                ?>
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <label>Title (<?= $vTitle; ?>
                                                                            ) <?php echo $required_msg; ?></label>
                                                                    </div>
                                                                    <div class="<?= $page_title_class ?>">
                                                                        <input type="text" class="form-control" name="<?= $vValue; ?>"
                                                                               id="<?= $vValue; ?>" value="<?= $$vValue; ?>"
                                                                               data-originalvalue="<?= $$vValue; ?>"
                                                                               placeholder="<?= $vTitle; ?> Value">
                                                                        <div class="text-danger" id="<?= $vValue . '_error'; ?>"
                                                                             style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                                    </div>
                                                                    <?php
                                                                    if (scount($db_master) > 1) {
                                                                        if ($EN_available) {
                                                                            if ($vCode == "EN") { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type="button" name="allLanguage"
                                                                                            id="allLanguage" class="btn btn-primary"
                                                                                            onClick="getAllLanguageCode('vRentEstateTitle_', 'EN');">
                                                                                        Convert To All Language
                                                                                    </button>
                                                                                </div>
                                                                            <?php }
                                                                        } else {
                                                                            if ($vCode == $default_lang) { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type="button" name="allLanguage"
                                                                                            id="allLanguage" class="btn btn-primary"
                                                                                            onClick="getAllLanguageCode('vRentEstateTitle_', '<?= $default_lang ?>');">
                                                                                        Convert To All Language
                                                                                    </button>
                                                                                </div>
                                                                            <?php }
                                                                        }
                                                                    }
                                                                    ?>
                                                                </div>
                                                                <?php
                                                            }
                                                            ?>
                                                        </div>
                                                        <div class="modal-footer" style="margin-top: 0">
                                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                                <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                                                </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                                <button type="button" class="save" style="margin-left: 0 !important"
                                                                        onclick="saveRentEstateTitle()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                                        onclick="resetToOriginalValue(this, 'vRentEstateTitle_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                            </div>
                                                        </div>
                                                        <div style="clear:both;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Title</label>
                                                </div>
                                                <div class="col-md-4 col-sm-4">
                                                    <input type="text" class="form-control" id="vRentEstateTitle_<?= $default_lang ?>"
                                                           name="vRentEstateTitle_<?= $default_lang ?>"
                                                           value="<?= $userEditDataArr['vRentEstateTitle_' . $default_lang]; ?>">
                                                </div>
                                            </div>
                                        <?php } ?>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Text Color</label>
                                            </div>
                                            <div class="col-md-1 col-sm-1">
                                                <input type="color" data-id="vTitleColorRentEstate" class="form-control txt-color" value="<?= $vTitleColorRentEstate ?>"/>
                                                <input type="hidden" name="vTitleColorRentEstate" id="vTitleColorRentEstate" value="<?= $vTitleColorRentEstate ?>">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Background Color</label>
                                            </div>
                                            <div class="col-md-1 col-sm-1">
                                                <input type="color" data-id="vBgColorRentEstate" class="form-control bg-color" value="<?= $vBgColorRentEstate ?>"/>
                                                <input type="hidden" name="vBgColorRentEstate" id="vBgColorRentEstate" value="<?= $vBgColorRentEstate ?>">
                                            </div>
                                        </div>

                                        <div class="row pb-10">
                                            <div class="col-lg-12">
                                                <label>Image</label>
                                            </div>
                                            <div class="col-md-4 col-sm-4 marginbottom-10">
                                                <?php if(!empty($vImageOldRentEstate)) { ?>
                                                <div class="marginbottom-10">
                                                    <img src="<?=$tconfig["tsite_url"].'resizeImg.php?h=100&src=' . $tconfig['tsite_upload_app_home_screen_images'] . $vImageOldRentEstate; ?>" id="rentestate_img">
                                                </div>
                                                <?php } ?>
                                                <input type="file" class="form-control" name="vImageRentEstate" id="vImageRentEstate" onchange="previewImage(this, event);" data-img="rentestate_img">
                                                <input type="hidden" class="form-control" name="vImageOldRentEstate" id="vImageOldRentEstate" value="<?= $vImageOldRentEstate ?>">
                                                <strong class="img-note">Note: Upload only png image size of 1024px X 618px. <br> <?= IMAGE_INSTRUCTION_NOTES ?> </strong>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <button type="button" class="btn btn-primary save-section-btn" id="saveRentEstateSection">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>                        
                                <?php } ?>

                                <?php if($MODULES_OBJ->isEnableRentCarsService()) { ?>
                                <div class="tabcontent tabcontent-buysellrent <?= $show_rentcars_content ?>" id="manage-rentcars-content">
                                    <div class="col-lg-12">
                                        <?php if (scount($db_master) > 1) { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Title</label>
                                                </div>
                                                <div class="col-md-4 col-sm-4">
                                                    <input type="text" class="form-control" id="vRentCarsTitle_Default"
                                                           name="vRentCarsTitle_Default"
                                                           value="<?= $userEditDataArr['vRentCarsTitle_' . $default_lang]; ?>"
                                                           data-originalvalue="<?= $userEditDataArr['vRentCarsTitle_' . $default_lang]; ?>"
                                                           readonly="readonly" required>
                                                </div>
                                                <div class="col-lg-2">
                                                    <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                            data-original-title="Edit" onclick="editRentCarsTitle('Edit')">
                                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="RentCarsTitle_Modal" tabindex="-1" role="dialog" aria-hidden="true"
                                                 data-backdrop="static" data-keyboard="false">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content nimot-class">
                                                        <div class="modal-header">
                                                            <h4>
                                                                <span id="rentcars_title_modal_action"></span>
                                                                Title
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                        onclick="resetToOriginalValue(this, 'vRentCarsTitle_')">x
                                                                </button>
                                                            </h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <?php
                                                            for ($i = 0; $i < $count_all; $i++) {
                                                                $vCode = $db_master[$i]['vCode'];
                                                                $vTitle = $db_master[$i]['vTitle'];
                                                                $eDefault = $db_master[$i]['eDefault'];
                                                                $vValue = 'vRentCarsTitle_' . $vCode;
                                                                $$vValue = $userEditDataArr[$vValue];
                                                                $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                                ?>
                                                                <?php
                                                                $page_title_class = 'col-lg-12';
                                                                if (scount($db_master) > 1) {
                                                                    if ($EN_available) {
                                                                        if ($vCode == "EN") {
                                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                                        }
                                                                    } else {
                                                                        if ($vCode == $default_lang) {
                                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                                        }
                                                                    }
                                                                }
                                                                ?>
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <label>Title (<?= $vTitle; ?>
                                                                            ) <?php echo $required_msg; ?></label>
                                                                    </div>
                                                                    <div class="<?= $page_title_class ?>">
                                                                        <input type="text" class="form-control" name="<?= $vValue; ?>"
                                                                               id="<?= $vValue; ?>" value="<?= $$vValue; ?>"
                                                                               data-originalvalue="<?= $$vValue; ?>"
                                                                               placeholder="<?= $vTitle; ?> Value">
                                                                        <div class="text-danger" id="<?= $vValue . '_error'; ?>"
                                                                             style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                                    </div>
                                                                    <?php
                                                                    if (scount($db_master) > 1) {
                                                                        if ($EN_available) {
                                                                            if ($vCode == "EN") { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type="button" name="allLanguage"
                                                                                            id="allLanguage" class="btn btn-primary"
                                                                                            onClick="getAllLanguageCode('vRentCarsTitle_', 'EN');">
                                                                                        Convert To All Language
                                                                                    </button>
                                                                                </div>
                                                                            <?php }
                                                                        } else {
                                                                            if ($vCode == $default_lang) { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type="button" name="allLanguage"
                                                                                            id="allLanguage" class="btn btn-primary"
                                                                                            onClick="getAllLanguageCode('vRentCarsTitle_', '<?= $default_lang ?>');">
                                                                                        Convert To All Language
                                                                                    </button>
                                                                                </div>
                                                                            <?php }
                                                                        }
                                                                    }
                                                                    ?>
                                                                </div>
                                                                <?php
                                                            }
                                                            ?>
                                                        </div>
                                                        <div class="modal-footer" style="margin-top: 0">
                                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                                <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                                                </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                                <button type="button" class="save" style="margin-left: 0 !important"
                                                                        onclick="saveRentCarsTitle()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                                        onclick="resetToOriginalValue(this, 'vRentCarsTitle_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                            </div>
                                                        </div>
                                                        <div style="clear:both;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Title</label>
                                                </div>
                                                <div class="col-md-4 col-sm-4">
                                                    <input type="text" class="form-control" id="vRentCarsTitle_<?= $default_lang ?>"
                                                           name="vRentCarsTitle_<?= $default_lang ?>"
                                                           value="<?= $userEditDataArr['vRentCarsTitle_' . $default_lang]; ?>">
                                                </div>
                                            </div>
                                        <?php } ?>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Text Color</label>
                                            </div>
                                            <div class="col-md-1 col-sm-1">
                                                <input type="color" data-id="vTitleColorRentCars" class="form-control txt-color" value="<?= $vTitleColorRentCars ?>"/>
                                                <input type="hidden" name="vTitleColorRentCars" id="vTitleColorRentCars" value="<?= $vTitleColorRentCars ?>">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Background Color</label>
                                            </div>
                                            <div class="col-md-1 col-sm-1">
                                                <input type="color" data-id="vBgColorRentCars" class="form-control bg-color" value="<?= $vBgColorRentCars ?>"/>
                                                <input type="hidden" name="vBgColorRentCars" id="vBgColorRentCars" value="<?= $vBgColorRentCars ?>">
                                            </div>
                                        </div>

                                        <div class="row pb-10">
                                            <div class="col-lg-12">
                                                <label>Image</label>
                                            </div>
                                            <div class="col-md-4 col-sm-4 marginbottom-10">
                                                <?php if(!empty($vImageOldRentCars)) { ?>
                                                <div class="marginbottom-10">
                                                    <img src="<?=$tconfig["tsite_url"].'resizeImg.php?h=100&src=' . $tconfig['tsite_upload_app_home_screen_images'] . $vImageOldRentCars; ?>" id="rentcars_img">
                                                </div>
                                                <?php } ?>
                                                <input type="file" class="form-control" name="vImageRentCars" id="vImageRentCars" onchange="previewImage(this, event);" data-img="rentcars_img">
                                                <input type="hidden" class="form-control" name="vImageOldRentCars" id="vImageOldRentCars" value="<?= $vImageOldRentCars ?>">
                                                <strong class="img-note">Note: Upload only png image size of 1024px X 494px. <br> <?= IMAGE_INSTRUCTION_NOTES ?> </strong>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <button type="button" class="btn btn-primary save-section-btn" id="saveRentCarsSection">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>                        
                                <?php } ?>

                                <?php if($MODULES_OBJ->isEnableRentItemService()) { ?>
                                <div class="tabcontent tabcontent-buysellrent <?= $show_rentitem_content ?>" id="manage-rentitem-content">
                                    <div class="col-lg-12">
                                        <?php if (scount($db_master) > 1) { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Title</label>
                                                </div>
                                                <div class="col-md-4 col-sm-4">
                                                    <input type="text" class="form-control" id="vRentItemTitle_Default"
                                                           name="vRentItemTitle_Default"
                                                           value="<?= $userEditDataArr['vRentItemTitle_' . $default_lang]; ?>"
                                                           data-originalvalue="<?= $userEditDataArr['vRentItemTitle_' . $default_lang]; ?>"
                                                           readonly="readonly" required>
                                                </div>
                                                <div class="col-lg-2">
                                                    <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                            data-original-title="Edit" onclick="editRentItemTitle('Edit')">
                                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="RentItemTitle_Modal" tabindex="-1" role="dialog" aria-hidden="true"
                                                 data-backdrop="static" data-keyboard="false">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content nimot-class">
                                                        <div class="modal-header">
                                                            <h4>
                                                                <span id="rentitem_title_modal_action"></span>
                                                                Title
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                        onclick="resetToOriginalValue(this, 'vRentItemTitle_')">x
                                                                </button>
                                                            </h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <?php
                                                            for ($i = 0; $i < $count_all; $i++) {
                                                                $vCode = $db_master[$i]['vCode'];
                                                                $vTitle = $db_master[$i]['vTitle'];
                                                                $eDefault = $db_master[$i]['eDefault'];
                                                                $vValue = 'vRentItemTitle_' . $vCode;
                                                                $$vValue = $userEditDataArr[$vValue];
                                                                $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                                ?>
                                                                <?php
                                                                $page_title_class = 'col-lg-12';
                                                                if (scount($db_master) > 1) {
                                                                    if ($EN_available) {
                                                                        if ($vCode == "EN") {
                                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                                        }
                                                                    } else {
                                                                        if ($vCode == $default_lang) {
                                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                                        }
                                                                    }
                                                                }
                                                                ?>
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <label>Title (<?= $vTitle; ?>
                                                                            ) <?php echo $required_msg; ?></label>
                                                                    </div>
                                                                    <div class="<?= $page_title_class ?>">
                                                                        <input type="text" class="form-control" name="<?= $vValue; ?>"
                                                                               id="<?= $vValue; ?>" value="<?= $$vValue; ?>"
                                                                               data-originalvalue="<?= $$vValue; ?>"
                                                                               placeholder="<?= $vTitle; ?> Value">
                                                                        <div class="text-danger" id="<?= $vValue . '_error'; ?>"
                                                                             style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                                    </div>
                                                                    <?php
                                                                    if (scount($db_master) > 1) {
                                                                        if ($EN_available) {
                                                                            if ($vCode == "EN") { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type="button" name="allLanguage"
                                                                                            id="allLanguage" class="btn btn-primary"
                                                                                            onClick="getAllLanguageCode('vRentItemTitle_', 'EN');">
                                                                                        Convert To All Language
                                                                                    </button>
                                                                                </div>
                                                                            <?php }
                                                                        } else {
                                                                            if ($vCode == $default_lang) { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type="button" name="allLanguage"
                                                                                            id="allLanguage" class="btn btn-primary"
                                                                                            onClick="getAllLanguageCode('vRentItemTitle_', '<?= $default_lang ?>');">
                                                                                        Convert To All Language
                                                                                    </button>
                                                                                </div>
                                                                            <?php }
                                                                        }
                                                                    }
                                                                    ?>
                                                                </div>
                                                                <?php
                                                            }
                                                            ?>
                                                        </div>
                                                        <div class="modal-footer" style="margin-top: 0">
                                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                                <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                                                </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                                <button type="button" class="save" style="margin-left: 0 !important"
                                                                        onclick="saveRentItemTitle()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                                        onclick="resetToOriginalValue(this, 'vRentItemTitle_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                            </div>
                                                        </div>
                                                        <div style="clear:both;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Title</label>
                                                </div>
                                                <div class="col-md-4 col-sm-4">
                                                    <input type="text" class="form-control" id="vRentItemTitle_<?= $default_lang ?>"
                                                           name="vRentItemTitle_<?= $default_lang ?>"
                                                           value="<?= $userEditDataArr['vRentItemTitle_' . $default_lang]; ?>">
                                                </div>
                                            </div>
                                        <?php } ?>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Text Color</label>
                                            </div>
                                            <div class="col-md-1 col-sm-1">
                                                <input type="color" data-id="vTitleColorRentItem" class="form-control txt-color" value="<?= $vTitleColorRentItem ?>"/>
                                                <input type="hidden" name="vTitleColorRentItem" id="vTitleColorRentItem" value="<?= $vTitleColorRentItem ?>">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Background Color</label>
                                            </div>
                                            <div class="col-md-1 col-sm-1">
                                                <input type="color" data-id="vBgColorRentItem" class="form-control bg-color" value="<?= $vBgColorRentItem ?>"/>
                                                <input type="hidden" name="vBgColorRentItem" id="vBgColorRentItem" value="<?= $vBgColorRentItem ?>">
                                            </div>
                                        </div>

                                        <div class="row pb-10">
                                            <div class="col-lg-12">
                                                <label>Image</label>
                                            </div>
                                            <div class="col-md-4 col-sm-4 marginbottom-10">
                                                <?php if(!empty($vImageOldRentItem)) { ?>
                                                <div class="marginbottom-10">
                                                    <img src="<?=$tconfig["tsite_url"].'resizeImg.php?h=100&src=' . $tconfig['tsite_upload_app_home_screen_images'] . $vImageOldRentItem; ?>" id="rentitem_img">
                                                </div>
                                                <?php } ?>
                                                <input type="file" class="form-control" name="vImageRentItem" id="vImageRentItem" onchange="previewImage(this, event);" data-img="rentitem_img">
                                                <input type="hidden" class="form-control" name="vImageOldRentItem" id="vImageOldRentItem" value="<?= $vImageOldRentItem ?>">
                                                <strong class="img-note">Note: Upload only png image size of 973px X 748px. <br> <?= IMAGE_INSTRUCTION_NOTES ?> </strong>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <button type="button" class="btn btn-primary save-section-btn" id="saveRentItemSection">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($MODULES_OBJ->isEnableRideShareService()) { ?>
                        <hr />
                        <div class="show-help-section section-title">Ride Sharing/Car Pool</div>
                        <div class="underline-section-title"></div>
                        <div class="row paddingbottom-0">
                            <div class="col-lg-12">
                                <div class="tab">
                                    <button class="tablinks manage-rideshare-home-screen-tab active" onclick="openTabContent(event, 'manage-rideshare-home-screen-content', 'tabcontent-rideshare')">Home Screen</button>
                                    <button class="tablinks manage-rideshare-info-screen-tab" onclick="openTabContent(event, 'manage-rideshare-info-screen-content', 'tabcontent-rideshare')">Info Screen</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tabcontent tabcontent-rideshare display-tab-content" id="manage-rideshare-home-screen-content">
                            <?php if (scount($db_master) > 1) { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Title</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control" id="vRideShareTitle_Default"
                                               name="vRideShareTitle_Default"
                                               value="<?= $userEditDataArr['vRideShareTitle_' . $default_lang]; ?>"
                                               data-originalvalue="<?= $userEditDataArr['vRideShareTitle_' . $default_lang]; ?>"
                                               readonly="readonly" required>
                                    </div>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                data-original-title="Edit" onclick="editRideShareTitle('Edit')">
                                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="modal fade" id="RideShareTitle_Modal" tabindex="-1" role="dialog" aria-hidden="true"
                                     data-backdrop="static" data-keyboard="false">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content nimot-class">
                                            <div class="modal-header">
                                                <h4>
                                                    <span id="rideshare_title_modal_action"></span>
                                                    Title
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'vRideShareTitle_')">x
                                                    </button>
                                                </h4>
                                            </div>
                                            <div class="modal-body">
                                                <?php
                                                for ($i = 0; $i < $count_all; $i++) {
                                                    $vCode = $db_master[$i]['vCode'];
                                                    $vTitle = $db_master[$i]['vTitle'];
                                                    $eDefault = $db_master[$i]['eDefault'];
                                                    $vValue = 'vRideShareTitle_' . $vCode;
                                                    $$vValue = $userEditDataArr[$vValue];
                                                    $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                    ?>
                                                    <?php
                                                    $page_title_class = 'col-lg-12';
                                                    if (scount($db_master) > 1) {
                                                        if ($EN_available) {
                                                            if ($vCode == "EN") {
                                                                $page_title_class = 'col-md-9 col-sm-9';
                                                            }
                                                        } else {
                                                            if ($vCode == $default_lang) {
                                                                $page_title_class = 'col-md-9 col-sm-9';
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label>Title (<?= $vTitle; ?>
                                                                ) <?php echo $required_msg; ?></label>
                                                        </div>
                                                        <div class="<?= $page_title_class ?>">
                                                            <input type="text" class="form-control" name="<?= $vValue; ?>"
                                                                   id="<?= $vValue; ?>" value="<?= $$vValue; ?>"
                                                                   data-originalvalue="<?= $$vValue; ?>"
                                                                   placeholder="<?= $vTitle; ?> Value">
                                                            <div class="text-danger" id="<?= $vValue . '_error'; ?>"
                                                                 style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                        </div>
                                                        <?php
                                                        if (scount($db_master) > 1) {
                                                            if ($EN_available) {
                                                                if ($vCode == "EN") { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vRideShareTitle_', 'EN');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                            } else {
                                                                if ($vCode == $default_lang) { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vRideShareTitle_', '<?= $default_lang ?>');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <div class="modal-footer" style="margin-top: 0">
                                                <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                    <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                                    </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                <div class="nimot-class-but" style="margin-bottom: 0">
                                                    <button type="button" class="save" style="margin-left: 0 !important"
                                                            onclick="saveRideShareTitle()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                    <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'vRideShareTitle_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                </div>
                                            </div>
                                            <div style="clear:both;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Subtitle</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control" id="vRideShareSubTitle_Default"
                                               name="vRideShareSubTitle_Default"
                                               value="<?= $userEditDataArr['vRideShareSubTitle_' . $default_lang]; ?>"
                                               data-originalvalue="<?= $userEditDataArr['vRideShareSubTitle_' . $default_lang]; ?>"
                                               readonly="readonly" required>
                                    </div>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                data-original-title="Edit" onclick="editRideShareSubTitle('Edit')">
                                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="modal fade" id="RideShareSubTitle_Modal" tabindex="-1" role="dialog"
                                     aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content nimot-class">
                                            <div class="modal-header">
                                                <h4>
                                                    <span id="rideshare_subtitle_modal_action"></span>
                                                    Title
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'vRideShareSubTitle_')">x
                                                    </button>
                                                </h4>
                                            </div>
                                            <div class="modal-body">
                                                <?php
                                                for ($i = 0; $i < $count_all; $i++) {
                                                    $vCode = $db_master[$i]['vCode'];
                                                    $vTitle = $db_master[$i]['vTitle'];
                                                    $eDefault = $db_master[$i]['eDefault'];
                                                    $vValue = 'vRideShareSubTitle_' . $vCode;
                                                    $$vValue = $userEditDataArr[$vValue];
                                                    $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                    ?>
                                                    <?php
                                                    $page_title_class = 'col-lg-12';
                                                    if (scount($db_master) > 1) {
                                                        if ($EN_available) {
                                                            if ($vCode == "EN") {
                                                                $page_title_class = 'col-md-9 col-sm-9';
                                                            }
                                                        } else {
                                                            if ($vCode == $default_lang) {
                                                                $page_title_class = 'col-md-9 col-sm-9';
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label>Subtitle (<?= $vTitle; ?>
                                                                ) <?php echo $required_msg; ?></label>
                                                        </div>
                                                        <div class="<?= $page_title_class ?>">
                                                            <input type="text" class="form-control" name="<?= $vValue; ?>"
                                                                   id="<?= $vValue; ?>" value="<?= $$vValue; ?>"
                                                                   data-originalvalue="<?= $$vValue; ?>"
                                                                   placeholder="<?= $vTitle; ?> Value">
                                                            <div class="text-danger" id="<?= $vValue . '_error'; ?>"
                                                                 style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                        </div>
                                                        <?php
                                                        if (scount($db_master) > 1) {
                                                            if ($EN_available) {
                                                                if ($vCode == "EN") { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vRideShareSubTitle_', 'EN');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                            } else {
                                                                if ($vCode == $default_lang) { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vRideShareSubTitle_', '<?= $default_lang ?>');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <div class="modal-footer" style="margin-top: 0">
                                                <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                    <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                                    </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                <div class="nimot-class-but" style="margin-bottom: 0">
                                                    <button type="button" class="save" style="margin-left: 0 !important"
                                                            onclick="saveRideShareSubTitle()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                    <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'vRideShareSubTitle_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                </div>
                                            </div>
                                            <div style="clear:both;"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Title</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control" id="vRideShareTitle_<?= $default_lang ?>"
                                               name="vRideShareTitle_<?= $default_lang ?>"
                                               value="<?= $userEditDataArr['vRideShareTitle_' . $default_lang]; ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Subtitle</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control" id="vRideShareSubTitle_<?= $default_lang ?>"
                                               name="vRideShareSubTitle_<?= $default_lang ?>"
                                               value="<?= $userEditDataArr['vRideShareSubTitle_' . $default_lang]; ?>">
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Title Color</label>
                                </div>
                                <div class="col-md-1 col-sm-1">
                                    <input type="color" data-id="vTitleColorRideShare" class="form-control txt-color" value="<?= $vTitleColorRideShare ?>"/>
                                    <input type="hidden" name="vTitleColorRideShare" id="vTitleColorRideShare" value="<?= $vTitleColorRideShare ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Subtitle Color</label>
                                </div>
                                <div class="col-md-1 col-sm-1">
                                    <input type="color" data-id="vSubTitleColorRideShare" class="form-control txt-color" value="<?= $vSubTitleColorRideShare ?>"/>
                                    <input type="hidden" name="vSubTitleColorRideShare" id="vSubTitleColorRideShare" value="<?= $vSubTitleColorRideShare ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Background Color</label>
                                </div>
                                <div class="col-md-1 col-sm-1">
                                    <input type="color" data-id="vBgColorRideShare" class="form-control bg-color" value="<?= $vBgColorRideShare ?>"/>
                                    <input type="hidden" name="vBgColorRideShare" id="vBgColorRideShare" value="<?= $vBgColorRideShare ?>">
                                </div>
                            </div>

                            <div class="row pb-10">
                                <div class="col-lg-12">
                                    <label>Image</label>
                                </div>
                                <div class="col-md-4 col-sm-4 marginbottom-10">
                                    <?php if(!empty($vImageOldRideShare)) { ?>
                                    <div class="marginbottom-10">
                                        <img src="<?=$tconfig["tsite_url"].'resizeImg.php?h=100&src=' . $tconfig['tsite_upload_app_home_screen_images'] . 'AppHomeScreen/' . $vImageOldRideShare; ?>" id="rideshare_img">
                                    </div>
                                    <?php } ?>
                                    <input type="file" class="form-control" name="vImageRideShare" id="vImageRideShare" onchange="previewImage(this, event);" data-img="rideshare_img">
                                    <input type="hidden" class="form-control" name="vImageOldRideShare" id="vImageOldRideShare" value="<?= $vImageOldRideShare ?>">
                                    <strong class="img-note">Note: Upload only png image size of 795px X 650px. <br> <?= IMAGE_INSTRUCTION_NOTES ?> </strong>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <button type="button" class="btn btn-primary save-section-btn" id="saveRideShareSection">Save</button>
                                </div>
                            </div>
                        </div>
                        <div class="tabcontent tabcontent-rideshare" id="manage-rideshare-info-screen-content">
                            <?php if (scount($db_master) > 1) { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Publish Ride Title</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control" id="RideSharePublishTitle_Default"
                                               name="RideSharePublishTitle_Default"
                                               value="<?= $userEditDataArr['RideSharePublishTitle_' . $default_lang]; ?>"
                                               data-originalvalue="<?= $userEditDataArr['RideSharePublishTitle_' . $default_lang]; ?>"
                                               readonly="readonly" required>
                                    </div>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                data-original-title="Edit" onclick="editRideSharePublishTitle('Edit')">
                                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="modal fade" id="RideSharePublishTitle_Modal" tabindex="-1" role="dialog" aria-hidden="true"
                                     data-backdrop="static" data-keyboard="false">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content nimot-class">
                                            <div class="modal-header">
                                                <h4>
                                                    <span id="rideshare_publish_title_modal_action"></span>
                                                    Title
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'RideSharePublishTitle_')">x
                                                    </button>
                                                </h4>
                                            </div>
                                            <div class="modal-body">
                                                <?php
                                                for ($i = 0; $i < $count_all; $i++) {
                                                    $vCode = $db_master[$i]['vCode'];
                                                    $vTitle = $db_master[$i]['vTitle'];
                                                    $eDefault = $db_master[$i]['eDefault'];
                                                    $vValue = 'RideSharePublishTitle_' . $vCode;
                                                    $$vValue = $userEditDataArr[$vValue];
                                                    $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                    ?>
                                                    <?php
                                                    $page_title_class = 'col-lg-12';
                                                    if (scount($db_master) > 1) {
                                                        if ($EN_available) {
                                                            if ($vCode == "EN") {
                                                                $page_title_class = 'col-md-9 col-sm-9';
                                                            }
                                                        } else {
                                                            if ($vCode == $default_lang) {
                                                                $page_title_class = 'col-md-9 col-sm-9';
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label>Title (<?= $vTitle; ?>
                                                                ) <?php echo $required_msg; ?></label>
                                                        </div>
                                                        <div class="<?= $page_title_class ?>">
                                                            <input type="text" class="form-control" name="<?= $vValue; ?>"
                                                                   id="<?= $vValue; ?>" value="<?= $$vValue; ?>"
                                                                   data-originalvalue="<?= $$vValue; ?>"
                                                                   placeholder="<?= $vTitle; ?> Value">
                                                            <div class="text-danger" id="<?= $vValue . '_error'; ?>"
                                                                 style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                        </div>
                                                        <?php
                                                        if (scount($db_master) > 1) {
                                                            if ($EN_available) {
                                                                if ($vCode == "EN") { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('RideSharePublishTitle_', 'EN');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                            } else {
                                                                if ($vCode == $default_lang) { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('RideSharePublishTitle_', '<?= $default_lang ?>');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <div class="modal-footer" style="margin-top: 0">
                                                <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                    <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                                    </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                <div class="nimot-class-but" style="margin-bottom: 0">
                                                    <button type="button" class="save" style="margin-left: 0 !important"
                                                            onclick="saveRideSharePublishTitle()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                    <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'RideSharePublishTitle_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                </div>
                                            </div>
                                            <div style="clear:both;"></div>
                                        </div>
                                    </div>
                                </div>

                            <?php } else { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Publish Ride Title</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control" id="RideSharePublishTitle_<?= $default_lang ?>"
                                               name="RideSharePublishTitle_<?= $default_lang ?>"
                                               value="<?= $userEditDataArr['RideSharePublishTitle_' . $default_lang]; ?>">
                                    </div>
                                </div>
                            <?php } ?>


                            <div class="row pb-10">
                                <div class="col-lg-12">
                                    <label>Publish Image</label>
                                </div>
                                <div class="col-md-4 col-sm-4 marginbottom-10">
                                    <?php if(!empty($vImageOldRideSharePublish)) { ?>
                                    <div class="marginbottom-10">
                                        <img src="<?=$tconfig["tsite_url"].'resizeImg.php?h=100&src=' . $tconfig["tsite_upload_app_home_screen_images"] . $vImageOldRideSharePublish; ?>" id="ridesharepublish_img">
                                    </div>
                                    <?php } ?>
                                    <input type="file" class="form-control" name="vImageRideSharePublish" id="vImageRideSharePublish" onchange="previewImage(this, event);" data-img="ridesharepublish_img">
                                    <input type="hidden" class="form-control" name="vImageOldRideSharePublish" id="vImageOldRideSharePublish" value="<?= $vImageOldRideSharePublish ?>">
                                    <strong class="img-note">Note: Upload only png image size of 795px X 650px. <br> <?= IMAGE_INSTRUCTION_NOTES ?> </strong>
                                </div>
                            </div>

                            <?php if (scount($db_master) > 1) { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Book Title</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control" id="RideShareBookTitle_Default"
                                               name="RideShareBookTitle_Default"
                                               value="<?= $userEditDataArr['RideShareBookTitle_' . $default_lang]; ?>"
                                               data-originalvalue="<?= $userEditDataArr['RideShareBookTitle_' . $default_lang]; ?>"
                                               readonly="readonly" required>
                                    </div>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                data-original-title="Edit" onclick="editRideShareBookTitle('Edit')">
                                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="modal fade" id="RideShareBookTitle_Modal" tabindex="-1" role="dialog" aria-hidden="true"
                                     data-backdrop="static" data-keyboard="false">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content nimot-class">
                                            <div class="modal-header">
                                                <h4>
                                                    <span id="rideshare_publish_title_modal_action"></span>
                                                    Title
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'RideShareBookTitle_')">x
                                                    </button>
                                                </h4>
                                            </div>
                                            <div class="modal-body">
                                                <?php
                                                for ($i = 0; $i < $count_all; $i++) {
                                                    $vCode = $db_master[$i]['vCode'];
                                                    $vTitle = $db_master[$i]['vTitle'];
                                                    $eDefault = $db_master[$i]['eDefault'];
                                                    $vValue = 'RideShareBookTitle_' . $vCode;
                                                    $$vValue = $userEditDataArr[$vValue];
                                                    $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                    ?>
                                                    <?php
                                                    $page_title_class = 'col-lg-12';
                                                    if (scount($db_master) > 1) {
                                                        if ($EN_available) {
                                                            if ($vCode == "EN") {
                                                                $page_title_class = 'col-md-9 col-sm-9';
                                                            }
                                                        } else {
                                                            if ($vCode == $default_lang) {
                                                                $page_title_class = 'col-md-9 col-sm-9';
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label>Title (<?= $vTitle; ?>
                                                                ) <?php echo $required_msg; ?></label>
                                                        </div>
                                                        <div class="<?= $page_title_class ?>">
                                                            <input type="text" class="form-control" name="<?= $vValue; ?>"
                                                                   id="<?= $vValue; ?>" value="<?= $$vValue; ?>"
                                                                   data-originalvalue="<?= $$vValue; ?>"
                                                                   placeholder="<?= $vTitle; ?> Value">
                                                            <div class="text-danger" id="<?= $vValue . '_error'; ?>"
                                                                 style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                        </div>
                                                        <?php
                                                        if (scount($db_master) > 1) {
                                                            if ($EN_available) {
                                                                if ($vCode == "EN") { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('RideShareBookTitle_', 'EN');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                            } else {
                                                                if ($vCode == $default_lang) { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('RideShareBookTitle_', '<?= $default_lang ?>');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <div class="modal-footer" style="margin-top: 0">
                                                <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                    <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                                    </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                <div class="nimot-class-but" style="margin-bottom: 0">
                                                    <button type="button" class="save" style="margin-left: 0 !important"
                                                            onclick="saveRideShareBookTitle()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                    <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'RideShareBookTitle_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                </div>
                                            </div>
                                            <div style="clear:both;"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Book Title</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control" id="RideShareBookTitle_<?= $default_lang ?>"
                                               name="RideShareBookTitle_<?= $default_lang ?>"
                                               value="<?= $userEditDataArr['RideShareBookTitle_' . $default_lang]; ?>">
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="row pb-10">
                                <div class="col-lg-12">
                                    <label>Book Image</label>
                                </div>
                                <div class="col-md-4 col-sm-4 marginbottom-10">
                                    <?php if(!empty($vImageOldRideShareBook)) { ?>
                                        <div class="marginbottom-10">
                                            <img src="<?=$tconfig["tsite_url"].'resizeImg.php?h=100&src=' . $tconfig['tsite_upload_app_home_screen_images'] . $vImageOldRideShareBook; ?>" id="ridesharebook_img">
                                        </div>
                                    <?php } ?>
                                    <input type="file" class="form-control" name="vImageRideShareBook" id="vImageRideShareBook" onchange="previewImage(this, event);" data-img="ridesharebook_img">
                                    <input type="hidden" class="form-control" name="vImageOldRideShareBook" id="vImageOldRideShareBook" value="<?= $vImageOldRideShareBook ?>">
                                    <strong class="img-note">Note: Upload only png image size of 795px X 650px. <br> <?= IMAGE_INSTRUCTION_NOTES ?> </strong>
                                </div>
                            </div>

                            <?php if (scount($db_master) > 1) { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>MyRide Title</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control" id="RideShareMyRideTitle_Default"
                                               name="RideShareMyRideTitle_Default"
                                               value="<?= $userEditDataArr['RideShareMyRideTitle_' . $default_lang]; ?>"
                                               data-originalvalue="<?= $userEditDataArr['RideShareMyRideTitle_' . $default_lang]; ?>"
                                               readonly="readonly" required>
                                    </div>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                data-original-title="Edit" onclick="editRideShareMyRideTitle('Edit')">
                                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="modal fade" id="RideShareMyRideTitle_Modal" tabindex="-1" role="dialog" aria-hidden="true"
                                     data-backdrop="static" data-keyboard="false">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content nimot-class">
                                            <div class="modal-header">
                                                <h4>
                                                    <span id="rideshare_publish_title_modal_action"></span>
                                                    Title
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'RideShareMyRideTitle_')">x
                                                    </button>
                                                </h4>
                                            </div>
                                            <div class="modal-body">
                                                <?php
                                                for ($i = 0; $i < $count_all; $i++) {
                                                    $vCode = $db_master[$i]['vCode'];
                                                    $vTitle = $db_master[$i]['vTitle'];
                                                    $eDefault = $db_master[$i]['eDefault'];
                                                    $vValue = 'RideShareMyRideTitle_' . $vCode;
                                                    $$vValue = $userEditDataArr[$vValue];
                                                    $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                    ?>
                                                    <?php
                                                    $page_title_class = 'col-lg-12';
                                                    if (scount($db_master) > 1) {
                                                        if ($EN_available) {
                                                            if ($vCode == "EN") {
                                                                $page_title_class = 'col-md-9 col-sm-9';
                                                            }
                                                        } else {
                                                            if ($vCode == $default_lang) {
                                                                $page_title_class = 'col-md-9 col-sm-9';
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label>Title (<?= $vTitle; ?>
                                                                ) <?php echo $required_msg; ?></label>
                                                        </div>
                                                        <div class="<?= $page_title_class ?>">
                                                            <input type="text" class="form-control" name="<?= $vValue; ?>"
                                                                   id="<?= $vValue; ?>" value="<?= $$vValue; ?>"
                                                                   data-originalvalue="<?= $$vValue; ?>"
                                                                   placeholder="<?= $vTitle; ?> Value">
                                                            <div class="text-danger" id="<?= $vValue . '_error'; ?>"
                                                                 style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                        </div>
                                                        <?php
                                                        if (scount($db_master) > 1) {
                                                            if ($EN_available) {
                                                                if ($vCode == "EN") { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('RideShareMyRideTitle_', 'EN');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                            } else {
                                                                if ($vCode == $default_lang) { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('RideShareMyRideTitle_', '<?= $default_lang ?>');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <div class="modal-footer" style="margin-top: 0">
                                                <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                    <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                                    </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                <div class="nimot-class-but" style="margin-bottom: 0">
                                                    <button type="button" class="save" style="margin-left: 0 !important"
                                                            onclick="saveRideShareMyRideTitle()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                    <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'RideShareMyRideTitle_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                </div>
                                            </div>
                                            <div style="clear:both;"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>MyRide Title</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control" id="RideShareMyRideTitle_<?= $default_lang ?>"
                                               name="RideShareMyRideTitle_<?= $default_lang ?>"
                                               value="<?= $userEditDataArr['RideShareMyRideTitle_' . $default_lang]; ?>">
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="row pb-10">
                                    <div class="col-lg-12">
                                        <label>My Ride Image</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4 marginbottom-10">
                                        <?php if(!empty($vImageOldRideShareMyRides)) { ?>
                                        <div class="marginbottom-10">
                                            <img src="<?=$tconfig["tsite_url"].'resizeImg.php?h=100&src=' . $tconfig['tsite_upload_app_home_screen_images'] . $vImageOldRideShareMyRides; ?>" id="rideshareMyRide_img">
                                        </div>
                                        <?php } ?>
                                        <input type="file" class="form-control" name="vImageRideShareMyRides" id="vImageRideShareMyRides" onchange="previewImage(this, event);" data-img="rideshareMyRide_img">
                                        <input type="hidden" class="form-control" name="vImageOldRideShareMyRides" id="vImageOldRideShareMyRides" value="<?= $vImageOldRideShareMyRides ?>">
                                        <strong class="img-note">Note: Upload only png image size of 795px X 650px. <br> <?= IMAGE_INSTRUCTION_NOTES ?> </strong>
                                    </div>
                                </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <button type="button" class="btn btn-primary save-section-btn" id="saveRideShareInfoSection">Save</button>
                                </div>
                            </div>

                        </div>

                    <?php } ?>

                    <?php if ($MODULES_OBJ->isEnableMedicalServices()) { ?>
                        <hr />
                        <div class="show-help-section section-title">Medical Services</div>
                        <div class="underline-section-title"></div>
                        <?php if (scount($db_master) > 1) { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Title</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="vMSTitle_Default"
                                           name="vMSTitle_Default"
                                           value="<?= $userEditDataArr['vMSTitle_' . $default_lang]; ?>"
                                           data-originalvalue="<?= $userEditDataArr['vMSTitle_' . $default_lang]; ?>"
                                           readonly="readonly" required>
                                </div>
                                <div class="col-lg-2">
                                    <button type="button" class="btn btn-info" data-toggle="tooltip"
                                            data-original-title="Edit" onclick="editMSTitle('Edit')">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>
                            <div class="modal fade" id="MSTitle_Modal" tabindex="-1" role="dialog" aria-hidden="true"
                                 data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content nimot-class">
                                        <div class="modal-header">
                                            <h4>
                                                <span id="ms_title_modal_action"></span>
                                                Title
                                                <button type="button" class="close" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vMSTitle_')">x
                                                </button>
                                            </h4>
                                        </div>
                                        <div class="modal-body">
                                            <?php
                                            for ($i = 0; $i < $count_all; $i++) {
                                                $vCode = $db_master[$i]['vCode'];
                                                $vTitle = $db_master[$i]['vTitle'];
                                                $eDefault = $db_master[$i]['eDefault'];
                                                $vValue = 'vMSTitle_' . $vCode;
                                                $$vValue = $userEditDataArr[$vValue];
                                                $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                ?>
                                                <?php
                                                $page_title_class = 'col-lg-12';
                                                if (scount($db_master) > 1) {
                                                    if ($EN_available) {
                                                        if ($vCode == "EN") {
                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                        }
                                                    } else {
                                                        if ($vCode == $default_lang) {
                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                        }
                                                    }
                                                }
                                                ?>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <label>Title (<?= $vTitle; ?>
                                                            ) <?php echo $required_msg; ?></label>
                                                    </div>
                                                    <div class="<?= $page_title_class ?>">
                                                        <input type="text" class="form-control" name="<?= $vValue; ?>"
                                                               id="<?= $vValue; ?>" value="<?= $$vValue; ?>"
                                                               data-originalvalue="<?= $$vValue; ?>"
                                                               placeholder="<?= $vTitle; ?> Value">
                                                        <div class="text-danger" id="<?= $vValue . '_error'; ?>"
                                                             style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                    </div>
                                                    <?php
                                                    if (scount($db_master) > 1) {
                                                        if ($EN_available) {
                                                            if ($vCode == "EN") { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vMSTitle_', 'EN');">
                                                                        Convert To All Language
                                                                    </button>
                                                                </div>
                                                            <?php }
                                                        } else {
                                                            if ($vCode == $default_lang) { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vMSTitle_', '<?= $default_lang ?>');">
                                                                        Convert To All Language
                                                                    </button>
                                                                </div>
                                                            <?php }
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <div class="modal-footer" style="margin-top: 0">
                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                                </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                <button type="button" class="save" style="margin-left: 0 !important"
                                                        onclick="saveMSTitle()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vMSTitle_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                            </div>
                                        </div>
                                        <div style="clear:both;"></div>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Title</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="vMSTitle_<?= $default_lang ?>" name="vMSTitle_<?= $default_lang ?>" value="<?= $userEditDataArr['vMSTitle_' . $default_lang]; ?>">
                                </div>
                            </div>
                        <?php } ?>
                        <div class="row">
                            <div class="col-lg-12">
                                <button type="button" class="btn btn-primary save-section-btn" id="saveMSTitleSection">Save</button>
                            </div>
                        </div>

                        <div class="row paddingbottom-0">
                            <div class="col-lg-12">
                                <div class="tab">
                                    <?php $ms_count = 1; foreach ($MEDICAL_SERVICES_ARR as $MEDICAL_SERVICE) { ?>
                                    <button class="tablinks manage-<?= strtolower($MEDICAL_SERVICE['ManageServiceKey']) ?>-tab <?= $ms_count == 1 ? "active" : "" ?>" onclick="openTabContent(event, 'manage-<?= strtolower($MEDICAL_SERVICE['ManageServiceKey']) ?>-content', 'tabcontent-ms')"> <?= $MEDICAL_SERVICE['ServiceTitle']['EN'] ?>
                                    </button>
                                    <?php $ms_count++; } ?>
                                </div>

                                <?php $ms_count = 1; foreach ($MEDICAL_SERVICES_ARR as $MEDICAL_SERVICE) { ?>
                                <div class="tabcontent tabcontent-ms <?= $ms_count == 1 ? "display-tab-content" : "" ?>" id="manage-<?= strtolower($MEDICAL_SERVICE['ManageServiceKey']) ?>-content">
                                    <div class="col-lg-12">
                                        <?php if (scount($db_master) > 1) { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Title</label>
                                                </div>
                                                <div class="col-md-4 col-sm-4">
                                                    <input type="text" class="form-control" id="v<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MSTitle_Default" name="v<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MSTitle_Default" value="<?= $MEDICAL_SERVICE['ServiceTitle'][$default_lang]; ?>" data-originalvalue="<?= $MEDICAL_SERVICE['ServiceTitle'][$default_lang]; ?>" readonly="readonly" required>
                                                </div>
                                                <div class="col-lg-2">
                                                    <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                            data-original-title="Edit" onclick="edit<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MSTitle('Edit')">
                                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MSTitle_Modal" tabindex="-1" role="dialog" aria-hidden="true"
                                                 data-backdrop="static" data-keyboard="false">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content nimot-class">
                                                        <div class="modal-header">
                                                            <h4>
                                                                <span id="<?= strtolower($MEDICAL_SERVICE['ManageServiceKey']) ?>ms_title_modal_action"></span>
                                                                Title
                                                                <button type="button" class="close" data-dismiss="modal" onclick="resetToOriginalValue(this, 'v<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MSTitle_')">x
                                                                </button>
                                                            </h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <?php
                                                            for ($i = 0; $i < $count_all; $i++) {
                                                                $vCode = $db_master[$i]['vCode'];
                                                                $vTitle = $db_master[$i]['vTitle'];
                                                                $eDefault = $db_master[$i]['eDefault'];
                                                                $vValue = 'v' . $MEDICAL_SERVICE['ManageServiceKey'] . 'MSTitle_' . $vCode;
                                                                $$vValue = $MEDICAL_SERVICE['ServiceTitle'][$vCode];
                                                                $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                                ?>
                                                                <?php
                                                                $page_title_class = 'col-lg-12';
                                                                if (scount($db_master) > 1) {
                                                                    if ($EN_available) {
                                                                        if ($vCode == "EN") {
                                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                                        }
                                                                    } else {
                                                                        if ($vCode == $default_lang) {
                                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                                        }
                                                                    }
                                                                }
                                                                ?>
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <label>Title (<?= $vTitle; ?>
                                                                            ) <?php echo $required_msg; ?></label>
                                                                    </div>
                                                                    <div class="<?= $page_title_class ?>">
                                                                        <input type="text" class="form-control" name="<?= $vValue; ?>"
                                                                               id="<?= $vValue; ?>" value="<?= $$vValue; ?>"
                                                                               data-originalvalue="<?= $$vValue; ?>"
                                                                               placeholder="<?= $vTitle; ?> Value">
                                                                        <div class="text-danger" id="<?= $vValue . '_error'; ?>"
                                                                             style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                                    </div>
                                                                    <?php
                                                                    if (scount($db_master) > 1) {
                                                                        if ($EN_available) {
                                                                            if ($vCode == "EN") { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type="button" name="allLanguage"
                                                                                            id="allLanguage" class="btn btn-primary"
                                                                                            onClick="getAllLanguageCode('v<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MSTitle_', 'EN');">
                                                                                        Convert To All Language
                                                                                    </button>
                                                                                </div>
                                                                            <?php }
                                                                        } else {
                                                                            if ($vCode == $default_lang) { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type="button" name="allLanguage"
                                                                                            id="allLanguage" class="btn btn-primary"
                                                                                            onClick="getAllLanguageCode('v<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MSTitle_', '<?= $default_lang ?>');">
                                                                                        Convert To All Language
                                                                                    </button>
                                                                                </div>
                                                                            <?php }
                                                                        }
                                                                    }
                                                                    ?>
                                                                </div>
                                                                <?php
                                                            }
                                                            ?>
                                                        </div>
                                                        <div class="modal-footer" style="margin-top: 0">
                                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                                <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                                                </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                                <button type="button" class="save" style="margin-left: 0 !important"
                                                                        onclick="save<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MSTitle()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal" onclick="resetToOriginalValue(this, 'v<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MSTitle_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                            </div>
                                                        </div>
                                                        <div style="clear:both;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Subtitle</label>
                                                </div>
                                                <div class="col-md-4 col-sm-4">
                                                    <input type="text" class="form-control" id="v<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MSSubTitle_Default" name="v<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MSSubTitle_Default" value="<?= $MEDICAL_SERVICE['ServiceDesc'][$default_lang] ?>"  data-originalvalue="<?= $MEDICAL_SERVICE['ServiceDesc'][$default_lang] ?>" readonly="readonly" required>
                                                </div>
                                                <div class="col-lg-2">
                                                    <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                            data-original-title="Edit" onclick="edit<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MSSubTitle('Edit')">
                                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MSSubTitle_Modal" tabindex="-1" role="dialog"
                                                 aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content nimot-class">
                                                        <div class="modal-header">
                                                            <h4>
                                                                <span id="<?= strtolower($MEDICAL_SERVICE['ManageServiceKey']) ?>_subtitle_modal_action"></span>
                                                                Title
                                                                <button type="button" class="close" data-dismiss="modal" onclick="resetToOriginalValue(this, 'v<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MSSubTitle_')">x
                                                                </button>
                                                            </h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <?php
                                                            for ($i = 0; $i < $count_all; $i++) {
                                                                $vCode = $db_master[$i]['vCode'];
                                                                $vTitle = $db_master[$i]['vTitle'];
                                                                $eDefault = $db_master[$i]['eDefault'];
                                                                $vValue = 'v' . $MEDICAL_SERVICE['ManageServiceKey'] . 'MSSubTitle_' . $vCode;
                                                                $$vValue = $MEDICAL_SERVICE['ServiceDesc'][$vCode];
                                                                $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                                ?>
                                                                <?php
                                                                $page_title_class = 'col-lg-12';
                                                                if (scount($db_master) > 1) {
                                                                    if ($EN_available) {
                                                                        if ($vCode == "EN") {
                                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                                        }
                                                                    } else {
                                                                        if ($vCode == $default_lang) {
                                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                                        }
                                                                    }
                                                                }
                                                                ?>
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <label>Subtitle (<?= $vTitle; ?>
                                                                            ) <?php echo $required_msg; ?></label>
                                                                    </div>
                                                                    <div class="<?= $page_title_class ?>">
                                                                        <input type="text" class="form-control" name="<?= $vValue; ?>"
                                                                               id="<?= $vValue; ?>" value="<?= $$vValue; ?>"
                                                                               data-originalvalue="<?= $$vValue; ?>"
                                                                               placeholder="<?= $vTitle; ?> Value">
                                                                        <div class="text-danger" id="<?= $vValue . '_error'; ?>"
                                                                             style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                                    </div>
                                                                    <?php
                                                                    if (scount($db_master) > 1) {
                                                                        if ($EN_available) {
                                                                            if ($vCode == "EN") { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type="button" name="allLanguage"
                                                                                            id="allLanguage" class="btn btn-primary"
                                                                                            onClick="getAllLanguageCode('v<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MSSubTitle_', 'EN');">
                                                                                        Convert To All Language
                                                                                    </button>
                                                                                </div>
                                                                            <?php }
                                                                        } else {
                                                                            if ($vCode == $default_lang) { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type="button" name="allLanguage"
                                                                                            id="allLanguage" class="btn btn-primary"
                                                                                            onClick="getAllLanguageCode('v<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MSSubTitle_', '<?= $default_lang ?>');">
                                                                                        Convert To All Language
                                                                                    </button>
                                                                                </div>
                                                                            <?php }
                                                                        }
                                                                    }
                                                                    ?>
                                                                </div>
                                                                <?php
                                                            }
                                                            ?>
                                                        </div>
                                                        <div class="modal-footer" style="margin-top: 0">
                                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                                <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                                                </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                                <button type="button" class="save" style="margin-left: 0 !important"
                                                                        onclick="save<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MSSubTitle()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal" onclick="resetToOriginalValue(this, 'v<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MSSubTitle_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                            </div>
                                                        </div>
                                                        <div style="clear:both;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Title</label>
                                                </div>
                                                <div class="col-md-4 col-sm-4">
                                                    <input type="text" class="form-control" id="v<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MSTitle_<?= $default_lang ?>" name="v<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MSTitle_<?= $default_lang ?>"
                                                           value="<?= $MEDICAL_SERVICE['ServiceTitle'][$default_lang] ?>">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Subtitle</label>
                                                </div>
                                                <div class="col-md-4 col-sm-4">
                                                    <input type="text" class="form-control" id="v<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MSSubTitle_<?= $default_lang ?>" name="v<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MSSubTitle_<?= $default_lang ?>" value="<?= $MEDICAL_SERVICE['ServiceDesc'][$default_lang] ?>">
                                                </div>
                                            </div>
                                        <?php } ?>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Text Color</label>
                                            </div>
                                            <div class="col-md-1 col-sm-1">
                                                <input type="color" data-id="vTitleColor<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MS" class="form-control txt-color" value="<?= $TextColorMS[$MEDICAL_SERVICE['ManageServiceKey']] ?>"/>
                                                <input type="hidden" name="vTitleColor<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MS" id="vTitleColor<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MS" value="<?= $TextColorMS[$MEDICAL_SERVICE['ManageServiceKey']] ?>">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Background Color</label>
                                            </div>
                                            <div class="col-md-1 col-sm-1">
                                                <input type="color" data-id="vBgColor<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MS" class="form-control bg-color" value="<?= $BgColorMS[$MEDICAL_SERVICE['ManageServiceKey']] ?>"/>
                                                <input type="hidden" name="vBgColor<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MS" id="vBgColor<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MS" value="<?= $BgColorMS[$MEDICAL_SERVICE['ManageServiceKey']] ?>">
                                            </div>
                                        </div>

                                        <div class="row pb-10">
                                            <div class="col-lg-12">
                                                <label>Image</label>
                                            </div>
                                            <div class="col-md-4 col-sm-4 marginbottom-10">
                                                <?php if(!empty($vImageOldMS[$MEDICAL_SERVICE['ManageServiceKey']])) { ?>
                                                <div class="marginbottom-10">
                                                    <img src="<?=$tconfig["tsite_url"].'resizeImg.php?h=100&src=' . $tconfig['tsite_upload_app_home_screen_images'] . $vImageOldMS[$MEDICAL_SERVICE['ManageServiceKey']]; ?>" id="<?= strtolower($MEDICAL_SERVICE['ManageServiceKey']) ?>MS_img">
                                                </div>
                                                <?php } ?>
                                                <input type="file" class="form-control" name="vImage<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MS" id="vImage<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MS" onchange="previewImage(this, event);" data-img="<?= strtolower($MEDICAL_SERVICE['ManageServiceKey']) ?>MS_img">
                                                <input type="hidden" class="form-control" name="vImageOld<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MS" id="vImageOld<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MS" value="<?= $vImageOldMS[$MEDICAL_SERVICE['ManageServiceKey']] ?>">
                                                <strong class="img-note">Note: Upload only png image size of 512px X 512px. <br> <?= IMAGE_INSTRUCTION_NOTES ?> </strong>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Services</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="manage-banner-section">
                                                    <button type="button" class="manage-banner-btn manage-icon-btn" data-toggle="modal" data-target="#ms_<?= strtolower($MEDICAL_SERVICE['ManageServiceKey']) ?>_modal">Manage Services for App Home Screen</button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade" id="ms_<?= strtolower($MEDICAL_SERVICE['ManageServiceKey']) ?>_modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content nimot-class">
                                                    <div class="modal-header">
                                                        <h5>
                                                            Medical Services - <?= $MEDICAL_SERVICE["ServiceTitle"][$default_lang] ?>
                                                            <button type="button" class="close" data-dismiss="modal">x </button>
                                                        </h5>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>
                                                            <strong>Note:</strong>
                                                            Enable any <?= $MEDICAL_SERVICE['ManageServiceKey'] != 'MoreService' ? '2'  : '3' ?> service categories from below list to be shown on App home
                                                            screen. <?php if ($MEDICAL_SERVICE['ManageServiceKey'] != 'MoreService') { ?>All other service categories will be shown under more. <?php } ?>
                                                            <br>
                                                            Icons uploaded will only be shown on App home screen and not under more section.
                                                            <br><br>
                                                            <strong>Upload only png image size of 512px X 512px. <br> <?= IMAGE_INSTRUCTION_NOTES ?></strong>
                                                        </p>
                                                        <input type="hidden" name="<?= $MEDICAL_SERVICE["HiddenInput"] ?>" id="<?= $MEDICAL_SERVICE["HiddenInput"] ?>" value="No">
                                                        <table class="table table-striped table-bordered table-hover">
                                                            <thead>
                                                            <tr>
                                                                <th style="text-align: center;">Icon
                                                                </th>
                                                                <th>Service Category</th>
                                                                <th>Display Order</th>
                                                                <th>Upload Icon</th>
                                                                <th>Status</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php
                                                            foreach ($MEDICAL_SERVICE["ServicesArr"] as $ServiceMS) {
                                                                $vServiceImg = "";
                                                                $vServiceStatus = "";
                                                                $vServiceImgOld = "";
                                                                $vServiceDisplay = 'style="display: none"';
                                                                $vServiceDisplayOrder = "1";
                                                                if (isset($tServiceDetailsMSArr[$MEDICAL_SERVICE['ManageServiceKey']])) {
                                                                    $tServiceDetails = $tServiceDetailsMSArr[$MEDICAL_SERVICE['ManageServiceKey']]['iVehicleCategoryId_' . $ServiceMS['iVehicleCategoryId']];
                                                                    if (!empty($tServiceDetails['vImage'])) {
                                                                        $vServiceImg = $tconfig['tsite_url'] . 'resizeImg.php?w=50&src=' . $tconfig["tsite_upload_app_home_screen_images"] . 'AppHomeScreen/' . $tServiceDetails['vImage'];
                                                                    }
                                                                    $vServiceImgOld = $tServiceDetails['vImage'];
                                                                    if ($tServiceDetails['eStatus'] == "Active") {
                                                                        $vServiceStatus = "checked";
                                                                        $vServiceDisplay = "";
                                                                        $vServiceDisplayOrder = $tServiceDetails['iDisplayOrder'];
                                                                    }
                                                                }
                                                                ?>
                                                                <tr>
                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                        <?php if (!empty($vServiceImg)) { ?>
                                                                            <img src="<?= $vServiceImg ?>">
                                                                        <?php } else { ?>
                                                                            --
                                                                        <?php } ?>
                                                                    </td>
                                                                    <td style="vertical-align: middle;"><?= $ServiceMS['vCategoryName'] ?></td>
                                                                    <td>
                                                                        <select class="form-control" name="iDisplayOrder<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MSArr[]" <?= $vServiceDisplay ?>>
                                                                            <?php for ($disp_order = 1; $disp_order <= scount($OnDemandServicesArr); $disp_order++) { ?>
                                                                                <option value="<?= $disp_order ?>" <?= $vServiceDisplayOrder == $disp_order ? 'selected' : '' ?>><?= $disp_order ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input type="file" class="form-control" name="v<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MSImage[]" <?= $vServiceDisplay ?>>
                                                                        <input type="hidden" class="form-control" name="v<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MSImageOld[]" value="<?= $vServiceImgOld ?>">
                                                                    </td>
                                                                    <td>
                                                                        <div class="make-switch" data-on="success" data-off="warning">
                                                                            <input type="checkbox" name="iVehicleCategoryId<?= $MEDICAL_SERVICE['ManageServiceSuffix'] ?>[]" value="<?= $ServiceMS['iVehicleCategoryId'] ?>" <?= $vServiceStatus ?> />
                                                                        </div>
                                                                        <input type="hidden" name="iVehicleCategoryIdVal<?= $MEDICAL_SERVICE['ManageServiceSuffix'] ?>[]" value="<?= $ServiceMS['iVehicleCategoryId'] ?>">
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                            </tbody>
                                                        </table>
                                                        <a href="<?= $tconfig['tsite_url_main_admin'] ?>vehicle_category.php?eType=MedicalServices"
                                                           class="btn btn-info" target="_blank">Click
                                                            here to add more services
                                                        </a>
                                                    </div>
                                                    <div class="modal-footer" style="text-align: left">
                                                        <button type="button" class="btn btn-default"
                                                                onclick="saveMS<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>('Yes')">
                                                            Save
                                                        </button>
                                                        <button type="button" class="btn btn-default"
                                                                onclick="saveMS<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>('No')">
                                                            Cancel
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <button type="button" class="btn btn-primary save-section-btn" id="save<?= $MEDICAL_SERVICE['ManageServiceKey'] ?>MSSection">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php $ms_count++; } ?>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($MODULES_OBJ->isEnableTrackAnyServiceFeature()) { ?>
                        <hr />
                        <div class="show-help-section section-title">Track Your Members</div>
                        <div class="underline-section-title"></div>
                        <div class="row paddingbottom-0">
                            <div class="col-lg-12">
                                <div class="tab">
                                    <button class="tablinks manage-trackany-service-home-screen-tab active" onclick="openTabContent(event, 'manage-trackany-service-home-screen-content', 'tabcontent-trackany-service')">Home Screen</button>
                                    <button class="tablinks manage-trackany-service-info-screen-tab" onclick="openTabContent(event, 'manage-trackany-service-info-screen-content', 'tabcontent-trackany-service')">Info Screen</button>
                                </div>
                            </div>
                        </div>
                        <div class="tabcontent tabcontent-trackany-service display-tab-content" id="manage-trackany-service-home-screen-content">
                            <?php if (scount($db_master) > 1) { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Title</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control" id="vTrackServiceTitle_Default"
                                               name="vTrackServiceTitle_Default"
                                               value="<?= $userEditDataArr['vTrackServiceTitle_' . $default_lang]; ?>"
                                               data-originalvalue="<?= $userEditDataArr['vTrackServiceTitle_' . $default_lang]; ?>"
                                               readonly="readonly" required>
                                    </div>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                data-original-title="Edit" onclick="editTrackServiceTitle('Edit')">
                                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="modal fade" id="TrackServiceTitle_Modal" tabindex="-1" role="dialog" aria-hidden="true"
                                     data-backdrop="static" data-keyboard="false">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content nimot-class">
                                            <div class="modal-header">
                                                <h4>
                                                    <span id="trackservice_title_modal_action"></span>
                                                    Title
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'vTrackServiceTitle_')">x
                                                    </button>
                                                </h4>
                                            </div>
                                            <div class="modal-body">
                                                <?php
                                                for ($i = 0; $i < $count_all; $i++) {
                                                    $vCode = $db_master[$i]['vCode'];
                                                    $vTitle = $db_master[$i]['vTitle'];
                                                    $eDefault = $db_master[$i]['eDefault'];
                                                    $vValue = 'vTrackServiceTitle_' . $vCode;
                                                    $$vValue = $userEditDataArr[$vValue];
                                                    $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                    ?>
                                                    <?php
                                                    $page_title_class = 'col-lg-12';
                                                    if (scount($db_master) > 1) {
                                                        if ($EN_available) {
                                                            if ($vCode == "EN") {
                                                                $page_title_class = 'col-md-9 col-sm-9';
                                                            }
                                                        } else {
                                                            if ($vCode == $default_lang) {
                                                                $page_title_class = 'col-md-9 col-sm-9';
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label>Title (<?= $vTitle; ?>
                                                                ) <?php echo $required_msg; ?></label>
                                                        </div>
                                                        <div class="<?= $page_title_class ?>">
                                                            <input type="text" class="form-control" name="<?= $vValue; ?>"
                                                                   id="<?= $vValue; ?>" value="<?= $$vValue; ?>"
                                                                   data-originalvalue="<?= $$vValue; ?>"
                                                                   placeholder="<?= $vTitle; ?> Value">
                                                            <div class="text-danger" id="<?= $vValue . '_error'; ?>"
                                                                 style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                        </div>
                                                        <?php
                                                        if (scount($db_master) > 1) {
                                                            if ($EN_available) {
                                                                if ($vCode == "EN") { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vTrackServiceTitle_', 'EN');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                            } else {
                                                                if ($vCode == $default_lang) { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vTrackServiceTitle_', '<?= $default_lang ?>');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <div class="modal-footer" style="margin-top: 0">
                                                <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                    <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                                    </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                <div class="nimot-class-but" style="margin-bottom: 0">
                                                    <button type="button" class="save" style="margin-left: 0 !important"
                                                            onclick="saveTrackServiceTitle()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                    <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'vTrackServiceTitle_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                </div>
                                            </div>
                                            <div style="clear:both;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Subtitle</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control" id="vTrackServiceSubTitle_Default"
                                               name="vTrackServiceSubTitle_Default"
                                               value="<?= $userEditDataArr['vTrackServiceSubTitle_' . $default_lang]; ?>"
                                               data-originalvalue="<?= $userEditDataArr['vTrackServiceSubTitle_' . $default_lang]; ?>"
                                               readonly="readonly" required>
                                    </div>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                data-original-title="Edit" onclick="editTrackServiceSubTitle('Edit')">
                                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="modal fade" id="TrackServiceSubTitle_Modal" tabindex="-1" role="dialog"
                                     aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content nimot-class">
                                            <div class="modal-header">
                                                <h4>
                                                    <span id="trackservice_subtitle_modal_action"></span>
                                                    Title
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'vTrackServiceSubTitle_')">x
                                                    </button>
                                                </h4>
                                            </div>
                                            <div class="modal-body">
                                                <?php
                                                for ($i = 0; $i < $count_all; $i++) {
                                                    $vCode = $db_master[$i]['vCode'];
                                                    $vTitle = $db_master[$i]['vTitle'];
                                                    $eDefault = $db_master[$i]['eDefault'];
                                                    $vValue = 'vTrackServiceSubTitle_' . $vCode;
                                                    $$vValue = $userEditDataArr[$vValue];
                                                    $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                    ?>
                                                    <?php
                                                    $page_title_class = 'col-lg-12';
                                                    if (scount($db_master) > 1) {
                                                        if ($EN_available) {
                                                            if ($vCode == "EN") {
                                                                $page_title_class = 'col-md-9 col-sm-9';
                                                            }
                                                        } else {
                                                            if ($vCode == $default_lang) {
                                                                $page_title_class = 'col-md-9 col-sm-9';
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label>Subtitle (<?= $vTitle; ?>
                                                                ) <?php echo $required_msg; ?></label>
                                                        </div>
                                                        <div class="<?= $page_title_class ?>">
                                                            <input type="text" class="form-control" name="<?= $vValue; ?>"
                                                                   id="<?= $vValue; ?>" value="<?= $$vValue; ?>"
                                                                   data-originalvalue="<?= $$vValue; ?>"
                                                                   placeholder="<?= $vTitle; ?> Value">
                                                            <div class="text-danger" id="<?= $vValue . '_error'; ?>"
                                                                 style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                        </div>
                                                        <?php
                                                        if (scount($db_master) > 1) {
                                                            if ($EN_available) {
                                                                if ($vCode == "EN") { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vTrackServiceSubTitle_', 'EN');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                            } else {
                                                                if ($vCode == $default_lang) { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vTrackServiceSubTitle_', '<?= $default_lang ?>');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <div class="modal-footer" style="margin-top: 0">
                                                <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                    <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                                    </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                <div class="nimot-class-but" style="margin-bottom: 0">
                                                    <button type="button" class="save" style="margin-left: 0 !important"
                                                            onclick="saveTrackServiceSubTitle()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                    <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'vTrackServiceSubTitle_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                </div>
                                            </div>
                                            <div style="clear:both;"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Title</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control" id="vTrackServiceTitle_<?= $default_lang ?>"
                                               name="vTrackServiceTitle_<?= $default_lang ?>"
                                               value="<?= $userEditDataArr['vTrackServiceTitle_' . $default_lang]; ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Subtitle</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control" id="vTrackServiceSubTitle_<?= $default_lang ?>"
                                               name="vTrackServiceSubTitle_<?= $default_lang ?>"
                                               value="<?= $userEditDataArr['vTrackServiceSubTitle_' . $default_lang]; ?>">
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Title Color</label>
                                </div>
                                <div class="col-md-1 col-sm-1">
                                    <input type="color" data-id="vTitleColorTrackService" class="form-control txt-color" value="<?= $vTitleColorTrackService ?>"/>
                                    <input type="hidden" name="vTitleColorTrackService" id="vTitleColorTrackService" value="<?= $vTitleColorTrackService ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Subtitle Color</label>
                                </div>
                                <div class="col-md-1 col-sm-1">
                                    <input type="color" data-id="vSubTitleColorTrackService" class="form-control txt-color" value="<?= $vSubTitleColorTrackService ?>"/>
                                    <input type="hidden" name="vSubTitleColorTrackService" id="vSubTitleColorTrackService" value="<?= $vSubTitleColorTrackService ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Background Color</label>
                                </div>
                                <div class="col-md-1 col-sm-1">
                                    <input type="color" data-id="vBgColorTrackService" class="form-control bg-color" value="<?= $vBgColorTrackService ?>"/>
                                    <input type="hidden" name="vBgColorTrackService" id="vBgColorTrackService" value="<?= $vBgColorTrackService ?>">
                                </div>
                            </div>

                            <div class="row pb-10">
                                <div class="col-lg-12">
                                    <label>Image</label>
                                </div>
                                <div class="col-md-4 col-sm-4 marginbottom-10">
                                    <?php if(!empty($vImageOldTrackService)) { ?>
                                    <div class="marginbottom-10">
                                        <img src="<?=$tconfig["tsite_url"].'resizeImg.php?h=100&src=' . $tconfig['tsite_upload_app_home_screen_images'] . 'AppHomeScreen/' . $vImageOldTrackService; ?>" id="trackservice_img">
                                    </div>
                                    <?php } ?>
                                    <input type="file" class="form-control" name="vImageTrackService" id="vImageTrackService" onchange="previewImage(this, event);" data-img="trackservice_img">
                                    <input type="hidden" class="form-control" name="vImageOldTrackService" id="vImageOldTrackService" value="<?= $vImageOldTrackService ?>">
                                    <strong class="img-note">Note: Upload only png image size of 512px X 512px. <br> <?= IMAGE_INSTRUCTION_NOTES ?> </strong>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <button type="button" class="btn btn-primary save-section-btn" id="saveTrackServiceSection">Save</button>
                                </div>
                            </div>
                        </div>

                        <div class="tabcontent tabcontent-trackany-service" id="manage-trackany-service-info-screen-content">
                            <?php foreach($userEditDataArrNew as $Tkey=>$userEditDataArrTrack) { 
                                if (scount($db_master) > 1) { ?>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label>Title</label>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <input type="text" class="form-control" id="vTrackServiceCategory<?php echo $Tkey;?>_Default"
                                                   name="vTrackServiceCategory<?php echo $Tkey;?>_Default"
                                                   value="<?= $userEditDataArrTrack['vTrackServiceCategory_' . $default_lang]; ?>"
                                                   data-originalvalue="<?= $userEditDataArrTrack['vTrackServiceCategory_' . $default_lang]; ?>"
                                                   readonly="readonly" required>
                                            <input type="hidden" name="iTrackServiceCategoryId<?php echo $Tkey;?>" id="iTrackServiceCategoryId<?php echo $Tkey;?>" value="<?php echo $userEditDataArrTrack['iTrackServiceCategoryId']?>">
                                        </div>
                                        <div class="col-lg-2">
                                            <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editTrackServiceCategoryTitle('Edit','<?php echo $Tkey;?>')">
                                                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="TrackServiceTitle<?php echo $Tkey;?>_Modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content nimot-class">
                                                <div class="modal-header">
                                                    <h4>
                                                        <span id="trackservice_title_<?php echo $Tkey;?>_modal_action"></span> Title
                                                        <button type="button" class="close" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vTrackServiceCategory<?php echo $Tkey;?>_')">x
                                                        </button>
                                                    </h4>
                                                </div>
                                                <div class="modal-body">
                                                    <?php
                                                    for ($i = 0; $i < $count_all; $i++) {
                                                        $vCode = $db_master[$i]['vCode'];
                                                        $vTitle = $db_master[$i]['vTitle'];
                                                        $eDefault = $db_master[$i]['eDefault'];
                                                        $vValue = 'vTrackServiceCategory'.$Tkey.'_' . $vCode;
                                                        $$vValue = $userEditDataArrTrack['vTrackServiceCategory_' . $vCode];
                                                        $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                        ?>
                                                        <?php
                                                        $page_title_class = 'col-lg-12';
                                                        if (scount($db_master) > 1) {
                                                            if ($EN_available) {
                                                                if ($vCode == "EN") {
                                                                    $page_title_class = 'col-md-9 col-sm-9';
                                                                }
                                                            } else {
                                                                if ($vCode == $default_lang) {
                                                                    $page_title_class = 'col-md-9 col-sm-9';
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <label>Title (<?= $vTitle; ?>
                                                                    ) <?php echo $required_msg; ?></label>
                                                            </div>
                                                            <div class="<?= $page_title_class ?>">
                                                                <input type="text" class="form-control" name="<?= $vValue; ?>"
                                                                       id="<?= $vValue; ?>" value="<?= $$vValue; ?>"
                                                                       data-originalvalue="<?= $$vValue; ?>"
                                                                       placeholder="<?= $vTitle; ?> Value">
                                                                <div class="text-danger" id="<?= $vValue . '_error'; ?>"
                                                                     style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                            </div>
                                                            <?php
                                                            if (scount($db_master) > 1) {
                                                                if ($EN_available) {
                                                                    if ($vCode == "EN") { ?>
                                                                        <div class="col-md-3 col-sm-3">
                                                                            <button type="button" name="allLanguage"
                                                                                    id="allLanguage" class="btn btn-primary"
                                                                                    onClick="getAllLanguageCode('vTrackServiceCategory<?php echo $Tkey;?>_', 'EN');">
                                                                                Convert To All Language
                                                                            </button>
                                                                        </div>
                                                                    <?php }
                                                                } else {
                                                                    if ($vCode == $default_lang) { ?>
                                                                        <div class="col-md-3 col-sm-3">
                                                                            <button type="button" name="allLanguage"
                                                                                    id="allLanguage" class="btn btn-primary"
                                                                                    onClick="getAllLanguageCode('vTrackServiceCategor<?php echo $Tkey;?>y_', '<?= $default_lang ?>');">
                                                                                Convert To All Language
                                                                            </button>
                                                                        </div>
                                                                    <?php }
                                                                }
                                                            }
                                                            ?>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                                <div class="modal-footer" style="margin-top: 0">
                                                    <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                        <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                                        </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                    <div class="nimot-class-but" style="margin-bottom: 0">
                                                        <button type="button" class="save" style="margin-left: 0 !important"
                                                                onclick="saveTrackServiceCategoryTitle(<?php echo $Tkey;?>)"><?= $langage_lbl['LBL_Save']; ?></button>
                                                        <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                                onclick="resetToOriginalValue(this, 'vTrackServiceCategory<?php echo $Tkey;?>_',)"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                    </div>
                                                </div>
                                                <div style="clear:both;"></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label>Title</label>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <input type="text" class="form-control" id="vTrackServiceCategory<?php echo $Tkey;?>_<?= $default_lang ?>" name="vTrackServiceCategory<?php echo $Tkey;?>_<?= $default_lang ?>" value="<?= $userEditDataArrTrack['vTrackServiceCategory_' . $default_lang]; ?>">
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="row pb-10">
                                    <div class="col-lg-12">
                                        <label>Image</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4 marginbottom-10">
                                        <?php if(!empty($userEditDataArrTrack['vImage'])) { ?>
                                        <div class="marginbottom-10">
                                            <img src="<?=$tconfig["tsite_url"].'resizeImg.php?h=100&src=' . $tconfig['tsite_upload_app_home_screen_images'] . 'AppHomeScreen/' . $userEditDataArrTrack['vImage']; ?>" id="trackservice_img<?php echo $Tkey;?>">
                                        </div>
                                        <?php } ?>
                                        <input type="file" class="form-control" name="vImageTrackService<?php echo $Tkey;?>" id="vImageTrackService<?php echo $Tkey;?>" onchange="previewImage(this, event);" data-img="trackservice_img<?php echo $Tkey;?>">
                                        <input type="hidden" class="form-control" name="vImageOldTrackService<?php echo $Tkey;?>" id="vImageOldTrackService<?php echo $Tkey;?>" value="<?= $userEditDataArrTrack['vImage'] ?>">
                                        <strong class="img-note">Note: Upload only png image size of 512px X 512px. <br> <?= IMAGE_INSTRUCTION_NOTES ?> </strong>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="row">
                                <div class="col-lg-12">
                                    <button type="button" class="btn btn-primary save-section-btn" id="saveTrackServiceCategorySection" >Save</button>
                                </div>
                            </div>
                        </div>

                    <?php } ?>

                    <?php if ($MODULES_OBJ->isEnableNearByService()) { ?>
                        <hr />
                        <div class="show-help-section section-title">Nearby Services</div>
                        <div class="underline-section-title"></div>
                        <?php if (scount($db_master) > 1) { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Title</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="vNearbyServiceTitle_Default"
                                           name="vNearbyServiceTitle_Default"
                                           value="<?= $userEditDataArr['vNearbyServiceTitle_' . $default_lang]; ?>"
                                           data-originalvalue="<?= $userEditDataArr['vNearbyServiceTitle_' . $default_lang]; ?>"
                                           readonly="readonly" required>
                                </div>
                                <div class="col-lg-2">
                                    <button type="button" class="btn btn-info" data-toggle="tooltip"
                                            data-original-title="Edit" onclick="editNearbyServiceTitle('Edit')">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>
                            <div class="modal fade" id="NearbyServiceTitle_Modal" tabindex="-1" role="dialog" aria-hidden="true"
                                 data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content nimot-class">
                                        <div class="modal-header">
                                            <h4>
                                                <span id="nearbyservice_title_modal_action"></span>
                                                Title
                                                <button type="button" class="close" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vNearbyServiceTitle_')">x
                                                </button>
                                            </h4>
                                        </div>
                                        <div class="modal-body">
                                            <?php
                                            for ($i = 0; $i < $count_all; $i++) {
                                                $vCode = $db_master[$i]['vCode'];
                                                $vTitle = $db_master[$i]['vTitle'];
                                                $eDefault = $db_master[$i]['eDefault'];
                                                $vValue = 'vNearbyServiceTitle_' . $vCode;
                                                $$vValue = $userEditDataArr[$vValue];
                                                $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                ?>
                                                <?php
                                                $page_title_class = 'col-lg-12';
                                                if (scount($db_master) > 1) {
                                                    if ($EN_available) {
                                                        if ($vCode == "EN") {
                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                        }
                                                    } else {
                                                        if ($vCode == $default_lang) {
                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                        }
                                                    }
                                                }
                                                ?>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <label>Title (<?= $vTitle; ?>
                                                            ) <?php echo $required_msg; ?></label>
                                                    </div>
                                                    <div class="<?= $page_title_class ?>">
                                                        <input type="text" class="form-control" name="<?= $vValue; ?>"
                                                               id="<?= $vValue; ?>" value="<?= $$vValue; ?>"
                                                               data-originalvalue="<?= $$vValue; ?>"
                                                               placeholder="<?= $vTitle; ?> Value">
                                                        <div class="text-danger" id="<?= $vValue . '_error'; ?>"
                                                             style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                    </div>
                                                    <?php
                                                    if (scount($db_master) > 1) {
                                                        if ($EN_available) {
                                                            if ($vCode == "EN") { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vNearbyServiceTitle_', 'EN');">
                                                                        Convert To All Language
                                                                    </button>
                                                                </div>
                                                            <?php }
                                                        } else {
                                                            if ($vCode == $default_lang) { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vNearbyServiceTitle_', '<?= $default_lang ?>');">
                                                                        Convert To All Language
                                                                    </button>
                                                                </div>
                                                            <?php }
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <div class="modal-footer" style="margin-top: 0">
                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                <strong><?= $langage_lbl['LBL_NOTE']; ?>:
                                                </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                <button type="button" class="save" style="margin-left: 0 !important"
                                                        onclick="saveNearbyServiceTitle()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vNearbyServiceTitle_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                            </div>
                                        </div>
                                        <div style="clear:both;"></div>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Title</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="vNearbyServiceTitle_<?= $default_lang ?>"
                                           name="vNearbyServiceTitle_<?= $default_lang ?>"
                                           value="<?= $userEditDataArr['vNearbyServiceTitle_' . $default_lang]; ?>">
                                </div>
                            </div>
                        <?php } ?>

                        <div class="row">
                            <div class="col-lg-12">
                                <label>Services</label>
                            </div>
                            <div class="col-lg-6">
                                <div class="manage-banner-section">
                                    <div class="service-img-block">
                                    <?php foreach ($nearbyData as $nearByArr) {
                                        if (isset($tServiceDetailsNearbyArr['iCategoryId_' . $nearByArr['iCategoryId']])) {
                                            $tServiceDetails = $tServiceDetailsNearbyArr['iCategoryId_' . $nearByArr['iCategoryId']];
                                            if (!empty($tServiceDetails['vImage'])) {
                                                $vServiceImg = $tconfig['tsite_url'] . 'resizeImg.php?w=60&src=' . $tconfig["tsite_upload_app_home_screen_images"] . 'AppHomeScreen/' . $tServiceDetails['vImage'];
                                            }
                                            $vServiceImgOld = $tServiceDetails['vImage'];
                                            if ($tServiceDetails['eStatus'] == "Active") {
                                        
                                    ?>
                                        <div class="service-preview-img">
                                            <img src="<?= $vServiceImg ?>">
                                            <div class="service-img-title"><?= $nearByArr['vCategory'] ?></div>
                                        </div>
                                    
                                    <?php }} } ?>
                                    <div class="service-preview-img">
                                        <img src="<?= $tconfig['tsite_url'] . 'resizeImg.php?w=60&src=' . $tconfig["tsite_url"] . "webimages/icons/DefaultImg/ic_more_near_by.png" ?>">
                                        <div class="service-img-title">More</div>
                                    </div>
                                    </div>
                                    <button type="button" class="manage-banner-btn manage-icon-btn" data-toggle="modal" data-target="#nearbyservices_modal">Manage Services for App Home Screen</button>
                                </div>
                            </div>                            
                        </div>

                        <div class="modal fade" id="nearbyservices_modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content nimot-class">
                                    <div class="modal-header">
                                        <h4>
                                            Nearby Categories
                                            <button type="button" class="close" data-dismiss="modal">x</button>
                                        </h4>
                                    </div>
                                    <div class="modal-body">
                                        <p>
                                            <strong>Note:</strong>
                                            Enable any 3 nearby categories from below list to be shown on App
                                            home screen. All other nearby categories will be shown under more.
                                            <br>
                                            Icons uploaded will only be shown on App home screen and not under
                                            more section.
                                            <br><br>
                                            <strong>Upload only png image size of 512px X 512px. <br> <?= IMAGE_INSTRUCTION_NOTES ?></strong>
                                        </p>
                                        <input type="hidden" name="saveNearbyServices" id="saveNearbyServices" value="No">
                                        <table class="table table-striped table-bordered table-hover">
                                            <thead>
                                            <tr>
                                                <th style="text-align: center;">Icon</th>
                                                <th>Nearby Category</th>
                                                <th>Display Order</th>
                                                <th>Upload Icon</th>
                                                <th>Status</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            foreach ($nearbyData as $nearByArr) {
                                                $vServiceImg = "";
                                                $vServiceStatus = "";
                                                $vServiceImgOld = "";
                                                $vServiceDisplay = 'style="display: none"';
                                                $vServiceDisplayOrder = "1";
                                                if (isset($tServiceDetailsNearbyArr['iCategoryId_' . $nearByArr['iCategoryId']])) {
                                                    $tServiceDetails = $tServiceDetailsNearbyArr['iCategoryId_' . $nearByArr['iCategoryId']];
                                                    if (!empty($tServiceDetails['vImage'])) {
                                                        $vServiceImg = $tconfig['tsite_url'] . 'resizeImg.php?w=50&src=' . $tconfig["tsite_upload_app_home_screen_images"] . 'AppHomeScreen/' . $tServiceDetails['vImage'];
                                                    }
                                                    $vServiceImgOld = $tServiceDetails['vImage'];
                                                    if ($tServiceDetails['eStatus'] == "Active") {
                                                        $vServiceStatus = "checked";
                                                        $vServiceDisplay = "";
                                                        $vServiceDisplayOrder = $tServiceDetails['iDisplayOrder'];
                                                    }
                                                }
                                                ?>
                                                <tr>
                                                    <td style="text-align: center; vertical-align: middle;">
                                                        <?php if (!empty($vServiceImg)) { ?>
                                                            <img src="<?= $vServiceImg ?>">
                                                        <?php } else { ?>
                                                            --
                                                        <?php } ?>
                                                    </td>
                                                    <td style="vertical-align: middle;"><?= $nearByArr['vCategory'] ?></td>
                                                    <td>
                                                        <select class="form-control" name="iDisplayOrderNearbyArr[]" <?= $vServiceDisplay ?>>
                                                            <?php for ($disp_order = 1; $disp_order <= scount($nearbyData); $disp_order++) { ?>
                                                                <option value="<?= $disp_order ?>" <?= $vServiceDisplayOrder == $disp_order ? 'selected' : '' ?>><?= $disp_order ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="file" class="form-control" name="vNearbyImage[]" <?= $vServiceDisplay ?>>
                                                        <input type="hidden" class="form-control" name="vNearbyImageOld[]" value="<?= $vServiceImgOld ?>">
                                                    </td>
                                                    <td>
                                                        <div class="make-switch" data-on="success" data-off="warning">
                                                            <input type="checkbox" name="iCategoryId[]" value="<?= $nearByArr['iCategoryId'] ?>" <?= $vServiceStatus ?> />
                                                        </div>
                                                        <input type="hidden" name="iCategoryIdVal[]" value="<?= $nearByArr['iCategoryId'] ?>">
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="modal-footer" style="text-align: left">
                                        <button type="button" class="btn btn-default" onclick="saveServicesNearby('Yes')">Save
                                        </button>
                                        <button type="button" class="btn btn-default" onclick="saveServicesNearby('No')">Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-lg-12">
                                <button type="button" class="btn btn-primary save-section-btn" id="saveNearbyServiceSection">Save</button>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <!--END PAGE CONTENT -->
</div>


<!--END MAIN WRAPPER -->
<div class="row loding-action" id="loaderIcon" style="display:none;">
    <div>
        <img src="default.gif">
        <span>Language Translation is in Process. Please Wait...</span>
    </div>
</div>
<? include_once('footer.php'); ?>
<script type="text/javascript" src="js/fancybox.umd.js"></script>
<script type="text/javascript" src="../assets/js/jquery-ui.min.js"></script>
<script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
<script src="../assets/plugins/ckeditor/ckeditor.js"></script>
<script src="../assets/js/modal_alert.js"></script>
<script type="text/javascript">
    $('.ckeditor').each(function(e){
        CKEDITOR.replace(this.id, {
            toolbarGroups: [
                { name: 'insert'},
                { name: 'paragraph',   groups: [ 'list', 'align' ] },
            ]
        });
    });

    function editIntroTitle(action) {
        $('#intro_title_modal_action').html(action);
        $('#IntroTitle_Modal').modal('show');
    }

    function saveIntroTitle() {
        if ($('#vIntroTitle_<?= $default_lang ?>').val() == "") {
            $('#vIntroTitle_<?= $default_lang ?>_error').show();
            $('#vIntroTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vIntroTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vIntroTitle_Default').val($('#vIntroTitle_<?= $default_lang ?>').val());
        $('#vIntroTitle_Default').closest('.row').removeClass('has-error');
        $('#vIntroTitle_Default-error').remove();
        $('#IntroTitle_Modal').modal('hide');
    }

    function editIntroSubTitle(action) {
        $('#intro_subtitle_modal_action').html(action);
        $('#IntroSubTitle_Modal').modal('show');
    }

    function saveIntroSubTitle() {
        if ($('#vIntroSubTitle_<?= $default_lang ?>').val() == "") {
            $('#vIntroSubTitle_<?= $default_lang ?>_error').show();
            $('#vIntroSubTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vIntroSubTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vIntroSubTitle_Default').val($('#vIntroSubTitle_<?= $default_lang ?>').val());
        $('#vIntroSubTitle_Default').closest('.row').removeClass('has-error');
        $('#vIntroSubTitle_Default-error').remove();
        $('#IntroSubTitle_Modal').modal('hide');
    }

    $('#saveIntroSection').click(function() {
        var vIntroTitleArr = $('[name^="vIntroTitle_"]').serializeArray();
        var vTitleArr = {};
        $.each(vIntroTitleArr, function(key, value) {
            if(value.name != "vIntroTitle_Default") {
                var name_key = value.name.replace('vIntroTitle', 'vTitle');
                vTitleArr[name_key] = value.value;
            }
        });

        var vIntroSubTitleArr = $('[name^="vIntroSubTitle_"]').serializeArray();
        var vSubTitleArr = {};
        $.each(vIntroSubTitleArr, function(key, value) {
            if(value.name != "vIntroSubTitle_Default") {
                var name_key = value.name.replace('vIntroSubTitle', 'vSubtitle');
                vSubTitleArr[name_key] = value.value;
            }
        });

        var postData = {};
        postData['vTitleArr'] = vTitleArr;
        postData['vSubTitleArr'] = vSubTitleArr;
        postData['ViewType'] = 'TitleView';
        saveHomeScreenData('saveIntroSection', postData, 'No');
    });

    function editTaxiBookingTitle(action) {
        $('#taxibooking_title_modal_action').html(action);
        $('#TaxiBookingTitle_Modal').modal('show');
    }

    function saveTaxiBookingTitle() {
        if ($('#vTaxiBookingTitle_<?= $default_lang ?>').val() == "") {
            $('#vTaxiBookingTitle_<?= $default_lang ?>_error').show();
            $('#vTaxiBookingTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vTaxiBookingTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vTaxiBookingTitle_Default').val($('#vTaxiBookingTitle_<?= $default_lang ?>').val());
        $('#vTaxiBookingTitle_Default').closest('.row').removeClass('has-error');
        $('#vTaxiBookingTitle_Default-error').remove();
        $('#TaxiBookingTitle_Modal').modal('hide');
    }

    $('#saveTaxiBookingSection').click(function() {
        var vTaxiBookingTitleArr = $('[name^="vTaxiBookingTitle_"]').serializeArray();
        var vTitleArr = {};
        $.each(vTaxiBookingTitleArr, function(key, value) {
            if(value.name != "vTaxiBookingTitle_Default") {
                var name_key = value.name.replace('vTaxiBookingTitle', 'vCategoryName');
                vTitleArr[name_key] = value.value;
            }
        });

        var vImageTaxiBooking = $('#vImageTaxiBooking')[0].files[0];
        var vImageOldTaxiBooking = $('#vImageOldTaxiBooking').val();

        var postData = new FormData();
        postData.append('vTitleArr', JSON.stringify(vTitleArr));
        postData.append('vImage', vImageTaxiBooking);
        postData.append('vImageOld', vImageOldTaxiBooking);
        postData.append('ViewType', 'TextBannerView');
        postData.append('ServiceType', 'Ride');

        saveHomeScreenData('saveTaxiBookingSection', postData);
    });

    function editDeliverAllTitle(action) {
        $('#deliverall_title_modal_action').html(action);
        $('#DeliverAllTitle_Modal').modal('show');
    }

    function saveDeliverAllTitle() {
        if ($('#vDeliverAllTitle_<?= $default_lang ?>').val() == "") {
            $('#vDeliverAllTitle_<?= $default_lang ?>_error').show();
            $('#vDeliverAllTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vDeliverAllTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vDeliverAllTitle_Default').val($('#vDeliverAllTitle_<?= $default_lang ?>').val());
        $('#vDeliverAllTitle_Default').closest('.row').removeClass('has-error');
        $('#vDeliverAllTitle_Default-error').remove();
        $('#DeliverAllTitle_Modal').modal('hide');
    }

    $('#saveDeliverAllSection').click(function() {
        var vDeliverAllTitleArr = $('[name^="vDeliverAllTitle_"]').serializeArray();
        var vTitleArr = {};
        $.each(vDeliverAllTitleArr, function(key, value) {
            if(value.name != "vDeliverAllTitle_Default") {
                var name_key = value.name.replace('vDeliverAllTitle', 'vCategoryName');
                vTitleArr[name_key] = value.value;
            }
        });

        var vImageDeliverAll = $('#vImageDeliverAll')[0].files[0];
        var vImageOldDeliverAll = $('#vImageOldDeliverAll').val();

        var postData = new FormData();
        postData.append('vTitleArr', JSON.stringify(vTitleArr));
        postData.append('vImage', vImageDeliverAll);
        postData.append('vImageOld', vImageOldDeliverAll);
        postData.append('ViewType', 'TextBannerView');
        postData.append('ServiceType', 'DeliverAll');

        saveHomeScreenData('saveDeliverAllSection', postData);
    });

    function editDeliveryTitle(action) {
        $('#delivery_title_modal_action').html(action);
        $('#DeliveryTitle_Modal').modal('show');
    }

    function saveDeliveryTitle() {
        if ($('#vDeliveryTitle_<?= $default_lang ?>').val() == "") {
            $('#vDeliveryTitle_<?= $default_lang ?>_error').show();
            $('#vDeliveryTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vDeliveryTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vDeliveryTitle_Default').val($('#vDeliveryTitle_<?= $default_lang ?>').val());
        $('#vDeliveryTitle_Default').closest('.row').removeClass('has-error');
        $('#vDeliveryTitle_Default-error').remove();
        $('#DeliveryTitle_Modal').modal('hide');
    }

    $('#saveDeliverySection').click(function() {
        var vDeliveryTitleArr = $('[name^="vDeliveryTitle_"]').serializeArray();
        var vTitleArr = {};
        $.each(vDeliveryTitleArr, function(key, value) {
            if(value.name != "vDeliveryTitle_Default") {
                var name_key = value.name.replace('vDeliveryTitle', 'vCategoryName');
                vTitleArr[name_key] = value.value;
            }
        });

        var vImageDelivery = $('#vImageDelivery')[0].files[0];
        var vImageOldDelivery = $('#vImageOldDelivery').val();

        var postData = new FormData();
        postData.append('vTitleArr', JSON.stringify(vTitleArr));
        postData.append('vImage', vImageDelivery);
        postData.append('vImageOld', vImageOldDelivery);
        postData.append('ViewType', 'TextBannerView');
        postData.append('ServiceType', 'Deliver');

        saveHomeScreenData('saveDeliverySection', postData);
    });

    function editTaxiBidTitle(action) {
        $('#taxibid_title_modal_action').html(action);
        $('#TaxiBidTitle_Modal').modal('show');
    }

    function saveTaxiBidTitle() {
        if ($('#vTaxiBidTitle_<?= $default_lang ?>').val() == "") {
            $('#vTaxiBidTitle_<?= $default_lang ?>_error').show();
            $('#vTaxiBidTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vTaxiBidTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vTaxiBidTitle_Default').val($('#vTaxiBidTitle_<?= $default_lang ?>').val());
        $('#vTaxiBidTitle_Default').closest('.row').removeClass('has-error');
        $('#vTaxiBidTitle_Default-error').remove();
        $('#TaxiBidTitle_Modal').modal('hide');
    }

    function editBtnTxt(action) {
        $('#btntxt_modal_action').html(action);
        $('#BtnTxt_Modal').modal('show');
    }

    function saveBtnTxt() {
        if ($('#vBtnTxt_<?= $default_lang ?>').val() == "") {
            $('#vBtnTxt_<?= $default_lang ?>_error').show();
            $('#vBtnTxt_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vBtnTxt_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vBtnTxt_Default').val($('#vBtnTxt_<?= $default_lang ?>').val());
        $('#vBtnTxt_Default').closest('.row').removeClass('has-error');
        $('#vBtnTxt_Default-error').remove();
        $('#BtnTxt_Modal').modal('hide');
    }

    $('#saveTaxiBidSection').click(function() {
        var vTaxiBidTitleArr = $('[name^="vTaxiBidTitle_"]').serializeArray();
        var vTitleArr = {};
        $.each(vTaxiBidTitleArr, function(key, value) {
            if(value.name != "vTaxiBidTitle_Default") {
                var name_key = value.name.replace('vTaxiBidTitle', 'vTitle');
                vTitleArr[name_key] = value.value;
            }
        });

        var vBtnTxtTaxiBidArr = $('[name^="vBtnTxt_"]').serializeArray();
        var vBtnTxtArr = {};
        $.each(vBtnTxtTaxiBidArr, function(key, value) {
            if(value.name != "vBtnTxt_Default") {
                var name_key = value.name;
                vBtnTxtArr[name_key] = value.value;
            }
        });

        var vImageTaxiBid = $('#vImageTaxiBid')[0].files[0];
        var vImageOldTaxiBid = $('#vImageOldTaxiBid').val();
        var vTitleColorTaxiBid = $('#vTitleColorTaxiBid').val();
        var vBgColorTaxiBid = $('#vBgColorTaxiBid').val();

        var postData = new FormData();
        postData.append('vTitleArr', JSON.stringify(vTitleArr));
        postData.append('vBtnTxtArr', JSON.stringify(vBtnTxtArr));
        postData.append('vImage', vImageTaxiBid);
        postData.append('vImageOld', vImageOldTaxiBid);
        postData.append('vTxtTitleColor', vTitleColorTaxiBid);
        postData.append('vBgColor', vBgColorTaxiBid);
        postData.append('ViewType', 'TextBannerView');
        postData.append('ServiceType', 'TaxiBid');

        saveHomeScreenData('saveTaxiBidSection', postData);
    });


    function editTaxiBidInfoTitle(action) {
        $('#taxibid_infotitle_modal_action').html(action);
        $('#TaxiBidInfoTitle_Modal').modal('show');
    }

    function saveTaxiBidInfoTitle() {
        if ($('#vTaxiBidInfoTitle_<?= $default_lang ?>').val() == "") {
            $('#vTaxiBidInfoTitle_<?= $default_lang ?>_error').show();
            $('#vTaxiBidInfoTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vTaxiBidInfoTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vTaxiBidInfoTitle_Default').val($('#vTaxiBidInfoTitle_<?= $default_lang ?>').val());
        $('#vTaxiBidInfoTitle_Default').closest('.row').removeClass('has-error');
        $('#vTaxiBidInfoTitle_Default-error').remove();
        $('#TaxiBidInfoTitle_Modal').modal('hide');
    }

    function editTaxiBidInfoSubTitle(action) {
        $('#taxibid_infosubtitle_modal_action').html(action);
        $('#TaxiBidInfoSubTitle_Modal').modal('show');
    }

    function saveTaxiBidInfoSubTitle(input_id, modal_id) {
        var DescLength = CKEDITOR.instances[input_id+'<?= $default_lang ?>'].getData().replace(/<[^>]*>/gi, '').length;
        if(!DescLength) {
            $('#'+input_id+'<?= $default_lang ?>_error').show();
            $('#'+input_id+'<?= $default_lang ?>').focus();
            clearInterval(myVar);
            myVar = setTimeout(function() {
                $('#'+input_id+'<?= $default_lang ?>_error').hide();
            }, 5000);
            e.preventDefault();
            return false;
        }

        var DescHTML = CKEDITOR.instances[input_id + '<?= $default_lang ?>'].getData();
        CKEDITOR.instances[input_id+'Default'].setData(DescHTML);
        $('#'+modal_id).modal('hide');
    }

    $('#saveTaxiBidInfoSection').click(function() {
        var vTaxiBidInfoTitleArr = $('[name^="vTaxiBidInfoTitle_"]').serializeArray();
        var vTitleArr = {};
        $.each(vTaxiBidInfoTitleArr, function(key, value) {
            if(value.name != "vTaxiBidInfoTitle_Default") {
                var name_key = value.name.replace('vTaxiBidInfoTitle', 'vTitle');
                vTitleArr[name_key] = value.value;
            }
        });

        var vTaxiBidInfoSubTitleArr = $('[name^="vTaxiBidInfoSubTitle_"]').serializeArray();
        var vSubTitleArr = {};
        $.each(vTaxiBidInfoSubTitleArr, function(key, value) {
            if(value.name != "vTaxiBidInfoSubTitle_Default") {
                var name_key = value.name.replace('vTaxiBidInfoSubTitle', 'vSubtitle');
                vSubTitleArr[name_key] = CKEDITOR.instances[value.name].getData();;
            }
        });
        var vImageTaxiBidInfo = $('#vImageTaxiBidInfo')[0].files[0];
        var vImageOldTaxiBidInfo = $('#vImageOldTaxiBidInfo').val();

        var postData = new FormData();
        postData.append('vTitleArr', JSON.stringify(vTitleArr));
        postData.append('vSubTitleArr', JSON.stringify(vSubTitleArr));
        postData.append('vImage', vImageTaxiBidInfo);
        postData.append('vImageOld', vImageOldTaxiBidInfo);
        postData.append('ViewType', 'GridView');
        postData.append('ServiceType', 'TaxiBid');
        postData.append('ServiceTypeOther', 'TaxiBidInfo');

        saveHomeScreenData('saveTaxiBidInfoSection', postData);
    });


    function editGenieTitle(action) {
        $('#genie_title_modal_action').html(action);
        $('#GenieTitle_Modal').modal('show');
    }

    function saveGenieTitle() {
        if ($('#vGenieTitle_<?= $default_lang ?>').val() == "") {
            $('#vGenieTitle_<?= $default_lang ?>_error').show();
            $('#vGenieTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vGenieTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vGenieTitle_Default').val($('#vGenieTitle_<?= $default_lang ?>').val());
        $('#vGenieTitle_Default').closest('.row').removeClass('has-error');
        $('#vGenieTitle_Default-error').remove();
        $('#GenieTitle_Modal').modal('hide');
    }

    function editGenieSubTitle(action) {
        $('#genie_subtitle_modal_action').html(action);
        $('#GenieSubTitle_Modal').modal('show');
    }

    function saveGenieSubTitle() {
        if ($('#vGenieSubTitle_<?= $default_lang ?>').val() == "") {
            $('#vGenieSubTitle_<?= $default_lang ?>_error').show();
            $('#vGenieSubTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vGenieSubTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vGenieSubTitle_Default').val($('#vGenieSubTitle_<?= $default_lang ?>').val());
        $('#vGenieSubTitle_Default').closest('.row').removeClass('has-error');
        $('#vGenieSubTitle_Default-error').remove();
        $('#GenieSubTitle_Modal').modal('hide');
    }

    $('#saveGenieSection').click(function() {
        var vGenieTitleArr = $('[name^="vGenieTitle_"]').serializeArray();
        var vTitleArr = {};
        $.each(vGenieTitleArr, function(key, value) {
            if(value.name != "vGenieTitle_Default") {
                var name_key = value.name.replace('vGenieTitle', 'vTitle');
                vTitleArr[name_key] = value.value;
            }
        });

        var vGenieSubTitleArr = $('[name^="vGenieSubTitle_"]').serializeArray();
        var vSubTitleArr = {};
        $.each(vGenieSubTitleArr, function(key, value) {
            if(value.name != "vGenieSubTitle_Default") {
                var name_key = value.name.replace('vGenieSubTitle', 'vSubtitle');
                vSubTitleArr[name_key] = value.value;
            }
        });
        var vImageGenie = $('#vImageGenie')[0].files[0];
        var vImageOldGenie = $('#vImageOldGenie').val();
        var vTitleColorGenie = $('#vTitleColorGenie').val();
        var vSubTitleColorGenie = $('#vSubTitleColorGenie').val();
        var vBgColorGenie = $('#vBgColorGenie').val();

        var postData = new FormData();
        postData.append('vTitleArr', JSON.stringify(vTitleArr));
        postData.append('vSubTitleArr', JSON.stringify(vSubTitleArr));
        postData.append('vImage', vImageGenie);
        postData.append('vImageOld', vImageOldGenie);
        postData.append('vTxtTitleColor', vTitleColorGenie);
        postData.append('vTxtSubTitleColor', vSubTitleColorGenie);
        postData.append('vBgColor', vBgColorGenie);
        postData.append('ViewType', 'TextBannerView');
        postData.append('ServiceType', 'Genie');

        saveHomeScreenData('saveGenieSection', postData);
    });

    function editVideoConsultTitle(action) {
        $('#videoconsult_title_modal_action').html(action);
        $('#VideoConsultTitle_Modal').modal('show');
    }

    function saveVideoConsultTitle() {
        if ($('#vVideoConsultTitle_<?= $default_lang ?>').val() == "") {
            $('#vVideoConsultTitle_<?= $default_lang ?>_error').show();
            $('#vVideoConsultTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vVideoConsultTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vVideoConsultTitle_Default').val($('#vVideoConsultTitle_<?= $default_lang ?>').val());
        $('#vVideoConsultTitle_Default').closest('.row').removeClass('has-error');
        $('#vVideoConsultTitle_Default-error').remove();
        $('#VideoConsultTitle_Modal').modal('hide');
    }

    function editVideoConsultSubTitle(action) {
        $('#videoconsult_subtitle_modal_action').html(action);
        $('#VideoConsultSubTitle_Modal').modal('show');
    }

    function saveVideoConsultSubTitle() {
        if ($('#vVideoConsultSubTitle_<?= $default_lang ?>').val() == "") {
            $('#vVideoConsultSubTitle_<?= $default_lang ?>_error').show();
            $('#vVideoConsultSubTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vVideoConsultSubTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vVideoConsultSubTitle_Default').val($('#vVideoConsultSubTitle_<?= $default_lang ?>').val());
        $('#vVideoConsultSubTitle_Default').closest('.row').removeClass('has-error');
        $('#vVideoConsultSubTitle_Default-error').remove();
        $('#VideoConsultSubTitle_Modal').modal('hide');
    }

    $('#saveVideoConsultSection').click(function() {
        var vVideoConsultTitleArr = $('[name^="vVideoConsultTitle_"]').serializeArray();
        var vTitleArr = {};
        $.each(vVideoConsultTitleArr, function(key, value) {
            if(value.name != "vVideoConsultTitle_Default") {
                var name_key = value.name.replace('vVideoConsultTitle', 'vTitle');
                vTitleArr[name_key] = value.value;
            }
        });

        var vVideoConsultSubTitleArr = $('[name^="vVideoConsultSubTitle_"]').serializeArray();
        var vSubTitleArr = {};
        $.each(vVideoConsultSubTitleArr, function(key, value) {
            if(value.name != "vVideoConsultSubTitle_Default") {
                var name_key = value.name.replace('vVideoConsultSubTitle', 'vSubtitle');
                vSubTitleArr[name_key] = value.value;
            }
        });
        var vImageVideoConsult = $('#vImageVideoConsult')[0].files[0];
        var vImageOldVideoConsult = $('#vImageOldVideoConsult').val();
        var vTitleColorVideoConsult = $('#vTitleColorVideoConsult').val();
        var vSubTitleColorVideoConsult = $('#vSubTitleColorVideoConsult').val();
        var vBgColorVideoConsult = $('#vBgColorVideoConsult').val();


        var postData = new FormData();
        postData.append('vTitleArr', JSON.stringify(vTitleArr));
        postData.append('vSubTitleArr', JSON.stringify(vSubTitleArr));
        postData.append('vImage', vImageVideoConsult);
        postData.append('vImageOld', vImageOldVideoConsult);
        postData.append('vTxtTitleColor', vTitleColorVideoConsult);
        postData.append('vTxtSubTitleColor', vSubTitleColorVideoConsult);
        postData.append('vBgColor', vBgColorVideoConsult);
        postData.append('ViewType', 'TextBannerView');
        postData.append('ServiceType', 'VideoConsult');

        saveHomeScreenData('saveVideoConsultSection', postData);
    });

    function editBiddingTitle(action) {
        $('#bidding_title_modal_action').html(action);
        $('#BiddingTitle_Modal').modal('show');
    }

    function saveBiddingTitle() {
        if ($('#vBiddingTitle_<?= $default_lang ?>').val() == "") {
            $('#vBiddingTitle_<?= $default_lang ?>_error').show();
            $('#vBiddingTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vBiddingTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vBiddingTitle_Default').val($('#vBiddingTitle_<?= $default_lang ?>').val());
        $('#vBiddingTitle_Default').closest('.row').removeClass('has-error');
        $('#vBiddingTitle_Default-error').remove();
        $('#BiddingTitle_Modal').modal('hide');
    }

    function editBiddingSubTitle(action) {
        $('#bidding_subtitle_modal_action').html(action);
        $('#BiddingSubTitle_Modal').modal('show');
    }

    function saveBiddingSubTitle() {
        if ($('#vBiddingSubTitle_<?= $default_lang ?>').val() == "") {
            $('#vBiddingSubTitle_<?= $default_lang ?>_error').show();
            $('#vBiddingSubTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vBiddingSubTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vBiddingSubTitle_Default').val($('#vBiddingSubTitle_<?= $default_lang ?>').val());
        $('#vBiddingSubTitle_Default').closest('.row').removeClass('has-error');
        $('#vBiddingSubTitle_Default-error').remove();
        $('#BiddingSubTitle_Modal').modal('hide');
    }

    $('#saveBiddingSection').click(function() {
        var vBiddingTitleArr = $('[name^="vBiddingTitle_"]').serializeArray();
        var vTitleArr = {};
        $.each(vBiddingTitleArr, function(key, value) {
            if(value.name != "vBiddingTitle_Default") {
                var name_key = value.name.replace('vBiddingTitle', 'vTitle');
                vTitleArr[name_key] = value.value;
            }
        });

        var vBiddingSubTitleArr = $('[name^="vBiddingSubTitle_"]').serializeArray();
        var vSubTitleArr = {};
        $.each(vBiddingSubTitleArr, function(key, value) {
            if(value.name != "vBiddingSubTitle_Default") {
                var name_key = value.name.replace('vBiddingSubTitle', 'vSubtitle');
                vSubTitleArr[name_key] = value.value;
            }
        });
        var vImageBidding = $('#vImageBidding')[0].files[0];
        var vImageOldBidding = $('#vImageOldBidding').val();
        var vTitleColorBidding = $('#vTitleColorBidding').val();
        var vSubTitleColorBidding = $('#vSubTitleColorBidding').val();
        var vBgColorBidding = $('#vBgColorBidding').val();

        var postData = new FormData();
        postData.append('vTitleArr', JSON.stringify(vTitleArr));
        postData.append('vSubTitleArr', JSON.stringify(vSubTitleArr));
        postData.append('vImage', vImageBidding);
        postData.append('vImageOld', vImageOldBidding);
        postData.append('vTxtTitleColor', vTitleColorBidding);
        postData.append('vTxtSubTitleColor', vSubTitleColorBidding);
        postData.append('vBgColor', vBgColorBidding);
        postData.append('ViewType', 'TextBannerView');
        postData.append('ServiceType', 'Bidding');

        saveHomeScreenData('saveBiddingSection', postData);
    });

    function editOnDemandServiceTitle(action) {
        $('#ondemandservice_title_modal_action').html(action);
        $('#OnDemandServiceTitle_Modal').modal('show');
    }

    function saveOnDemandServiceTitle() {
        if ($('#vOnDemandServiceTitle_<?= $default_lang ?>').val() == "") {
            $('#vOnDemandServiceTitle_<?= $default_lang ?>_error').show();
            $('#vOnDemandServiceTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vOnDemandServiceTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vOnDemandServiceTitle_Default').val($('#vOnDemandServiceTitle_<?= $default_lang ?>').val());
        $('#vOnDemandServiceTitle_Default').closest('.row').removeClass('has-error');
        $('#vOnDemandServiceTitle_Default-error').remove();
        $('#OnDemandServiceTitle_Modal').modal('hide');
    }

    function editOnDemandServiceSubTitle(action) {
        $('#ondemandservice_subtitle_modal_action').html(action);
        $('#OnDemandServiceSubTitle_Modal').modal('show');
    }

    function saveOnDemandServiceSubTitle() {
        if ($('#vOnDemandServiceSubTitle_<?= $default_lang ?>').val() == "") {
            $('#vOnDemandServiceSubTitle_<?= $default_lang ?>_error').show();
            $('#vOnDemandServiceSubTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vOnDemandServiceSubTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vOnDemandServiceSubTitle_Default').val($('#vOnDemandServiceSubTitle_<?= $default_lang ?>').val());
        $('#vOnDemandServiceSubTitle_Default').closest('.row').removeClass('has-error');
        $('#vOnDemandServiceSubTitle_Default-error').remove();
        $('#OnDemandServiceSubTitle_Modal').modal('hide');
    }

    $('#saveOnDemandServiceSection').click(function() {
        var vOnDemandServiceTitleArr = $('[name^="vOnDemandServiceTitle_"]').serializeArray();
        var vTitleArr = {};
        $.each(vOnDemandServiceTitleArr, function(key, value) {
            if(value.name != "vOnDemandServiceTitle_Default") {
                var name_key = value.name.replace('vOnDemandServiceTitle', 'vTitle');
                vTitleArr[name_key] = value.value;
            }
        });

        var vOnDemandServiceSubTitleArr = $('[name^="vOnDemandServiceSubTitle_"]').serializeArray();
        var vSubTitleArr = {};
        $.each(vOnDemandServiceSubTitleArr, function(key, value) {
            if(value.name != "vOnDemandServiceSubTitle_Default") {
                var name_key = value.name.replace('vOnDemandServiceSubTitle', 'vSubtitle');
                vSubTitleArr[name_key] = value.value;
            }
        });

        var saveOnDemandDisplay = $('#saveOnDemandDisplay').val();

        var postData = new FormData();
        postData.append('vTitleArr', JSON.stringify(vTitleArr));
        postData.append('vSubTitleArr', JSON.stringify(vSubTitleArr));

        $('[name="vOnDemandServiceImage[]"]').each(function(i) {
            postData.append('vImage['+i+']', $(this)[0].files[0]);
        });

        $('[name="vOnDemandServiceImageOld[]"]').each(function(i) {
            postData.append('vImageOld['+i+']', $(this).val());
        });

        $('[name="iVehicleCategoryId[]"]').each(function(i) {
            if($(this).is(':checked')) {
                postData.append('iVehicleCategoryId['+i+']', $(this).val());
            }   
        });

        $('[name="iVehicleCategoryIdVal[]"]').each(function(i) {
            postData.append('iVehicleCategoryIdVal['+i+']', $(this).val());
        });

        $('[name="iDisplayOrderOnDemandServiceArr[]"]').each(function(i) {
            postData.append('iDisplayOrderOnDemandServiceArr['+i+']', $(this).val());
        });

        postData.append('ViewType', 'GridView');
        postData.append('ServiceType', 'UberX');
        postData.append('saveOnDemandDisplay', saveOnDemandDisplay);

        saveHomeScreenData('saveOnDemandServiceSection', postData);
    });

    function saveOnDemandServices(eStatus) {
        $('#saveOnDemandDisplay').val(eStatus);
        $('#ondemanservices_modal').modal('hide');
    }

    $('[name="iVehicleCategoryId[]"]').change(function (e) {
        if ($(this).is(':checked')) {
            if ($('[name="iVehicleCategoryId[]"]:checked').length > 3) {
                alert("You can only enable 3 service categories to be shown on App home screen.");
                $(this).prop('checked', false);
                e.stopPropagation();
                e.preventDefault();
            } else {
                $(this).closest('tr').find('select, input[type="file"]').show();
            }
        } else {
            $(this).closest('tr').find('select, input[type="file"]').hide();
            $(this).closest('tr').find('select').val('1');
            $(this).closest('tr').find('input[type="file"]').val('').bootstrapSwitch();
        }
    });

    function editRentEstateTitle(action) {
        $('#rentestate_title_modal_action').html(action);
        $('#RentEstateTitle_Modal').modal('show');
    }

    function saveRentEstateTitle() {
        if ($('#vRentEstateTitle_<?= $default_lang ?>').val() == "") {
            $('#vRentEstateTitle_<?= $default_lang ?>_error').show();
            $('#vRentEstateTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vRentEstateTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vRentEstateTitle_Default').val($('#vRentEstateTitle_<?= $default_lang ?>').val());
        $('#vRentEstateTitle_Default').closest('.row').removeClass('has-error');
        $('#vRentEstateTitle_Default-error').remove();
        $('#RentEstateTitle_Modal').modal('hide');
    }

    $('#saveRentEstateSection').click(function() {
        var vRentEstateTitleArr = $('[name^="vRentEstateTitle_"]').serializeArray();
        var vTitleArr = {};
        $.each(vRentEstateTitleArr, function(key, value) {
            if(value.name != "vRentEstateTitle_Default") {
                var name_key = value.name.replace('vRentEstateTitle', 'vCategoryName');
                vTitleArr[name_key] = value.value;
            }
        });

        var vImageRentEstate = $('#vImageRentEstate')[0].files[0];
        var vImageOldRentEstate = $('#vImageOldRentEstate').val();
        var vTitleColorRentEstate = $('#vTitleColorRentEstate').val();
        var vBgColorRentEstate = $('#vBgColorRentEstate').val();

        var postData = new FormData();
        postData.append('vTitleArr', JSON.stringify(vTitleArr));
        postData.append('vImage', vImageRentEstate);
        postData.append('vImageOld', vImageOldRentEstate);
        postData.append('vTxtTitleColor', vTitleColorRentEstate);
        postData.append('vBgColor', vBgColorRentEstate);
        postData.append('ViewType', 'TextBannerView');
        postData.append('ServiceType', 'RentEstate');

        saveHomeScreenData('saveRentEstateSection', postData);
    });

    function editRentCarsTitle(action) {
        $('#rentcars_title_modal_action').html(action);
        $('#RentCarsTitle_Modal').modal('show');
    }

    function saveRentCarsTitle() {
        if ($('#vRentCarsTitle_<?= $default_lang ?>').val() == "") {
            $('#vRentCarsTitle_<?= $default_lang ?>_error').show();
            $('#vRentCarsTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vRentCarsTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vRentCarsTitle_Default').val($('#vRentCarsTitle_<?= $default_lang ?>').val());
        $('#vRentCarsTitle_Default').closest('.row').removeClass('has-error');
        $('#vRentCarsTitle_Default-error').remove();
        $('#RentCarsTitle_Modal').modal('hide');
    }

    $('#saveRentCarsSection').click(function() {
        var vRentCarsTitleArr = $('[name^="vRentCarsTitle_"]').serializeArray();
        var vTitleArr = {};
        $.each(vRentCarsTitleArr, function(key, value) {
            if(value.name != "vRentCarsTitle_Default") {
                var name_key = value.name.replace('vRentCarsTitle', 'vCategoryName');
                vTitleArr[name_key] = value.value;
            }
        });

        var vImageRentCars = $('#vImageRentCars')[0].files[0];
        var vImageOldRentCars = $('#vImageOldRentCars').val();
        var vTitleColorRentCars = $('#vTitleColorRentCars').val();
        var vBgColorRentCars = $('#vBgColorRentCars').val();

        var postData = new FormData();
        postData.append('vTitleArr', JSON.stringify(vTitleArr));
        postData.append('vImage', vImageRentCars);
        postData.append('vImageOld', vImageOldRentCars);
        postData.append('vTxtTitleColor', vTitleColorRentCars);
        postData.append('vBgColor', vBgColorRentCars);
        postData.append('ViewType', 'TextBannerView');
        postData.append('ServiceType', 'RentCars');

        saveHomeScreenData('saveRentCarsSection', postData);
    });

    function editRentItemTitle(action) {
        $('#rentitem_title_modal_action').html(action);
        $('#RentItemTitle_Modal').modal('show');
    }

    function saveRentItemTitle() {
        if ($('#vRentItemTitle_<?= $default_lang ?>').val() == "") {
            $('#vRentItemTitle_<?= $default_lang ?>_error').show();
            $('#vRentItemTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vRentItemTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vRentItemTitle_Default').val($('#vRentItemTitle_<?= $default_lang ?>').val());
        $('#vRentItemTitle_Default').closest('.row').removeClass('has-error');
        $('#vRentItemTitle_Default-error').remove();
        $('#RentItemTitle_Modal').modal('hide');
    }

    $('#saveRentItemSection').click(function() {
        var vRentItemTitleArr = $('[name^="vRentItemTitle_"]').serializeArray();
        var vTitleArr = {};
        $.each(vRentItemTitleArr, function(key, value) {
            if(value.name != "vRentItemTitle_Default") {
                var name_key = value.name.replace('vRentItemTitle', 'vCategoryName');
                vTitleArr[name_key] = value.value;
            }
        });

        var vImageRentItem = $('#vImageRentItem')[0].files[0];
        var vImageOldRentItem = $('#vImageOldRentItem').val();
        var vTitleColorRentItem = $('#vTitleColorRentItem').val();
        var vBgColorRentItem = $('#vBgColorRentItem').val();

        var postData = new FormData();
        postData.append('vTitleArr', JSON.stringify(vTitleArr));
        postData.append('vImage', vImageRentItem);
        postData.append('vImageOld', vImageOldRentItem);
        postData.append('vTxtTitleColor', vTitleColorRentItem);
        postData.append('vBgColor', vBgColorRentItem);
        postData.append('ViewType', 'TextBannerView');
        postData.append('ServiceType', 'RentItem');

        saveHomeScreenData('saveRentItemSection', postData);
    });

    function editRideShareTitle(action) {
        $('#rideshare_title_modal_action').html(action);
        $('#RideShareTitle_Modal').modal('show');
    }

    function saveRideShareTitle() {
        if ($('#vRideShareTitle_<?= $default_lang ?>').val() == "") {
            $('#vRideShareTitle_<?= $default_lang ?>_error').show();
            $('#vRideShareTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vRideShareTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vRideShareTitle_Default').val($('#vRideShareTitle_<?= $default_lang ?>').val());
        $('#vRideShareTitle_Default').closest('.row').removeClass('has-error');
        $('#vRideShareTitle_Default-error').remove();
        $('#RideShareTitle_Modal').modal('hide');
    }

    function editRideSharePublishTitle(action) {
        $('#rideshare_publish_title_modal_action').html(action);
        $('#RideSharePublishTitle_Modal').modal('show');
    }

    function saveRideSharePublishTitle() {
        if ($('#RideSharePublishTitle_<?= $default_lang ?>').val() == "") {
            $('#RideSharePublishTitle_<?= $default_lang ?>_error').show();
            $('#RideSharePublishTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#RideSharePublishTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#RideSharePublishTitle_Default').val($('#RideSharePublishTitle_<?= $default_lang ?>').val());
        $('#RideSharePublishTitle_Default').closest('.row').removeClass('has-error');
        $('#RideSharePublishTitle_Default-error').remove();
        $('#RideSharePublishTitle_Modal').modal('hide');
    }

    function editRideShareBookTitle(action) {
        $('#rideshare_Book_title_modal_action').html(action);
        $('#RideShareBookTitle_Modal').modal('show');
    }

    function saveRideShareBookTitle() {
        if ($('#RideShareBookTitle_<?= $default_lang ?>').val() == "") {
            $('#RideShareBookTitle_<?= $default_lang ?>_error').show();
            $('#RideShareBookTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#RideShareBookTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#RideShareBookTitle_Default').val($('#RideShareBookTitle_<?= $default_lang ?>').val());
        $('#RideShareBookTitle_Default').closest('.row').removeClass('has-error');
        $('#RideShareBookTitle_Default-error').remove();
        $('#RideShareBookTitle_Modal').modal('hide');
    }

    function editRideShareMyRideTitle(action) {
        $('#rideshare_MyRide_title_modal_action').html(action);
        $('#RideShareMyRideTitle_Modal').modal('show');
    }

    function saveRideShareMyRideTitle() {
        if ($('#RideShareMyRideTitle_<?= $default_lang ?>').val() == "") {
            $('#RideShareMyRideTitle_<?= $default_lang ?>_error').show();
            $('#RideShareMyRideTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#RideShareMyRideTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#RideShareMyRideTitle_Default').val($('#RideShareMyRideTitle_<?= $default_lang ?>').val());
        $('#RideShareMyRideTitle_Default').closest('.row').removeClass('has-error');
        $('#RideShareMyRideTitle_Default-error').remove();
        $('#RideShareMyRideTitle_Modal').modal('hide');
    }

    function editRideShareSubTitle(action) {
        $('#rideshare_subtitle_modal_action').html(action);
        $('#RideShareSubTitle_Modal').modal('show');
    }

    function saveRideShareSubTitle() {
        if ($('#vRideShareSubTitle_<?= $default_lang ?>').val() == "") {
            $('#vRideShareSubTitle_<?= $default_lang ?>_error').show();
            $('#vRideShareSubTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vRideShareSubTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vRideShareSubTitle_Default').val($('#vRideShareSubTitle_<?= $default_lang ?>').val());
        $('#vRideShareSubTitle_Default').closest('.row').removeClass('has-error');
        $('#vRideShareSubTitle_Default-error').remove();
        $('#RideShareSubTitle_Modal').modal('hide');
    }

    $('#saveRideShareSection').click(function() {
        var vRideShareTitleArr = $('[name^="vRideShareTitle_"]').serializeArray();
        var vTitleArr = {};
        $.each(vRideShareTitleArr, function(key, value) {
            if(value.name != "vRideShareTitle_Default") {
                var name_key = value.name.replace('vRideShareTitle', 'vTitle');
                vTitleArr[name_key] = value.value;
            }
        });

        var vRideShareSubTitleArr = $('[name^="vRideShareSubTitle_"]').serializeArray();
        var vSubTitleArr = {};
        $.each(vRideShareSubTitleArr, function(key, value) {
            if(value.name != "vRideShareSubTitle_Default") {
                var name_key = value.name.replace('vRideShareSubTitle', 'vSubtitle');
                vSubTitleArr[name_key] = value.value;
            }
        });
        var vImageRideShare = $('#vImageRideShare')[0].files[0];
        var vImageOldRideShare = $('#vImageOldRideShare').val();
        var vTitleColorRideShare = $('#vTitleColorRideShare').val();
        var vSubTitleColorRideShare = $('#vSubTitleColorRideShare').val();
        var vBgColorRideShare = $('#vBgColorRideShare').val();

        var postData = new FormData();
        postData.append('vTitleArr', JSON.stringify(vTitleArr));
        postData.append('vSubTitleArr', JSON.stringify(vSubTitleArr));
        postData.append('vImage', vImageRideShare);
        postData.append('vImageOld', vImageOldRideShare);
        postData.append('vTxtTitleColor', vTitleColorRideShare);
        postData.append('vTxtSubTitleColor', vSubTitleColorRideShare);
        postData.append('vBgColor', vBgColorRideShare);
        postData.append('ViewType', 'TextBannerView');
        postData.append('ServiceType', 'RideShare');

        saveHomeScreenData('saveRideShareSection', postData);
    });

    $('#saveRideShareInfoSection').click(function() {
        var vRideSharePublishTitleArr = $('[name^="RideSharePublishTitle_"]').serializeArray();

        var vPublishTitleArr = {};
        $.each(vRideSharePublishTitleArr, function(key, value) {
            if(value.name != "RideSharePublishTitle_Default") {
                var name_key = value.name.replace('RideSharePublishTitle', 'vTitle');
                vPublishTitleArr[name_key] = value.value;
            }
        });

        var vRideShareBookTitleArr = $('[name^="RideShareBookTitle_"]').serializeArray();
        var vBookTitleArr = {};
        $.each(vRideShareBookTitleArr, function(key, value) {
            if(value.name != "RideShareBookTitle_Default") {
                var name_key = value.name.replace('RideShareBookTitle', 'vTitle');
                vBookTitleArr[name_key] = value.value;
            }
        });

        var vRideShareMyRideTitleArr = $('[name^="RideShareMyRideTitle_"]').serializeArray();
        var vMyRideTitleArr = {};
        $.each(vRideShareMyRideTitleArr, function(key, value) {
            if(value.name != "RideShareMyRideTitle_Default") {
                var name_key = value.name.replace('RideShareMyRideTitle', 'vTitle');
                vMyRideTitleArr[name_key] = value.value;
            }
        });

        var vImageRideSharePublish = $('#vImageRideSharePublish')[0].files[0];
        var vImageOldRideSharePublish = $('#vImageOldRideSharePublish').val();

        var vImageRideShareBook = $('#vImageRideShareBook')[0].files[0];
        var vImageOldRideShareBook = $('#vImageOldRideShareBook').val();

        var vImageRideShareMyRides = $('#vImageRideShareMyRides')[0].files[0];
        var vImageOldRideShareMyRides = $('#vImageOldRideShareMyRides').val();
     

        var postData = new FormData();
        postData.append('vPublishTitleArr', JSON.stringify(vPublishTitleArr));
        postData.append('vBookTitleArr', JSON.stringify(vBookTitleArr));
        postData.append('vMyRideTitleArr', JSON.stringify(vMyRideTitleArr));
        
        postData.append('vImagePublish', vImageRideSharePublish);
        postData.append('vImageOldPublish', vImageOldRideSharePublish);

        postData.append('vImageBook', vImageRideShareBook);
        postData.append('vImageOldBook', vImageOldRideShareBook);

        postData.append('vImageMyRides', vImageRideShareMyRides);
        postData.append('vImageOldMyRides', vImageOldRideShareMyRides);

        postData.append('ViewType', 'TextBannerView');
        postData.append('ServiceType', 'RideShareInfo');

        saveHomeScreenData('saveRideShareInfoSection', postData);
    });

    function editMSTitle(action) {
        $('#ms_title_modal_action').html(action);
        $('#MSTitle_Modal').modal('show');
    }

    function saveMSTitle() {
        if ($('#vMSTitle_<?= $default_lang ?>').val() == "") {
            $('#vMSTitle_<?= $default_lang ?>_error').show();
            $('#vMSTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vMSTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vMSTitle_Default').val($('#vMSTitle_<?= $default_lang ?>').val());
        $('#vMSTitle_Default').closest('.row').removeClass('has-error');
        $('#vMSTitle_Default-error').remove();
        $('#MSTitle_Modal').modal('hide');
    }

    $('#saveMSTitleSection').click(function() {
        var vMSTitleArr = $('[name^="vMSTitle_"]').serializeArray();
        var vTitleArr = {};
        $.each(vMSTitleArr, function(key, value) {
            if(value.name != "vMSTitle_Default") {
                var name_key = value.name.replace('vMSTitle', 'vTitle');
                vTitleArr[name_key] = value.value;
            }
        });

        var postData = {};
        postData['vTitleArr'] = vTitleArr;
        postData['ViewType'] = 'TitleView';
        postData['ServiceType'] = 'MedicalServices';
        saveHomeScreenData('saveMSTitleSection', postData, 'No');
    });

    function editBookServiceMSTitle(action) {
        $('#bookservicems_title_modal_action').html(action);
        $('#BookServiceMSTitle_Modal').modal('show');
    }

    function saveBookServiceMSTitle() {
        if ($('#vBookServiceMSTitle_<?= $default_lang ?>').val() == "") {
            $('#vBookServiceMSTitle_<?= $default_lang ?>_error').show();
            $('#vBookServiceMSTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vBookServiceMSTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vBookServiceMSTitle_Default').val($('#vBookServiceMSTitle_<?= $default_lang ?>').val());
        $('#vBookServiceMSTitle_Default').closest('.row').removeClass('has-error');
        $('#vBookServiceMSTitle_Default-error').remove();
        $('#BookServiceMSTitle_Modal').modal('hide');
    }

    function editBookServiceMSSubTitle(action) {
        $('#bookservicems_subtitle_modal_action').html(action);
        $('#BookServiceMSSubTitle_Modal').modal('show');
    }

    function saveBookServiceMSSubTitle() {
        if ($('#vBookServiceMSSubTitle_<?= $default_lang ?>').val() == "") {
            $('#vBookServiceMSSubTitle_<?= $default_lang ?>_error').show();
            $('#vBookServiceMSSubTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vBookServiceMSSubTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vBookServiceMSSubTitle_Default').val($('#vBookServiceMSSubTitle_<?= $default_lang ?>').val());
        $('#vBookServiceMSSubTitle_Default').closest('.row').removeClass('has-error');
        $('#vBookServiceMSSubTitle_Default-error').remove();
        $('#BookServiceMSSubTitle_Modal').modal('hide');
    }

    $('#saveBookServiceMSSection').click(function() {
        var vBookServiceMSTitleArr = $('[name^="vBookServiceMSTitle_"]').serializeArray();
        var vTitleArr = {};
        $.each(vBookServiceMSTitleArr, function(key, value) {
            if(value.name != "vBookServiceMSTitle_Default") {
                var name_key = value.name.replace('vBookServiceMSTitle', 'vTitle');
                vTitleArr[name_key] = value.value;
            }
        });

        var vBookServiceMSSubTitleArr = $('[name^="vBookServiceMSSubTitle_"]').serializeArray();
        var vSubTitleArr = {};
        $.each(vBookServiceMSSubTitleArr, function(key, value) {
            if(value.name != "vBookServiceMSSubTitle_Default") {
                var name_key = value.name.replace('vBookServiceMSSubTitle', 'vSubtitle');
                vSubTitleArr[name_key] = value.value;
            }
        });
        

        var saveBookServiceMS = $('#saveBookServiceMS').val();
        // var vImageBookService = $('#vImageBookService')[0].files[0]; // NM Commented
        var vImageBookService = $('#vImageBookServiceMS')[0].files[0]; // NM Added
        var vImageOldBookService = $('#vImageOldBookService').val();
        var vTitleColorBookServiceMS = $('#vTitleColorBookServiceMS').val();
        var vBgColorBookServiceMS = $('#vBgColorBookServiceMS').val();

        var postData = new FormData();
        postData.append('vTitleArr', JSON.stringify(vTitleArr));
        postData.append('vSubTitleArr', JSON.stringify(vSubTitleArr));
        postData.append('vImage', vImageBookService);
        postData.append('vImageOld', vImageOldBookService);
        postData.append('vTxtTitleColor', vTitleColorBookServiceMS);
        postData.append('vBgColor', vBgColorBookServiceMS);

        $('[name="vBookServiceMSImage[]"]').each(function(i) {
            postData.append('vImageBS['+i+']', $(this)[0].files[0]);
        });

        $('[name="vBookServiceMSImageOld[]"]').each(function(i) {
            postData.append('vImageOldBS['+i+']', $(this).val());
        });

        $('[name="iVehicleCategoryIdBS[]"]').each(function(i) {
            if($(this).is(':checked')) {
                postData.append('iVehicleCategoryIdBS['+i+']', $(this).val());
            }   
        });

        $('[name="iVehicleCategoryIdValBS[]"]').each(function(i) {
            postData.append('iVehicleCategoryIdValBS['+i+']', $(this).val());
        });

        $('[name="iDisplayOrderBookServiceMSArr[]"]').each(function(i) {
            postData.append('iDisplayOrderBookServiceMSArr['+i+']', $(this).val());
        });

        postData.append('ViewType', 'TextBannerGridView');
        postData.append('ServiceType', 'MedicalServices');
        postData.append('ServiceTypeMS', 'BookService');
        postData.append('saveBookServiceMS', saveBookServiceMS);

        saveHomeScreenData('saveBookServiceMSSection', postData);
    });

    function editVideoConsultMSTitle(action) {
        $('#videoconsultms_title_modal_action').html(action);
        $('#VideoConsultMSTitle_Modal').modal('show');
    }

    function saveVideoConsultMSTitle() {
        if ($('#vVideoConsultMSTitle_<?= $default_lang ?>').val() == "") {
            $('#vVideoConsultMSTitle_<?= $default_lang ?>_error').show();
            $('#vVideoConsultMSTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vVideoConsultMSTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vVideoConsultMSTitle_Default').val($('#vVideoConsultMSTitle_<?= $default_lang ?>').val());
        $('#vVideoConsultMSTitle_Default').closest('.row').removeClass('has-error');
        $('#vVideoConsultMSTitle_Default-error').remove();
        $('#VideoConsultMSTitle_Modal').modal('hide');
    }

    function editVideoConsultMSSubTitle(action) {
        $('#videoconsultms_subtitle_modal_action').html(action);
        $('#VideoConsultMSSubTitle_Modal').modal('show');
    }

    function saveVideoConsultMSSubTitle() {
        if ($('#vVideoConsultMSSubTitle_<?= $default_lang ?>').val() == "") {
            $('#vVideoConsultMSSubTitle_<?= $default_lang ?>_error').show();
            $('#vVideoConsultMSSubTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vVideoConsultMSSubTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vVideoConsultMSSubTitle_Default').val($('#vVideoConsultMSSubTitle_<?= $default_lang ?>').val());
        $('#vVideoConsultMSSubTitle_Default').closest('.row').removeClass('has-error');
        $('#vVideoConsultMSSubTitle_Default-error').remove();
        $('#VideoConsultMSSubTitle_Modal').modal('hide');
    }

    $('#saveVideoConsultMSSection').click(function() {
        var vVideoConsultMSTitleArr = $('[name^="vVideoConsultMSTitle_"]').serializeArray();
        var vTitleArr = {};
        $.each(vVideoConsultMSTitleArr, function(key, value) {
            if(value.name != "vVideoConsultMSTitle_Default") {
                var name_key = value.name.replace('vVideoConsultMSTitle', 'vTitle');
                vTitleArr[name_key] = value.value;
            }
        });

        var vVideoConsultMSSubTitleArr = $('[name^="vVideoConsultMSSubTitle_"]').serializeArray();
        var vSubTitleArr = {};
        $.each(vVideoConsultMSSubTitleArr, function(key, value) {
            if(value.name != "vVideoConsultMSSubTitle_Default") {
                var name_key = value.name.replace('vVideoConsultMSSubTitle', 'vSubtitle');
                vSubTitleArr[name_key] = value.value;
            }
        });

        var saveVideoConsultMS = $('#saveVideoConsultMS').val();
        var vImageVideoConsultMS = $('#vImageVideoConsultMS')[0].files[0];
        var vImageOldVideoConsultMS = $('#vImageOldVideoConsultMS').val();
        var vTitleColorVideoConsultMS = $('#vTitleColorVideoConsultMS').val();
        var vBgColorVideoConsultMS = $('#vBgColorVideoConsultMS').val();

        var postData = new FormData();
        postData.append('vTitleArr', JSON.stringify(vTitleArr));
        postData.append('vSubTitleArr', JSON.stringify(vSubTitleArr));
        postData.append('vImage', vImageVideoConsultMS);
        postData.append('vImageOld', vImageOldVideoConsultMS);
        postData.append('vTxtTitleColor', vTitleColorVideoConsultMS);
        postData.append('vBgColor', vBgColorVideoConsultMS);

        $('[name="vVideoConsultMSImage[]"]').each(function(i) {
            postData.append('vImageVC['+i+']', $(this)[0].files[0]);
        });

        $('[name="vVideoConsultMSImageOld[]"]').each(function(i) {
            postData.append('vImageOldVC['+i+']', $(this).val());
        });

        $('[name="iVehicleCategoryIdVC[]"]').each(function(i) {
            if($(this).is(':checked')) {
                postData.append('iVehicleCategoryIdVC['+i+']', $(this).val());
            }   
        });

        $('[name="iVehicleCategoryIdValVC[]"]').each(function(i) {
            postData.append('iVehicleCategoryIdValVC['+i+']', $(this).val());
        });

        $('[name="iDisplayOrderVideoConsultMSArr[]"]').each(function(i) {
            postData.append('iDisplayOrderVideoConsultMSArr['+i+']', $(this).val());
        });

        postData.append('ViewType', 'TextBannerGridView');
        postData.append('ServiceType', 'MedicalServices');
        postData.append('ServiceTypeMS', 'VideoConsult');
        postData.append('saveVideoConsultMS', saveVideoConsultMS);

        saveHomeScreenData('saveVideoConsultMSSection', postData);
    });

    function editMoreServiceMSTitle(action) {
        $('#moreservicems_title_modal_action').html(action);
        $('#MoreServiceMSTitle_Modal').modal('show');
    }

    function saveMoreServiceMSTitle() {
        if ($('#vMoreServiceMSTitle_<?= $default_lang ?>').val() == "") {
            $('#vMoreServiceMSTitle_<?= $default_lang ?>_error').show();
            $('#vMoreServiceMSTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vMoreServiceMSTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vMoreServiceMSTitle_Default').val($('#vMoreServiceMSTitle_<?= $default_lang ?>').val());
        $('#vMoreServiceMSTitle_Default').closest('.row').removeClass('has-error');
        $('#vMoreServiceMSTitle_Default-error').remove();
        $('#MoreServiceMSTitle_Modal').modal('hide');
    }

    function editMoreServiceMSSubTitle(action) {
        $('#moreservicems_subtitle_modal_action').html(action);
        $('#MoreServiceMSSubTitle_Modal').modal('show');
    }

    function saveMoreServiceMSSubTitle() {
        if ($('#vMoreServiceMSSubTitle_<?= $default_lang ?>').val() == "") {
            $('#vMoreServiceMSSubTitle_<?= $default_lang ?>_error').show();
            $('#vMoreServiceMSSubTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vMoreServiceMSSubTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vMoreServiceMSSubTitle_Default').val($('#vMoreServiceMSSubTitle_<?= $default_lang ?>').val());
        $('#vMoreServiceMSSubTitle_Default').closest('.row').removeClass('has-error');
        $('#vMoreServiceMSSubTitle_Default-error').remove();
        $('#MoreServiceMSSubTitle_Modal').modal('hide');
    }

    $('#saveMoreServiceMSSection').click(function() {
        var vMoreServiceMSTitleArr = $('[name^="vMoreServiceMSTitle_"]').serializeArray();
        var vTitleArr = {};
        $.each(vMoreServiceMSTitleArr, function(key, value) {
            if(value.name != "vMoreServiceMSTitle_Default") {
                var name_key = value.name.replace('vMoreServiceMSTitle', 'vTitle');
                vTitleArr[name_key] = value.value;
            }
        });

        var vMoreServiceMSSubTitleArr = $('[name^="vMoreServiceMSSubTitle_"]').serializeArray();
        var vSubTitleArr = {};
        $.each(vMoreServiceMSSubTitleArr, function(key, value) {
            if(value.name != "vMoreServiceMSSubTitle_Default") {
                var name_key = value.name.replace('vMoreServiceMSSubTitle', 'vSubtitle');
                vSubTitleArr[name_key] = value.value;
            }
        });

        var saveMoreServiceMS = $('#saveMoreServiceMS').val();
        // var vImageMoreService = $('#vImageMoreService')[0].files[0]; // NM Commented
        var vImageMoreService = $('#vImageMoreServiceMS')[0].files[0]; // NM Added
        var vImageOldMoreService = $('#vImageOldMoreService').val();
        var vTitleColorMoreServiceMS = $('#vTitleColorMoreServiceMS').val();
        var vBgColorMoreServiceMS = $('#vBgColorMoreServiceMS').val();

        var postData = new FormData();
        postData.append('vTitleArr', JSON.stringify(vTitleArr));
        postData.append('vSubTitleArr', JSON.stringify(vSubTitleArr));
        postData.append('vImage', vImageMoreService);
        postData.append('vImageOld', vImageOldMoreService);
        postData.append('vTxtTitleColor', vTitleColorMoreServiceMS);
        postData.append('vBgColor', vBgColorMoreServiceMS);

        $('[name="vMoreServiceMSImage[]"]').each(function(i) {
            postData.append('vImageMS['+i+']', $(this)[0].files[0]);
        });

        $('[name="vMoreServiceMSImageOld[]"]').each(function(i) {
            postData.append('vImageOldMS['+i+']', $(this).val());
        });

        $('[name="iVehicleCategoryIdMS[]"]').each(function(i) {
            if($(this).is(':checked')) {
                postData.append('iVehicleCategoryIdMS['+i+']', $(this).val());
            }   
        });

        $('[name="iVehicleCategoryIdValMS[]"]').each(function(i) {
            postData.append('iVehicleCategoryIdValMS['+i+']', $(this).val());
        });

        $('[name="iDisplayOrderMoreServiceMSArr[]"]').each(function(i) {
            postData.append('iDisplayOrderMoreServiceMSArr['+i+']', $(this).val());
        });

        postData.append('ViewType', 'TextBannerGridView');
        postData.append('ServiceType', 'MedicalServices');
        postData.append('ServiceTypeMS', 'MoreService');
        postData.append('saveMoreServiceMS', saveMoreServiceMS);

        saveHomeScreenData('saveMoreServiceMSSection', postData);
    });

    function saveMSBookService(eStatus) {
        $('#saveBookServiceMS').val(eStatus);
        $('#ms_bookservice_modal').modal('hide');
    }

    function saveMSVideoConsult(eStatus) {
        $('#saveVideoConsultMS').val(eStatus);
        $('#ms_videoconsult_modal').modal('hide');
    }

    function saveMSMoreService(eStatus) {
        $('#saveMoreServiceMS').val(eStatus);
        $('#ms_moreservice_modal').modal('hide');
    }

    $('[name="iVehicleCategoryIdBS[]"]').change(function (e) {
        if ($(this).is(':checked')) {
            if ($('[name="iVehicleCategoryIdBS[]"]:checked').length > 2) {
                alert("You can only enable 2 service categories to be shown on App home screen.");
                $(this).prop('checked', false);
                e.stopPropagation();
                e.preventDefault();
            } else {
                $(this).closest('tr').find('select, input[type="file"]').show();
            }
        } else {
            $(this).closest('tr').find('select, input[type="file"]').hide();
            $(this).closest('tr').find('select').val('1');
            $(this).closest('tr').find('input[type="file"]').val('').bootstrapSwitch();
        }
    });

    $('[name="iVehicleCategoryIdVC[]"]').change(function (e) {
        if ($(this).is(':checked')) {
            if ($('[name="iVehicleCategoryIdVC[]"]:checked').length > 2) {
                alert("You can only enable 2 service categories to be shown on App home screen.");
                $(this).prop('checked', false);
                e.stopPropagation();
                e.preventDefault();
            } else {
                $(this).closest('tr').find('select, input[type="file"]').show();
            }
        } else {
            $(this).closest('tr').find('select, input[type="file"]').hide();
            $(this).closest('tr').find('select').val('1');
            $(this).closest('tr').find('input[type="file"]').val('').bootstrapSwitch();
        }
    });

    $('[name="iVehicleCategoryIdMS[]"]').change(function (e) {
        if ($(this).is(':checked')) {
            if ($('[name="iVehicleCategoryIdMS[]"]:checked').length > 3) {
                alert("You can only enable 3 service categories to be shown on App home screen.");
                $(this).prop('checked', false);
                e.stopPropagation();
                e.preventDefault();
            } else {
                $(this).closest('tr').find('select, input[type="file"]').show();
            }
        } else {
            $(this).closest('tr').find('select, input[type="file"]').hide();
            $(this).closest('tr').find('select').val('1');
            $(this).closest('tr').find('input[type="file"]').val('').bootstrapSwitch();
        }
    });

    function editTrackServiceTitle(action) {
        $('#trackservice_title_modal_action').html(action);
        $('#TrackServiceTitle_Modal').modal('show');
    }

    function saveTrackServiceTitle() {
        if ($('#vTrackServiceTitle_<?= $default_lang ?>').val() == "") {
            $('#vTrackServiceTitle_<?= $default_lang ?>_error').show();
            $('#vTrackServiceTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vTrackServiceTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vTrackServiceTitle_Default').val($('#vTrackServiceTitle_<?= $default_lang ?>').val());
        $('#vTrackServiceTitle_Default').closest('.row').removeClass('has-error');
        $('#vTrackServiceTitle_Default-error').remove();
        $('#TrackServiceTitle_Modal').modal('hide');
    }

    function editTrackServiceSubTitle(action) {
        $('#trackservice_subtitle_modal_action').html(action);
        $('#TrackServiceSubTitle_Modal').modal('show');
    }

    function saveTrackServiceSubTitle() {
        if ($('#vTrackServiceSubTitle_<?= $default_lang ?>').val() == "") {
            $('#vTrackServiceSubTitle_<?= $default_lang ?>_error').show();
            $('#vTrackServiceSubTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vTrackServiceSubTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vTrackServiceSubTitle_Default').val($('#vTrackServiceSubTitle_<?= $default_lang ?>').val());
        $('#vTrackServiceSubTitle_Default').closest('.row').removeClass('has-error');
        $('#vTrackServiceSubTitle_Default-error').remove();
        $('#TrackServiceSubTitle_Modal').modal('hide');
    }

    $('#saveTrackServiceSection').click(function() {
        var vTrackServiceTitleArr = $('[name^="vTrackServiceTitle_"]').serializeArray();
        var vTitleArr = {};
        $.each(vTrackServiceTitleArr, function(key, value) {
            if(value.name != "vTrackServiceTitle_Default") {
                var name_key = value.name.replace('vTrackServiceTitle', 'vTitle');
                vTitleArr[name_key] = value.value;
            }
        });

        var vTrackServiceSubTitleArr = $('[name^="vTrackServiceSubTitle_"]').serializeArray();
        var vSubTitleArr = {};
        $.each(vTrackServiceSubTitleArr, function(key, value) {
            if(value.name != "vTrackServiceSubTitle_Default") {
                var name_key = value.name.replace('vTrackServiceSubTitle', 'vSubtitle');
                vSubTitleArr[name_key] = value.value;
            }
        });
        var vImageTrackService = $('#vImageTrackService')[0].files[0];
        var vImageOldTrackService = $('#vImageOldTrackService').val();
        var vTitleColorTrackService = $('#vTitleColorTrackService').val();
        var vSubTitleColorTrackService = $('#vSubTitleColorTrackService').val();
        var vBgColorTrackService = $('#vBgColorTrackService').val();

        var postData = new FormData();
        postData.append('vTitleArr', JSON.stringify(vTitleArr));
        postData.append('vSubTitleArr', JSON.stringify(vSubTitleArr));
        postData.append('vImage', vImageTrackService);
        postData.append('vImageOld', vImageOldTrackService);
        postData.append('vTxtTitleColor', vTitleColorTrackService);
        postData.append('vTxtSubTitleColor', vSubTitleColorTrackService);
        postData.append('vBgColor', vBgColorTrackService);
        postData.append('ViewType', 'TextBannerView');
        postData.append('ServiceType', 'TrackAnyService');

        saveHomeScreenData('saveTrackServiceSection', postData);
    });

    function editTrackServiceCategoryTitle(action,key) {
        $('#trackservice_title_'+key+'_modal_action').html(action);
        $('#TrackServiceTitle'+key+'_Modal').modal('show');
    }
    
    function saveTrackServiceCategoryTitle(key) {
        if ($('#vTrackServiceCategory'+key+'_<?= $default_lang ?>').val() == "") {
            $('#vTrackServiceCategory'+key+'_<?= $default_lang ?>_error').show();
            $('#vTrackServiceCategory'+key+'_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vTrackServiceCategory'+key+'_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vTrackServiceCategory'+key+'_Default').val($('#vTrackServiceCategory'+key+'_<?= $default_lang ?>').val());
        $('#vTrackServiceCategory'+key+'_Default').closest('.row').removeClass('has-error');
        $('#vTrackServiceCategory'+key+'_Default-error').remove();
        $('#TrackServiceTitle'+key+'_Modal').modal('hide');
    }

    $('#saveTrackServiceCategorySection').click(function() {
        var phpArray = <?php echo json_encode($userEditDataArrNew); ?>;
        var vTitleArrnew = []; // Initialize as an array
        var vDataArrnew = []; // Initialize as an array
        for (var i = 0; i < phpArray.length; i++) {
            var vTrackServiceCategory = "vTrackServiceCategory" + i + "_";
            var vTitleArr = {}; // Initialize vTitleArr for each iteration
            var vTrackServiceCategoryArr = $('[name^="' + vTrackServiceCategory + '"]').serializeArray();

            $.each(vTrackServiceCategoryArr, function (key, value) {
                if (value.name != "vTrackServiceCategory" + i + "_Default") {
                    var name_key = value.name.replace('vTrackServiceCategory' + i, 'vCategoryName');
                    vTitleArr[name_key] = value.value;
                }
            });

            var iTrackServiceCategoryId = $('#iTrackServiceCategoryId' + i).val();
            var vImageTrackService = $('#vImageTrackService' + i)[0].files[0];
            var vImageOldTrackService = $('#vImageOldTrackService' + i).val();
          
            var entry = {
                'iTrackServiceCategoryId': iTrackServiceCategoryId,
                'vTitleArr': vTitleArr
            };
            vTitleArrnew.push(entry);
            var entryData = {
                'iTrackServiceCategoryId': iTrackServiceCategoryId,
                'vImageTrackService': vImageTrackService,
                'vImageOldTrackService': vImageOldTrackService
            };
            vDataArrnew.push(entryData)
        }

        var postData = new FormData();
        postData.append('vTitleArr', JSON.stringify(vTitleArrnew));
        // Append vDataArrnew
        for (var j = 0; j < vDataArrnew.length; j++) {
            //postData.append('iTrackServiceCategoryId[]', vDataArrnew[j].iTrackServiceCategoryId);
            postData.append('vImageTrackService[]', vDataArrnew[j].vImageTrackService);
            postData.append('vImageOldTrackService[]', vDataArrnew[j].vImageOldTrackService);
        }
        postData.append('ViewType', 'TextBannerViewTrack');
        postData.append('ServiceType', 'TrackAnyServiceInfo');

        saveHomeScreenData('saveTrackServiceCategorySection', postData);
    });

    function editNearbyServiceTitle(action) {
        $('#nearbyservice_title_modal_action').html(action);
        $('#NearbyServiceTitle_Modal').modal('show');
    }

    function saveNearbyServiceTitle() {
        if ($('#vNearbyServiceTitle_<?= $default_lang ?>').val() == "") {
            $('#vNearbyServiceTitle_<?= $default_lang ?>_error').show();
            $('#vNearbyServiceTitle_<?= $default_lang ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vNearbyServiceTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vNearbyServiceTitle_Default').val($('#vNearbyServiceTitle_<?= $default_lang ?>').val());
        $('#vNearbyServiceTitle_Default').closest('.row').removeClass('has-error');
        $('#vNearbyServiceTitle_Default-error').remove();
        $('#NearbyServiceTitle_Modal').modal('hide');
    }

    $('#saveNearbyServiceSection').click(function() {
        var vNearbyServiceTitleArr = $('[name^="vNearbyServiceTitle_"]').serializeArray();
        var vTitleArr = {};
        $.each(vNearbyServiceTitleArr, function(key, value) {
            if(value.name != "vNearbyServiceTitle_Default") {
                var name_key = value.name.replace('vNearbyServiceTitle', 'vTitle');
                vTitleArr[name_key] = value.value;
            }
        });

        var saveNearbyServices = $('#saveNearbyServices').val();

        var postData = new FormData();
        postData.append('vTitleArr', JSON.stringify(vTitleArr));

        $('[name="vNearbyImage[]"]').each(function(i) {
            postData.append('vImage['+i+']', $(this)[0].files[0]);
        });

        $('[name="vNearbyImageOld[]"]').each(function(i) {
            postData.append('vImageOld['+i+']', $(this).val());
        });

        $('[name="iCategoryId[]"]').each(function(i) {
            if($(this).is(':checked')) {
                postData.append('iCategoryId['+i+']', $(this).val());
            }   
        });

        $('[name="iCategoryIdVal[]"]').each(function(i) {
            postData.append('iCategoryIdVal['+i+']', $(this).val());
        });

        $('[name="iDisplayOrderNearbyArr[]"]').each(function(i) {
            postData.append('iDisplayOrderNearbyArr['+i+']', $(this).val());
        });

        postData.append('ViewType', 'GridView');
        postData.append('ServiceType', 'NearBy');
        postData.append('saveNearbyServices', saveNearbyServices);

        saveHomeScreenData('saveNearbyServiceSection', postData);
    });

    function saveServicesNearby(eStatus) {
        $('#saveNearbyServices').val(eStatus);
        $('#nearbyservices_modal').modal('hide');
    }

    $('[name="iCategoryId[]"]').change(function (e) {
        if ($(this).is(':checked')) {
            if ($('[name="iCategoryId[]"]:checked').length > 3) {
                alert("You can only enable 3 nearby categories to be shown on App home screen.");
                $(this).prop('checked', false);
                e.stopPropagation();
                e.preventDefault();
            } else {
                $(this).closest('tr').find('select, input[type="file"]').show();
            }
        } else {
            $(this).closest('tr').find('select, input[type="file"]').hide();
            $(this).closest('tr').find('select').val('1');
            $(this).closest('tr').find('input[type="file"]').val('').bootstrapSwitch();
        }
    });

    function saveHomeScreenData(saveBtnId, postData, isImageUpload = 'Yes') {
        $('#' + saveBtnId).prop('disabled', true);
        $('#' + saveBtnId).append(' <i class="fa fa-spinner fa-spin"></i>');
        var ajaxData = {
            'URL': '<?= $tconfig['tsite_url_main_admin'] ?>ajax_manage_app_home_screen.php',
            'AJAX_DATA': postData
        };

        if(isImageUpload == "Yes") {
            ajaxData.REQUEST_CONTENT_TYPE = false;
            ajaxData.REQUEST_PROCESS_DATA = false;
        }
        getDataFromAjaxCall(ajaxData, function(response) {
            $('#' + saveBtnId).prop('disabled', false);
            if(response.action == "1") {
                var responseData = JSON.parse(response.result);
                if(responseData.Action == "1") {
                    $('#' + saveBtnId).find('i').remove();
                    $('#' + saveBtnId).append(' <i class="fa fa-check"></i>');
                    setTimeout(function() {
                        $('#' + saveBtnId).find('i').remove();
                    }, 3000);
                } else {
                    $('#' + saveBtnId).find('i').remove();
                    $('#' + saveBtnId).append(' <i class="fa fa-times"></i>');
                    setTimeout(function() {
                        $('#' + saveBtnId).find('i').remove();
                    }, 3000);
                    show_alert("", responseData.message, "", "Ok", "", function (btn_id) {}, true, true, true);
                }
            }
            else {
                $('#' + saveBtnId).find('i').remove();
                $('#' + saveBtnId).append(' <i class="fa fa-times"></i>');
                setTimeout(function() {
                    $('#' + saveBtnId).find('i').remove();
                }, 3000);
                show_alert("", "Something went wrong.", "", "Ok", "", function (btn_id) {}, true, true, true);
            }
        });
    }

    function previewImage(elem, event) {
        var img_id = $(elem).data('img');
        $('#' + img_id).attr('src', URL.createObjectURL(event.target.files[0]));
        $('#' + img_id).css('height', '100px');
    }

    $(".txt-color").on("input", function () {
        var color = $(this).val();
        var input_id = $(this).data('id');
        $('#' + input_id).val(color);
    });


    $(".bg-color").on("input", function () {
        var color = $(this).val();
        var input_id = $(this).data('id');
        $('#' + input_id).val(color);
    });


    function openTabContent(evt, Pagename, tabcontent_hide) {
        // Declare all variables
        var i, tabcontent, tablinks;

        // Get all elements with class="tabcontent" and hide them
        tabcontent = $('.' + tabcontent_hide).hide();

        // Get all elements with class="tablinks" and remove the class "active"
        tablinks = $(evt.currentTarget).closest('.tab').find('.tablinks').removeClass('active');

        // Show the current tab, and add an "active" class to the button that opened the tab
        $('#' + Pagename).show();
        $(evt.currentTarget).addClass('active');
    }
</script>
</body>
<!-- END BODY-->
</html>