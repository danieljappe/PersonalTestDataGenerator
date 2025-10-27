This table describes the valid and invalid output partitions for the `FakeInfo` address generator.

| Field       | Valid Output | Invalid Output | Notes / Examples |
|------------|--------------|----------------|----------------|
| `street`    | Non-empty string, 40 characters, letters (a-z, A-Z) including Danish letters æ, ø, å, spaces allowed | Empty string, != 40 characters, includes numbers or symbols | `"Brogade"`, `"Ågade"` |
| `number`    | `"1"` to `"999"`, optionally followed by uppercase letter (`A-Z`) | `"0"`, `"1000"`, lowercase letters, symbols | `"43"`, `"12B"`, `"7K"` |
| `floor`     | `"st"` or integer 1–99 | `"0"`, `"100"`, negative numbers, strings not equal `"st"` | `"st"`, `5`, `32` |
| `door`      | `"th"`, `"tv"`, `"mf"`, integer 1–50, lowercase letter + 1–999, lowercase letter + '-' + 1–999 | uppercase letter only, numbers >50 (if numeric door), missing dash when required | `"th"`, `"tv"`, `"mf"`, `1`, `"a3"`, `"b-14"` |
| `postal_code` | 4-digit string | Not 4 digits, includes letters, empty | `"2200"`, `"4220"` |
| `town_name`   | Non-empty string, starts with uppercase letter, letters only (a-z, A-Z + æ, ø, å), spaces allowed | Empty string, starts with lowercase, contains digits or symbols | `"København N"`, `"Korsør"` |
| `phone`       | String starting with valid prefix (from PHONE_PREFIXES), total length 8 digits | Wrong prefix, total length not 8 digits, contains letters | `"66553501"`, `"30123456"` |

---


## 1. Street
- Always a string of length 40.
- **Test strategy:** Check that `street` is a string and `mb_strlen(street) <= 40`.

---

## 2. Number
- Numeric: `"1"` to `"999"` (≈80% probability)
- Numeric + uppercase letter: `"1A"` to `"999Z"` (≈20% probability)
- **Test strategy:** 
  - Assert `number` starts with digits.
  - Optionally ends with an uppercase letter.
  - Length ≤ 4 characters.

---

## 3. Floor
- `"st"` 30% or number `1–99` 70%
- **Test strategy:** 
  - Assert `floor` is either `"st"` or an integer 1–99.

---

## 4. Door
- `"th"` (35%), `"tv"` (35%), `"mf"` (10%), number 1–50 (10%), lowercase+number (5%), lowercase+`-`+number (5%)
- **Test strategy:** 
  - Use regex patterns for each case:
    - `/^(th|tv|mf)$/`
    - `/^\d{1,2}$/` for 1–50
    - `/^[a-zæøå]\d{1,3}$/`
    - `/^[a-zæøå]-\d{1,3}$/`
  - Run test multiple times to hit rare branches.

---

## 5. Postal Code and Town
- Postal code: 4-digit string.
- Town: string starting with uppercase letter.
- **Test strategy:** 
  - Assert postal code matches `/^\d{4}$/`.
  - Assert town name matches `/^[A-ZÆØÅ]/`.

---
