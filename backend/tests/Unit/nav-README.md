# Unit testing guidance — FakeInfo

This document explains how to apply black-box and white-box testing to the two unit tests in `tests/Unit/FakeInfoTest.php`:

- Return a fake CPR
- Return a fake first name, last name and gender

It includes test contracts, edge-cases, PHPUnit examples, and quick commands so you can reproduce and extend the tests in the future.

---

## What are black-box and white-box tests and why we use them

- Black-box testing (behavioral testing): we test the public behavior of a unit without looking at its internal implementation. We provide inputs and assert outputs and side effects. It's useful because it ensures the module meets its external contract and helps prevent regressions when refactoring internals.

- White-box testing (structural testing): we examine the internals of the implementation and design tests that cover specific branches, loops, boundary conditions, and internal helper functions. White-box tests are useful for ensuring internal logic (edge cases, error handling) is exercised.

We use both styles because they complement each other:
- Black-box tests verify the contract from the caller's perspective.
- White-box tests verify internal correctness, branch coverage, and help pinpoint bugs in complex logic.

---

## Test scope and contract

### 1) Return a fake CPR
Contract (black-box):
- Input: no input (method generates a single CPR)
- Output: a string containing exactly 10 digits (format DDMMYYXXXX)
- Constraints: Day must be 01-31, month 01-12. The test should not rely on a specific date or sequence.

White-box attention points:
- Ensure any zero-padding logic is correct for days/months < 10
- Ensure the generator returns a string (not integer) and preserves leading zeros

Edge cases:
- Verify CPR is always 10 characters long
- Verify day/month numeric ranges
- If the generator uses randomness, assert format rather than exact value

### 2) Return a fake name and gender
Contract (black-box):
- Input: no input
- Output: an associative array or object containing keys/fields `firstName`, `lastName`, and `gender`.
- Constraints: `firstName` and `lastName` are non-empty strings. `gender` is one of `male` or `female`.

White-box attention points:
- If first/last names are pulled from a data file, verify fallback behavior (no exception) when entries are missing
- If gender selection affects name formatting, test both branches if possible (mock randomness)

Edge cases:
- Non-ASCII characters in names should be handled (tests may assert string type and non-empty)
- Names with middle initials or punctuation — tests can assert that returned value is a string and not empty

---

## How to implement tests (examples)

Run unit tests folder:

```bash
cd backend
php ./vendor/bin/phpunit --configuration phpunit.xml --colors=always tests/Unit
```

Example PHPUnit assertions (pseudo-code that matches existing tests):

- CPR test (black-box):
```php
$output = $fakeInfo->getCpr();
$this->assertMatchesRegularExpression('/^\d{10}$/', $output);
// validate day/month if you want
$day = (int) substr($output, 0, 2);
$month = (int) substr($output, 2, 2);
$this->assertGreaterThanOrEqual(1, $day);
$this->assertLessThanOrEqual(31, $day);
$this->assertGreaterThanOrEqual(1, $month);
$this->assertLessThanOrEqual(12, $month);
```

- Name & gender test (black-box):
```php
$output = $fakeInfo->getFullNameAndGender();
$this->assertArrayHasKey('firstName', $output);
$this->assertArrayHasKey('lastName', $output);
$this->assertArrayHasKey('gender', $output);
$this->assertIsString($output['firstName']);
$this->assertNotEmpty($output['firstName']);
$this->assertIsString($output['lastName']);
$this->assertNotEmpty($output['lastName']);
$this->assertContains($output['gender'], ['male', 'female']);
```

### Making tests deterministic (white-box / advanced)
Because the code likely uses randomness, white-box testing can make the tests deterministic:

- Inject or mock the random source: if the class uses `mt_rand` or `random_int`, extract this into a protected method or inject a RNG dependency (or use a Mock class in tests) so you can return predictable values.
- Use the provided mock `MockFakeInfo` (the project contains a Mocks folder) to ensure deterministic outputs for certain tests.

---

## Example test-driven checklist

- [ ] Ensure methods to test are `public` and return predictable types
- [ ] Add assertions that validate format and constraints (length, regex, allowed values)
- [ ] Avoid asserting exact random data values — assert structure and format
- [ ] When necessary, add mock classes to assert branches and fallback behavior

---

## Where to store these notes
Save this file under `backend/tests/Unit/README.md` (already created). Keep it updated as tests evolve.

If you'd like, I can also:
- Add example deterministic mocks and a couple of extra white-box tests
- Re-run unit tests and show the current coverage for these two test cases

Tell me which of these you'd like next.