<?php

namespace app\controllers;

use app\models\Bank;
use app\models\BankDetails;
use app\models\ChartAccounts;
use Yii;
use yii\web\Controller;

class BankController extends Controller
{
public function actionIndex(){
    $tipoBanco = 0;
    $Banco = '';
    if(isset($_GET["values"])){
        $data = json_decode($_GET["values"]);
        yii::debug($data->TipoCuenta);
        if($data->TipoCuenta == 'Ahorros'){
            $tipoBanco = 2;
        }
       else{$tipoBanco = 1;}


        $consulta = Bank::find()->where(['like', 'name', $data->Banco])->asArray()->one();;
        $Banco = $consulta["id"];
        $chart_account=New ChartAccounts;
        $chart_account->institution_id=1;
        $chart_account->parent_id=13125;
        $chart_account->code="1.1.1.8";
        $chart_account->slug=$data->Banco." ".$data->NombreCuenta;
        $chart_account->bigparent_id=NULL;
        $chart_account->save();
        $id=ChartAccounts::findOne([$chart_account->id_ins])->id;
        if($chart_account->save()) {
            $bank_details = new BankDetails();
            $bank_details->name = $data->NombreCuenta;
            $bank_details->number_account = intval($data->NumeroCuenta);
            $bank_details->chart_account_id = $id;
            $bank_details->city_id = strval(178);
            $bank_details->status = true;
            $bank_details->bank_type_id = $tipoBanco;
            $bank_details->bank_id = $Banco;
            $bank_details->save();
            yii::debug($bank_details->errors);
        }


    }

}

}