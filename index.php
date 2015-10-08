<?php

use PayGate\Common\CreditCard;
use PayGate\PayGate;
use PayGate\Common;
use Silex\Application;
use Silex\Provider;

require __DIR__ . '/vendor/autoload.php';

// create Silex APP
$app = new Application();
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/views',
));

$app['debug'] = true;


//For production move config above main web folder!
$env = getenv('APP_ENV') ? : 'prod';

try {
    $app->register(new Igorw\Silex\ConfigServiceProvider(__DIR__ . "/config/" . $env . ".json"));
} catch (\Exception $ex) {
    exit('Please check config file!');
}


$app->before(function () use ($app) {
    $app["twig"]->addGlobal('baseurl', $app['request']->getBaseUrl());
    $app["twig"]->addGlobal('config', $app['APP_CONFIG']);
});

// Database
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'mysqli',
        'host'     => $app['APP_CONFIG']['DB']['host'],
        'dbname'   => $app['APP_CONFIG']['DB']['dbname'],
        'user'     => $app['APP_CONFIG']['DB']['user'],
        'password' => $app['APP_CONFIG']['DB']['password'],
        'charset'  => 'utf8'
    ),
));
$app['db']->connect();

//check that table exists
try {
    $app['db']->fetchAll('SELECT * FROM orders');
} catch (\Exception $e) {
    exit('No Database Connection! Please check settings and table schemas!');
}


// route to default order form
$app->get('/', function() use ($app) {

    return $app['twig']->render('index.twig', array('errors' => array(), 'success' => ''));
});

//route to order form submission
$app->post('/', function() use ($app) {


    $isCardOk       = true;
    $formErrors     = array();
    $gateWay        = 'Braintree';
    $paymentSucceed = false;

    //init card and verify
    $cardInputData = array(
        'firstName'       => $app['request']->get('order_cc_fname'),
        'lastName'        => $app['request']->get('order_cc_lname'),
        'number'          => $app['request']->get('order_cc_number'),
        'expiryMonth'     => $app['request']->get('order_cc_exp_m'),
        'expiryYear'      => $app['request']->get('order_cc_exp_y'),
        'cvv'             => $app['request']->get('order_cc_cvv'),
        'billingAddress1' => 'address line 1',
        'billingCity'     => 'City',
        'billingPostcode' => '20000',
        'billingCountry'  => 'TH',
    );

    try {
        $ccard = new CreditCard($cardInputData);
        $ccard->validate();
    } catch (Exception $e) {
        $formErrors[] = 'Verify Credit Card Data!';
        $isCardOk     = false;
    }

    //
    //   Card is ok - select proper payment method and card brand based on these rules:
    // - if credit card type is AMEX, then use Paypal.
    // - if currency is USD, EUR, or AUD, then use Paypal. Otherwise use Braintree.
    // - if currency is not USD and credit card is AMEX, return error message, that AMEX is possible to use only for USD

    if ($isCardOk) {

        if ($ccard->getBrand() == 'amex' || in_array($app['request']->get('order_currency'), array('USD', 'EUR', 'AUD'))) {

            $gateWay = 'PayPal_Rest';
        } else {

            //amex with USD only!
            if ($ccard->getBrand() == 'amex' && $app['request']->get('order_currency') != 'USD') {
                $formErrors[] = 'AMEX is possible to use only for USD!';
            }
        }
    }

    if (!count($formErrors)) {// no errors - create a payment
        $gateway = PayGate::create($gateWay);

        try {

            $gateway->initialize($app['APP_CONFIG']['payment_methods'][$gateWay]);
            $requestParams = array('amount' => number_format($app['request']->get('order_price'), 2), 'currency' => $app['request']->get('order_currency'), 'card' => $ccard);

            if ($gateWay == 'Braintree') {
                $clientToken            = $gateway->clientToken(['card' => $ccard])->send()->getToken();
                $requestParams['token'] = 'fake-valid-nonce';
            }

            $response = $gateway->purchase($requestParams)->send();

            if ($response->isSuccessful()) {

                // all ok, save to db
                $paymentSucceed = true;

                $app['db']->insert('orders', array('transaction_info' => var_export($response->getData(), 1)));
                
            } elseif ($response->isRedirect()) {

                // some redirect?
                // save to db and redirect
                $response->redirect();
            } else {
                // payment failed
                $formErrors[] = "Sorry, there was a transaction error:";
                $formErrors[] = $response->getMessage();
            }
        } catch (\Exception $e) {

            // internal error
            $formErrors[] = "Sorry, there was an internal error:";
            $formErrors[] = $e->getMessage();
        }
    }

    return $app['twig']->render('index.twig', array('errors' => $formErrors, 'success' => $paymentSucceed));
});


// submit gateway purchase
$app->post('/gateways/{name}/purchase', function($name) use ($app) {
    $gateway    = PayGate::create($name);
    $sessionVar = 'paygate.' . $gateway->getShortName();
    $gateway->initialize((array) $app['session']->get($sessionVar));

    // load POST data
    $params = $app['request']->get('params');
    $card   = $app['request']->get('card');

    // save POST data into session
    $app['session']->set($sessionVar . '.purchase', $params);
    $app['session']->set($sessionVar . '.card', $card);

    $params['card']     = $card;
    $params['clientIp'] = $app['request']->getClientIp();
    $response           = $gateway->purchase($params)->send();

    return $app['twig']->render('response.twig', array(
                'gateway'  => $gateway,
                'response' => $response,
    ));
});


$app->run();
