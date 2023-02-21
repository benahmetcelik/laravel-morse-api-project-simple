# Laravel Simple Morse Api Project

Bu proje, kullanıcıların, [http://ik.olleco.net/morse-api/](http://ik.olleco.net/morse-api/) API'ı ile sunucu hakkında bazı bilgileri mors alfabesi formatında edinmesini sağlamaktadır. 


##### Kullanıcıların API ile erişebileceği bilgiler şu şekildedir :
- CPU 
- ARCH 
- FREE MEMORY
- HOSTNAME 
- PLATFORM 
- TOTAL MEMORY 
- TYPE
- UP TIME








Bu API kullanılarak anlık sistem değerlerini görebileceğimiz bir sistem monitörünün
geliştirilmiştir.

# Proje Kurulum Ve İlk İstek: 
Sunucuya [https://github.com/benahmetcelik/laravel-morse-api-project-simple](https://github.com/benahmetcelik/laravel-morse-api-project-simple) linki üzerinden proje çekildikten sonra php artisan serve komutu ile çalışması sağlanır.

Daha sonraki atılacak isteklerde method POST olmak koşulu ile mors alfabesi formatında form-data içerisinde “command/checksum” olarak istenilen değer gönderilir.

Eğer belirtilen command bulunuyor ise geriye aşağıdaki görselde görüldüğü üzere json yanıtı döndürür.

Eğer command bulunamaz veya bir hata ile karşılaşılır ise json formatında error_msj değerine karşılık olarak mors alfabesi kullanılarak hata bastırılır.

# Checksum Kullanımı  : 

Checksum , bu projede dönecek değerlerin byte cinsinden integer toplamları olarak belirlenmiştir.

Form-data içerisinde checksum inputu değeri her istekten dönen checksum ile eşleyerek önbellekte bulunuyor ise kayıtlı datayı, bulunmuyor ise hata mesajını json olarak döndürecektir.

## Postman Request Docs : 
[https://www.postman.com/lively-crescent-264420/workspace/olle/request/21759520-1cc221d3-b855-4949-9428-0b75fec1cdd9](https://www.postman.com/lively-crescent-264420/workspace/olle/request/21759520-1cc221d3-b855-4949-9428-0b75fec1cdd9)

