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

## Usage

```php
use PhDevUtils\Peso;
use PhDevUtils\Validators\Tin;
use PhDevUtils\Validators\Sss;
use PhDevUtils\Validators\PhilHealth;
use PhDevUtils\Validators\PagIbig;
use PhDevUtils\Phone;
use PhDevUtils\Address;

// Peso
Peso::format(1234.5);                       // '₱1,234.50'
Peso::parse('₱1,234.50');                   // 1234.5
Peso::toWords(1234);                        // 'one thousand two hundred thirty-four pesos'

// Government IDs (format-level only; no reverse-engineered checksums)
Tin::validate('123-456-789-000');           // true
Tin::format('123456789');                   // '123-456-789'

// Phone
Phone::parseMobile('09171234567');
// ['e164' => '+639171234567', 'national' => '09171234567', 'network' => 'Globe']

Phone::parseLandline('(02) 8123-4567');
// ['e164' => '+6328123-4567', 'areaCode' => '2', 'area' => 'Metro Manila']

// Address
Address::findProvince('Cebu');              // ['code' => '0722', 'name' => 'Cebu', 'region' => '07']
count(Address::listProvinces());            // ~80
```

See the [project README](https://github.com/kon2raya24/ph-dev-utils#readme) for the full module table, JavaScript sibling package, and roadmap.

## ⚠️ Important note

Government ID validators are **format-level only**. SSS / PhilHealth / Pag-IBIG do not publish official checksum algorithms; unofficial implementations produce confident-but-wrong results in production. This package will return `true` for any value with the correct digit count.

## License

MIT
