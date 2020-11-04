Toto je projekt RSP 2020 týmu Študáci

https://alpha.kts.vspj.cz/~studaci

Momentální struktura:  
/Produkt
  - /admin
  - /autor
  - /clanky (adresář, kam se budou ukládat články)
  - /redaktor
    - index.php (přehled článků)
    - clanek.php (detail jednoho článku)
    - scripty/ (složka pro PHP a JS skripty pro danou roli, dohromady sou scripty nepřehledné)
    - style.css (styly pro jednu roli)
  - /sefredaktor
  - /scripty (složka pro samostatné PHP skripty)
  - /img (složka pro případné obrázky používané na stránce (pokud bude potřeba?))
    - /profile_pics (pro profilovky jednotlivých uživatelů)
  - index.php (zobrazí stránku pro čtenáře, po loginu přesměruje do složky pro danou roli.?)
  - style.css (globální styly)
  - head.php (společně s foot.php se snaží odlehčit množství opakujícího se kódu v jednotlivých php souborech, rád bych to prokonzultoval)
  - foot.php
  - další scripty...
