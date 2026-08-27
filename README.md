# Student Registration System

A Laravel-based digital student registration system built for **ITST 302 – Client-Server
Technologies, Week 4 Laboratory Activity (Mini Project 03)**. It replaces a paper-based
registration process with a validated, database-backed, step-by-step registration wizard
that captures a student's personal, contact, address, and academic information.

## 2. Introduction

Universities, companies, hospitals, banks, and government agencies all rely on registration
systems to collect and manage information about the people they serve. A **Student
Registration System** is one of the most common examples: it is the entry point where a
person's identity and academic record first become digital data that every other system in
the institution will depend on.

**Why data validation matters.** A registration form is the boundary between the outside
world and the application's database. Anything can arrive at that boundary — an empty field,
a malformed email, a duplicate student number, a name typed with digits in it, a birthdate
set in the future. Without validation, bad data doesn't just look untidy; it corrupts
downstream processes such as grade reporting, billing, and alumni records. Server-side
validation is what guarantees that only complete, correct, and properly formatted data is
ever persisted, regardless of what the client sends — client-side checks (this project's
step-by-step wizard included) exist purely to make that easier to satisfy up front, not to
replace the server-side guarantee.

**Role in enterprise applications.** Registration modules like this one are rarely
standalone. They are the first link in a chain of enterprise systems — enrollment, billing,
academic records, learning management systems — that all read from the same trusted source
of truth. Getting the registration step right (validated input, clear guidance, clear
feedback to the user) is what makes every system built on top of it reliable.

## 3. Objectives

This activity accomplished the following learning objectives:

- Built a multi-step registration wizard using Blade templates, reusable Blade components,
  and vanilla JavaScript for step navigation and live client-side field feedback.
- Processed client requests through a dedicated `StudentController`.
- Implemented strict server-side validation using a Laravel Form Request
  (`StoreStudentRequest`) — format-checked student numbers, letters-only names, digits-only
  mobile numbers, and a birthdate that can't be in the future.
- Displayed flash messages for successful registration and inline validation errors for
  failed submissions, with the wizard automatically returning the user to the exact step
  that failed validation.
- Designed and implemented a relational `students` table using a Laravel migration, with the
  address split into province, municipality/city, and barangay.
- Covered the registration flow with automated Pest feature tests.
- Documented the development process, request lifecycle, and validation design in this
  README.

## 4. Laravel Request Lifecycle

A registration submission moves through the framework in the following order:

1. **Browser** — the user completes the four-step wizard at `/students/register` (Personal,
   Contact & Address, Academic, Review) and submits it as a `POST` request only once every
   step has passed client-side checks.
2. **Route** — `routes/web.php` matches `POST /students` to `StudentController@store`.
3. **Controller** — `StudentController::store()` receives the request.
4. **Validation** — before the controller body runs, Laravel resolves `StoreStudentRequest`
   and runs its `rules()`. If any rule fails, the request is redirected back with `$errors`
   and the old input, and the controller method never executes.
5. **Model** — once validation passes, the controller creates a `Student` Eloquent model
   with the validated data.
6. **Database** — the `Student` model persists the record to the `students`
   SQLite/MySQL table via Eloquent's query builder.
7. **Response** — the controller redirects to `students.show`, flashing a `success` message
   to the session; the browser follows the redirect and renders the student's profile page.

```
Browser (wizard submit)
      │
      ▼
   Route (POST /students)
      │
      ▼
StudentController@store
      │
      ▼
StoreStudentRequest validation ──fails──▶ redirect back with errors (wizard reopens on the failing step)
      │ passes
      ▼
Student::create() (Model)
      │
      ▼
students table (Database)
      │
      ▼
Redirect + flash message (Response) ──▶ Browser renders profile page
```

*A polished version of this diagram is included in `documentation/`.*

## 5. Validation Rules

Validation is implemented in `app/Http/Requests/StoreStudentRequest.php`, and mirrored on
the client (masking + `pattern`/`max` attributes in `resources/js/app.js` and
`resources/views/students/create.blade.php`) purely for instant feedback:

```php
public function rules(): array
{
    return [
        'student_number' => ['required', 'regex:/^\d{4}-\d{4}$/', 'unique:students,student_number'],
        'first_name' => ['required', 'string', 'max:100', 'regex:'.self::NAME_PATTERN],
        'middle_name' => ['nullable', 'string', 'max:100', 'regex:'.self::NAME_PATTERN],
        'last_name' => ['required', 'string', 'max:100', 'regex:'.self::NAME_PATTERN],
        'email' => ['required', 'email', 'unique:students,email'],
        'mobile_number' => ['required', 'digits_between:10,11'],
        'date_of_birth' => ['required', 'date', 'before:today'],
        'gender' => ['required', 'in:Male,Female,Other'],
        'program' => ['required', 'string', 'max:150'],
        'year_level' => ['required', 'string', 'max:50'],
        'province' => ['required', 'string', 'max:100'],
        'municipality_city' => ['required', 'string', 'max:100'],
        'barangay' => ['required', 'string', 'max:100'],
    ];
}
```

| Rule | Why it matters |
| --- | --- |
| **Required fields** | Every field except middle name is essential to identify, contact, and place the student. Missing data at this stage means broken records everywhere downstream. |
| **Unique constraints** (`student_number`, `email`) | Prevents duplicate enrollment records and guarantees each student can be uniquely identified and reached by email. |
| **Student number format** (`regex:/^\d{4}-\d{4}$/`) | Enforces the institution's `0000-0000` numbering scheme and rejects letters or malformed input before it ever reaches the database. |
| **Letters-only names** (`regex` on `first_name`/`middle_name`/`last_name`) | A digit in a name field is never valid data — this rule (and the matching live input mask) rejects it outright instead of silently storing a typo. |
| **Digits-only mobile number** (`digits_between:10,11`) | Rejects letters or symbols in a field that should only ever contain digits, catching typos before they reach the database. |
| **Email validation** | Confirms the address is well-formed before it is stored and later used for official communication. |
| **Future-date rejection** (`before:today` on `date_of_birth`) | A birthdate in the future is never valid — this rule (backed by the date input's `max` attribute) makes that impossible to submit. |

## 6. Database Design

### Table: `students`

| Column | Type | Constraints |
| --- | --- | --- |
| `id` | `bigint unsigned` | Primary key, auto-increment |
| `student_number` | `string` | Unique, not null, format `0000-0000` |
| `first_name` | `string` | Not null, letters only |
| `middle_name` | `string` | Nullable, letters only |
| `last_name` | `string` | Not null, letters only |
| `email` | `string` | Unique, not null |
| `mobile_number` | `string` | Not null, digits only |
| `date_of_birth` | `date` | Not null, cannot be a future date |
| `gender` | `string` | Not null |
| `program` | `string` | Not null |
| `year_level` | `string` | Not null |
| `province` | `string` | Not null |
| `municipality_city` | `string` | Not null |
| `barangay` | `string` | Not null |
| `created_at` / `updated_at` | `timestamp` | Managed automatically by Eloquent |

*The Entity Relationship Diagram is included in `documentation/`.*

## 7. Flowchart

```
User Opens Registration Page (Step 1: Personal)
        │
        ▼
Step 2: Contact & Address ──▶ Step 3: Academic ──▶ Step 4: Review
        │ (each "Continue" click validates the current step client-side)
        ▼
  Submit Registration
        │
        ▼
  Laravel Validation
   ┌─────────────┐
   │ Valid Data? │
   └──────┬──────┘
          │
    Yes   │   No
     ▼    │    ▼
Save to Database   Reopen wizard on the failing step, display errors
     │
     ▼
Success Message
     │
     ▼
Student Profile Page
```

*A designed version of this flowchart is included in `documentation/`.*

## 8. Screenshots

Screenshots documenting the working system are provided in the `screenshots/` folder,
including each step of the registration wizard, validation warnings, the flash success
message, the database table, the student profile page, the VS Code project structure, and
the GitHub repository.

## 9. Problems Encountered

1. **No local MySQL credentials available.** The lab spec calls for MySQL, but the local
   MySQL 8.0 service required a root password that wasn't available in this environment.
2. **Failed server-side validation didn't return the user to the right wizard step.** Since
   the whole page reloads on a failed submission, the wizard's JavaScript naturally restarts
   at step one — even when the actual validation error was on step two or three, leaving the
   user staring at a page with no visible error.
3. **Blade facade resolution.** Before relying on facades like `Storage` inside Blade views,
   it wasn't obvious whether they'd resolve without an explicit `use` import, since Laravel
   11+ no longer lists facade aliases in `config/app.php`.

## 10. Solutions

1. Switched `DB_CONNECTION` to the `sqlite` driver that ships pre-configured with a fresh
   Laravel installation. It uses the exact same Eloquent models, migrations, and query
   builder as MySQL, so the application code is unchanged — only the `.env` connection
   differs.
2. Marked every input with a server-side validation error (`@error($name) data-server-error
   @enderror`) and had the wizard's JavaScript scan for that marker on page load, jumping
   straight to the step that contains the first invalid field instead of always defaulting
   to step one.
3. Confirmed with `php artisan tinker --execute 'var_dump(class_exists("Storage"));'` that
   Laravel still registers default facade class aliases (`Storage`, `Auth`, etc.) globally
   at boot time even though `config/app.php` no longer lists them explicitly, so no import
   was needed in the Blade views.

## 11. Reflection

Building this registration system made the difference between client-side and server-side
validation concrete instead of theoretical. The wizard's live input masks — stripping digits
out of a name field, blocking letters in the mobile number, refusing a future birthdate —
make the form pleasant to fill out, but none of them are trustworthy on their own. Every one
of those checks disappears the moment someone submits the form with JavaScript disabled,
crafts a raw HTTP request, or edits the DOM before clicking submit. The Form Request class is
the actual gatekeeper: it runs on the server, where the developer controls it, and no
client-side trick can bypass it. The rule I'll carry forward is simple: validate on the
server always, and validate on the client only as a courtesy that mirrors — never replaces —
the server-side rule.

Handling user input also reframed how I think about a form. Every field the registration
wizard asks for — student number, name, mobile number, date of birth, address — is a promise
about the shape of the data future code can rely on. The regex rule that rejects a digit in a
name field isn't pedantry; it's what keeps "Juan123" from silently becoming a permanent
database record that every later report, export, or search has to work around. The `unique`
rule on `student_number` and `email` is the same idea at the record level: it's what keeps
one student from silently becoming two rows, which would eventually corrupt anything built on
top of it, from attendance to billing. Designing the validation array field by field forced
me to ask, for each one, "what actually breaks if this is wrong?" rather than reflexively
marking everything `required` and calling it done.

Splitting the address into province, municipality/city, and barangay instead of one free-text
field taught me something similar about structured data. A single "address" text box is easy
to build but nearly useless to query — you can't reliably filter students by city if the city
name might be spelled three different ways inside a paragraph of free text. Three separate,
required fields cost a little more form-building effort but turn the address into data a
report can actually group and filter on.

Finally, this project made the enterprise angle of "just a registration form" clear. A
student record created here is exactly the kind of record that a billing system, an academic
records system, and a learning management system will all eventually read. If the data
entering that pipeline is wrong — a mistyped email, a duplicate student number, a malformed
address — the damage doesn't stay contained to this form; it propagates to every system
downstream. That is the real argument for strict validation and a guided, step-by-step
collection process: not that they make the form "nicer," but that they are what makes the
rest of the enterprise software built on top of this data trustworthy.

## 12. References

*(Citations to be added — APA 7th edition.)*

## Local Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run build   # or `npm run dev` / `composer run dev` during development
php artisan serve
```

Visiting the app's root URL redirects straight to `/students/register`, the registration
wizard. Visit `/students` to view all registered students.

## Running Tests

```bash
php artisan test --compact
```
