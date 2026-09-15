# Web Recon Lab — Katılımcı Talimatı

Bu lab, gerçekçi ama **kasıtlı olarak zafiyetli** bir sosyal ağ sitesidir: **feysbook (not fake)**.
Amacın, web keşif (reconnaissance) tekniklerini kullanarak siteye gizlenmiş **5 flag'i** sırayla
bulmak ve **CTF paneline** girmek. Her flag bir sonraki adımı açar.

## 1. Lab'i ayağa kaldır

Docker kurulu olmalı. Tek komut:

```bash
docker run -p 8080:80 -p 1337:1337 restilov/recon-lab
```

> İmaj Docker Hub'da yayında; komut Windows / Linux / Kali / Mac (amd64 + arm64) hepsinde çalışır.
> Proje klasöründeysen alternatif: `docker compose up`

İki adres açılır:

| Adres | Ne |
| --- | --- |
| <http://localhost:8080> | **Hedef site** — flag'leri burada ararsın |
| <http://localhost:1337> | **CTF paneli** — bulduğun flag'i buraya girersin |

## 2. Nasıl oynanır

1. **Panel'i aç:** <http://localhost:1337> — 5 flag kutusu göreceksin, ilk kutu açık, diğerleri kilitli.
2. **Hedefi keşfet:** <http://localhost:8080> adresine `curl`, `gobuster` (veya `ffuf`/`dirb`) ve
   tarayıcı ile bağlan.
3. **Flag'i gir:** Bulduğun `FLAG{...}` değerini paneldeki aktif kutuya yapıştır → **Gönder**.
4. Doğruysa bir sonraki kutu açılır. 5/5 olunca **PWNED** ekranını görürsün.

Kullanacağın araçlar:

- `curl` — istek göndermek, yanıtları ve **HTTP başlıklarını** okumak (`curl -I ...`)
- `gobuster` / `ffuf` / `dirb` — gizli dizin/dosya taraması

> **Wordlist hazır:** Dizin taraması için gereken kelime listesi lab ile birlikte gelir.
> İndir: `curl -o common.txt http://localhost:1337/common.txt` — sonra `gobuster dir -u http://localhost:8080 -w common.txt`
- tarayıcının **kaynağı görüntüle** / geliştirici araçları (HTML yorumları!)

## 3. Kurallar

- Flag formatı: `FLAG{...}`
- 5 flag var, **sıralı** ilerlersin (bir flag açılmadan sonraki kutu açılmaz).
- Takıldığında sunumu takip et — eğitmen "şimdi şunu yapın" dedikçe ilerle.

## Etik uyarı

Bu sistem eğitim amaçlı kasıtlı olarak zafiyetli hazırlanmıştır. Burada öğrendiğin teknikleri
**yalnızca izniniz olan sistemlerde** uygula. İzinsiz sistemlere uygulamak yasa dışıdır.
