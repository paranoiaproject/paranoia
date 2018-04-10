# 3.4. İptal İşlemi

## 3.4.1. Genel Bakış

İptal işlemi, gerçekleştirilen ödeme işleminin iptal edilmesidir. Yalnızca ödeme ile aynı gün içerisinde gerçekleştirilebilir. Ödeme işleminden sonraki günlerde paranın tekrar müşteriye iade edilmesi için iade işleminin gerçekleştirilmesi gerekmektedir. Konuyla ilgili dökümana [buradan](/docs/35-iade-islemi.md) ulaşabilirsiniz.

## 3.4.2. İptal İsteği Oluşturma

İptal isteği, **Paranoia\Request**  tipinde bir nesnenin transaction numarası ile doldurulması suretiyle elde edilir.

```php
$request = new \Paranoia\Request();
$request->setTransactionId('1234567890');
```

İptal işlemi sipariş numarası ile de gerçekleştirilebilmektedir.<br />
**Not:** Bu kullanım şekli bazı bankalar tarafından tavsiye edilmemektedir.
```php
$request = new \Paranoia\Request();
$request->setOrderId('987654321');
```


## 3.4.3. İptal İsteği Sırasında Beklenen Parametreler

| Parametre | Tip | Zorunluluk | Açıklama |
| ----------| ---- | -------------- | -------------------- |
| TransactionId | String | Evet**\*** | Banka tarafından ödeme hareketi için gönderilmiş olan transaction numarası. |
| OrderId | String | Evet**\*** | Banka veya sizin tarafınızdan belirlenmiş olan sipariş numarası |
**\* **: İşlem sırasında transaction numarası veya sipariş numarasından yalnızca bir tanesi kullanılabilir.

## 3.4.4. İptal İşleminin Gerçekleştirilmesi.

* İptal işlemi için yeni bir iptal isteği oluşturuyoruz.
```php
$request = new \Paranoia\Request();
$request->setTransactionId('1234567890');
```

* Sağlayıcı API'sine bağlantı kurmak için gerekli konfigürasyon bilgilerini dolduruyoruz. Konfigürasyon parametreleri, ödeme sistemi sağlayıcısına göre değişkenlik göstermektedir. Ödeme sağlayıcılarına göre gerekli konfigürasyon tanımlamaları hakkında daha fazla bilgi edinmek için [bu bölümü]() inceleyiniz.
```php
$configuration = new \Paranoia\Configuration\NestPay();
$configuration->setClientId('123456789')
        ->setUsername('API_USERNAME')
        ->setPassword('API_PASSWORD')
        ->setMode('P');

```

* İptal işlemini gerçekleştiriyoruz. Sağlayıcı uyarlamaları, Sağlayıcının [2. Desteklenen Ödeme Sistemleri](/docs/2-desteklenen-odeme-sistemleri.md) dökümanında belirtilen **ödeme sistemi** nin adı ile adlandırılmışlardır. Örnekte belirtilen NestPay uyarlaması için \Paranoia\Pos\**NestPay** sınıfını kullanabildiğiniz gibi Posnet uyarlaması için **\Paranoia\Pos\**Posnet** sınıfını kullanabilirsiniz.
```php
try {
        $adapter = new \Paranoia\Pos\NestPay($configuration);
        $response = $adapter->cancel($request);
} catch(\Paranoia\Exception\CommunicationError $e) {
         // Bağlantı hatası durumunda yapılacak işlemleri
         // bu bölümde greçekleştirebilirsiniz.
} catch(\Paranoia\Exception\UnexpectedResponse $e) {
        // Ödeme sistemi sağlayıcısından beklenmedik bir yanıt
        // dönmesi (boş yanıt veya beklenmedik bir hata mesajı gibi)
        // durumunda yapılacak işlemleri bu bölümde gerçekleştirebilirsiniz.
} catch(\Exception $e) {
        // Uygulamada beklenmedik bir hata meydana gelmesi durumunda
        // yapılacak işlemleri bu bölümde gerçekleştirebilirsiniz.
}

if($response->isSuccess()) {
        // İptal işlemi başarılı olduğu durumda yapılacak
        // işlemleri bu bölümde gerçekleştirebilirsiniz.
} else {
        // İptal işlemi başarısız olduğu durumda yapılacak
        // işlemleri bu bölümde gerçekleştirebilirsiniz.
}
```

* İşlem başarısız olduğu durumda hatanın nedeni ve hata koduna ait bilgilere aşağıdaki gibi ulaşabilirsiniz.
```php
$code = $response->getResponseCode();
$message = $response->getResponseMessage();
```

* [3. İşlemler sayfasına dön](/docs/3-islemler.md)
