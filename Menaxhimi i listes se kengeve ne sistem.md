# Udhëzues për Përdoruesit: Menaxhimi i Listës së Këngëve në Sistem

Ky udhëzues është krijuar për të ndihmuar përdoruesit të menaxhojnë listën e këngëve në sistem. Ndiqni hapat e mëposhtëm për të përdorur funksionalitetet e reja si importimi i këngëve, shfaqja e këngëve të fshira, eksportimi i tabelave dhe fshirja e regjistrimeve.

## 1. Hyrja në Sistem dhe Navigimi

### Hapi 1: Hyni në Sistem
Para se të filloni, sigurohuni që jeni të identifikuar në sistem. Nëse nuk keni aksesin e nevojshëm, kontaktoni administratorin e sistemit.

### Hapi 2: Navigoni në Seksionin "Videot & Ngarkimi"
Pas hyrjes, klikoni në seksionin "Videot & Ngarkimi" në panelin kryesor të navigimit.

### Hapi 3: Zgjidhni "Lista e këngëve"
Në menunë e "Videot & Ngarkimi", zgjidhni opsionin "Lista e këngëve" për të hapur faqen e menaxhimit të këngëve.

## 2. Importimi i Këngëve

### Hapi 1: Importoni Këngët
Për të importuar këngë të reja në sistem, përdorni parametrin `import` në URL. Për shembull:

```
http://panel.bareshaoffice.com/lista_e_kengeve.php?import=emri_i_kenges
```

**Shënim:** Sigurohuni që të keni URL-në e duhur dhe token-in e vlefshëm për importimin.

### Hapi 2: Kontrolloni Mesazhin e Përpunimit
Pas importimit, një mesazh do të shfaqet përmes një dritareje alert që tregon statusin e importimit (sukses ose gabim).

## 3. Shfaqja e Listës së Këngëve

### Hapi 1: Shikoni Tabelën e Këngëve
Në faqen "Lista e këngëve", do të shihni një tabelë që liston të gjitha këngët e regjistruara. Tabela përfshin kolonat:

- **Id:** Identifikuesi unik i këngës.
- **Këngëtari:** Emri i këngëtarit që interpreton këngën.
- **Informacioni:** Detaje të ndryshme rreth këngës si emri, teksti, muzika, orkestrë, etj.
- **Rrjete sociale:** Informacione për platformat sociale të lidhura me këngën.
- **Klienti:** Klienti i lidhur me këngën.
- **Info Shtes:** Informacion shtesë rreth këngës.

### Hapi 2: Përdorni Funksionet e Tabelës

#### a. Kërkimi dhe Filtrimi
Përdorni fushën e kërkimit në krye të tabelës për të gjetur këngë specifike bazuar në emër, këngëtar, klient, etj.

#### b. Rregullimi i Rreshtave
Klikoni në titullin e çdo kolone për të rregulluar tabelën në renditje rritëse ose zbritëse.

#### c. Paginimi
Navigoni mes faqeve të tabelës përmes butonave të paginimit në fund të tabelës për të parë më shumë regjistrime.

## 4. Eksportimi i Tabelave

### Hapi 1: Përdorni Butonat e Eksportimit
Në krye të tabelës, do të gjeni butona për eksportimin e të dhënave në format të ndryshme:

- **PDF:** Eksporto tabelën në formatin PDF.
- **Kopjo:** Kopjo tabelën në clipboard.
- **Excel:** Eksporto tabelën në formatin Excel.
- **Printo:** Printo tabelën.

Klikoni në butonin e dëshiruar për të kryer veprimin e eksportimit.

## 5. Fshirja e Këngëve

### Hapi 1: Klikoni Butonin "Fshije"
Në kolonën "Këngëtari", klikoni butonin me ikonën e trashës (`<i class="fi fi-rr-trash"></i>`) për të fshirë një këngë të caktuar.

### Hapi 2: Konfirmoni Fshirjen
Një dritare konfirmimi me SweetAlert do të shfaqet, duke kërkuar që të konfirmoni fshirjen:

- **Po, fshije!**: Konfirmoni fshirjen.
- **Anulo**: Anulo veprimin.

### Hapi 3: Kontrolloni Mesazhin e Suksesit
Pas konfirmimit të fshirjes, një mesazh do të shfaqet duke konfirmuar që fshirja është kryer me sukses dhe tabela do të rifreskohet për të reflektuar ndryshimet.

### Hapi 4: Menaxhimi i Gabimeve
Në rast se ka ndodhur një gabim gjatë fshirjes, një mesazh gabimi do të shfaqet me SweetAlert. Në këtë rast, kontrolloni lidhjen me internetin ose kontaktoni administratorin e sistemit.

## 6. Shfaqja e Këngëve të Fshira

### Hapi 1: Hapni Modal-in për Këngët e Fshira
Klikoni butonin "Lista e këngëve të fshira" për të hapur modal-in që shfaq listën e këngëve të fshira.

### Hapi 2: Shikoni Tabelën e Këngëve të Fshira
Në modal-in që hapet, do të shihni një tabelë që liston këngët e fshira me kolonat:

- **ID:** Identifikuesi unik i regjistrimit të fshirjes.
- **Rekordi i fshirë:** Informacioni rreth këngës së fshirë.
- **Koha e fshirjes:** Data dhe ora e fshirjes së regjistrimit.

### Hapi 3: Përdorni Funksionet e Tabelës së Fshirë
Tabelat e fshirë kanë të njëjtat funksionalitete si tabela kryesore (kërkimi, filtrimi, eksportimi).

## 7. Menaxhimi i Informacionit të Këngëve

### Hapi 1: Shfaqja e Informacionit të Detajuar
Në kolonën "Informacioni", klikoni butonin "Shfaq më shumë" për të hapur më shumë detaje rreth këngës. Kjo do të shfaqë informacion të detajuar si emri, teksti, muzika, orkestrë, etj.

### Hapi 2: Mbyllja e Informacionit të Detajuar
Pas shqyrtimit të informacionit të detajuar, klikoni butonin "Mbyll" për të mbyllur sekcionin e informacionit.

## 8. Siguria dhe Privatësia

### Mbrojtja e të Dhënave
Sigurohuni që të dhënat e regjistruara janë të mbrojtura dhe nuk janë të disponueshme për palë të paautorizuara. Mos ndani informacionet konfidenciale me persona të paligjshëm.

### Përdorimi i HTTPS
Sigurohuni që lidhja me faqen e menaxhimit të këngëve përdor HTTPS për të mbrojtur të dhënat gjatë transmetimit.

### Ruajtja e Informacionit Personal
Informacioni personal i përdoruesve dhe këngëtarëve duhet të mbahet i sigurt dhe të mos zbulohet pa lejen e tyre.

## 9. Çështje të Shpeshta dhe Zgjidhjet

### Problemet me Importimin e Këngëve
Nëse nuk mund të importoni këngë, kontrolloni:

- Sigurohuni që URL-ja e përdorur për import është e saktë.
- Kontrolloni nëse token-i për importimin është i vlefshëm dhe i përditësuar.
- Kontrolloni lidhjen me internetin.

Nëse problemi vazhdon, kontaktoni administratorin e sistemit.

### Probleme me Fshirjen e Këngëve
Nëse fshirja e këngëve nuk funksionon:

- Kontrolloni lidhjen me internetin.
- Sigurohuni që keni privilegje të mjaftueshme për të fshirë regjistrimet.
- Kontrolloni nëse serveri është në punë pa probleme teknike.

Në rast të problemeve të vazhdueshme, kontaktoni administratorin.
