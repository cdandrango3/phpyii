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

class CuentasPagarController extends Controller
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

            $Cantidad = $_POST["Cantidad"];
            $Costo = $_POST["Costo"];
            $CuentaBanco = $_POST["CuentaBanco"];
            $CuentaNombre = $_POST["CuentaNombre"];
            $CuentaUid = $_POST["CuentaUid"];
            $Detalle = $_POST["Detalle"];
            $Fecha = $_POST["Fecha"];
            $FechaFactura = $_POST["FechaFactura"];
            $Nombre = $_POST["Nombre"];
            $NumeroFactura = $_POST["NumeroFactura"];
            $Pagado = $_POST["Pagado"];
            $Plazo = $_POST["Plazo"];
            $ProveedorCelular = $_POST["ProveedorCelular"];
            $ProveedorId = $_POST["ProveedorId"];
            $ProveedorNombre = $_POST["ProveedorNombre"];
            $ProveedorRuc = $_POST["ProveedorRuc"];
            $Rubro = $_POST["Rubro"];
            $SubRubro = $_POST["SubRubro"];

            $postdata = json_encode(
                array(
                    'fields' => array(
                            'Cantidad'=>array('stringValue'=>$Cantidad),
                            'Costo'=>array('stringValue'=>$Costo),
                            'CuentaBanco'=>array('stringValue'=>$CuentaBanco),
                            'CuentaNombre'=>array('stringValue'=>$CuentaNombre),
                            'CuentaUid'=>array('stringValue'=>$CuentaUid),
                            'Detalle'=>array('stringValue'=>$Detalle),
                            'Fecha'=>array('stringValue'=>$Fecha),
                            'FechaFactura'=>array('stringValue'=>$FechaFactura),
                            'Nombre'=>array('stringValue'=>$Nombre),
                            'NumeroFactura'=>array('stringValue'=>$NumeroFactura),
                            'Pagado'=>array('stringValue'=>$Pagado),
                            'Plazo'=>array('stringValue'=>$Plazo),
                            'ProveedorCelular'=>array('stringValue'=>$ProveedorCelular),
                            'ProveedorId'=>array('stringValue'=>$ProveedorId),
                            'ProveedorNombre'=>array('stringValue'=>$ProveedorNombre),
                            'ProveedorRuc'=>array('stringValue'=>$ProveedorRuc),
                            'Rubro'=>array('stringValue'=>$Rubro),
                            'SubRubro'=>array('stringValue'=>$SubRubro),

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
            try{
                $result = file_get_contents("https://firestore.googleapis.com/v1/projects/micasitaapp-d4b5c/databases/(default)/documents/conjuntos/9SUqbinOZqfRVN1mEKf4/cuentasPagar?key=AIzaSyBQc3z-lFzBoX2jx0d-XArGSKgdSpbDRkg", false, $context);
      
            }catch(e){

            }

            return $this->renderContent("<h1>No se encontro</h1>");

        
    }
}
