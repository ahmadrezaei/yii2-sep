Yii2 Iranian SEP Payment Gateway extension
==========================================
By this extension you can add SEP gateway to your yii2 project

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist ahmadrezaei/yii2-sep "*"
```

or add

```
"ahmadrezaei/yii2-sep": "*"
```

to the require section of your `composer.json` file.


Configuring application
-----

After extension is installed you need to setup auth client collection application component:

```php
return [
    'components' => [
        'sep' => [
            'class' => 'ahmadrezaei\yii\sep\components\Sep',
            'MerchantID' => 'YOUR-MID',
            'Password' => 'YOUR-PASSWORD',
            'mysql' => true, // If you want to save records in db
        ]
        // ...
    ],
    // ...
];
```

If you want to save records in database, create migrations:

```
php yii migrate -p=@vendor/ahmadrezaei/yii2-sep/src/migrations 
```

Usage
-----

For create a payment request:

```php
$amount = 1000; // Rial
$callBackUrl = Url::to(['callback'], true); // callback url
Yii::$app->sep->createPayment($amount, $callBackUrl);
```

For verify payment request:

```php
$sep = Yii::$app->sep;
if( $sep->verify() ) {
    // payment is successfull
    $transactionID = $sep->RefNumber;
} else {
    // payment is unsuccessfull
}
```