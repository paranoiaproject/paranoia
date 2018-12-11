# 3.1. Satış İşlemi

## 3.1.1. Genel Bakış

Satış işlemi, müşterinin ödeme aracı bilgilerinin (Kredi kartı vb.) ilgili ödeme sistemi sağlayıcısının sistemine iletilip müşteri hesabının borçlandırılması şeklinde gerçekleştirilir.

## 3.1.2. Satış İsteği Oluşturma

Satış isteği, **\Paranoia\Request\Request**  tipinde bir nesnenin sipariş ve ödeme aracı (kredi kartı vb.) bilgileri ile doldurulması suretiyle elde edilir.

```php
$request = new \Paranoia\Request\Request();
$request->setOrderId('ORDER000000' . time())
        ->setAmount(100.35)
        ->setCurrency(\Paranoia\Currency::CODE_TRY);
$card = new \Paranoia\Request\Resource\Card();
$card->setNumber('5406******675403')
     ->setSecurityCode('000')
     ->setExpireMonth(12)
     ->setExpireYear(2015);
$request->setResource($card);
```

## 3.1.3. Satış İsteği Sırasında Beklenen Parametreler

| Parametre | Tip | Zorunluluk | Açıklama |
| ----------| ---- | -------------- | -------------------- |
| CardNumber | Numeric | Evet | Kart Numarası |
| SecurityCode | Numeric | Evet | Kartın arka yüzünde yazan güvenlik numarası |
| ExpireMonth | Numeric | Evet | Kartın son kullanma tarihinin ay bölümü. |
| ExpireYear | Numeric | Evet | Kartın son kullanma tarihinin 4 haneli yıl bölümü |
| OrderId | String | Hayır | Sipariş numarası. Gönderilmediği durumlarda desteklenen sağlayıcılar tarafından oluşturularak otomatik olarak işlem yanıtında döner. |
| Amount | Decimal | Evet | Sipariş Tutarı Format: ###.## |
| Currency | String | Evet | 3 Haneli para birimi kodu. TRY, EUR veya USD değerlerinden birini alabilir. |

## 3.1.4. Satış İşleminin Gerçekleştirilmesi.

* Satış işlemi için yeni bir sipariş isteği oluşturuyoruz.
```php
$request = new \Paranoia\Request\Request();
$request->setOrderId('ORDER000000' . time())
        ->setAmount(100.35)
        ->setCurrency(\Paranoia\Currency::CODE_TRY);
$card = new \Paranoia\Request\Resource\Card();
$card->setNumber('5406******675403')
     ->setSecurityCode('000')
     ->setExpireMonth(12)
     ->setExpireYear(2015);
$request->setResource($card);        
```

* Sağlayıcı API'sine bağlantı kurmak için gerekli konfigürasyon bilgilerini dolduruyoruz. Konfigürasyon parametreleri, ödeme sistemi sağlayıcısına göre değişkenlik göstermektedir. Ödeme sağlayıcılarına göre gerekli konfigürasyon tanımlamaları hakkında daha fazla bilgi edinmek için [bu bölümü]() inceleyiniz.
```php
$configuration = new \Paranoia\Configuration\NestPay();
$configuration->setClientId('123456789')
        ->setUsername('API_USERNAME')
        ->setPassword('API_PASSWORD')
        ->setMode('P');

```

* Satış işlemini gerçekleştiriyoruz. Sağlayıcı uyarlamaları, Sağlayıcının [2. Desteklenen Ödeme Sistemleri](/docs/2-desteklenen-odeme-sistemleri.md) dökümanında belirtilen **ödeme sistemi** nin adı ile adlandırılmışlardır. Örnekte belirtilen NestPay uyarlaması için \Paranoia\Pos\\**NestPay** sınıfını kullanabildiğiniz gibi Posnet uyarlaması için \Paranoia\Pos\\**Posnet** sınıfını kullanabilirsiniz.
```php
try {
        $adapter = new \Paranoia\Pos\NestPay($configuration);
        $response = $adapter->sale($request);
} catch(\Paranoia\Exception\CommunicationError $e) {
         // Bağlantı hatası durumunda yapılacak işlemleri
         // bu bölümde greçekleştirebilirsiniz.
} catch(\Exception $e) {
        // Uygulamada beklenmedik bir hata meydana gelmesi durumunda
        // yapılacak işlemleri bu bölümde gerçekleştirebilirsiniz.
}

if($response->isSuccess()) {
        // ödeme işlemi başarılı olduğu durumda yapılacak
        // işlemleri bu bölümde gerçekleştirebilirsiniz.
} else {
        // ödeme işlemi başarısız olduğu durumda yapılacak
        // işlemleri bu bölümde gerçekleştirebilirsiniz.
}
```

* Ödeme işlemi başarılı olduğu takdirde, işleme ait transaction numarası saklanmalıdır. Transaction numarası işlem iptali gerektiği durumlarda kullanılacaktır.
```php
$transactionId = $response->getTransactionId();
//...
```

* İşlem başarısız olduğu durumda hatanın nedeni ve hata koduna ait bilgilere aşağıdaki gibi ulaşabilirsiniz.
```php
$code = $response->getResponseCode();
$message = $response->getResponseMessage();
```

* [3. İşlemler sayfasına dön](/docs/3-islemler.md)
