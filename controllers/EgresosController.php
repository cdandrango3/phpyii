<?php

namespace app\controllers;

use app\models\AccountingSeats;
use app\models\AccountingSeatsDetails;
use app\models\Charges;
use app\models\ChargesDetail;
use app\models\Clients;
use app\models\FacturaBody;
use app\models\Facturafin;
use app\models\HeadFact;
use app\models\Institution;
use app\models\Person;
use app\models\Product;
use Yii;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\helpers\VarDumper;

class EgresosController extends Controller
{
    public function behaviors()
    {
        return ArrayHelper::merge([
            [
                'class' => Cors::className(),
            ],
        ], parent::behaviors());
    }

    public function actionIndex()
    {
            yii::debug($_POST["Fecha"]);
            yii::debug("cualquier cosa");
            $Comprobante = $_POST["Comprobante"];
            $CuentaUid = $_POST["CuentaUid"];
            $Descripcion = $_POST["Descripcion"];
            $Fecha = $_POST["Fecha"];
            $Proveedor = $_POST["Proveedor"];
            $Rubro = $_POST["Rubro"];
            $SubRubro = $_POST["SubRubro"];
            $Valor = $_POST["Valor"];
            $postdata = json_encode(
                array(
                    'fields' => array(
                            'Comprobante'=>array('stringValue'=>$Comprobante),
                            'CuentaUid'=>array('stringValue'=>$CuentaUid),
                            'Descripcion'=>array('stringValue'=>$Descripcion),
                            'Fecha'=>array('stringValue'=>$Fecha),
                            'Proveedor'=>array('stringValue'=>$Proveedor),
                            'Rubro'=>array('stringValue'=>$Rubro),
                            'SubRubro'=>array('stringValue'=>$SubRubro),
                            'Valor'=>array('stringValue'=>$Valor),
            
                    ),
                    'createTime'=>'2020-05-13T23:00:29.0Z',
                    'updateTime'=>'2020-05-13T23:00:29.0Z'
                ),

            );
            
            $opts = array('http' =>
                array(
                    'ignore_errors'=>true,
                    'method' => 'POST',
                    'header' => 'Content-type: application/json',
                    'content' => $postdata
                )
                

            );

            $context = stream_context_create($opts);

                $result = file_get_contents("https://firestore.googleapis.com/v1/projects/micasitaapp-d4b5c/databases/(default)/documents/conjuntos/9SUqbinOZqfRVN1mEKf4/egresos?key=AIzaSyBQc3z-lFzBoX2jx0d-XArGSKgdSpbDRkg", false, $context);
                yii::debug($result);


            return $this->renderContent("<h1>No se encontro</h1>");

        
    }
}
