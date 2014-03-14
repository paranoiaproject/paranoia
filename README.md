# Paranoia
## Nedir ?
Paranoia, Türkiye dahilinde kullanılan popüler ödeme sistemlerinin tek bir API arayüzü üzerinden basitce kullanımına olanak veren açık kaynak kodlu bir kütüphanedir.

## Nasıl çalışır ?
Paranoia kullanarak desteklenen senkron ve asenkron ödeme servisleri üzerinden kolayca satış, iptal ve iade işlemlerinizi gerçekleştirebilir, siparişlerinize ait hareketleri sorgulayabilirsiniz.

### Örnek Satış İşlemi:

```php
<?php
require "vendor/autoload.php";

use Paranoia\Payment\Factory;
use Paranoia\Payment\Request;
use Paranoia\Communication\Exception\CommunicationFailed;
use Paranoia\Payment\Exception\UnexpectedResponse;
// Iletisim sirasinda gonderilen ve alinan verileri dinlemek icin asagidaki
// satir(lar)da yeralan yorum isaretlerini kaldiriniz.
// use Paranoia\EventManager\Listener\CommunicationListener;

$config  = json_decode(file_get_contents('tests/Resources/config/config.json'));

$adapter = Factory::createInstance($config, 'estbank');

// Iletisim sirasinda gonderilen ve alinan verileri dinlemek icin asagidaki
// satir(lar)da yeralan yorum isaretlerini kaldiriniz.
// $listener = new CommunicationListener();
// $adapter->getConnector()->addListener('BeforeRequest', $listener);
// $adapter->getConnector()->addListener('AfterRequest', $listener);

$request = new Request();
$request->setCardNumber('5406675406675403')
        ->setSecurityCode('000')
        ->setExpireMonth(12)
        ->setExpireYear(2015)
        ->setOrderId('ORDER000000' . time())
        ->setAmount(100.35)
        ->setCurrency('TRY');

try {
    $response = $adapter->sale($request);
    if($response->isSuccess()) {
        print "Odeme basariyla gerceklestirildi." . PHP_EOL;
    } else {
        print "Odeme basarisiz." . PHP_EOL;
    }
} catch(CommunicationFailed $e) {
    print "Baglanti saglanamadi." . PHP_EOL;
} catch(UnexpectedResponse $e) {
    print "Banka beklenmedik bir cevap dondu." . PHP_EOL;
} catch(Exception $e) {
    print "Beklenmeyen bir hata olustu." . PHP_EOL;
}
```

## Desteklenen Ödeme Sistemleri:

* ***Est***
	* İşbankası, Akbank, Finansbank, Denizbank, Kuveytturk, Halkbank, Anadolubank, ING Bank, Citibank, Cardplus
* ***Gvp***
	* Denizbank, TEB, ING, Şekerbank, TFKB, Garanti
* ***Posnet***
	* Yapı Kredi, Vakıfbank, Anadolubank - ***Yakında***
* ***BKM Express***
	* ***Yakında***
* ***Turkcell Cüzdan***
	* ***Yakında***
* ***PayPal***
	* ***Yakında***
* ***Ininal***
	* ***Yakında***

## Desteklenen Para Birimleri:

* ***TRL:*** Türk Lirası
* ***EUR:*** Avro
* ***USD:*** Amerikan Doları

## Katkıda Bulunun:
Siz de yapacağınız geliştirmelerle açık kaynaklı Paranoia kütüphanesine katkıda bulunabilirsiniz.

Katkıda bulunmak için aşağıdaki işlem adımlarını gerçekleştirin:

### Hazırlık:

* ***git@github.com:ibrahimgunduz34/paranoia.git*** deposunu kendi github hesabınıza fork edin.
* Kendi hesabınızdaki fork edilmiş repoyu yerel geliştirme ortamınıza kopyalayın.

```sh

$ git clone git@github.com:youruser/paranoia.github

```
* ***git@github.com:ibrahimgunduz34/paranoia.git*** reposunu yerel geliştirme ortamınıza upstream olarak tanımlayın.

```sh

$ git remote add upstream https://github.com/ibrahimgunduz34/paranoia

```

### Değişikliklerin/Geliştirmelerin İletilmesi:

* Projenin issues bölümünden dilediğiniz bir konuyu seçin veya yeni bir konu yaratın.
* Geliştirmeye başlamadan önce upstreamdeki değişiklikleri yerel deponuza alın.

```sh

$ git checkout master
$ git fetch upstream
$ git merge upstream/master

```

* Yeni bir dal oluşturun ve giriş yapın.

```sh

$ git checkout -b <branch-adi-issueid>

```

* Değişikliklerinizi tamamlayın ve yerel deponuza gönderin.

```sh

$ git add .
$ git commit -m "#<issueid> Geliştirme hakkında kısa bir commit mesajı."

```

* Yerel depodaki değişimi fork ettiğiniz depoya iletin.

```sh

$ git push origin <yourbranchname-issueid>

```
* Son olarak değişiklikleri bildirmek için bize bir ***pull request*** gönderin.
