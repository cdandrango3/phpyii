<?php

namespace app\controllers;

use app\models\AccountingSeats;
use app\models\AccountingSeatsDetails;
use app\models\Bank;
use app\models\BankDetails;
use app\models\Charges;
use app\models\ChargesDetail;
use app\models\ChartAccounts;
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

class IngresosController extends Controller
{
    public function behaviors()
    {
        return ArrayHelper::merge([
            [
                'class' => Cors::className(),
            ],
        ], parent::behaviors());
    }

    public function actionIndex($values)
    {
        if (isset($_GET["values"])) {
            $data = $_GET["values"];
            yii::debug($data);
            $json = json_decode($data);
            $headfact = new Headfact();
            yii::debug($json);
            $person=Person::find()->where(["id_myhouse"=>$json->Usuario])->one();
            $nfac=$headfact->find()->select(["n_documentos"])->where(["id"=>($headfact->find()->select(["MAX(id)"]))])->asArray()->one();
            $num=explode("-",$nfac["n_documentos"])[2]?:1;
            $headfact->n_documentos = $this->getnfact(1,3)."-".$this->getnfact(1,3)."-".$this->getnfact(intval($num)+1,12);
            $headfact->autorizacion = "";
            $headfact->id_personas = $person->cedula;
            $headfact->tipo_de_documento = "Cliente";
            $headfact->save();
            yii::debug($headfact->errors);

            if ($headfact->save()) {
                $sum = 0;
                $sumiva=0;
                $sum12=0;
                $sum0=0;
                if (!empty($json->Cobros)) {
                    foreach ($json->Cobros as $val) {
                        $id_product = new Product;
                        $i_pro = $id_product::find()->where(['like', 'name', $val->SubRubro])->asArray()->one();
                        $facbody = new FacturaBody;
                        $facbody->cant = 1;
                        $isiva=$i_pro["product_iva_id"];
                        $facbody->precio_u = ($isiva>0)?$val->Valor-($val->Valor)*($isiva/100):$val->Valor;
                        $sumiva+=($isiva>0)?($val->Valor)*($isiva/100):0;
                        $facbody->precio_total =$facbody->precio_u * $facbody->cant ;
                        $facbody->id_producto = $i_pro["id"];
                        $facbody->id_head = $headfact->n_documentos;
                        $facbody->save();
                        $sum12+=($isiva>0)?$facbody->precio_total:0;
                        $sum0+=($isiva==0)?$facbody->precio_total:0;
                    }
                }
                else{
                    $id_product = new Product;
                    $i_pro = $id_product::find()->where(['like', 'name', $json->SubRubro])->asArray()->one();
                    $facbody = new FacturaBody;
                    $facbody->cant = 1;
                    $isiva=$i_pro["product_iva_id"];
                    $facbody->precio_u = ($isiva>0)?($json->Valor)-($json->Valor)*($isiva/100):$json->Valor;
                    $sumiva+=($isiva > 0)?($json->Valor)*($isiva/100):0;
                    $facbody->precio_total =$facbody->precio_u * $facbody->cant ;
                    $facbody->id_producto = $i_pro["id"];
                    $facbody->id_head = $headfact->n_documentos;
                    $facbody->save();
                    $sum12+=($isiva>0)?$facbody->precio_total:0;
                    $sum0+=($isiva==0)?$facbody->precio_total:0;
                }
                $facturafin = new Facturafin;
                $c = rand(1, 100090000);
                $this->id = $c;
                $facturafin->id = $c;
                $facturafin->subtotal12 = $sum12 ?: 0;
                $facturafin->subtotal0 = $sum0?:0;
                $facturafin->iva = $sumiva;
                $facturafin->total = $facturafin->subtotal12 + $facturafin->subtotal0+ $facturafin->iva;
                $facturafin->description = $json->Descripcion;
                $facturafin->id_head = $headfact->n_documentos;
                $facturafin->save();
                if ($facturafin->save()) {
                    $tipo = $headfact->tipo_de_documento;
                    if ($tipo == "Cliente") {
                        //* Aqui inicia si es una compra//
                        $ch1 = Clients::findOne(['person_id' => $headfact->id_personas]);
                        $accou_c = $ch1->chart_account_id;
                        $ins = Person::findOne(['id' => $headfact->id_personas]);
                        $accounting_seats = new AccountingSeats;
                        $descripcion = $facturafin->description;
                        $nodeductible = False;
                        $status = True;
                        $h = rand(1, 10000000);
                        $accounting_seats->id = $h;
                        $accounting_seats->head_fact = $headfact->n_documentos;
                        $accounting_seats->institution_id = 1;
                        $accounting_seats->description = $descripcion;
                        $accounting_seats->nodeductible = $nodeductible;
                        $accounting_seats->status = $status;
                        $accounting_seats->type = "ingresos";
                        if ($accounting_seats->save()) {
                            $debea = $accou_c;
                            $bodyf = FacturaBody::find()->where(['id_head' => $headfact->n_documentos])->all();
                            $haber = array();
                            $suma = array();
                            $sum = 0;
                            foreach ($bodyf as $bod) {
                                $cos = Product::findOne(["id" => $bod->id_producto]);
                                $sum = $sum + ($bod->precio_total);
                                if (!(is_null($cos->charingresos))) {
                                    $haber[] = $cos->charingresos;
                                    $suma[] = $bod->precio_total;
                                }

                            }


                            Yii::debug(count($haber));
                            if (count($haber) != 0) {
                                $debea = $accou_c;
                                $haber[] = 13273;
                                $i = count($haber);
                                $count = 0;

                                $accounting_seats_details = new AccountingSeatsDetails;
                                $accounting_seats_details->accounting_seat_id = $accounting_seats->id;
                                $accounting_seats_details->chart_account_id = $debea;
                                $accounting_seats_details->debit = $facturafin->total;
                                $accounting_seats_details->credit = 0;
                                $accounting_seats_details->cost_center_id = 1;
                                $accounting_seats_details->status = true;
                                $accounting_seats_details->save();
                                foreach ($haber as $habe) {

                                    if ($count < $i - 1) {
                                        $accounting_seats_details = new AccountingSeatsDetails;
                                        $accounting_seats_details->accounting_seat_id = $accounting_seats->id;
                                        $accounting_seats_details->chart_account_id = $habe;
                                        $accounting_seats_details->debit = 0;
                                        $accounting_seats_details->credit = $suma[$count];
                                        $accounting_seats_details->cost_center_id = 1;
                                        $accounting_seats_details->status = true;
                                        $accounting_seats_details->save();

                                    } else {
                                        $accounting_seats_details = new AccountingSeatsDetails;
                                        $accounting_seats_details->accounting_seat_id = $accounting_seats->id;
                                        $accounting_seats_details->chart_account_id = $habe;
                                        $accounting_seats_details->debit = 0;
                                        $accounting_seats_details->credit = $facturafin->iva;;
                                        $accounting_seats_details->cost_center_id = 1;
                                        $accounting_seats_details->status = true;
                                        $accounting_seats_details->save();
                                    }
                                    $count = $count + 1;

                                }
                            }
                            $gr = rand(1, 100090000);
                            //Aqui inicia Inventarios
                            $bodyf = FacturaBody::find()->where(['id_head' => $headfact->n_documentos])->all();
                            $sum = 0;
                            $debe = array();
                            $haber = array();
                            $suma = array();
                            foreach ($bodyf as $bod) {
                                $cos = Product::findOne(["id" => $bod->id_producto]);
                                Yii::debug($cos);
                                if (!(is_null($cos->Chairinve))) {
                                    $sum = $sum + (($cos->costo) * ($bod->cant));
                                    $debe[] = $cos->Chairinve;
                                    $haber[] = $cos->chairaccount_id;
                                    $suma[] = ($cos->costo) * ($bod->cant);
                                    yii::debug($haber);
                                }


                            }
                            if (count($haber) != 0) {
                                $accounting_sea = new AccountingSeats;
                                $accounting_sea->head_fact = $headfact->n_documentos;
                                $accounting_sea->id = $gr;
                                $accounting_sea->institution_id = $_SESSION['id_ins']->id;
                                $accounting_sea->description = $descripcion;
                                $accounting_sea->nodeductible = $nodeductible;
                                $accounting_sea->status = $status;
                                $accounting_sea->type = "inventario";
                                if ($accounting_sea->save()) {

                                    $pro = Yii::$app->request->post("Product");
                                    for ($i = 0; $i < count($debe); $i++) {
                                        $accounting_seats_details = new AccountingSeatsDetails;
                                        $accounting_seats_details->accounting_seat_id = $accounting_sea->id;
                                        $accounting_seats_details->chart_account_id = $debe[$i];
                                        yii::debug($debe[$i]);
                                        $accounting_seats_details->debit = $suma[$i];
                                        $accounting_seats_details->credit = 0;
                                        $accounting_seats_details->cost_center_id = 1;
                                        $accounting_seats_details->status = true;
                                        $accounting_seats_details->save();
                                        $accounting_seats_details = new AccountingSeatsDetails;
                                        $accounting_seats_details->accounting_seat_id = $accounting_sea->id;
                                        $accounting_seats_details->chart_account_id = $haber[$i];
                                        $accounting_seats_details->debit = 0;
                                        $accounting_seats_details->credit = $suma[$i];
                                        $accounting_seats_details->cost_center_id = 1;
                                        $accounting_seats_details->status = true;
                                        $accounting_seats_details->save();
                                    }
                                }

                    }
                            $chargem = new Charges();
                            $chargem->n_document = $headfact->n_documentos;;
                            $chargem->person_id = $person->id;
                            $chargem->Description = $json->Descripcion;;
                            $chargem->type_charge = "Cobro";
                            $chargem->save();
                            if ($chargem->save()) {
                                $bank=BankDetails::find()->where(["number_account"=>$json->Formas->NCuenta])->one();
                                $charges_detail = new ChargesDetail();
                                $charges_detail->id_charge = $chargem->id;
                                $charges_detail->chart_account = ($json->Formas->TipoMetodo=="Caja")?1770:$bank->chart_account_id;
                                $charges_detail->Description = $json->Descripcion;;
                                $charges_detail->balance = $facturafin->total;
                                $charges_detail->comprobante = ($json->Comprobante)?:"efectivo";
                                $charges_detail->saldo = $facturafin->total;
                                $charges_detail->amount=$facturafin->total;
                                $charges_detail->type_transaccion = ($json->Formas->TipoMetodo=="Caja")?"Caja":"Transferencia";
                                $charges_detail->save();
                                if ($charges_detail->save()) {
                                    $charges_detail->updateAttributes(['saldo' => ($facturafin->total) - ($charges_detail->amount)]);
                                }

                                $gr = rand(1, 100090000);
                                $charges_detail->updateAttributes(['id_asiento' => $gr]);
                                if ($chargem->type_charge == "Cobro") {
                                    if ($charges_detail->type_transaccion == "Caja") {
                                        $this->asientoscreate($gr, $charges_detail->chart_account, 13133, $charges_detail->amount, $chargem->n_document, $charges_detail->Description);

                                    } else {
                                        if ($charges_detail->type_transaccion == "Transferencia" || $chargem->type_charge == "Cheque") {
                                            $this->asientoscreate($gr, $charges_detail->chart_account, 13133, $charges_detail->amount, $chargem->n_document, $charges_detail->Description);
                                        }
                                    }

                                }
                            }
                }
                    }
                }
            }
            return $this->renderContent("<h1>No se encontro</h1>");
        }
    }
        public function eliminar_tildes($cadena)
        {

            //Codificamos la cadena en formato utf8 en caso de que nos de errores
            $cadena = utf8_encode($cadena);

            //Ahora reemplazamos las letras
            $cadena = str_replace(
                array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
                array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
                $cadena
            );

            $cadena = str_replace(
                array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
                array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
                $cadena);

            $cadena = str_replace(
                array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î', 'í'),
                array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
                $cadena);

            $cadena = str_replace(
                array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
                array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
                $cadena);

            $cadena = str_replace(
                array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
                array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
                $cadena);

            $cadena = str_replace(
                array('ñ', 'Ñ', 'ç', 'Ç'),
                array('n', 'N', 'c', 'C'),
                $cadena
            );

            return $cadena;
        }
    public function asientoscreate($gr,$debe,$haber,$body,$id_head,$description){
        $accounting_sea=new AccountingSeats;
        $accounting_sea->id= $gr;
        $accounting_sea->institution_id=1;
        $accounting_sea->description=$description;
        $accounting_sea->head_fact=$id_head;
        $accounting_sea->nodeductible=false;
        $accounting_sea->status=true;
        if($accounting_sea->save()) {

            $debe = $debe;
            $haber = $haber;

            $accounting_seats_details = new AccountingSeatsDetails;
            $accounting_seats_details->accounting_seat_id = $accounting_sea->id;
            $accounting_seats_details->chart_account_id = $debe;
            $accounting_seats_details->debit = $body;
            $accounting_seats_details->credit = 0;
            $accounting_seats_details->cost_center_id = 1;
            $accounting_seats_details->status = true;
            $accounting_seats_details->save();
            $accounting_seats_details = new AccountingSeatsDetails;
            $accounting_seats_details->accounting_seat_id = $accounting_sea->id;
            $accounting_seats_details->chart_account_id = $haber;
            $accounting_seats_details->debit = 0;
            $accounting_seats_details->credit = $body;
            $accounting_seats_details->cost_center_id = 1;
            $accounting_seats_details->status = true;
            $accounting_seats_details->save();
        }
    }
    function getnfact($input, $pad_len = 7, $prefix = null){
        if ($pad_len <= strlen($input))
            trigger_error('<strong>$pad_len</strong> cannot be less than or equal to the length of <strong>$input</strong> to generate invoice number', E_USER_ERROR);

        if (is_string($prefix))
            return sprintf("%s%s", $prefix, str_pad($input, $pad_len, "0", STR_PAD_LEFT));

        return str_pad($input, $pad_len, "0", STR_PAD_LEFT);
    }

    }

