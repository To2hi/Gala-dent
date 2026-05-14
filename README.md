# Gala Den

Ta wersja zachowuje oryginalne widoki 1:1 z dostarczonych plików HTML i osadza je w aplikacji React/Vite bez GSAP.

## Start

```bash
npm install
npm run dev
```

## Build produkcyjny

```bash
npm install
npm run build
```

Gotowe pliki do wrzucenia na serwer znajdują się w katalogu `dist/`.
Po tej poprawce build działa także po wdrożeniu do podfolderu, a nie tylko z root domeny.

## Routing

Wewnętrzne linki działają przez `HashRouter`, więc przejścia między podstronami oraz wejście na stronę główną z nowej karty nie wymagają konfiguracji rewrite po stronie serwera.
