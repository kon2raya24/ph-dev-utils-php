# phdevutils/core

[![Packagist version](https://img.shields.io/packagist/v/phdevutils/core?label=Packagist&color=f28d1a&logo=packagist&logoColor=white)](https://packagist.org/packages/phdevutils/core)
[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](https://github.com/kon2raya24/ph-dev-utils/blob/main/LICENSE)
[![Made in PH](https://img.shields.io/badge/made%20in-🇵🇭%20Philippines-0038A8)](https://github.com/kon2raya24)

Filipino developer utilities for PHP — peso formatting, government ID validators (TIN / SSS / PhilHealth / Pag-IBIG), PH phone parsing with network detection, and PSGC region / province lookup.

Need fake test data on top of this? See the sibling [`phdevutils/faker`](https://packagist.org/packages/phdevutils/faker).

## Install

```bash
composer require phdevutils/core
```

Requires PHP 8.1+.

## Quick start

```php
use PhDevUtils\Peso;
use PhDevUtils\Validators\Tin;
use PhDevUtils\Phone;
use PhDevUtils\Address;

Peso::format(1234.5);                       // '₱1,234.50'
Peso::parse('₱1,234.50');                   // 1234.5
Tin::validate('123-456-789-000');           // true
Phone::parseMobile('09171234567');          // ['network' => 'Globe', 'e164' => '+639171234567', ...]
Address::findProvince('Cebu');              // ['code' => '0722', 'name' => 'Cebu', 'region' => '07']
```

## API Reference

### `PhDevUtils\Peso`

#### `Peso::format(float $value, array $opts = []): string`

Format a number as a peso amount with thousands separators.

Options array shape: `['decimals' => int, 'symbol' => 'peso'|'php'|'none']`. Defaults: `decimals: 2`, `symbol: 'peso'`.

```php
Peso::format(1234.5);                            // '₱1,234.50'
Peso::format(1234.5, ['decimals' => 0]);         // '₱1,235'
Peso::format(1234.5, ['symbol' => 'php']);       // 'PHP 1,234.50'
Peso::format(1234.5, ['symbol' => 'none']);      // '1,234.50'
Peso::format(-50.0);                             // '-₱50.00'
```

#### `Peso::parse(string $input): ?float`

Parse a peso-formatted string back to a float. Strips `₱`, `PHP`, whitespace, and commas. Returns `null` for unparseable input.

```php
Peso::parse('₱1,234.50');     // 1234.5
Peso::parse('PHP 50');        // 50.0
Peso::parse('-1,000');        // -1000.0
Peso::parse('not a number');  // null
```

#### `Peso::toWords(float $value): string`

Convert to English peso-and-centavos word form. Singular/plural handled.

```php
Peso::toWords(1);             // 'one peso'
Peso::toWords(1234);          // 'one thousand two hundred thirty-four pesos'
Peso::toWords(1234.56);       // 'one thousand two hundred thirty-four pesos and fifty-six centavos'
Peso::toWords(-50);           // 'negative fifty pesos'
```

#### `Peso::toWordsFilipino(float $value): string` (v0.3)

Convert to **Filipino (Tagalog)** peso-and-centavos word form, using the check/receipt convention `[whole-words] at [XX]/100 piso`. Handles ligature rules and the `daan`/`raan` initial-consonant alternation.

```php
Peso::toWordsFilipino(1);            // 'Isang piso'
Peso::toWordsFilipino(100);          // 'Isang daang piso'
Peso::toWordsFilipino(400);          // 'Apat na raang piso'
Peso::toWordsFilipino(1000);         // 'Isang libong piso'
Peso::toWordsFilipino(1_000_000);    // 'Isang milyong piso'
Peso::toWordsFilipino(12345.67);     // "Labindalawang libo tatlong daan apatnapu't lima at 67/100 piso"
Peso::toWordsFilipino(0);            // 'Sero piso'
Peso::toWordsFilipino(0.5);          // 'Sero at 50/100 piso'
Peso::toWordsFilipino(-1);           // throws OutOfRangeException
```

Range: `sero` (0) through `trilyon` (10^12).

---

### Government ID validators (`PhDevUtils\Validators\*`)

> ⚠️ All validators are **format-level only**. SSS / PhilHealth / Pag-IBIG do not publish official checksum algorithms; unofficial implementations produce confident-but-wrong results in production. This package returns `true` for any input with the correct digit count.

#### `Validators\Tin::validate(string $input): bool` / `Validators\Tin::format(string $input): ?string`

BIR TIN: 9 digits (individual) or 12 digits (with branch).

```php
use PhDevUtils\Validators\Tin;

Tin::validate('123-456-789');         // true
Tin::validate('123-456-789-000');     // true
Tin::validate('123');                 // false

Tin::format('123456789');             // '123-456-789'
Tin::format('123456789000');          // '123-456-789-000'
Tin::format('123');                   // null
```

#### `Validators\Sss::validate(string $input): bool` / `Validators\Sss::format(string $input): ?string`

SSS: exactly 10 digits, formatted `XX-XXXXXXX-X`.

```php
use PhDevUtils\Validators\Sss;

Sss::validate('12-3456789-0');   // true
Sss::format('1234567890');       // '12-3456789-0'
```

#### `Validators\PhilHealth::validate(string $input): bool` / `Validators\PhilHealth::format(string $input): ?string`

PhilHealth PIN: 12 digits, formatted `XX-XXXXXXXXX-X`.

```php
use PhDevUtils\Validators\PhilHealth;

PhilHealth::validate('123456789012');   // true
PhilHealth::format('123456789012');     // '12-345678901-2'
```

#### `Validators\PagIbig::validate(string $input): bool` / `Validators\PagIbig::format(string $input): ?string`

Pag-IBIG MID: 12 digits, formatted `XXXX-XXXX-XXXX`.

```php
use PhDevUtils\Validators\PagIbig;

PagIbig::validate('123456789012');   // true
PagIbig::format('123456789012');     // '1234-5678-9012'
```

---

### `PhDevUtils\Phone`

#### `Phone::parseMobile(string $input): ?array`

Parse a PH mobile number with network detection. Accepts `+63...`, `63...`, `09...`, and `9...` forms. Returns `null` if it doesn't normalize to an 11-digit PH mobile.

Return shape:
```php
[
  'e164'     => '+63XXXXXXXXXX',
  'national' => '0XXXXXXXXXX',
  'network'  => 'Globe' | 'Smart' | 'Sun' | 'DITO' | null,
]
```

```php
Phone::parseMobile('09171234567');
// ['e164' => '+639171234567', 'national' => '09171234567', 'network' => 'Globe']

Phone::parseMobile('+639951234567');
// ['e164' => '+639951234567', 'national' => '09951234567', 'network' => 'DITO']

Phone::parseMobile('not a phone');   // null
```

#### `Phone::parseLandline(string $input): ?array`

Parse a PH landline with area code lookup.

```php
[
  'e164'     => '+63XXXXXXXXXX',
  'national' => '(0X) XXX-XXXX' | '(0XX) XXX-XXXX',
  'areaCode' => '2' | '32' | '74' | ...,
  'area'     => 'Metro Manila' | 'Cebu' | ... | null,
]
```

```php
Phone::parseLandline('(02) 8123-4567');
// ['areaCode' => '2', 'area' => 'Metro Manila', ...]

Phone::parseLandline('322345678');
// ['areaCode' => '32', 'area' => 'Cebu', 'national' => '(032) 234-5678', ...]
```

---

### `PhDevUtils\Address`

Region and province data follows the [Philippine Standard Geographic Code](https://psa.gov.ph/classification/psgc) at v0.1 granularity (regions + provinces). Cities, municipalities, and barangays are on the v0.2 roadmap.

#### `Address::listRegions(): array`

Returns all 17 PH regions. Each element shape: `['code' => string, 'name' => string, 'designation' => string]`.

```php
count(Address::listRegions());   // 17
Address::listRegions()[0];
// ['code' => '01', 'name' => 'Ilocos Region', 'designation' => 'Region I']
```

#### `Address::findRegion(string $query): ?array`

Look up by code, name, or designation (case-insensitive). Returns `null` if not found.

```php
Address::findRegion('NCR');                       // ['code' => '13', 'name' => 'National Capital Region', ...]
Address::findRegion('04');                        // ['code' => '04', 'name' => 'CALABARZON', ...]
Address::findRegion('calabarzon');                // same (case-insensitive)
Address::findRegion('Atlantis');                  // null
```

#### `Address::listProvinces(?string $regionCode = null): array`

Returns all provinces, optionally filtered by region code. Each element shape: `['code' => string, 'name' => string, 'region' => string]`.

```php
count(Address::listProvinces());           // ~80
count(Address::listProvinces('04'));       // CALABARZON only
Address::listProvinces('04')[0];           // ['code' => '0420', 'name' => 'Batangas', 'region' => '04']
```

#### `Address::findProvince(string $query): ?array`

Look up by code or name (case-insensitive).

```php
Address::findProvince('Cebu');     // ['code' => '0722', 'name' => 'Cebu', 'region' => '07']
Address::findProvince('0722');     // same
Address::findProvince('Atlantis'); // null
```

---

## Modules table

Direct mapping to the JavaScript sibling package:

| Capability | PHP | JS |
| --- | --- | --- |
| Format peso | `Peso::format($n)` | `formatPHP(n)` |
| Parse peso | `Peso::parse($s)` | `parsePHP(s)` |
| Peso to words (English) | `Peso::toWords($n)` | `pesoToWords(n)` |
| Peso to words (Filipino) | `Peso::toWordsFilipino($n)` | `pesoToWordsFilipino(n)` |
| Validate TIN | `Validators\Tin::validate($s)` | `validateTIN(s)` |
| Validate SSS | `Validators\Sss::validate($s)` | `validateSSS(s)` |
| Validate PhilHealth | `Validators\PhilHealth::validate($s)` | `validatePhilHealth(s)` |
| Validate Pag-IBIG | `Validators\PagIbig::validate($s)` | `validatePagIBIG(s)` |
| Parse mobile | `Phone::parseMobile($s)` | `parseMobile(s)` |
| Parse landline | `Phone::parseLandline($s)` | `parseLandline(s)` |
| List regions | `Address::listRegions()` | `listRegions()` |
| Find region | `Address::findRegion($q)` | `findRegion(q)` |
| List provinces | `Address::listProvinces($code = null)` | `listProvinces(code?)` |
| Find province | `Address::findProvince($q)` | `findProvince(q)` |

## License

MIT
