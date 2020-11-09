Toto je projekt RSP 2020 týmu Študáci

REDAKTOR DONE v0
https://alpha.kts.vspj.cz/~studaci/product/development/v0_redaktor/redaktor/


Navrhovaná struktura:  
Produkt/
  - admin/
  - autor/
  - clanky/ (adresář, kam se budou ukládat články)
  - redaktor/
    - index.php (přehled článků)
    - clanek.php (detail jednoho článku)
  - sefredaktor/
  - index.php (zobrazí stránku pro čtenáře, po loginu přesměruje do složky pro danou roli.?)
  - style.css
  - head.php (společně s foot.php se snaží odlehčit množství opakujícího se kódu v jednotlivých php souborech, rád bych to prokonzultoval)
  - foot.php
  - další scripty...