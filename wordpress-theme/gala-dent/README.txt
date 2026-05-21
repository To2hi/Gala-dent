GALA DENT - motyw WordPress

Ten katalog można skopiować bezpośrednio do:
wp-content/themes/gala-dent

Szybka konfiguracja:
1. Aktywuj motyw w WordPressie.
2. Utwórz strony o slugach:
   - o-klinice
   - oferta
   - sprzet
   - kontakt
3. Ustaw stronę główną według potrzeb.
   Motyw ma własny `front-page.php`, więc strona startowa zachowa obecny layout GALA DENT.

Jak to działa:
- motyw renderuje istniejące statyczne pliki HTML z katalogu `static/pages`
- linki do podstron są zamieniane na adresy WordPressa
- obrazy i logotypy są ładowane z katalogu `assets`
- WordPress nadal może dołączać własne style, skrypty i admin bar przez `wp_head()` / `wp_footer()`

Jeśli chcesz rozbudować motyw:
- dodaj nowe pliki HTML do `static/pages`
- utwórz odpowiadające im strony w WordPressie
- motyw automatycznie obsłuży plik, jeśli slug strony będzie zgodny z nazwą pliku
