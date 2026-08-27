# Student Registration System

A Laravel-based digital student registration system built for ITST 302 – Client-Server
Technologies, Week 4 Laboratory Activity. It replaces a paper-based registration process with a validated, database-backed, step-by-step registration wizard that captures a student's personal, contact, address, and academic information along with a profile picture uploaded through a drag-and-drop file attachment control.

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
  mobile numbers, a birthdate that can't be in the future, and a program restricted to the
  college's official offerings.
- Uploaded and securely stored a student profile picture via a drag-and-drop attachment
  control backed by Laravel's `Storage` facade.
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

1. **Browser** — the user completes the five-step wizard at `/students/register` (Personal,
   Photo, Contact & Address, Academic, Review) and submits it as a `multipart/form-data`
   `POST` request (required because a file is being uploaded) only once every step has
   passed client-side checks.
2. **Route** — `routes/web.php` matches `POST /students` to `StudentController@store`.
3. **Controller** — `StudentController::store()` receives the request.
4. **Validation** — before the controller body runs, Laravel resolves `StoreStudentRequest`
   and runs its `rules()`. If any rule fails, the request is redirected back with `$errors`
   and the old input, and the controller method never executes.
5. **Model** — once validation passes, the controller stores the uploaded photo via
   `Storage::disk('public')` and creates a `Student` Eloquent model with the validated data.
6. **Database** — the `Student` model persists the record to the `students`
   SQLite/MySQL table via Eloquent's query builder.
7. **Response** — the controller redirects to `students.show`, flashing a `success` message
   to the session; the browser follows the redirect and renders the student's profile page.

For better visuals, check the diagram in documentations/ folder




## 5. Validation Rules

All student data is checked both on the server and on the user's screen for quick feedback before it is saved. Almost every field is required except for the middle name. The system makes sure that:

- The student number follows a fixed 0000‑0000 format and is not already used.

- Each student has a unique email address that is properly formatted.

- Name fields contain only letters with no numbers or symbols.

- The mobile number contains only digits and is 10 or 11 digits long.

- The date of birth must be a real date in the past, not today or in the future.

- Gender must be chosen from a short list of options.

- The program of study must be one of the college's 18 official programs, with no free‑text entries allowed.

- The profile picture must be a real image file in JPG, JPEG, or PNG format and no larger than 2 MB.

These checks prevent bad data from entering the system, avoid duplicate records, and protect against common typos or malicious uploads. The main table stores student information, with each column holding a specific piece of data:

- A unique system ID that is automatic.

- Student number, which is unique and follows the fixed format.

- First, middle (optional), and last names, with letters only.

- Email, which is unique, and mobile number with digits only.

- Date of birth, which must be in the past.

- Gender, program from the official list, and year level.

- Address details including province, city or municipality, and barangay.

- Profile picture stored as a file path rather than the image itself.

- Automatic timestamps for when the record was created and last updated.

## 7. Flowchart
A designed version of this flowchart is included in `documentation/`.

## 8. Screenshots

Screenshots documenting the working system are provided in the `screenshots/` folder.

## 9. Problems Encountered


Here is the simplified description with numbering starting from 1:

---

1. **Failed server-side validation didn't return the user to the right wizard step.** When validation failed, the whole page reloaded and the wizard's JavaScript always restarted at step one, even if the actual error was on step two or three, leaving the user with no visible error on the page.

2. **Blade facade resolution.** It wasn't clear whether Laravel's default facades like `Storage` would work inside Blade views without an explicit import, since Laravel 11+ no longer lists facade aliases in `config/app.php`.

3. **A drag-and-drop file input has no native wrong type validity state.** The HTML `accept` attribute only filters what the OS file picker shows, but files dragged in from the desktop can be any type, and the browser's built‑in validation has no check for that.

---

## 10 SOlutions

1. Marked every input that had a server‑side validation error with a special marker, then had the wizard's JavaScript scan for that marker when the page loaded. This made the wizard jump directly to the step containing the first invalid field instead of always going back to step one.

2. Confirmed that Laravel still registers default facade class aliases globally at boot time, even though they are no longer listed in `config/app.php`. This meant no import was needed in the Blade views.

3. Added a small client‑side check that inspects the selected file's type and size directly when a file is chosen or dropped. It rejects anything that is not a JPEG or PNG image or is over 2MB, before any preview is generated. The server‑side validation still performs the real, authoritative check.

## 11. Reflection

Here is the expanded version at roughly 500 words, keeping the casual and humanized tone:

---

Building this registration system made the difference between client-side and server-side validation feel real instead of just something I read about in a textbook. The wizard's live input masks, like stripping digits out of a name field, blocking letters in the mobile number, or refusing a future birthdate, make the form pleasant to fill out. But none of them are trustworthy on their own. Every one of those checks disappears the moment someone submits the form with JavaScript disabled, crafts a raw HTTP request, or edits the DOM before clicking submit. The Form Request class is the actual gatekeeper. It runs on the server, where the developer controls it, and no client-side trick can bypass it. The rule I'll carry forward is simple: always validate on the server, and only validate on the client as a courtesy that mirrors the server-side rule, never replaces it.

Handling user input also reframed how I think about a form. Every field the registration wizard asks for, like student number, name, mobile number, date of birth, and address, is a promise about the shape of the data future code can rely on. The regex rule that rejects a digit in a name field isn't pedantry. It is what keeps "Juan123" from silently becoming a permanent database record that every later report, export, or search has to work around. The `unique` rule on `student_number` and `email` is the same idea at the record level. It is what keeps one student from silently becoming two rows, which would eventually corrupt anything built on top of it, from attendance to billing. Designing the validation array field by field forced me to ask, for each one, "what actually breaks if this is wrong?" rather than reflexively marking everything `required` and calling it done. That kind of thinking changed how I approach forms entirely. I started seeing each input not as a box to fill but as a contract between the user and the system.

Splitting the address into province, municipality or city, and barangay instead of one free-text field taught me something similar about structured data. A single "address" text box is easy to build but nearly useless to query. You can't reliably filter students by city if the city name might be spelled three different ways inside a paragraph of free text. Three separate required fields cost a little more form-building effort but turn the address into data a report can actually group and filter on. This might seem like a small detail, but in practice, it makes a huge difference when someone in the admin office needs to pull a list of all students from a specific province.

Building the drag-and-drop photo upload changed how I think about file security specifically. A file input looks like just another form field, but it is a door into the server's filesystem, and a drag-and-drop zone makes that door even wider. Nothing about dropping a file onto a styled rectangle guarantees it is actually an image. The `image` and `mimes:jpg,jpeg,png` rules exist to make sure a `.php` file renamed to `photo.jpg` never gets treated as a real image, and the `max:2048` rule exists so a single upload can't exhaust server storage. The client-side type and size check makes the experience nicer by rejecting an obviously wrong file before it is even uploaded, but I made sure it was never load-bearing. The server-side rule is what actually protects the application, and I verified that by writing a test that submits a `.pdf` disguised with an image-sounding name and confirming the server still rejects it. Storing the file through Laravel's `Storage` facade rather than writing to `public/` by hand also matters. The framework handles path generation and keeps uploaded content off predictable public paths until a symbolic link deliberately exposes it. It gave me peace of mind knowing that even if something slipped past the front end, the back end would catch it.

Finally, this project made the enterprise angle of "just a registration form" clear. A student record created here is exactly the kind of record that a billing system, an academic records system, and a learning management system will all eventually read. If the data entering that pipeline is wrong, like a mistyped email, a duplicate student number, or a malformed address, the damage doesn't stay contained to this form. It propagates to every system downstream. That is the real argument for strict validation and a guided step-by-step collection process. It is not that they make the form "nicer," but that they are what makes the rest of the enterprise software built on top of this data trustworthy. Getting this right from the start saves countless headaches later.

## 12. References

CMU/SEI. (n.d.). IDS56-J. Prevent arbitrary file upload. Carnegie Mellon University. https://cmu-sei.github.io/secure-coding-standards/sei-cert-oracle-coding-standard-for-java/recommendations/input-validation-and-data-sanitization-ids/ids56-j 

Laracasts. (2025, August 13). How can I validate a form with Get method using FormRequest class? [Forum discussion]. https://laracasts.com/discuss/channels/laravel/how-can-i-validate-a-form-with-get-method-usng-formrequest-class?page=1&replyId=969344 

Laravel. (n.d.). Storage (Version 6.x) [API documentation]. https://api.laravel.com/docs/6.x/Illuminate/Support/Facades/Storage.html 

Laravel. (n.d.). File storage (Version 10.x). https://laravel.com/docs/10.x/filesystem 

Sencha. (2026, May 26). The complete guide to form validation in JavaScript (client & server side). https://www.sencha.com/blog/complete-guide-form-validation-javascript-client-server-side/ 

Safeguard. (2026, July 14). Secure file upload handling in Node.js and Fastify. https://safeguard.sh/resources/blog/secure-file-uploads-nodejs-fastify 

