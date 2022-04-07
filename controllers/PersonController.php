<?php

namespace app\controllers;

use app\models\Clients;
use app\models\Person;
use Yii;
use yii\web\Controller;
class PersonController extends Controller
{
public function actionIndex(){
    ob_start();
    ob_implicit_flush(false);
    require('data.json');
    $details = json_decode(ob_get_clean(),true);
foreach($details as $value){
    $NombreC=$value["Nombre"] ." ".$value["Apellido"] ;
    $Cedula=$value["Cedula"];
    $Correo=$value["Correo"];
    $Telefono=$value["Telefono"];
    $Adress="Nelson Estupiñan N75-12 y Francisco Granizo";
    $id=$value["id"];
$person=New Person();
$person->person_type_id=1;
$person->name=$NombreC;
$person->cedula=strval($Cedula);
$person->phones=$Telefono;
$person->institution_id=1;
$person->city_id=1;
$person->address=$Adress;
$person->person_type_id=2;
$person->special_taxpayer=1;
$person->id_myhouse=$id;
$person->emails=$Correo;
$person->save();
yii::debug($person->errors);
if($person->save()){
    $clients=New Clients();
    $clients->chart_account_id=13133;
    $clients->person_id=$person->id;
    $clients->save();
};



}
}
}