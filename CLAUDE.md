# CLAUDE.md

## Projekt

`brainbits/phpcs-standard` ist ein PHP_CodeSniffer-Standard (Composer-Typ
`phpcodesniffer-standard`). Er basiert auf dem Doctrine Coding Standard (der wiederum
Slevomat einbindet), schaltet einige Doctrine-Regeln ab und ergänzt einen eigenen Sniff.
Installiert wird er über `dealerdirect/phpcodesniffer-composer-installer`, der das
Verzeichnis `Brainbits/` als Standard registriert.

- PHP ^8.4. Abhängigkeiten: `squizlabs/php_codesniffer` ^4, `doctrine/coding-standard` ^14,
  `slevomat/coding-standard` ^8.23 (Minimum von Doctrine 14), Dev: PHPUnit ^13.4.
- CI (`.github/workflows/build.yml`): PHP 8.4 und 8.5, jeweils mit `lowest`- und
  `highest`-Abhängigkeiten.

## Aufbau

- `Brainbits/ruleset.xml` — der Standard. Name des Standards ist **`Brainbits`**.
  Bindet `Doctrine` ein und schließt einzelne Sniffs aus. Konfigurationen, die Doctrine
  bereits mitbringt (z. B. `DNFTypeHintFormat`), nicht erneut referenzieren.
- `Brainbits/Sniffs/` — eigene Sniffs, Namespace `Brainbits\Sniffs\` (PSR-4). Sniffs in
  diesem Verzeichnis werden von PHPCS automatisch Teil des Standards, sie müssen nicht im
  Ruleset referenziert werden.
  - `Exception/GlobalExceptionSniff.php` — meldet `throw new` mit globalen SPL-Exceptions
    (`\RuntimeException`, `\InvalidArgumentException`, …). Der Klassenname wird über
    Namespace und `use`-Statements aufgelöst (voll qualifiziert, importiert, Alias,
    unqualifiziert im globalen Namespace, `namespace\…`), Vergleich case-insensitive.
    `throw $e` u. ä. wird ignoriert. Vollständiger Fehlercode:
    `Brainbits.Exception.GlobalException.GlobalException`. Nutzt Slevomat-Helper
    (`TokenHelper`, `NamespaceHelper`).
- `tests/Brainbits/Sniffs/` — PHPUnit-Tests der Sniffs. Sie erben von
  `SlevomatCodingStandard\Sniffs\TestCase`; die Sniff-Klasse wird aus dem Testklassennamen
  abgeleitet (`FooSniffTest` → `FooSniff`), Fixtures liegen in `fixture/`.
- `tests/Brainbits/input/` + `tests/Brainbits/expected_report.txt` — Integrationstest des
  gesamten Rulesets: PHPCS läuft über alle Input-Dateien, die Summary muss exakt dem
  erwarteten Report entsprechen. Dateien ohne Verstöße tauchen in der Summary nicht auf
  (z. B. `ChangedFromDoctrineCodingStandard.php`, das die ausgeschlossenen Regeln belegt).
- `phpcs.xml.dist` — prüft den Code dieses Repos (`Brainbits/`, `tests/` ohne `input/` und
  `fixture/`) mit dem eigenen Standard; läuft auch in CI.

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

## Arbeitsweise

- Regel ändern (ausschließen/hinzufügen): `Brainbits/ruleset.xml` anpassen, passende
  Input-Datei unter `tests/Brainbits/input/` ergänzen und `expected_report.txt`
  aktualisieren — den neuen Report erzeugen und den Diff bewusst prüfen, nicht blind
  übernehmen. Input-Dateien müssen gültiges PHP sein, sonst prüfen sie nichts. Für
  Ausschlüsse gegenprüfen, dass `--standard=Doctrine` den Verstoß in der Datei meldet.
- Neuer Sniff: Klasse unter `Brainbits/Sniffs/<Kategorie>/<Name>Sniff.php`, Test unter
  `tests/Brainbits/Sniffs/<Kategorie>/<Name>SniffTest.php` mit Fixtures in `fixture/`.
  Fehlercodes als öffentliche Konstante `CODE_*` definieren.
- Code muss den eigenen Standard erfüllen (`vendor/bin/phpcs`), inkl.
  `declare(strict_types=1);`, `self::assert…` in Tests und importierter Funktionen/Konstanten (`use function`,
  `use const`).
- Abhängigkeits-Upgrades (PHPCS, Doctrine, Slevomat) können den Integrationsreport ändern;
  nach einem Upgrade immer beide Testarten laufen lassen. CI testet auch `--prefer-lowest`,
  Versionsconstraints also nicht höher setzen als nötig, aber auch nicht so niedrig, dass
  die niedrigste Version die Tests bricht.
- Commits im Stil Conventional Commits (`fix:`, `fix(deps):`, `chore:`).
- Branch für Entwicklung ist `main` (CI läuft auf Push nach `main` und auf PRs). Ältere
  Versionszweige: `1.0`, `2.0`, `3.0`; `master` ist veraltet.
