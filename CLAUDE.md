# CLAUDE.md

## Projekt

`brainbits/phpcs-standard` ist ein PHP_CodeSniffer-Standard (Composer-Typ
`phpcodesniffer-standard`). Er basiert auf dem Doctrine Coding Standard (der wiederum
Slevomat einbindet), schaltet einige Doctrine-Regeln ab und ergänzt einen eigenen Sniff.
Installiert wird er über `dealerdirect/phpcodesniffer-composer-installer`, der das
Verzeichnis `Brainbits/` als Standard registriert.

- Abhängigkeiten: `squizlabs/php_codesniffer` ^4, `doctrine/coding-standard` ^14,
  `slevomat/coding-standard` ^8.14, Dev: PHPUnit.
- CI (`.github/workflows/build.yml`): PHP 8.3 und 8.4, jeweils mit `lowest`- und
  `highest`-Abhängigkeiten.

## Aufbau

- `Brainbits/ruleset.xml` — der Standard. Name des Standards ist **`Brainbits`**.
  Bindet `Doctrine` ein und schließt einzelne Sniffs aus.
- `Brainbits/Sniffs/` — eigene Sniffs, Namespace `Brainbits\Sniffs\` (PSR-4). Sniffs in
  diesem Verzeichnis werden von PHPCS automatisch Teil des Standards, sie müssen nicht im
  Ruleset referenziert werden.
  - `Exception/GlobalExceptionSniff.php` — meldet `throw` von globalen SPL-Exceptions
    (`\RuntimeException`, `\InvalidArgumentException`, …), egal ob voll qualifiziert oder
    per `use` (auch mit Alias) importiert. Code: `Brainbits.Exception.GlobalException`.
    Nutzt die Helper aus Slevomat (`TokenHelper`, `UseStatementHelper`).
- `tests/Brainbits/Sniffs/` — PHPUnit-Tests der Sniffs. Sie erben von
  `SlevomatCodingStandard\Sniffs\TestCase`; die Sniff-Klasse wird aus dem Testklassennamen
  abgeleitet (`FooSniffTest` → `FooSniff`), Fixtures liegen in `fixture/`.
- `tests/Brainbits/input/` + `tests/Brainbits/expected_report.txt` — Integrationstest des
  gesamten Rulesets: PHPCS läuft über alle Input-Dateien, die Summary muss exakt dem
  erwarteten Report entsprechen. Dateien ohne Verstöße tauchen in der Summary nicht auf
  (z. B. `ChangedFromDoctrineCodingStandard.php`, das die ausgeschlossenen Regeln belegt).
- `phpcs.xml.dist` — prüft den Code dieses Repos (`Brainbits/`) mit dem eigenen Standard.
- `example/`, `docs/` — lose Beispieldateien, nicht Teil von Tests oder CI.

## Befehle

```bash
composer install

# Unit-Tests der Sniffs
vendor/bin/phpunit

# Integrationstest des Rulesets (wie in CI)
vendor/bin/phpcs --basepath=. --report=summary --report-file=phpcs.log --standard=Brainbits `find tests/Brainbits/input/* | sort`; diff -u tests/Brainbits/expected_report.txt phpcs.log

# Code des Repos selbst prüfen
vendor/bin/phpcs
```

`composer tests` ist **nicht** verwendbar: das Script verweist zusätzlich auf
`tests/BrainbitsCodingStandard*`, die es nicht mehr gibt. Stattdessen die Befehle oben
einzeln ausführen.

## Arbeitsweise

- Regel ändern (ausschließen/hinzufügen): `Brainbits/ruleset.xml` anpassen, passende
  Input-Datei unter `tests/Brainbits/input/` ergänzen und `expected_report.txt`
  aktualisieren — den neuen Report erzeugen und den Diff bewusst prüfen, nicht blind
  übernehmen.
- Neuer Sniff: Klasse unter `Brainbits/Sniffs/<Kategorie>/<Name>Sniff.php`, Test unter
  `tests/Brainbits/Sniffs/<Kategorie>/<Name>SniffTest.php` mit Fixtures in `fixture/`.
  Fehlercodes als öffentliche Konstante `CODE_*` definieren.
- Code muss den eigenen Standard erfüllen (`vendor/bin/phpcs`), inkl.
  `declare(strict_types=1);` und importierter Funktionen/Konstanten (`use function`,
  `use const`).
- Abhängigkeits-Upgrades (PHPCS, Doctrine, Slevomat) können den Integrationsreport ändern;
  nach einem Upgrade immer beide Testarten laufen lassen. CI testet auch `--prefer-lowest`,
  Versionsconstraints also nicht höher setzen als nötig, aber auch nicht so niedrig, dass
  die niedrigste Version die Tests bricht.
- Commits im Stil Conventional Commits (`fix:`, `fix(deps):`, `chore:`).
- Branch für Entwicklung ist `main` (CI läuft auf Push nach `main` und auf PRs). Ältere
  Versionszweige: `1.0`, `2.0`, `3.0`; `master` ist veraltet.

## Bekannte Baustellen

- `Brainbits/ruleset.xml`: `<rul ref="SlevomatCodingStandard.TypeHints.DNFTypeHintFormat" />`
  ist ein Tippfehler (`rul` statt `rule`) — die Regel wird dadurch stillschweigend nicht
  angewendet.
- `README.md` nennt den Standard `BrainbitsCodingStandard`; korrekt ist `Brainbits`. Der dort
  beschriebene Docker-Weg hat im Repo kein Dockerfile.
- `phpunit.xml.dist` verweist noch auf das Schema von PHPUnit 10.1.
- `CHANGELOG.md` endet bei 1.0.3 und wird nicht mehr gepflegt.
