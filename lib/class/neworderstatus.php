<?


use \Bitrix\Main\Config\Option;
use Bitrix\Main\Context,
    Bitrix\Main\Loader,
    Bitrix\Currency\CurrencyManager,
    Bitrix\Sale\Order,
    Bitrix\Sale\Basket,
    Bitrix\Sale\Delivery,
    Bitrix\Sale\PaySystem;


class NewOrderStatus
{
    public static $MODULE_ID = 'dmbgeo.neworderstatus';


    public static function getSites()
    {
        $SITES = array();
        $rsSites = \CSite::GetList($by = "sort", $order = "desc");
        while ($arSite = $rsSites->Fetch()) {
            $SITES[] = $arSite;
        }
        return $SITES;
    }



    public static function getOption($PARAM, $SITE_ID = SITE_ID)
    {
        return Option::get(static::$MODULE_ID, 'OPTION_' . $PARAM . '_' . $SITE_ID, "N");
    }


    public static function eventOrder(&$arFields){

        if(self::getOption('STATUS',$arFields['LID'])=="Y"){

            $orderStatus=self::getOption('ORDER_STATUS',$arFields['LID']);
            $filterDelivery=self::getOption('ORDER_FILTER_DELIVERY',$arFields['LID']);
            $filterPay=self::getOption('ORDER_FILTER_PAY',$arFields['LID']);
            if($filterDelivery!=="N"){
                if(!in_array($arFields['DELIVERY_ID'],explode(',',$filterDelivery))){
                    $filterLogs['DELIVERY_ID']=true;
                }
                else{
                    $filterLogs['DELIVERY_ID']=false;
                }
            }
            else{
                $filterLogs['DELIVERY_ID']='N';
            }
            if($filterPay!=="N"){
                if(in_array($arFields['PAY_SYSTEM_ID'],explode(',',$filterPay))){
                    $filterLogs['PAY_SYSTEM_ID']=true;
                }
                else{
                    $filterLogs['PAY_SYSTEM_ID']=false;
                }
            }
            else{
                $filterLogs['PAY_SYSTEM_ID']='N';
            }
            if(count($filterLogs)>0){
                $filter=true;
                foreach($filterLogs as $filterLog){
                    if($filterLog===false){
                        $filter=false;
                        break;
                    }
                }
                if($filter===true){
                    $arFields['STATUS_ID']=$orderStatus;
                }
            }
        }

        // if($arFields['USER_ID']=='40215'){
        //     var_dump($arFields,$orderStatus,$filterDelivery,$filterPay,$filterLogs,$filter);
        //     exit;
        // }

        return $arFields;
    }
}
