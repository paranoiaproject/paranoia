# 3.5. İade İşlemi

## 3.5.1. Genel Bakış

İade işlemi, ödeme işleminden sonraki günlerde tahsil edilen paranın müşteri hesabına geri iade edilmesidir.

## 3.5.2. İade İsteği Oluşturma

İade isteği, **Paranoia\Payment\Request**  tipinde bir nesnenin sipariş numarası ile doldurulması suretiyle elde edilir.

```php
$request = new \Paranoia\Payment\Request();
$request->setOrderId('987654321');
```

## 3.5.3. İade İsteği Sırasında Beklenen Parametreler

| Parametre | Tip | Zorunluluk | Açıklama |
| ----------| ---- | -------------- | -------------------- |
| OrderId | String | Evet | Banka veya sizin tarafınızdan belirlenmiş olan sipariş numarası |


## 3.5.4. İade İşleminin Gerçekleştirilmesi.

* İade işlemi için yeni bir iade isteği oluşturuyoruz.
```php
$request = new \Paranoia\Payment\Request();
$request->setOrderId('1234567890');
```

* Sağlayıcı API'sine bağlantı kurmak için gerekli konfigürasyon bilgilerini dolduruyoruz. Konfigürasyon parametreleri, ödeme sistemi sağlayıcısına göre değişkenlik göstermektedir. Ödeme sağlayıcılarına göre gerekli konfigürasyon tanımlamaları hakkında daha fazla bilgi edinmek için [bu bölümü]() inceleyiniz.
```php
$configuration = new \Paranoia\Configuration\NestPay();
$configuration->setClientId('123456789')
        ->setUsername('API_USERNAME')
        ->setPassword('API_PASSWORD')
        ->setMode('P');

```

* İade işlemini gerçekleştiriyoruz. Sağlayıcı uyarlamaları, Sağlayıcının [2. Desteklenen Ödeme Sistemleri](/docs/2-desteklenen-odeme-sistemleri.md) dökümanında belirtilen **ödeme sistemi** nin adı ile adlandırılmışlardır. Örnekte belirtilen NestPay uyarlaması için \Paranoia\Payment\Adapter\**NestPay** sınıfını kullanabildiğiniz gibi Posnet uyarlaması için **\Paranoia\Payment\Adapter\**Posnet** sınıfını kullanabilirsiniz.
```php
try {
        $adapter = new \Paranoia\Payment\Adapter\NestPay($configuration);
        $response = $adapter->cancel($request);
} catch(\Paranoia\Communication\Exception\CommunicationFailed $e) {
         // Bağlantı hatası durumunda yapılacak işlemleri
         // bu bölümde greçekleştirebilirsiniz.
} catch(\Paranoia\Communication\Exception\UnexpectedResponse $e) {
        // Ödeme sistemi sağlayıcısından beklenmedik bir yanıt
        // dönmesi (boş yanıt veya beklenmedik bir hata mesajı gibi)
        // durumunda yapılacak işlemleri bu bölümde gerçekleştirebilirsiniz.
} catch(\Exception $e) {
        // Uygulamada beklenmedik bir hata meydana gelmesi durumunda
        // yapılacak işlemleri bu bölümde gerçekleştirebilirsiniz.
}

if($response->isSuccess()) {
        // İade işlemi başarılı olduğu durumda yapılacak
        // işlemleri bu bölümde gerçekleştirebilirsiniz.
} else {
        // İade işlemi başarısız olduğu durumda yapılacak
        // işlemleri bu bölümde gerçekleştirebilirsiniz.
}
```

* İşlem başarısız olduğu durumda hatanın nedeni ve hata koduna ait bilgilere aşağıdaki gibi ulaşabilirsiniz.
```php
$code = $response->getResponseCode();
$message = $response->getResponseMessage();
```

* [3. İşlemler sayfasına dön](/docs/3-islemler.md)
