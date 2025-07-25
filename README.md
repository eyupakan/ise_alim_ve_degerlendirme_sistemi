# İş Başvuru ve Değerlendirme Sistemi

Kapsamlı bir iş başvuru ve değerlendirme platformu. Mevcut sistemlerin yapay zeka ile kelime eşleme sistemlerinin doğru adayı tespit edememe riskine karşılık olarak iş başvuru formunu bir standart haline getirerek adaylar için bir fırsat eşitliği yakalamasını hedefliyorum.
 
Bu sistem, adayların başvuru sırasında girdiği bilgilerin (örneğin sertifikalar,iş deneyimi(aylık bazda), test puanları) puanlanmasıyla bir toplam puan hesaplar. İk personeli işe alım yaparken bu puanı göz önünde bulundurarak işe alım yapar.

## 🌟 Özellikler

- 📋 4 Adımlı Başvuru Süreci
  - Kişisel Bilgiler
  - Eğitim Bilgileri
  - İş Deneyimi
  - Referanslar
- 📝 Online Test Sistemi
- 📤 Dosya Yükleme (Fotoğraf)
- 👥 Admin Paneli
- 📱 Responsive Tasarım


## 🛠️ Teknik Özellikler

- PHP 7.4+
- MySQL
- Bootstrap 5
- PDO Database Connection
- Composer Paket Yönetimi

## 📋 Gereksinimler

- PHP 7.4 veya üzeri
- MySQL 5.7 veya üzeri
- Composer

## 📦 Bağımlılıkların Kurulumu (vendor/ Klasörü)
Bu projede PHP bağımlılıkları Composer kullanılarak yönetilmektedir. vendor/ klasörü, proje bağımlılıklarını içerdiği için büyük boyutlu ve otomatik üretilebilir bir dizindir. Bu nedenle Git deposuna dahil edilmemiştir.

Projenin düzgün çalışabilmesi için aşağıdaki adımları takip ederek vendor/ klasörünü oluşturmalısınız:

🛠️ Gerekli Kurulum
Composer'ın yüklü olduğundan emin olun.
Henüz yüklü değilse: https://getcomposer.org/

Proje dizininde terminal açın ve şu komutu çalıştırın:

```bash
composer install
```


## 📁 Dizin Yapısı

```
├── admin/           # Yönetici paneli
├── api/            # API endpoint'leri
├── config/         # Yapılandırma dosyaları
├── database/       # Veritabanı şemaları
├── includes/       # Ortak PHP dosyaları
├── uploads/        # Kullanıcı yüklemeleri
├── vendor/         # Composer bağımlılıkları
```


## 📚 Kullanıcı ve Geliştirici Dokümantasyonu


#### Başvuru Süreci
1. **İş İlanlarını Görüntüleme**: Açık pozisyonları listeleyen sayfadan ilgilendiğiniz pozisyonu seçin.
2. **Başvuru Yapma**: Seçtiğiniz pozisyona başvuru yapmak için "Başvuru Yap" butonuna tıklayın.
3. **Başvuru Formunu Doldurma**: 4 adımlı başvuru sürecini takip edin:
   - **Kişisel Bilgiler**: Ad, soyad, iletişim bilgileri gibi temel bilgileri girin.
   - **Eğitim Bilgileri**: Mezun olduğunuz okullar, bölümler ve mezuniyet tarihlerini belirtin.
   - **İş Deneyimi**: Önceki iş deneyimlerinizi aylık bazda detaylı olarak girin.
   - **Referanslar**: İş veya eğitim hayatınızdan referans olabilecek kişilerin iletişim bilgilerini ekleyin.
4. **Testleri Tamamlama**: Başvurunuzu tamamlamak için gerekli testleri çözün.
5. **Başvuru Onayı**: Tüm adımları tamamladıktan sonra başvurunuz onaylanacak ve değerlendirme sürecine alınacaktır.
![image alt](https://github.com/eyupakan/ise_alim_ve_degerlendirme_sistemi/blob/892bbe85d90c68717ee860cf5c0d8a491f6af148/uploads/photos/ui_6.png)
![image alt](https://github.com/eyupakan/ise_alim_ve_degerlendirme_sistemi/blob/892bbe85d90c68717ee860cf5c0d8a491f6af148/uploads/photos/ui_7.png)

#### Admin Paneli
- **İş İlanı Yönetimi**: Yeni iş ilanları ekleyebilir, mevcut ilanları düzenleyebilir veya silebilirsiniz.
- **Başvuru Değerlendirme**: Gelen başvuruları görüntüleyebilir ve durumlarını güncelleyebilirsiniz.
- **Test Yönetimi**: Test sorularını ekleyebilir, düzenleyebilir veya silebilirsiniz.
![image alt](https://github.com/eyupakan/ise_alim_ve_degerlendirme_sistemi/blob/892bbe85d90c68717ee860cf5c0d8a491f6af148/uploads/photos/ui_5.jpg)
![image alt](https://github.com/eyupakan/ise_alim_ve_degerlendirme_sistemi/blob/892bbe85d90c68717ee860cf5c0d8a491f6af148/uploads/photos/ui_4.png)
![image alt](https://github.com/eyupakan/ise_alim_ve_degerlendirme_sistemi/blob/892bbe85d90c68717ee860cf5c0d8a491f6af148/uploads/photos/ui_1.png)
![image alt](https://github.com/eyupakan/ise_alim_ve_degerlendirme_sistemi/blob/892bbe85d90c68717ee860cf5c0d8a491f6af148/uploads/photos/ui_2.png)
![image alt](https://github.com/eyupakan/ise_alim_ve_degerlendirme_sistemi/blob/892bbe85d90c68717ee860cf5c0d8a491f6af148/uploads/photos/ui_3.png)


#### Proje Yapısı
- **admin/**: Yönetici paneli ile ilgili dosyalar.
- **api/**: API endpoint'leri ve ilgili işlemler. (Openrouter üzerinden Meta: Llama 4 Scout (free) api'ı kullanılmıştır.)
- **config/**: Veritabanı bağlantısı gibi yapılandırma dosyaları.
- **database/**: Veritabanı şemaları ve migration dosyaları.
- **includes/**: Ortak PHP fonksiyonları ve yardımcı dosyalar.
- **uploads/**: Kullanıcıların yüklediği dosyalar (fotoğraflar vb.).
- **vendor/**: Composer ile yönetilen bağımlılıklar.

#### Teknik Gereksinimler
- **PHP 7.4+**: Projenin çalışması için PHP 7.4 veya üzeri sürüm gereklidir.
- **MySQL 5.7+**: Veritabanı işlemleri için MySQL 5.7 veya üzeri sürüm gereklidir.
- **Composer**: Paket yönetimi için Composer kullanılmaktadır.

## 📞 İletişim

- Eyüp Akan (eyupakan@outlook.com)
- LinkedIn https://www.linkedin.com/in/eyupakan/

🔐 Lisans: Bu proje MIT lisansı ile lisanslanmıştır. Detaylar için LICENSE dosyasını inceleyin.
