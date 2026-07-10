# SMS Project — Test Results

**PHP:** 8.2.12 (XAMPP CLI) · **Deps:** none (zero-dependency runners) · **DB:** MySQL `smsDB`

| Suite | Command | Tests | Result |
|-------|---------|------:|:------:|
| Unit (logic + project lint) | `php tests/run_tests.php` | **94** | ✅ 94/94 |
| Integration (live DB)       | `php tests/run_integration.php` | **25** | ✅ 25/25 |
| **Total** | | **119** | ✅ **all pass** |

Tests run the **real** application code: pure logic was extracted into
[`sms_admissions_helper.php`](../application/helpers/sms_admissions_helper.php) and
[`sms_core_helper.php`](../application/helpers/sms_core_helper.php), and the
controllers / models / views now call those same functions.

---

## 🐞 Real bug found & fixed by the suite
`application/controllers/Simplexlsx.class.php:293` used `$col{$i}` (curly-brace
string offset), **removed in PHP 8.0**. Including the file fataled on this PHP 8.2
install — so **`.xlsx` bulk import (student *and* enquiry) was broken**. Fixed to
`$col[$i]`. The project-wide lint suite now passes across all 206 first-party files.

Also hardened during testing: `sms_fee_remaining` / `sms_salary_net` now return a
consistent `float` (previously returned int `0` when clamped).

---

## Unit suite — `run_tests.php` (64 tests)

| Module | Covers | Tests |
|--------|--------|------:|
| 1. Enquiry entry | enquiry-no padding, source→existing-student toggle | 9 |
| 2. Follow-up status | status→label mapping, XSS-escaping | 6 |
| 3. Bulk import | header normalise, dedup map, cell fallback/trim | 11 |
| 4. Course fees | equal split, remainder absorption, fee-match check | 9 |
| 5. Grading | `get_grade` range matching, boundaries, config gaps | 7 |
| 6. Student fees | highest-of-sources paid, remaining never negative | 5 |
| 7. Teacher salary | CTC sum, net after deductions, never negative | 4 |
| 8. Age & validation | age-from-DOB, 10-digit mobile, email, money format | 11 |
| 9. Student add/update/import | full-name assembly/splitting, academic year, own-mobile rule, payment extraction/total, message-template render, shared import cell lookup | 30 |
| 10. Project syntax lint | `php -l` on all 207 first-party PHP files | 1 |

Notable edge cases proven: unknown enquiry status is HTML-escaped (XSS-safe);
uneven fee split `10000/3 → [3333.33, 3333.33, 3333.34]` still sums to exactly the
fee; grade boundaries are inclusive; a mark falling in a grade-config gap returns
`null`; age handles day-before/day-of/day-after birthday correctly.

## Integration suite — `run_integration.php` (18 tests)

Runs against live `smsDB`, uses `__TEST__`-marked rows, cleans up after itself.

| Group | Covers | Tests |
|-------|--------|------:|
| A. Migrations | module tables + new enquiry columns created (idempotent) | 6 |
| B. Enquiry round-trip | insert → persist → activity log → status transition → delete | 6 |
| C. Course round-trip | insert → 3 installments sum to fee → subject add → delete | 5 |
| D. Student add/update/delete | insert (clean name) → payment total → update name → delete + cleanup | 7 |
| E. Grading (live table) | helper matches a real `grade` row for an in-range mark | 1 |

---

## How to run
```bash
php tests/run_tests.php        # pure logic + whole-project syntax lint (no DB)
php tests/run_integration.php  # DB round-trips (needs MySQL running)
```
Both exit `0` on success / `1` on failure (CI-friendly).

## Scope / not covered
- HTTP/session auth guards & redirects (need a running web request).
- Third-party `application/libraries/*` (twilio, etc.) — vendored, out of scope.
- Full browser UI (rendering, JS) — exercise the pages manually.
