# Paranoia!
### What is Paranoia ?
Paranoia is simple generic payment client for payment api of popular turkish banks. It provides simple usability 
with single interface for more than one payment api.

### How ?
All payment transaction is very easy with Paranoia in a few steps.
- Create a payment request.
- Create a payment instance with factory.
- Call intended transaction method of payment instance with request object.
That's it!

### Supported Adapters:
- Est (Akbank, Finansbank, İş Bankası, HSBC) 
- Gvp (Garanti Bankası, Teb Bonus)  - Coming soon
- Posnet  (Yapı Kredi, Vakıfbank, Anadolubank) - Coming soon

### Supported Currency Codes:
- TRL: Turkish Lira
- USD: U.S. Dollar
- EUR: Euro

### Supported Transactions:
- sale
- cancel
- refund
- preauthorization - coming soon
- postauthorization - coming soon
- payment with 3d secure - coming soon

### Usage:
```php
//loading configuration.
$config = new Zend_Config_Ini(APPLICATION_PATH . '/config/payment.ini', APPLICATION_ENV);

//creating payment request.
$request = new \Payment\Request();
$request->setCardNumber('5105105105105100')
        ->setSecurityCode('510')
        ->setExpireMonth(3)
        ->setExpireYear(2014)
        ->setOrderNumber('ORD000001')
        ->setAmount(100.35)
        ->setCurrency('TRL');  // Supported currency types: TRL, USD, EUR

//creating payment instance
$instance = \Payment\Factory::createInstance($config, 'Akbank');
try {
    // Performing sale transaction. Also you can perform cancel, refund and inquiry transaction.
    $response = $instance->sale($request);

    if( $response->isSuccess() ) {
        echo 'Payment is performed successfuly.';   
    } else {
        echo 'Payment is failed.';
    }
catch(Exception $e) {
    if($e instanceof \Payment\Exception\UnexpectedResponse) {
        echo 'Provider is responded an unexpected response.';
    } elseif( $e instanceof \Payment\Adapter\Container\Exception\ConnectionFailed ) {
        echo 'Provider connection is failed.';
    }
}
```
