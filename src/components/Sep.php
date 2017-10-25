<?php

namespace ahmadrezaei\yii\sep\components;

use ahmadrezaei\yii\sep\models\SepLog;
use yii\base\Component;
use yii\base\ErrorException;
use yii\web\HttpException;


class Sep extends Component
{
    /**
     * @var string
     */
    public $MerchantID = '';

    /**
     * @var string
     */
    public $Password = '';

    /**
     * @var string
     */
    public $ResNumber = '';

    /**
     * @var string
     */
    public $RefNumber = '';

    /**
     * @var bool
     */
    public $renderAjax = false;

    /**
     * @var bool
     */
    public $mysql = false;

    /**
     * @var int
     */
    public $startResNumber = 1;

    /**
     * Redirect User to Payment Page
     * @param $amount
     * @param $callBackUrl
     * @throws HttpException
     */
    public function createPayment($amount, $callBackUrl)
    {
        $this->ResNumber = rand(10000,99999);

        // check if use mysql
        if ($this->mysql) {
            $model = new SepLog();
            if($model->isEmpty()) {
                $model->id = $this->startResNumber;
            }
            if ($model->save()) {
                $this->ResNumber = $model->id;
            }
        }

        $controller =  \Yii::$app->controller;
        if( $this->renderAjax ) {
            echo $controller->renderAjax('@vendor/ahmadrezaei/yii2-sep/src/views/form', [
                'ResNumber' => $this->ResNumber,
                'MID' => $this->MerchantID,
                'Amount' => $amount,
                'RedirectURL' => $callBackUrl
            ]);
        } else {
            echo $controller->render('@vendor/ahmadrezaei/yii2-sep/src/views/form', [
                'ResNumber' => $this->ResNumber,
                'MID' => $this->MerchantID,
                'Amount' => $amount,
                'RedirectURL' => $callBackUrl
            ]);
        }
    }

    /**
     * Verify Payment
     * @param array|null $params
     * @return bool
     * @throws ErrorException
     * @throws HttpException
     */
    public function Verify($params = null)
    {
        if ($params == null) {
            $params = \Yii::$app->getRequest()->post();
        }

        if(empty($params) || is_array($params) == false)
        {
            throw new ErrorException(500, 'POST body is NULL!');
        }

        $state = $params['State'];
        $this->ResNumber = $params['ResNum'];

        // check if use mysql
        if ($this->mysql)
        {
            /** @var SepLog $model */
            $model = SepLog::findOne($this->ResNumber);

            if ($model !== null) {
                $model->ResNum = $params["ResNum"];

                if(!empty($params["RefNum"])) {
                    $model->RefNum = $params["RefNum"];
                }
                if(!empty($params["SecurePan"])) {
                    $model->CardNumber = $params["SecurePan"];
                }
                $model->data = print_r($params, true);
                $model->save();
            }
        }

        if(isset($state) && $state == 'OK' )
        {
            if(class_exists('soapclient') == false) {
                throw new HttpException(500, '"soapclient" class not found!');
            }

            $this->RefNumber = $params["RefNum"];
            $soapclient = new \soapclient('https://acquirer.samanepay.com/payments/referencepayment.asmx?WSDL');

            $result = $soapclient->VerifyTransaction($this->RefNumber, $this->MerchantID);

            if( $result > 0 ) {
                if ($this->mysql &&  isset($model)) {
                    $model->status = SepLog::STATUS_SUCCESS;
                    $model->save();
                }

                return true;
            }
        }

        // save data to database
        if ($this->mysql &&  isset($model)) {
            $model->status = SepLog::STATUS_UNSUCCESS;
            $model->save();
        }

        return false;
    }
}