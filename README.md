[![Travis](https://img.shields.io/travis/paranoiaproject/paranoia.svg)](https://travis-ci.org/paranoiaproject/paranoia)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/paranoiaproject/paranoia.svg)](https://scrutinizer-ci.com/g/paranoiaproject/paranoia/)
[![Packagist](https://img.shields.io/packagist/dt/paranoiaproject/paranoia.svg)](https://packagist.org/packages/paranoiaproject/paranoia)


# Paranoia 

Paranoia is a universal payment client library that helps you to perform card payment operations in an easy way with one single interface. 

## How To Use

Prepare the provider specific configuration (In example we choosed **Nestpay** but you can pay with **Garanti** and **Posnet** APIs as well)

```php
$configuration = new \Paranoia\Configuration\NestpayConfiguration();
$configuration->setClientId('000001');
$configuration->setUsername('NESTPAYUSER');
$configuration->setPassword('NESTPAYPASS');
```

Create the client instance with the provider specific context factory by using the configuration created.

```php
$client = new \Paranoia\Client(new \Paranoia\Nestpay\NestpayContextFactory($configuration));
```

Create a charge request with the order details and the credit/debit card credentials

```php
$request = new ChargeRequest();
$request->setOrderId('0000000001');
$request->setCardNumber('5105105105105100');
$request->setCardExpireMonth(5);
$request->setCardExpireYear(2025);
$request->setCardCvv('000');
$request->setAmount(100.5);
$request->setCurrency(Currency::CODE_TRY);
```

And... pay!

```php
try {
	$result = $client->charge($request);
	// TODO: Handle the successful charge operation here.
} catch(\Paranoia\Core\Exception\UnapprovedTransactionException $transactionError) {
	// TODO: Handle unapproved transaction error here
} catch(\Paranoia\Core\Exception\CommunicationError $communicationError) {
	// TODO: Handle communication errors here
}
```

You can checkout the full documentation for the other supported transactions and supported providers.
