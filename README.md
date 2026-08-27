# Student Registration System

A Laravel-based digital student registration system built for **ITST 302 – Client-Server
Technologies, Week 4 Laboratory Activity (Mini Project 03)**. It replaces a paper-based
registration process with a validated, database-backed web form that captures a student's
personal and academic information along with a profile picture.

## 2. Introduction

Universities, companies, hospitals, banks, and government agencies all rely on registration
systems to collect and manage information about the people they serve. A **Student
Registration System** is one of the most common examples: it is the entry point where a
person's identity and academic record first become digital data that every other system in
the institution will depend on.

**Why data validation matters.** A registration form is the boundary between the outside
world and the application's database. Anything can arrive at that boundary — an empty field,
a malformed email, a duplicate student ID, an executable file disguised as an image. Without
validation, bad data doesn't just look untidy; it corrupts downstream processes such as
grade reporting, billing, and alumni records, and unvalidated file uploads can turn into a
security vulnerability. Server-side validation is what guarantees that only complete, correct,
and safe data is ever persisted, regardless of what the client sends.

**Role in enterprise applications.** Registration modules like this one are rarely
standalone. They are the first link in a chain of enterprise systems — enrollment, billing,
academic records, learning management systems — that all read from the same trusted source
of truth. Getting the registration step right (validated input, secure file handling, clear
feedback to the user) is what makes every system built on top of it reliable.

## 3. Objectives

This activity accomplished the following learning objectives:

- Built an HTML registration form using Blade templates and reusable Blade components.
- Processed client requests through a dedicated `StudentController`.
- Implemented server-side validation using a Laravel Form Request (`StoreStudentRequest`).
- Displayed flash messages for successful registration and inline validation errors for
  failed submissions.
- Uploaded and securely stored a student profile picture using Laravel's `Storage` facade.
- Designed and implemented a relational `students` table using a Laravel migration.
- Covered the registration flow with automated Pest feature tests.
- Documented the development process, request lifecycle, and validation design in this
  README.

## 4. Laravel Request Lifecycle

A registration submission moves through the framework in the following order:

1. **Browser** — the user fills out the form at `/students/register` and submits it as a
   `multipart/form-data` `POST` request (required because a file is being uploaded).
2. **Route** — `routes/web.php` matches `POST /students` to `StudentController@store`.
3. **Controller** — `StudentController::store()` receives the request.
4. **Validation** — before the controller body runs, Laravel resolves `StoreStudentRequest`
   and runs its `rules()`. If any rule fails, the request is redirected back with
   `$errors` and the old input, and the controller method never executes.
5. **Model** — once validation passes, the controller stores the uploaded file via
   `Storage::disk('public')` and creates a `Student` Eloquent model with the validated data.
6. **Database** — the `Student` model persists the record to the `students` SQLite/MySQL
   table via Eloquent's query builder.
7. **Response** — the controller redirects to `students.show`, flashing a `success` message
   to the session; the browser follows the redirect and renders the student's profile page.

```
Browser (form submit)
      │
      ▼
   Route (POST /students)
      │
      ▼
StudentController@store
      │
      ▼
StoreStudentRequest validation ──fails──▶ redirect back with errors
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

Validation is implemented in `app/Http/Requests/StoreStudentRequest.php`:

```php
public function rules(): array
{
    return [
        'student_id' => ['required', 'string', 'max:50', 'unique:students,student_id'],
        'first_name' => ['required', 'string', 'max:100'],
        'middle_name' => ['nullable', 'string', 'max:100'],
        'last_name' => ['required', 'string', 'max:100'],
        'email' => ['required', 'email', 'unique:students,email'],
        'mobile_number' => ['required', 'numeric', 'digits_between:7,15'],
        'date_of_birth' => ['required', 'date', 'before:today'],
        'gender' => ['required', 'in:Male,Female,Other'],
        'program' => ['required', 'string', 'max:150'],
        'year_level' => ['required', 'string', 'max:50'],
        'address' => ['required', 'string', 'max:255'],
        'profile_picture' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
    ];
}
```

| Rule | Why it matters |
| --- | --- |
| **Required fields** | Every field except middle name is essential to identify and contact the student. Missing data at this stage means broken records everywhere downstream. |
| **Unique constraints** (`student_id`, `email`) | Prevents duplicate enrollment records and guarantees each student can be uniquely identified and reached by email. |
| **Email validation** | Confirms the address is well-formed before it is stored and later used for official communication. |
| **Numeric validation** (`mobile_number`) | Rejects letters or symbols in a field that should only ever contain digits, catching typos before they reach the database. |
| **Image validation** (`image`, `mimes:jpg,jpeg,png`) | Ensures the uploaded file is actually a displayable image and not an arbitrary file type — a basic but important defense against malicious uploads. |
| **File size restriction** (`max:2048`) | Caps uploads at 2MB to protect server storage and keep page loads fast. |

## 6. Database Design

### Table: `students`

| Column | Type | Constraints |
| --- | --- | --- |
| `id` | `bigint unsigned` | Primary key, auto-increment |
| `student_id` | `string` | Unique, not null |
| `first_name` | `string` | Not null |
| `middle_name` | `string` | Nullable |
| `last_name` | `string` | Not null |
| `email` | `string` | Unique, not null |
| `mobile_number` | `string` | Not null |
| `date_of_birth` | `date` | Not null |
| `gender` | `string` | Not null |
| `program` | `string` | Not null |
| `year_level` | `string` | Not null |
| `address` | `text` | Not null |
| `profile_picture` | `string` | Not null (stores the relative storage path) |
| `created_at` / `updated_at` | `timestamp` | Managed automatically by Eloquent |

*The Entity Relationship Diagram is included in `documentation/`.*

## 7. Flowchart

```
User Opens Registration Page
        │
        ▼
    Fill Out Form
        │
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
Save to Database   Display Errors
     │                   │
     ▼                   │
Upload Profile           │
     │                   │
     ▼                   │
Success Message ◄─────────┘
     │
     ▼
Student Profile Page
```

*A designed version of this flowchart is included in `documentation/`.*

## 8. Screenshots

Screenshots documenting the working system are provided in the `screenshots/` folder,
including the registration form, validation errors, the flash success message, the uploaded
profile picture, the database table, the student profile page, the VS Code project
structure, and the GitHub repository.

## 9. Problems Encountered

1. **No local MySQL credentials available.** The lab spec calls for MySQL, but the local
   MySQL 8.0 service required a root password that wasn't available in this environment.
2. **`UploadedFile::fake()->image()` failed in automated tests.** Pest's feature tests for
   the upload flow initially used Laravel's `image()` file fake, which generates real image
   bytes through the PHP GD extension.
3. **Blade facade resolution for `Storage::url()`.** Before wiring up the profile picture
   `<img>` tag, it wasn't obvious whether the bare `Storage` facade would resolve inside a
   Blade view without an explicit `use` import, since Laravel 11+ no longer lists facade
   aliases in `config/app.php`.

## 10. Solutions

1. Switched `DB_CONNECTION` to the `sqlite` driver that ships pre-configured with a fresh
   Laravel installation. It uses the exact same Eloquent models, migrations, and query
   builder as MySQL, so the application code is unchanged — only the `.env` connection
   differs.
2. Replaced `UploadedFile::fake()->image()` with `UploadedFile::fake()->create('profile.jpg',
   100, 'image/jpeg')`, which builds a fake upload with an explicit MIME type without
   needing GD to render real pixel data. Laravel's `image` validation rule checks the
   reported MIME type, so the test still exercises the real validation rule.
3. Confirmed with `php artisan tinker --execute 'var_dump(class_exists("Storage"));'` that
   Laravel still registers default facade class aliases (`Storage`, `Auth`, etc.) globally
   at boot time even though `config/app.php` no longer lists them explicitly, so no import
   was needed in the Blade views.

## 11. Reflection

Building this registration system made the difference between client-side and server-side
validation concrete instead of theoretical. It would have been easy to trust a `required`
attribute on an HTML input and call the form "validated," but that attribute disappears the
moment someone submits the form with JavaScript disabled, crafts a raw HTTP request, or
edits the DOM before clicking submit. The Form Request class is the actual gatekeeper: it
runs on the server, where the developer controls it, and no client-side trick can bypass it.
Client-side validation still earns its place — it gives the user instant feedback without a
round trip to the server — but treating it as anything more than a convenience is a mistake.
The rule I'll carry forward is simple: validate on the server always, and validate on the
client only as a courtesy.

Handling user input also reframed how I think about a form. Every field the registration
form asks for — student ID, email, mobile number, date of birth — is a promise about the
shape of the data future code can rely on. The `unique` rule on `student_id` and `email`
isn't just a nicety; it's what keeps one student from silently becoming two rows in the
database, which would eventually corrupt anything built on top of it, from attendance to
billing. Writing the validation array field by field forced me to ask, for each one, "what
actually breaks if this is wrong?" rather than reflexively marking everything `required`.

File uploads were the part that most changed how I think about security. A file input looks
like just another form field, but it is a door into the server's filesystem. The `image`
and `mimes:jpg,jpeg,png` rules exist to make sure a `.php` file renamed to `photo.jpg`
never gets treated as a real image, and the `max:2048` rule exists so a single upload can't
exhaust server storage. Storing the file through Laravel's `Storage` facade rather than
writing to `public/` by hand also matters: the framework handles path generation and keeps
uploaded content out of version control and off predictable public paths until a symbolic
link deliberately exposes it. None of this is exotic — it's the baseline expected of any
production form that accepts a file.

Finally, this project made the enterprise angle of "just a registration form" clear. A
student record created here is exactly the kind of record that a billing system, an
academic records system, and a learning management system will all eventually read. If the
data entering that pipeline is wrong — a mistyped email, a duplicate ID, a corrupted image
— the damage doesn't stay contained to this form; it propagates to every system downstream.
That is the real argument for validation, flash messaging, and careful file handling: not
that they make the form "nicer," but that they are what makes the rest of the enterprise
software built on top of this data trustworthy.

## 12. References

*(Citations to be added — APA 7th edition.)*

## Local Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
npm run build   # or `npm run dev` / `composer run dev` during development
php artisan serve
```

Visit `/students/register` to register a student and `/students` to view all registered
students.

## Running Tests

```bash
php artisan test --compact
```
