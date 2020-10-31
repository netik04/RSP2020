Toto je projekt RSP 2020 týmu Študáci

Uživatel(WIP):
https://alpha.kts.vspj.cz/~studaci/product/development/v0_uzivatel/

Momentální struktura:  
Produkt/
  - admin/
  - autor/
  - clanky/ (adresář, kam se budou ukládat články)
  - redaktor/
    - index.php (přehled článků)
    - clanek.php (detail jednoho článku)
  - sefredaktor/
  - Scripty/ (složka pro samostatné PHP skripty)
  - img/ (složka pro případné obrázky používané na stránce (pokud bude potřeba?))
    /profile_pics (pro profilovky jednotlivých uživatelů)
  - index.php (zobrazí stránku pro čtenáře, po loginu přesměruje do složky pro danou roli.?)
  - style.css
  - head.php (společně s foot.php se snaží odlehčit množství opakujícího se kódu v jednotlivých php souborech, rád bych to prokonzultoval)
  - foot.php
  - další scripty...
