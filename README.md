# Paranoia!
### What is Paranoia ?
Paranoia is simple generic payment client for payment api of popular turkish banks. It provides simple usability 
with single interface for more than one payment api.

## How ?
All payment transaction is very easy with Paranoia in a few steps.
- Create a payment request.
- Create a payment instance with factory.
- Call intended transaction method of payment instance with request object.
That's it!

## Usage:
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

## Supported Adapters:
- Est (İşbankası, Akbank, Finansbank, Denizbank, Kuveytturk, Halkbank, Anadolubank, ING Bank, Citibank, Cardplus)
- Gvp (Denizbank, TEB, ING, Şekerbank, TFKB, Garanti)  - Coming soon
- Posnet  (Yapı Kredi, Vakıfbank, Anadolubank) - Coming soon

## Supported Currency Codes:
- TRL: Turkish Lira
- USD: U.S. Dollar
- EUR: Euro

## Features:
- sale
- cancel
- refund
- preauthorization
- postauthorization
- payment with 3d secure - coming soon
- order inquiry - coming soon
- point query & usage (for est, gvp and posnet) - coming soon
- Pay with Turkcell Cuzdan - coming soon
- Pay with Isbank QRCode - coming soon

## Contribution:
Also you can contribute to Paraonoia project. If you want to contribute to the project, please perform the following
steps:
### Preparation:
- Fork this repository. Click to
  https://github.com/ibrahimgunduz34/paranoia/fork
- clone the forked repository to your local machine.  

```sh
$ git clone git@github.com:youruser/paranoia.git
```

- Add this repository to remote repository as upstream to your local environment.  

```sh
$ git remote add upstream https://github.com/ibrahimgunduz34/paranoia
```

### Contribution:
- Choose a issue in available issues or create a new
- Update your local repository with remote upstream.  

```sh
$ git checkout master
$ git fetch upstream
$ git merge upstream/master
```

- Create and checkout a new branch.  

```sh
$ git checkout -b <yourbranchname-issueid>
```

- Write your magic code...
- Add your changes to index and commit to local repository.  

```sh
$ git add .
$ git commit -m "#<issueid> Short description about your changes."
```

- Push your changes to remote origin.  

```sh
$ git push origin <yourbranchname-issueid>
```

- And finaly send pull request to us. That's it!  

