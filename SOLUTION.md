# SOLUTION — Sunumcu İçin (KATILIMCILARA VERİLMEZ)

Kurumsal kılık: **Feysbook** (sahte, Facebook benzeri sosyal ağ — "feysbook (not fake)"). Hedef `http://localhost:8080`.

## CTF paneli — flag'ler nereye girilir

Katılımcı flag'leri **`http://localhost:1337`** adresindeki HTB tarzı panele girer.
Panel sıralı çalışır (flag N doğrulanmadan N+1 açılmaz), ilerlemeyi tarayıcı oturumunda tutar
ve 5/5'te *PWNED* ekranı gösterir. Flag'ler panelde **SHA-256 hash** olarak durur, düz metin değil.

Çalıştırma (iki port):

```bash
docker run -p 8080:80 -p 1337:1337 recon-lab:latest
# veya
docker compose up --build
```


Flag zinciri sunumun bölümlerini takip eder:

| Flag | Bölüm | Teknik |
| --- | --- | --- |
| FLAG 1 | **Pasif** | HTTP başlık analizi |
| FLAG 2 | **Pasif** | robots.txt → dizin listeleme |
| FLAG 3 | **Aktif** | Linklenmemiş sayfa (HTML yorumundan ipucu) |
| FLAG 4 | **Aktif** | Dizin brute-force (gobuster) |
| FLAG 5 | **Final** | Yedek dosya sızıntısı + "???" bilgi sorusu (ARPANET) |

---

## Flag değerleri (özet)

1. `FLAG{h3ad3rs_l34k_s3cr3ts}`
2. `FLAG{r0b0ts_txt_r3v34ls_p4ths}`
3. `FLAG{unl1nk3d_1s_n0t_h1dd3n}`
4. `FLAG{d1r_brut3f0rc3_w1ns}`
5. **1969** (ARPANET'in kuruluş yılı — panel "???" adımı, flag değil bilgi sorusu)

---

## FLAG 1 — Pasif: HTTP başlık analizi

**Sunumcu:** "Hedefe tek bir sayfa bile istemeden, sadece başlıklara bakalım."

```bash
curl -I http://localhost:8080/
```

Yanıtta geliştirme ortamından unutulmuş bir başlık var:

```
X-Debug-Token: FLAG{h3ad3rs_l34k_s3cr3ts}
```

**Ders:** Sunucular kim olduklarını ve arkada ne çalıştığını başlıklarda sızdırır
(`Server`, `X-Powered-By`, debug başlıkları). Tek paket bile göndermeden çok şey öğrenilir.

---

## FLAG 2 — Pasif: robots.txt

**Sunumcu:** "Şimdi sitenin bize göstermek istemediği yerleri soralım."

```bash
curl http://localhost:8080/robots.txt
```

`Disallow: /internal-backup/` satırı gizli bir dizini ele veriyor. O dizinde
**dizin listeleme (autoindex) açık** bırakılmış:

```bash
curl http://localhost:8080/internal-backup/
curl http://localhost:8080/internal-backup/deployment-notes.txt
```

Dosyanın içinde:

```
FLAG{r0b0ts_txt_r3v34ls_p4ths}
```

**Ders:** robots.txt bir güvenlik önlemi değildir — tam tersine gizli yolların
haritasıdır. Buradaki HTML yorumu da FLAG 3'ün ipucunu verir (aşağı bak).

---

## FLAG 3 — Aktif: linklenmemiş sayfa

FLAG 2 dosyasındaki yorum satırı:

```
<!-- NOTE from ops: the old admin dashboard was moved to /admin-panel/ ... -->
```

**Sunumcu:** "Menüde hiçbir yerde linki yok ama ipucu adresi verdi. Deneyelim."

```bash
curl http://localhost:8080/admin-panel/
```

Sayfada:

```
Build token: FLAG{unl1nk3d_1s_n0t_h1dd3n}
```

Ayrıca sayfanın HTML kaynağındaki yorum, FLAG 4'e köprü kurar: yeni servis uçları
menüye eklenmemiş, **standart dizin adlarıyla** yayınlanmış → dizin taraması gerek.

```bash
curl http://localhost:8080/admin-panel/ | grep -A3 '<!--'
```

**Ders:** Bir sayfaya link olmaması onu gizli yapmaz. "Security through obscurity" işe yaramaz.

---

## FLAG 4 — Aktif: dizin brute-force (gobuster)

**Sunumcu:** "Hiçbir ipucu vermeyen uçları kaba kuvvetle tarayalım."

```bash
gobuster dir -u http://localhost:8080 \
  -w /usr/share/wordlists/dirb/common.txt
```

`api` kelimesi dirb `common.txt` içinde vardır; tarama `/api` ucunu **301** olarak bulur:

```
/api    (Status: 301)
```

```bash
curl http://localhost:8080/api/
```

JSON yanıtında:

```json
"flag": "FLAG{d1r_brut3f0rc3_w1ns}",
"message": "Config could not be loaded from config.php. Reminder: remove leftover *.bak backup files before go-live."
```

> gobuster yoksa alternatif: `ffuf -u http://localhost:8080/FUZZ -w common.txt`
> veya `dirb http://localhost:8080/`.

**Ders:** İçerik hiçbir yerde linklenmese de tahmin edilebilir isimler taranarak bulunur.

---

## FLAG 5 — Final: yedek/config dosyası

FLAG 4'ün mesajı `*.bak` yedek dosyalarına işaret ediyordu. API config'inin yedeği tahmin edilir:

```bash
curl http://localhost:8080/api/config.php.bak
```

`.bak` uzantısı PHP tarafından **çalıştırılmaz**, düz metin olarak döner — kaynak kodu ve
sahte veritabanı bilgileri açığa çıkar:

```php
define('DB_PASS', 'S0c14l_N3tw0rk_2024!');
define('JWT_SECRET', 'a7f3c9e1b2d84f60a1c5e7d9b3f2a6c8');
// Son adim (???) bir tarih soruyor: ARPANET'in kuruldugu yil.
```

**Son adımın cevabı bir flag değil, bir bilgi sorusudur.** Panelin 5. kutusu "???" olarak
görünür; açılınca **"ARPANET hangi yılda kuruldu?"** diye sorar. Doğru cevap: **1969**.
(config.php.bak dosyasındaki yorum bu soruya yönlendirir.)

**Ders:** `config.php` çalıştırılıp gizli kalır; ama `config.php.bak`, `.old`, `.swp`, `~`
gibi yedekler sunucu tarafından kaynak koduyla birlikte servis edilir. Gerçek dünyada bu,
veritabanı parolaları ve API anahtarları sızıntısıdır.

---

## Sunum akışı (özet komut sırası)

```bash
# --- PASİF ---
curl -I http://localhost:8080/                                   # FLAG 1
curl http://localhost:8080/robots.txt                            # -> /internal-backup/
curl http://localhost:8080/internal-backup/                      # dizin listesi
curl http://localhost:8080/internal-backup/deployment-notes.txt  # FLAG 2 (+ /admin-panel ipucu)

# --- AKTİF ---
curl http://localhost:8080/admin-panel/                          # FLAG 3 (+ brute-force ipucu)
gobuster dir -u http://localhost:8080 -w common.txt              # -> /api bulunur
curl http://localhost:8080/api/                                  # FLAG 4 (+ .bak ipucu)

# --- FINAL ---
curl http://localhost:8080/api/config.php.bak   # .bak sizar; final panel sorusu: ARPANET yili = 1969
```

## Sıfırlama

```bash
docker rm -f recon-lab      # veya: docker compose down
docker run -p 8080:80 -p 1337:1337 recon-lab:latest
```
