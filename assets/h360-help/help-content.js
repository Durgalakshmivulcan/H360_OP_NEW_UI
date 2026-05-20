/* H360 page-help content registry.
   Keys are page filenames (lowercase, no path). Pages without an entry get no
   help button. Keep copy short, plain-language, action-first.
   Reports + legacy *_old + print popups + entry pages are intentionally omitted.
   FIX_B_2343: drift sweep — buttons say "Submit" not "Save", removed print-popup
   entries (combinedbill/billview/patientview/patientprescription) which never
   load footer.php; corrected receptionist.php (Start/Done/Lapsed not Call Next)
   and AppointmentOnline.php (New/Existing tabs → Book Appointment).
   FIX_B_2350: every entry now has an `example` field — a realistic walkthrough
   that auto-localises per clinic via {{vars.X}} placeholders fed by the
   server-emitted window.H360_HELP_VARS (see ajax/footer.php). Names you see
   below — {{vars.doctor1}}, {{vars.doctor2}}, {{vars.clinicName}},
   {{vars.emailDomain}} — resolve to whatever the live deployment has. */
window.H360_HELP_CONTENT = {

  // ===== Front desk (Receptionist) =====
  'registration.php': {
    eyebrow: 'Admin · Access Control',
    title:   'User Registration',
    updated: 'Updated 2026-05-11',
    purpose:
      'Add or edit a staff account — receptionists, doctors, pharmacists, accountants, admins, super admins. The user logs in to H360 with the email and password you set here, and their role drives every screen they can see.',
    steps: [
      'Click into the form, fill Name, Email (this is the login), Mobile, Password, and Role. Pick the Organization (SA-only).',
      'For Receptionist users only, an "Allow switching between doctors" checkbox appears. Tick it for receptionists who handle both OP rooms and need to focus on one doctor\'s queue at times. Leave unchecked for receptionists who should always see the merged view.',
      'Click Submit. The user can log in immediately with the email + password you set.',
      'To edit an existing user, click the pencil icon on the row. To remove, click the trash icon (soft delete — preserves audit trail).',
    ],
    example: {
      title: 'Adding a weekend receptionist who handles both OP rooms at {{vars.clinicName}}',
      steps: [
        'Name: Priya Reddy',
        'Email: priya.reddy@{{vars.emailDomain}} · Mobile: 9876543210',
        'Role: Receptionist → "Allow switching between doctors" checkbox appears',
        'Tick the checkbox (she\'ll be alone at the desk on weekends)',
        'Submit → Priya logs in, sees a doctor picker in her top bar with All Doctors / {{vars.doctor1}} / {{vars.doctor2}}',
      ],
    },
    tips: [
      'One person = one user account. Don\'t share logins — the audit log loses meaning otherwise.',
      'Email is the login identifier; mobile is purely informational.',
      'The doctor-switcher capability is per-user, not per-role — you can give it to one receptionist and not the other.',
      'Toggling the checkbox takes effect immediately — the receptionist does not need to log out and back in for the picker to appear or disappear.',
    ],
    warnings: [
      'Don\'t reuse an email — the system de-duplicates on it and will reject the save.',
      'Patient registration is a different page — open AppointmentOnline.php or the receptionist queue to add patients. This page only manages staff logins.',
      'The Super Admin account is intentionally invisible here — it is bootstrap-only and never appears in the list, the role picker, or any edit/delete action. If you need to change the SA password, that has to be done by the platform admin out-of-band.',
    ],
  },

  'receptionist.php': {
    eyebrow: 'Receptionist · Front desk',
    title:   'Receptionist Queue',
    updated: 'Updated 2026-05-11',
    purpose:
      'The live dashboard of today\'s patient flow. See who has registered, who is waiting, who is in consultation, and what each doctor\'s load looks like in real time.',
    steps: [
      'Watch the queue table — it auto-refreshes. New patients appear as soon as a booking is saved on the Appointments page.',
      'On each row use Start to mark the patient in-consult, Done when the doctor finishes the visit, or Lapsed if the patient walked off without being seen. The visitor display board reflects the change instantly.',
      'Use Apply Filters at the top to narrow by doctor, date, or status before working through the queue.',
      'When a payment is collected at the desk, the payment modal appears — pick UPI/Cash/Card, enter the amount and (for UPI) the transaction number, then Save.',
    ],
    example: {
      title: 'Working a patient through the queue',
      steps: [
        '11:02 — Mr. Suresh Kumar (A202605110007) appears as Waiting under {{vars.doctor1}}',
        '11:05 — {{vars.doctor1}} calls him in → click Start. The visitor TV updates: "Now serving · Mr. Suresh Kumar"',
        '11:18 — Consultation done → click Done; the payment modal pops up',
        'Pick Cash · Amount 500 · Save → bill recorded, queue advances to the next patient',
      ],
    },
    tips: [
      'This is the page to keep open all day — it is the single source of truth for the front desk.',
      'The KPI tiles at the top (Total / Waiting / Done) reset at midnight automatically.',
      'If a doctor is running late, use the rescheduling action on a row instead of cancelling and re-registering.',
      'If an admin has enabled the "Allow switching between doctors" capability on your user account, a doctor picker appears in the top bar. Use it to narrow to one doctor when one OP room is busier, or pick "All Doctors" to return to the merged queue.',
    ],
    warnings: [
      'Don\'t mark a patient Done unless they have actually been seen — that flag drives the revenue and outcome reports.',
    ],
  },

  'appointmentonline.php': {
    eyebrow: 'Receptionist · Appointments',
    title:   'Add Appointment',
    updated: 'Updated 2026-05-11',
    purpose:
      'Book a new visit and create the patient record in one shot. Today this page handles new bookings only — the Existing-Patient and Modify flows have been retired from this screen; row-level actions on existing visits live on the Receptionist Queue.',
    steps: [
      'Fill the patient block: Name, Mobile, Age, Sex, Address. Mobile is the de-duplication key — if the number is already on file you\'ll see a warning, switch to that patient instead of creating a duplicate.',
      'Pick the Doctor and a Time Slot from the dropdowns.',
      'Click Book Appointment.',
      'A confirmation modal pops up with the booking summary. Review the doctor, slot, and patient details, then confirm. The new visit shows up immediately on the Receptionist Queue.',
    ],
    example: {
      title: 'Booking a walk-in patient for {{vars.doctor1}}',
      steps: [
        'Name: Lakshmi Iyer · Mobile: 9123456789 · Age: 54 · Sex: F',
        'Address: 12-3, MVP Colony, Visakhapatnam',
        'Doctor: {{vars.doctor1}} · Time Slot: 10:30 AM – 10:45 AM',
        'Click Book Appointment → confirmation modal shows token A202605110008',
        'Confirm → row appears on the Receptionist Queue under {{vars.doctor1}}',
      ],
    },
    tips: [
      'The page only handles adding new appointments. To call patients in / mark them done, reschedule, or cancel a visit, use the Receptionist Queue (receptionist.php).',
      'The unique ID printed on confirmation is what every other module references — note it on any paper notes for the patient.',
      'The doctor + slot dropdowns show options for the date you picked. To book ahead, change the date first.',
      'If saving fails (server error, duplicate mobile, missing field), the confirmation modal now closes automatically and an error message tells you what went wrong — the page no longer stays stuck behind a grey overlay.',
    ],
    warnings: [
      'Cancelling an appointment does not refund the patient — use the Refunds page if money has been collected.',
      'You may notice the breadcrumb still reads "Add & Modify Appointment". The modify flow has been retired from this page — only the New Patient form is live. The Existing Patient tab and its panel exist in the HTML source but are commented out.',
    ],
  },

  'receptionistboard.php': {
    eyebrow: 'Receptionist · Display',
    title:   'Reception Board',
    updated: 'Updated 2026-05-11',
    purpose:
      'A read-only board view of today\'s queue, intended for a secondary screen at the reception desk. Same data as the main queue but laid out for at-a-glance scanning.',
    steps: [
      'Open this page on the second monitor and leave it running.',
      'It auto-refreshes every few seconds — no need to reload manually.',
    ],
    example: {
      title: 'Setting up the second monitor at the reception desk',
      steps: [
        'On the secondary monitor, open Chrome / Edge',
        'Navigate to /ReceptionistBoard.php and log in',
        'Press F11 for full-screen — chrome disappears',
        'Leave it running. Numbers stay in sync with the main queue automatically',
      ],
    },
    tips: [
      'Best on a 1080p+ screen in full-screen mode (press F11).',
      'It is read-only — to actually move patients, use the main Receptionist Queue page.',
    ],
  },

  'tv_ads.php': {
    eyebrow: 'Admin · TV display',
    title:   'TV Ads',
    updated: 'Updated 2026-05-11',
    purpose:
      'Upload and manage the rotating ads shown in the bottom strip of the waiting-room TV (Visitor Display). Strict quality checks reject low-resolution or oversized images so the 60-inch panel always looks crisp.',
    steps: [
      'Drag an image onto the drop zone, or click to pick one. JPG, PNG, or WebP only.',
      'Wait for the live checks to run — Format, File size, Resolution, Aspect ratio. A green tick on every line means it will look great; a red one tells you exactly what to fix.',
      'Click Upload to TV. The new ad appears in the "Now showing" grid below and starts rotating on the TV within a few minutes.',
      'To take an ad down, click Remove on its tile. It disappears from the TV on the next refresh.',
    ],
    example: {
      title: 'Adding a "Free BP Check on Tuesdays" promo for {{vars.clinicName}}',
      steps: [
        'Designer hands you tuesday-bp-promo.jpg at 3840×1200, 720 KB',
        'Drag the file onto the drop zone',
        'Live checks: ✓ JPG · ✓ 720 KB · ✓ 3840×1200 · ✓ 3.20:1 — all green',
        'Click Upload to TV — new tile appears as "20260511..-tuesday-bp-promo.jpg"',
        'Within a minute, the waiting-room TV starts rotating it alongside any other ads',
      ],
    },
    tips: [
      'Use 3840×1200 (16:5) for the sharpest result on a 60-inch 4K panel — that is the recommended resolution.',
      'If you have zero ads uploaded, the TV automatically collapses the bottom strip and uses the entire screen for doctors and the queue — no empty placeholder is shown.',
      'Files starting with an underscore (e.g. _draft.jpg) on the server are hidden from the rotation. Useful for staging without removing the file.',
      'Ads rotate every 8 seconds with a crossfade. Keep total ads under ~15 so each one is shown often enough to be noticed.',
    ],
    warnings: [
      'Uploads below 1920×600 are rejected outright — they would look blurry on the waiting-room TV.',
      'Files over 5 MB are rejected to keep the crossfade smooth. Compress to JPG quality 80–85 before uploading large photos.',
      'GIFs and animations are not supported — they are distracting on a public display.',
    ],
  },

  'visitors_doctor_display.php': {
    eyebrow: 'Waiting room · TV display',
    title:   'Visitor Display Board',
    updated: 'Updated 2026-05-11',
    purpose:
      'The public-facing screen shown to patients in the waiting area. Top: each doctor\'s photo, name, currently-being-served patient, next in line, and the wait queue. Bottom: a rotating ad strip you control by dropping images into a folder. The layout auto-adapts when one OR both doctors are present.',
    steps: [
      'On the waiting-room TV, open Chrome / Edge in kiosk or fullscreen mode and navigate to /visitors_doctor_display.php. Press F11 (or tap anywhere) for fullscreen.',
      'In the receptionist queue, mark a patient as "called" — they appear instantly on this screen as "Now serving" for the right doctor.',
      'When both doctors are at the clinic, the screen splits down the middle with a gold divider; left side and right side update independently.',
      'To manage the rotating ad strip at the bottom, use the dedicated TV Ads page (Appointments → TV Ads). Uploads there are quality-checked for the 60-inch panel.',
    ],
    example: {
      title: 'A typical Tuesday morning at {{vars.clinicName}} (both OPs running)',
      steps: [
        '09:55 — Receptionist opens the TV to /visitors_doctor_display.php and presses F11',
        '10:00 — First patients of {{vars.doctor1}} and {{vars.doctor2}} arrive; she clicks Start on each → the screen splits 50/50',
        'Left half shows {{vars.doctor1}}\'s photo + Now serving + queue · Right half shows {{vars.doctor2}}\'s · Bottom strip rotates the latest 3 ads',
        '12:30 — {{vars.doctor2}} leaves; her side empties → the screen automatically collapses to a single full-width view of {{vars.doctor1}}',
      ],
    },
    tips: [
      'No login refresh is needed — the patient queue updates every few seconds via a live connection (the green dot top-right confirms it\'s connected; red means lost connection).',
      'When zero ads are uploaded, the TV hides the bottom strip entirely and the doctor stage takes the full screen — no empty placeholder.',
      'The ad strip rotates every 8 seconds with a crossfade once at least one ad is uploaded.',
      'Clock + clinic name in the top bar are decorative — leave the tab alone once running.',
      'The page suppresses all admin chrome (sidebar, breadcrumbs, help button) since it\'s meant as a kiosk view.',
    ],
    warnings: [
      'Don\'t use this tab to navigate the rest of H360 — open a separate browser window. Closing or refreshing the kiosk tab interrupts the live queue.',
    ],
  },

  // ===== Doctor =====
  'doctorstimeslot.php': {
    eyebrow: 'Doctor · Schedule',
    title:   'Doctor Time Slots',
    updated: 'Updated 2026-05-11',
    // FIX_B_2351: B-2343 drift sweep was inaccurate — the page has two tabs
    // (single Date + Range with weekday checkboxes), only Start/End Time inputs
    // (no Duration field), a "+" pill to add another slot row, and a Submit
    // button. There is no Generate button and no per-slot "Mark Unavailable" /
    // capacity feature — the table below the form is just the existing slots
    // with edit/delete row actions.
    purpose:
      'Set or change the consultation slots a doctor is available for on a given day or across a date range. Slots created here are what receptionists can book patients into.',
    steps: [
      'Pick the Day tab (one date) or the Range tab (From Date → To Date, plus the weekday checkboxes).',
      'Pick the doctor, then enter Start Time and End Time for the slot. Click the "+" pill on the right to add a second/third row if the doctor has split shifts.',
      'Click Submit — every slot row is saved against the selected day(s).',
      'The "Doctor Slots List" table below shows existing slots. Use the row\'s edit/delete actions to change or remove one.',
    ],
    example: {
      title: 'Setting morning slots Mon–Sat for {{vars.doctor1}} via the Range tab',
      steps: [
        'Switch to the Range tab',
        'Doctor: {{vars.doctor1}} · From Date: Mon 12 May · To Date: Sat 17 May 2026',
        'Tick weekdays: Mon, Tue, Wed, Thu, Fri, Sat',
        'Start Time: 09:30 · End Time: 12:30 — click "+" if you also need an evening slot row',
        'Click Submit → slots saved for each selected weekday in the range; row appears in Doctor Slots List',
      ],
      note: 'Wednesday is OT day → after saving, find the Wed slot row in the list and delete it (there is no separate Mark Unavailable action).',
    },
    tips: [
      'Create slots a week ahead so the receptionist can book future appointments.',
      'Use the Range tab + weekday checkboxes to set a whole month\'s recurring slots in one Submit.',
    ],
    warnings: [
      'Don\'t delete a slot that already has appointments — re-assign or cancel those visits first.',
    ],
  },

  'prescription.php': {
    eyebrow: 'Doctor · Clinical',
    title:   'Prescription (General)',
    updated: 'Updated 2026-05-11',
    purpose:
      'Write a prescription for a patient currently in consultation. This is the Cardiology / general-medicine Rx workspace — gynaecology has its own screen.',
    steps: [
      'Search the patient by name, mobile, or unique ID, and pick today\'s appointment row.',
      'Record vitals at the top (BP, pulse, weight, temp). Then add complaints, diagnosis, and notes.',
      'Add medicines one by one — pick the medicine, dosage, frequency, duration, and any instructions. Use Rx Groups to insert a saved template.',
      'Click Submit. The patient is marked as seen; the prescription is now visible to the pharmacist for dispensing.',
    ],
    example: {
      title: 'Writing a hypertension follow-up Rx (consultation by {{vars.doctor1}})',
      steps: [
        'Search "Suresh" → pick today\'s row for Mr. Suresh Kumar (A202605110007)',
        'Vitals: BP 148/92 · Pulse 78 · Weight 76 kg',
        'Complaint: Headache, occasional dizziness · Diagnosis: HTN poorly controlled',
        'Add: Telmisartan 40 mg · 1-0-0 · 30 days; Amlodipine 5 mg · 0-0-1 · 30 days; Aspirin 75 mg · 0-1-0 · 30 days',
        'Click Submit → patient flips to Seen on the queue; pharmacist sees the cart pre-filled',
      ],
    },
    tips: [
      'Use Rx Groups (in the masters menu) to save a frequently-prescribed combination — it cuts entry time dramatically.',
      'The Print button generates a clean PDF you can hand to the patient.',
      'Past visits for the same patient show in the history panel — quick way to refresh memory on chronic cases.',
    ],
    warnings: [
      'Only the doctor whose name is on the appointment can save against it — you cannot write Rx under another doctor\'s name.',
      'Once saved, edits are tracked in the audit log — use the edit window thoughtfully.',
    ],
  },

  'gynaec_prescription.php': {
    eyebrow: 'Doctor · Clinical',
    title:   'Prescription (Gynaec)',
    updated: 'Updated 2026-05-11',
    purpose:
      'The gynaecology-specific prescription workspace. Same flow as the general Rx page but with obstetric and gynaec-specific fields (LMP, EDD, gravida/para, ANC vitals, etc.).',
    steps: [
      'Search the patient and pick today\'s appointment.',
      'Fill the gynaec history block — LMP, cycle, EDD if pregnant — then the visit-specific complaints and findings.',
      'Add medicines, investigations, and advice. Templates are available for common ANC visits.',
      'Click Save Gynaec Prescription — the patient is marked seen and the Rx is queued for the pharmacist.',
    ],
    example: {
      title: 'Routine ANC visit (28 weeks, with {{vars.doctor2}})',
      steps: [
        'Patient: Mrs. Anjali Pillai (A202605110014) · LMP 28 Oct 2025 · EDD 4 Aug 2026',
        'Vitals: BP 110/70 · Wt 64 kg · FHR 144 · Fundal height 28 cm',
        'Complaints: Mild leg swelling, nil bleeding',
        'Add: Tab Iron-Folic Acid 1-0-0 × 30 days; Tab Calcium 0-0-1 × 30 days; Inj. Tdap 0.5 ml IM (single dose)',
        'Investigations: CBC, Urine routine; Advice: walk 30 min/day, return in 2 weeks',
        'Click Save Gynaec Prescription → pharmacist sees cart',
      ],
    },
    tips: [
      'LMP and EDD carry forward across visits — once entered for a pregnancy, you don\'t have to retype them.',
      'Use Gynaec Rx Templates for common visit types (ANC routine, postnatal, infertility follow-up).',
    ],
    warnings: [
      'This screen is gated to gynaecologists only — cardiologists cannot save here even if they reach the URL.',
    ],
  },

  // ===== Pharmacy =====
  'medicine_bill.php': {
    eyebrow: 'Pharmacist · Billing',
    title:   'Medicine Billing',
    updated: 'Updated 2026-05-11',
    purpose:
      'Dispense medicines against a prescription and generate the medicine bill. This is the pharmacist\'s main working screen.',
    steps: [
      'Search the patient by unique ID or mobile. Their latest prescription auto-loads in the cart.',
      'For each medicine, set the quantity actually being dispensed (the Rx is the suggested quantity).',
      'Apply any concession if approved. The system calculates tax, total, and any pending amount.',
      'Pick the payment method (Cash / Card / UPI), then click Print to save the bill and open the printable receipt. Stock is decremented at the same moment.',
    ],
    example: {
      title: 'Dispensing the HTN Rx written by {{vars.doctor1}} earlier in the day',
      steps: [
        'Search 9876543210 → cart auto-loads: Telmisartan 40 mg × 30, Amlodipine 5 mg × 30, Aspirin 75 mg × 30',
        'Aspirin 75 mg is out of stock → set its qty to 0',
        'Concession: Senior Citizen 10% (patient is 67)',
        'Cart total: ₹387 → Payment: UPI · Txn ID: 526112803345 · Click Print',
        'Bill prints, stock for Telmisartan and Amlodipine drops by 30 each, queue marks the patient Done',
      ],
    },
    tips: [
      'If a medicine on the Rx is out of stock, leave its quantity at 0 — the bill records what was actually dispensed, not what was prescribed.',
      'Use the medicine search to add a walk-in OTC item that isn\'t on the Rx.',
      'The cart total updates live — useful for telling the patient the price before they confirm.',
    ],
    warnings: [
      'Stock is decremented at Save — refunds must go through the Refunds page, not by re-saving the bill.',
      'Don\'t edit a saved bill to change the amount — pass refunds/adjustments through the dedicated workflows so the audit trail is clean.',
    ],
  },

  // ===== Doctor's office / Accountant — billing =====
  'bill.php': {
    eyebrow: 'Accountant · Billing',
    title:   'Consultation Billing',
    updated: 'Updated 2026-05-11',
    purpose:
      'Generate the consultation / service bill for a patient — separate from the medicine bill (which the pharmacist runs).',
    steps: [
      'Search the patient and pick today\'s visit.',
      'Add the services rendered (consultation, procedure, lab, etc.). Each one pulls its price from the Services master.',
      'Apply concession if any, pick the payment method, and click Print to save the bill and open the printable receipt.',
    ],
    example: {
      title: 'A consultation + ECG bill for a follow-up patient of {{vars.doctor1}}',
      steps: [
        'Search "Suresh" → pick A202605110007',
        'Add Service: OPD Consultation ({{vars.doctor1}}) ₹500',
        'Add Service: ECG ₹250',
        'Concession: none · Payment: Cash · Amount tendered: ₹750',
        'Click Print → A5 receipt: Subtotal ₹750, GST ₹0, Total ₹750',
      ],
    },
    tips: [
      'Consultation fee auto-populates from the doctor\'s configured fee — change it only when the doctor has approved a different amount.',
      'Use the Combined Bill page if you want consultation + medicine on a single invoice.',
    ],
    warnings: [
      'Bills cannot be deleted — only voided via the Refunds page. Make sure the visit + services are right before saving.',
    ],
  },

  // FIX_B_2343: 'combinedbill.php' and 'billview.php' entries removed — they are
  // print-only popup pages that do not include ajax/footer.php, so the help drawer
  // never loaded there. Per the registry header, print popups are out of scope.

  'refunds.php': {
    eyebrow: 'Accountant · Billing',
    title:   'Refunds & Cancellations',
    updated: 'Updated 2026-05-11',
    purpose:
      'Reverse a paid bill or cancel an appointment that was charged. Creates an auditable refund record — money is not silently removed from the books.',
    steps: [
      // FIX_B_2351: actual page flow — patient + appointment-ID select2 dropdowns,
      // then Search loads each invoice for that visit; per-invoice Cancel (full)
      // or Refund (partial amount) opens a modal with a single Amount field + Reason.
      'Pick the patient (by name, mobile, patient ID, or appointment ID) and click Search. The bills raised for that visit appear as rows.',
      'On the bill you want to reverse: click Cancel (full reversal) or Refund (partial — you enter the amount).',
      'In the modal, enter the Refund Amount (partial only) and a Reason. The reason is required for the audit log.',
      'Click Confirm. The bill is recorded as Cancelled or Refunded and appears in the "Cancelled / Refunded Bills" panel below.',
    ],
    example: {
      title: 'Patient returned an unopened strip of Amlodipine the next day',
      steps: [
        'Pick Mr. Suresh Kumar → his Appointment ID for the visit · Click Search',
        'Yesterday\'s medicine bill loads (Net ₹387) → click Refund on its row',
        'Refund Amount: ₹85 · Reason: "1 strip Amlodipine 5 mg returned unopened, verified by pharmacist"',
        'Click Confirm → the bill now appears under "Cancelled / Refunded Bills" with the Refunded badge',
        'Adjust pharmacy stock manually: +30 tabs Amlodipine on the Medicines page (refunds do not auto-credit stock)',
      ],
      note: 'The Refunded badge is shown for both partial and full refunds — there is no separate "Partially Refunded" label.',
    },
    tips: [
      'Full refunds use the same method the patient paid with — partial refunds can use a different method if the patient prefers.',
    ],
    warnings: [
      'Refunds are not reversible — double-check the amount and the bill before saving.',
      'For dispensed medicines, the stock is NOT auto-credited back — adjust stock manually if the medicines were returned.',
    ],
  },

  'concession.php': {
    eyebrow: 'Accountant · Masters',
    title:   'Concession',
    updated: 'Updated 2026-05-11',
    purpose:
      'Define the discount / concession types available on the billing screens (e.g. Senior Citizen 10%, Staff 50%, Camp Patient 100%).',
    // FIX_B_2351: page has no "Applies to" selector — concessions are global
    // and surface on every bill screen. Fields: Concession Name, Concession
    // Type (Percentage|Amount), Value. Button label: "Save Concession".
    steps: [
      'Fill Concession Name, Concession Type (Percentage or Amount), and Value.',
      'Click Save Concession. It now appears in the concession dropdown on the consultation and pharmacy bill screens.',
    ],
    example: {
      title: 'Adding a "Senior Citizen 10%" concession at {{vars.clinicName}}',
      steps: [
        'Concession Name: Senior Citizen',
        'Concession Type: Percentage · Value: 10',
        'Click Save Concession → accountants and the pharmacist see "Senior Citizen" in the concession dropdown on every bill',
      ],
      note: 'Concessions are global — there is no per-bill-type (consultation vs medicine) toggle on this screen.',
    },
    tips: [
      'Keep the list short — 5-6 well-defined concession types is easier to use than 20 overlapping ones.',
    ],
    warnings: [
      'Changing the % on an existing concession affects only future bills — old bills stay at the rate they were saved with.',
    ],
  },

  // ===== Masters (Admin / SA) =====
  'department.php': {
    eyebrow: 'Admin · Masters',
    title:   'Departments',
    updated: 'Updated 2026-05-11',
    purpose:
      'Manage the clinical departments (specialties) the clinic runs. ' +
      'Every doctor is linked to exactly one department, and that link decides which prescription module they get on login — Cardiology doctors see the general Rx screen, Gynaecology doctors see the gynaec Rx screen.',
    steps: [
      'To add: type a short Department Name (e.g. Pediatrics), a one-line Description, pick the Organization, then click Submit.',
      'To edit: click the pencil icon on the row, update the fields, and click Submit again.',
      'To remove: click the trash icon. The department is hidden from dropdowns but the historical data on past appointments and reports stays intact.',
    ],
    example: {
      title: 'Adding a "Pediatrics" department after a new specialist joins',
      steps: [
        'Department Name: Pediatrics',
        'Description: Children\'s health, vaccinations, growth checks',
        'Organization: {{vars.clinicName}}',
        'Submit → Pediatrics now appears as an option when adding the new doctor on the Doctors page',
      ],
    },
    tips: [
      'Keep names short and standard — they appear on patient receipts, prescriptions, and reports.',
      'The Description is what receptionists see when they pick a doctor — write one line that helps them route the patient correctly.',
      'AR Clinic currently runs with two departments: Cardiology and Gynaecology. Only add more when a new specialist actually joins.',
    ],
    warnings: [
      'Don\'t delete a department that still has active doctors — those doctors lose their specialty gate and end up on the wrong Rx screen. Move the doctor to another department first.',
      'Renaming a department changes the label everywhere instantly, including past records. If the specialty itself is changing, create a new department instead of renaming.',
    ],
  },

  'doctor.php': {
    eyebrow: 'Admin · Masters',
    title:   'Doctors',
    updated: 'Updated 2026-05-11',
    purpose:
      'Add, edit, or deactivate doctors. Each doctor here is the canonical record that appointments, prescriptions, and revenue reports tie back to.',
    steps: [
      'Click Add Doctor to create a new record. Required: Name, Mobile, Email, Department, Specialization, Consultation Fee.',
      'Upload a profile photo if you have one — it appears on the visitor display and the printed prescription header.',
      'To edit, click the pencil on the row. To deactivate, toggle status — the doctor stops appearing in the receptionist\'s doctor dropdown.',
    ],
    example: {
      title: 'Onboarding a visiting pediatrician at {{vars.clinicName}}',
      steps: [
        'Name: Dr. Karthik Naidu · Mobile: 9988776655 · Email: karthik@{{vars.emailDomain}}',
        'Department: Pediatrics · Specialization: PEDIATRICIAN · Consultation Fee: ₹600',
        'Photo: upload karthik-headshot.jpg (square, ~500 KB)',
        'Save → Dr. Karthik appears in the receptionist\'s doctor dropdown immediately and can have time slots generated',
      ],
    },
    tips: [
      'Department + Specialization together drive the Rx-screen gate (Cardiology → general Rx, Gynaecology → gynaec Rx). Pick them carefully.',
      'Consultation Fee here is the default — accountants can override it on individual bills if a discount applies.',
    ],
    warnings: [
      'Don\'t delete a doctor who has any historical visits — deactivate them instead, so their past Rx and bills still link cleanly.',
    ],
  },

  'medicines.php': {
    // FIX_B_2351: drift sweep — page has no Strength / Pack size / GST / Opening
    // Stock / Stock-In / Stock-Out fields. Actual fields: Medicine Type, Brand
    // Name, Composition Name, Unit, Medicine Price, Organization, Note. Button
    // is Submit. There is also an Excel-upload card at the top for bulk import.
    eyebrow: 'Pharmacist · Masters',
    title:   'Medicines',
    updated: 'Updated 2026-05-11',
    purpose:
      'The master catalogue of medicines available at the pharmacy. Every medicine prescribed or sold must exist here first, with its brand name, composition, unit, and price. This is a catalogue master — there is no stock tracking on this page; per-bill quantity is captured at the dispensing step.',
    steps: [
      'For a single entry: fill Medicine Type, Brand Name, Composition Name, Unit, Medicine Price, Organization, and an optional Note. Click "+" on the right if you want to add multiple rows in one Submit.',
      'Click Submit. The medicine is now available on the Rx and pharmacy-billing screens.',
      'For bulk import: use the "Upload Excel File" card at the top — pick an .xlsx with the same columns and Upload.',
      'Use the row\'s edit / delete actions in the list below to change a price or remove an entry.',
    ],
    example: {
      title: 'Adding Telmisartan 40 mg so {{vars.doctor1}} can prescribe it',
      steps: [
        'Medicine Type: Tablet · Brand Name: Telisar 40 · Composition Name: Telmisartan 40 mg',
        'Unit: Tab · Medicine Price: 3.20 · Organization: {{vars.clinicName}}',
        'Note: "first supplier delivery, 200 tabs"',
        'Click Submit → Telisar 40 is now in the Rx dropdown and the pharmacy cart',
      ],
    },
    tips: [
      'Composition (generic) is the searchable field — fill it so doctors can prescribe by generic name and pharmacists can find the matching brand.',
      'Use the Excel upload for the initial pharmacy catalogue load — much faster than one row at a time.',
    ],
    warnings: [
      'Editing a medicine\'s price affects only future bills, not past ones — that is by design.',
      'Don\'t delete a medicine that has been dispensed — its history must remain for refunds and audit.',
    ],
  },

  'dosageandtime.php': {
    eyebrow: 'Doctor · Masters',
    title:   'Dosage & Timing',
    updated: 'Updated 2026-05-11',
    purpose:
      'The master list of dosage strings (e.g. 1-0-1, 1 tab thrice daily, SOS) and timing labels that appear in the medicine row on prescriptions.',
    steps: [
      'Click Add to enter a new dosage label. Keep it short — it has to fit on the printed Rx line.',
      'Use Edit on the row to rename. Status toggle hides it from the Rx dropdown without deleting history.',
    ],
    example: {
      title: 'Adding "1-0-1 (after food)" so {{vars.doctor1}} can pick it directly',
      steps: [
        'Click Add · Label: 1-0-1 (after food)',
        'Save → next time {{vars.doctor1}} writes a Rx, this string is in the dosage dropdown',
      ],
    },
    tips: [
      'A small, well-curated list (10-15 entries) is faster to use than a long one — duplicates frustrate doctors.',
    ],
  },

  'rxgroup.php': {
    eyebrow: 'Doctor · Templates',
    title:   'Rx Groups',
    updated: 'Updated 2026-05-11',
    purpose:
      'Saved bundles of medicines that doctors can insert into a prescription with one click — useful for recurring regimens (e.g. "URI standard", "ANC routine").',
    steps: [
      'Click Add Group, name it, pick the department, and add the medicines with their default dosage/duration.',
      'Save. From the Rx screen, the doctor can now pick this group and the whole bundle pre-fills.',
    ],
    example: {
      title: '"HTN Standard" group for {{vars.doctor1}}',
      steps: [
        'Group Name: HTN Standard · Department: Cardiology',
        'Add: Telmisartan 40 mg · 1-0-0 · 30 days',
        'Add: Amlodipine 5 mg · 0-0-1 · 30 days',
        'Add: Aspirin 75 mg · 0-1-0 · 30 days',
        'Save → on the Rx screen, picking "HTN Standard" pre-fills all three rows; {{vars.doctor1}} just adjusts as needed',
      ],
    },
    tips: [
      'Encourage doctors to maintain their own groups — they speed up Rx writing dramatically for repeat conditions.',
    ],
    warnings: [
      'A group is a starting point — doctors should still review each line before saving the Rx.',
    ],
  },

  'services.php': {
    eyebrow: 'Accountant · Masters',
    title:   'Services',
    updated: 'Updated 2026-05-11',
    purpose:
      'The price list for non-medicine charges — consultation, procedures, lab tests, dressings, etc. Items here appear in the consultation billing dropdown.',
    // FIX_B_2351: form fields are Service Name, Price, GST %, Total Price
    // (auto-calculated, read-only), Organization. There is no Department field.
    steps: [
      'Fill Service Name, Price (pre-tax), and GST %. The Total Price field auto-fills (read-only).',
      'Pick the Organization (SA only) and click Submit. The service now appears on the consultation billing screen.',
    ],
    example: {
      title: 'Adding ECG to {{vars.clinicName}}\'s service list',
      steps: [
        'Service Name: ECG · Price: 250 · GST: 0',
        'Total Price auto-fills to 250',
        'Organization: {{vars.clinicName}} · Click Submit → "ECG" appears in the bill screen\'s service dropdown',
      ],
    },
    tips: [
      'Keep service names patient-friendly — they appear on the printed receipt.',
    ],
    warnings: [
      'Changing a service\'s price affects only future bills, not past ones.',
    ],
  },

  'taxes.php': {
    eyebrow: 'Accountant · Masters',
    title:   'Taxes',
    updated: 'Updated 2026-05-11',
    // FIX_B_2351: form fields are CGST + SGST + Percentage (auto-totalled from
    // the two halves, read-only). Button is Submit, organization picker is shown.
    purpose:
      'Define the GST slabs that services can be tagged with. Each entry has a CGST half and an SGST half — the total Percentage is auto-calculated from the two.',
    steps: [
      'Fill the CGST half (e.g. 6) and the SGST half (e.g. 6). The Percentage field auto-fills with the sum and is read-only.',
      'Pick the Organization (SA only) and click Submit.',
      'On the Services screen, pick this tax row to apply it on consultation / procedure bills.',
    ],
    example: {
      title: 'Setting up a 12% GST slab for procedure services',
      steps: [
        'CGST: 6 · SGST: 6 · Percentage auto-fills to 12 (read-only)',
        'Organization: {{vars.clinicName}}',
        'Click Submit → the 12% slab is now selectable on the Services master',
      ],
    },
    warnings: [
      'Changing a tax % updates future bills only — old bills retain the rate they were saved with.',
    ],
  },

  'billsizes.php': {
    eyebrow: 'Admin · Masters',
    title:   'Print Sizes',
    updated: 'Updated 2026-05-11',
    purpose:
      'Configure the paper size and layout for printed bills and prescriptions (A4, A5, thermal 80mm, etc.).',
    steps: [
      'Pick the layout you want to edit, adjust the margins / header / footer fields, and click Submit.',
      'Print a test bill to confirm the layout fits your printer.',
    ],
    example: {
      title: 'Switching the pharmacy bill to thermal 80 mm',
      steps: [
        'Pick "Pharmacy bill" from the layout list',
        'Width: 80 mm · Top margin: 5 mm · Footer: "Thank you · {{vars.clinicName}}"',
        'Submit → next pharmacy print uses the thermal layout',
        'Print a test bill at the pharmacy counter to confirm it cuts at the right place',
      ],
    },
    tips: [
      'Match the layout to the printer you actually use at reception / pharmacy — wrong size = wasted paper.',
    ],
  },

  'referrals.php': {
    eyebrow: 'Admin · Masters',
    title:   'Referrals',
    updated: 'Updated 2026-05-11',
    purpose:
      'List of referring sources — other doctors, hospitals, camps — that send patients to AR Clinic. Useful for tracking where new patients come from.',
    steps: [
      'Click Add, fill the referrer\'s name, type (Doctor / Camp / Hospital), and contact, and click Submit.',
      'On Registration, the receptionist can tag the new patient with the referral source.',
    ],
    example: {
      title: 'Logging a referring GP who started sending patients to {{vars.doctor1}}',
      steps: [
        'Name: Dr. Vamsi Krishna · Type: Doctor · Contact: 9000111222',
        'Hospital / Clinic: Sai Family Clinic, Madhurawada',
        'Submit → "Dr. Vamsi Krishna" now appears in the referral dropdown on Registration',
      ],
    },
    tips: [
      'Keep this list curated — it drives the referral-source report and helps the clinic decide where to invest in marketing.',
    ],
  },

  'specialization.php': {
    eyebrow: 'Admin · Masters',
    title:   'Specialization',
    updated: 'Updated 2026-05-11',
    purpose:
      'The fine-grained specialty tag assigned to each doctor (e.g. CARDIOLOGIST, INTERVENTIONAL CARDIOLOGIST, OBSTETRICIAN). Distinct from Department — Specialization drives which prescription module the doctor sees.',
    steps: [
      'Click Add, type the specialization name (uppercase, e.g. CARDIOLOGIST), and save.',
      'Open the Doctor master and assign this specialization to the relevant doctor.',
    ],
    example: {
      title: 'Adding PEDIATRICIAN before onboarding the new doctor',
      steps: [
        'Click Add · Specialization Name: PEDIATRICIAN (uppercase)',
        'Save',
        'Open Doctors → edit Dr. Karthik Naidu → set Specialization: PEDIATRICIAN → Save',
        'Dr. Karthik now sees the right Rx workspace on next login',
      ],
    },
    tips: [
      'Use uppercase for consistency — the gate logic compares specialization names exactly.',
      'AR Clinic currently uses CARDIOLOGIST and GYNAECOLOGIST. Add more only when a doctor with a new specialty joins.',
    ],
    warnings: [
      'Renaming a specialization here changes the gate — doctors tagged with the old name will lose access to their Rx screen until you re-tag them.',
    ],
  },

  'testgroup.php': {
    eyebrow: 'Admin · Masters',
    title:   'Test Group',
    updated: 'Updated 2026-05-11',
    purpose:
      'Bundle lab / diagnostic tests into a single named group (e.g. "Full Health Check") so doctors can prescribe the whole set with one click.',
    steps: [
      'Click Add, name the group, pick the tests it contains, save.',
      'On the prescription screen, the doctor can pick this group from the test-suggest dropdown.',
    ],
    example: {
      title: '"Full Health Check" bundle for annual screenings',
      steps: [
        'Group Name: Full Health Check',
        'Add tests: CBC, ESR, FBS, PPBS, HbA1c, Lipid Profile, LFT, RFT, TSH, Urine Routine, ECG',
        'Save → {{vars.doctor1}} or {{vars.doctor2}} can pick "Full Health Check" on Rx and all 11 tests pre-fill',
      ],
    },
  },

  // ===== Patient — view-only screens =====
  'patienthistory.php': {
    eyebrow: 'Doctor / Receptionist · View',
    title:   'Patient History',
    updated: 'Updated 2026-05-11',
    purpose:
      'A read-only timeline of every visit, prescription, bill, and refund for a single patient. Use this to brief yourself before the patient walks in.',
    steps: [
      'Search the patient by unique ID, mobile, or name.',
      'Scroll the timeline — each card is one event (visit, Rx, bill, refund) with a date and a quick summary.',
      'Click any card to open the full detail in a new tab.',
    ],
    example: {
      title: 'Briefing on a chronic patient before {{vars.doctor1}}\'s consultation',
      steps: [
        'Patient walks in at 11:00 — search 9876543210 → Mr. Suresh Kumar',
        'Timeline shows: 6 prior visits, 4 Rx (all HTN regimens), 5 bills (one partially refunded)',
        'Click the most recent Rx card → BP last visit was 152/96, on Telmisartan 40 + Amlodipine 5',
        'Open the Patient History tab in the doctor\'s consult window — full context in 30 seconds',
      ],
    },
    tips: [
      'This is the safest way to brief yourself on a patient — it does not let you accidentally edit anything.',
    ],
  },

  // FIX_B_2343: 'patientview.php' and 'patientprescription.php' entries removed —
  // both are PDF/print pages that do not include ajax/footer.php, so the drawer
  // never loaded. Patient History (patienthistory.php) is the in-chrome view.

  // ===== Org / RBAC / SA =====
  'organization.php': {
    eyebrow: 'Super Admin · Org',
    title:   'Organizations',
    updated: 'Updated 2026-05-11',
    purpose:
      'Manage the organisations (clinics) on the platform. AR Clinic is the single live org today — keep this list lean.',
    // FIX_B_2351: form fields are Organization Name, Contact, Email,
    // Description, GST Number (the GSTIN string — not a percentage), TAN Number,
    // Longitude, Latitude, "Logo And Caption" (image), "Only Logo" (image),
    // User Limit, Address, IP Access. Button: Submit.
    steps: [
      'Fill Organization Name, Contact, Email, Description, GST Number (the 15-character GSTIN), TAN Number, Longitude/Latitude, and Address.',
      'Upload the two logos: "Logo And Caption" (the wide variant with the clinic name) and "Only Logo" (the square mark used on the visitor display).',
      'Set User Limit and IP Access, then click Submit.',
    ],
    example: {
      title: 'Onboarding a sister clinic "KK ENT Clinic"',
      steps: [
        'Organization Name: KK ENT Clinic · Contact: 8912345670 · Email: admin@kkentclinic.local',
        'GST Number: 37ABCDE1234F1Z5 · TAN: HYDK01234E',
        'Address: 3-4-12, Madhurawada, Visakhapatnam · Longitude: 83.3812 · Latitude: 17.7600',
        'Logo And Caption: kkent-wide.png · Only Logo: kkent-mark.png · User Limit: 25',
        'Click Submit → KK ENT exists; platform admin must follow up by creating its doctors, services, roles, and first admin',
      ],
      note: 'GST Number on this screen is the 15-character GSTIN string — the per-bill tax % lives on the Taxes master.',
    },
    warnings: [
      'Adding an org without configuring its doctors, services, and roles will leave it visible-but-empty. Plan a full onboarding before adding.',
      'Deactivating an org hides ALL its data from every screen — only do this when truly winding it down.',
    ],
  },

  'roles.php': {
    eyebrow: 'Super Admin · Access',
    title:   'Roles & Permissions',
    updated: 'Updated 2026-05-11',
    purpose:
      'For each role (Doctor, Receptionist, Pharmacist, Accountant, Admin), control which menus they see and which actions (View / Add / Edit / Delete) they can perform on each menu.',
    steps: [
      'Pick the role on the left.',
      'Expand a parent menu card on the right. For each sub-menu row, tick View / Add / Edit / Delete as appropriate.',
      'Click Save Role in the sticky bar (top or bottom of the page). Changes apply on next login for that role.',
    ],
    example: {
      title: 'Granting Receptionist access to the new TV Ads page',
      steps: [
        'Pick role: Receptionist on the left',
        'Expand the "Appointments" parent card on the right',
        'Find row "TV Ads" → tick View only (receptionist should preview, not upload)',
        'Click Save Role → on her next login, the receptionist sees TV Ads in the sidebar',
      ],
    },
    tips: [
      'View is the gate — without it, none of Add/Edit/Delete works because the menu doesn\'t even show.',
      'Use the search box at the top to jump straight to a menu instead of scrolling.',
    ],
    warnings: [
      'Be careful removing Edit / Delete from Super Admin — you can lock yourself out of role management. Always keep at least one Super Admin account with full access.',
      'These permissions are enforced server-side (requireCan helper) — they are not just UI-hiding, they are real security gates.',
    ],
  },

  'menus.php': {
    eyebrow: 'Super Admin · Access',
    title:   'Menus',
    updated: 'Updated 2026-05-11',
    purpose:
      'The master list of every menu and sub-menu in the application. Roles → Permissions binds permissions to these menus.',
    steps: [
      'Add a new menu only when a new feature is shipped — name, parent, URL, display order, and icon.',
      'Use Edit to rename a menu or change its position in the sidebar.',
    ],
    example: {
      title: 'Registering a newly-built page (e.g. TV Ads) in the sidebar',
      steps: [
        'Click Add Menu',
        'Name: TV Ads · Parent: Appointments · URL: tv_ads.php · Icon: fas fa-tv · Order: 3',
        'Save',
        'Open Roles & Permissions → grant Admin + SA the View/Add/Delete permissions on this row',
      ],
    },
    warnings: [
      'Renaming or removing a menu has system-wide effects — every role\'s permission list references it. Coordinate with the dev team before changing.',
      'Most clinics never touch this page — it is owned by the platform admin.',
    ],
  },

  'user_codes.php': {
    // FIX_B_2351: B-2343 drift sweep missed this page entirely. The page is a
    // READ-ONLY directory of D-/R-/P- codes auto-assigned to active doctors,
    // receptionists, and pharmacists. There is no Add / Edit / Delete /
    // Reset-Password action — those live on User Registration (registration.php).
    eyebrow: 'Admin · Users',
    title:   'User Code Reference',
    updated: 'Updated 2026-05-11',
    purpose:
      'A read-only directory of the short user codes the system has auto-assigned to each active staff account — D-codes for doctors, R-codes for receptionists, P-codes for pharmacists. The codes are what audit-log entries and printed reports refer to; this page is how you find which person a code belongs to.',
    steps: [
      'Read the three summary cards at the top — the total count of doctors, receptionists, and pharmacists with active codes.',
      'Use the Doctors / Receptionists / Pharmacists tables below to map a code to a person — each row shows the user code, the name, contact, and (for receptionists) the doctors they are assigned to.',
      'There is no edit or delete on this page — to create a new account or change a password, use the User Registration screen.',
    ],
    example: {
      title: 'Looking up which receptionist appears as R-003 in an audit log entry',
      steps: [
        'Open the page → three summary cards confirm e.g. 2 D-codes, 3 R-codes, 1 P-code',
        'Scroll to the "Receptionists — User Code Reference" table',
        'Find the row where User Code = R-003 → Name: Priya Reddy · Assigned doctors: {{vars.doctor1}}, {{vars.doctor2}}',
        'Cross-check with the audit-log entry → action attributed to Priya',
      ],
    },
    tips: [
      'Codes are issued automatically when a user is created on User Registration — there is nothing to configure here.',
      'D-codes survive a deactivation; the historical audit log still resolves correctly even after a doctor leaves.',
      'To create or edit a staff login, open Admin → Access Control → User Registration.',
    ],
  },

  'profile.php': {
    eyebrow: 'All users',
    title:   'My Profile',
    updated: 'Updated 2026-05-11',
    purpose:
      'Your personal profile — what you see is tailored to your role. Doctors get a rich bio card (qualifications, expertise, awards, OPD timings, social links). Admins and Super Admins see responsibility cards and quick links to the screens they own. Receptionists, pharmacists and accountants get a compact "Staff" view with photo, password change, and personal details. The breadcrumb reads Doctor / Admin / Super Admin / Staff so you can tell which variant the page picked.',
    steps: [
      'Click the gold camera icon on your photo to upload a new one (JPG / JPEG / PNG, square, under 1.5 MB). The page reloads with the new photo applied.',
      'Personal details (name, email, mobile, user code, role) are read-only here — an admin updates them on the User Registration page.',
      'Use the Change Password block at the bottom to rotate your login password (current → new → confirm, minimum 6 characters).',
      'Doctors only: upload your digital signature (JPG / JPEG / PNG / GIF, recommended 300×100 px, transparent PNG looks cleanest, keep it under ~1 MB) — it appears on printed prescriptions and reports.',
    ],
    example: {
      title: 'First-time setup for {{vars.doctor1}}',
      steps: [
        'Open /profile.php — breadcrumb shows "Doctor", hero renders with photo, specialty pill, tagline "Precision. Strategy. Outcomes.", and affiliation pills (Medicover Hospitals · AR Heart & Women Care)',
        'Snapshot strip shows Affiliations 2 · Expertise areas 6 · Awards 4 (Experience tile appears once years_experience is filled in doctor_profiles)',
        'Click the gold camera icon → choose headshot.jpg → page reloads with the new photo',
        'Scroll to Digital Signature → upload signature.png (300×100, transparent) → posts to ajax/profiles/signature.php, page reloads with the new image inline',
        'Verify on next Rx print: name + signature appear at the bottom',
      ],
    },
    tips: [
      'Doctor variant pulls expertise / awards / education / OPD timings / connect links from the doctor_profiles table — initial values are seeded from each doctor\'s public site (drashwinkumarpanda.com / drramadevi.com). A new doctor without a doctor_profiles row still gets the doctor hero (photo + specialty + name) but no expertise/awards cards.',
      'Admin (Dinesh) variant shows 4 responsibility cards (Operations / Finance / Doctor switcher / Access guardrails) and 5 quick-link buttons (Audit Log · Daily Report · Revenue · Refunds · Doctors).',
      'Super Admin variant shows 4 governance cards (Organisations / Roles & menus / DB health / Audit owner) and 5 quick-link buttons (Organizations · Roles · Menus · Audit Log · Administration).',
      'Staff variant (Receptionist / Pharmacist / Accountant) shows only hero + Personal Details + Change Password — no responsibility cards, no quick links, no signature.',
      'The Digital Signature block only renders for doctors. Signature uploads go to ajax/profiles/signature.php and land in /signature/ as signature_<userId>_<timestamp>.<ext>.',
      'Password change posts to ajax/ChangePassword/ChangePassword.php and uses md5 hashing — same as the rest of H360 until B-017 ships bcrypt.',
    ],
    warnings: [
      'Don\'t use this page to change your name, email, mobile, or role — they\'re read-only here by design. Use User Registration.',
      'Profile photos accept .jpg / .jpeg / .png only; .gif is rejected even though signatures accept it.',
      'Variant detection cannot be spoofed via ?as= — the page picks the variant from your session role and doctors-table linkage, not query string.',
    ],
  },

  'change_passowrd.php': {
    eyebrow: 'All users',
    title:   'Change Password',
    updated: 'Updated 2026-05-11',
    purpose:
      'Change your own login password. Strongly recommended every 90 days.',
    steps: [
      'Type your current password.',
      'Type your new password twice (must be at least 8 characters, mix of letters and digits recommended).',
      'Click Submit. You stay logged in; the next login will use the new password.',
    ],
    example: {
      title: 'Quarterly password rotation for a receptionist',
      steps: [
        'Current Password: ardesk2026q1',
        'New Password: ardesk2026q2 · Confirm: ardesk2026q2',
        'Submit → still logged in; tomorrow\'s login uses the new password',
      ],
    },
    warnings: [
      'If you forget your new password, only an Admin or SA can reset it from the User Codes screen.',
    ],
  },

  // ===== Role dashboards (FIX_B_2370 — drawer-js picks dashboard.<roleKey>.php
  //       first for each viewer; the plain `dashboard.php` below is the fallback) =====

  'dashboard.sa.php': {
    eyebrow: 'Super Admin · Governance dashboard',
    title:   'Governance & Operations Dashboard',
    updated: 'Updated 2026-05-11',
    purpose:
      'Your platform-wide command centre. Six KPI tiles at the top track org, user, audit, doctor, RBAC, and DB-health vitals; cards below stream live audit events, plot the 7-day doctor utilisation heatmap, list recent governance changes, and give you six one-click Quick Actions for the most common SA tasks.',
    steps: [
      'Read the six KPI tiles across the top — Active Orgs, Active Users, Audit · 24h, Active Doctors, Roles · Menus, DB Health. Numbers update on a timer.',
      'Watch the Live Audit Stream on the left — every meaningful action (logins, Rx saves, bill saves, refunds, role changes) appears as it happens.',
      'Scan the Doctor Utilization · 7d heatmap to spot under-booked or over-booked sessions across both doctors.',
      'Use the Recent Governance Changes feed to see who edited what role/menu/org and when.',
      'Use the six Quick Actions cards — Add Organization, Add Role, Audit Log, User Registration, Doctors, Org Reports — for one-click jumps to the SA-only screens.',
    ],
    example: {
      title: 'Morning governance check at {{vars.clinicName}}',
      steps: [
        'Open /dashboard.php — six tiles paint: Orgs 1 · Users 7 · Audit 24h 42 · Doctors 2 · Roles 6 · DB Health green',
        'Live Audit Stream shows 08:55 — Dinesh login · 09:02 — Receptionist booked appointment for {{vars.doctor1}}',
        'Heatmap shows {{vars.doctor1}} averaging 84% slot utilisation last week; {{vars.doctor2}} 58% — note for the partner meeting',
        'Click Quick Action "Audit Log" → opens the full searchable log',
      ],
    },
    tips: [
      'This is the only screen that surfaces DB Health — keep it open during deployments.',
      'The audit feed is append-only; nothing here can be edited from this page.',
      'For per-clinic detail, drill into the Clinic Overview from the sidebar — it focuses on a single org\'s day.',
    ],
    warnings: [
      'SA can preview any role\'s dashboard via ?as=doctor / ?as=admin / ?as=pharmacist / ?as=accountant in the URL. Use it for spot-checks; don\'t leave the impersonation tab open or audit attributions get confusing.',
    ],
  },

  'dashboard.doctor.php': {
    eyebrow: 'Doctor · Clinical console',
    title:   'Doctor Console',
    updated: 'Updated 2026-05-11',
    purpose:
      'Your clinical home screen, scoped to your own work. A mono chrome bar across the top reads "DOCTOR · CONSOLE / Welcome, Dr. ..." with your specialty tag and today\'s date. Below: four KPI tiles (Today\'s Appointments · Next Patient in Queue · Follow-Ups Due Today/Overdue · Active Slots · Week %), a Quick Actions row, then Today\'s Queue, Recent Prescriptions, a specialty-aware panel (Antenatal Follow-ups for gynaec, Cardiology Risk Patients for cardio), and a Last-7-Days mini chart.',
    steps: [
      'Read the four KPI tiles — TODAY (today\'s total), NEXT (the patient currently at the front of your queue, with age + slot time pills), FOLLOW-UPS (due today + overdue), SLOTS · WEEK (active slots and your utilisation %).',
      'Each KPI has its own CTA arrow — Open queue, Open Rx, View queue, Open list, Manage slots — that jumps to the relevant screen.',
      'Use the Quick Actions row underneath the KPIs — Start Consultation, Manage Slots, Patient History, View Reports — for one-click jumps.',
      'Scroll to Today\'s Queue. The next patient to call is at the top; the rows auto-refresh as the receptionist marks Start / Done / Lapsed.',
      'Open Recent Prescriptions to skim what you wrote in the last few visits — useful before a follow-up consult.',
      'Glance at the specialty panel — for gynaecology it lists Antenatal Follow-ups due; for cardiology it lists high-risk patients flagged from recent visits.',
      'Last 7 Days chart shows your visit volume by day — quick eyeball check on a quiet/busy week.',
    ],
    example: {
      title: 'A typical morning for {{vars.doctor1}}',
      steps: [
        'Login at 09:30 → chrome bar greets "DOCTOR · CONSOLE / Welcome, {{vars.doctor1}}"',
        'KPIs: TODAY 6 · NEXT A202605110007 (Mr. Suresh Kumar · 52 yrs · 10:15) · FOLLOW-UPS 3 · SLOTS · WEEK 84% utilised',
        'Click NEXT tile → opens prescription.php pre-loaded for Mr. Suresh Kumar',
        'Specialty panel: 4 Cardiology Risk Patients flagged from last week — note for in-day follow-up calls',
        'Last 7 Days: Mon 12 · Tue 14 · Wed 10 · today (Sun) so far 6 — average week',
      ],
    },
    tips: [
      'You can only see your own queue and prescriptions — the back-end scope helper filters everything to your doctor record.',
      'The NEXT KPI tile pulses softly while a patient is waiting — a visual cue you have someone to see.',
      'Recent Prescriptions card shows the last 10 only — for the full history use Patient History.',
    ],
    warnings: [
      'Marking patients Done is the receptionist\'s job on the Receptionist Queue page — this dashboard is read-only for queue state.',
    ],
  },

  'dashboard.admin.php': {
    eyebrow: 'Admin · Operations dashboard',
    title:   'Operations & Finance Dashboard',
    updated: 'Updated 2026-05-11',
    purpose:
      'Cross-doctor operations & finance command centre. Four KPI tiles (today\'s appointments, today\'s revenue, patients in queue, outstanding bills) sit above three live cards: the next-7-days doctor schedule grid, recent staff activity, and a 30-day revenue chart. Six Quick Actions cover the most common admin jumps. A top-bar doctor switcher lets you narrow every view to one doctor.',
    steps: [
      'Use the doctor switcher in the top bar to pick "All Doctors" or a single doctor — every KPI and card below re-scopes immediately.',
      'Read the four KPI tiles — Today\'s Appointments, Today\'s Revenue, Patients In Queue, Outstanding Bills.',
      'Glance at Doctor Schedule · Next 7 Days for capacity planning across both OP rooms.',
      'Watch Staff Activity for a live feed of receptionist / pharmacist / accountant actions.',
      'Use Quick Actions (Add Staff, Doctor Slots, View Reports, Roles & Access, Refunds, Daily Report) for one-click jumps.',
    ],
    example: {
      title: 'A typical 11 AM check-in for Dinesh',
      steps: [
        'Open /dashboard.php → tiles paint: 32 appointments · ₹6,400 revenue so far · 9 in queue · ₹0 outstanding',
        '{{vars.doctor1}}\'s next 7 days look full Mon-Wed, two open slots Thu morning — flag for confirmation calls',
        'Staff Activity: Receptionist booked 3 appointments in last 5 min, Pharmacist printed 2 bills',
        'Click Quick Action "Daily Report" → opens today\'s cash-and-card close in a new tab',
      ],
    },
    tips: [
      'The doctor switcher persists in your session — switch to {{vars.doctor1}} once and every report you open until logout stays scoped.',
      'The Revenue · Last 30 Days chart is the same data as the Periodic Revenue report at weekly granularity — drill in for the month-on-month picture.',
      'Staff Activity here is a live tail of the same data the SA dashboard\'s Audit Stream shows — handy if you don\'t have audit access.',
    ],
    warnings: [
      'You see everything except governance (org / roles / menus / SA-only screens). To grant a new permission you\'ll need to ask the Super Admin.',
    ],
  },

  'dashboard.pharmacist.php': {
    eyebrow: 'Pharmacist · Billing dashboard',
    title:   'Medicine Billing Desk',
    updated: 'Updated 2026-05-11',
    purpose:
      'Your pharmacy command centre. Four KPI tiles (today\'s bills, today\'s revenue, pending Rx pickups, average bill value), four cards (today\'s bills list, pending-pickup queue, top medicines, payment-method split), three Quick Action cards, and a "Detailed Metrics" panel at the bottom that cross-checks the headline KPIs.',
    steps: [
      'Scan the four KPI tiles to read the day\'s billing pulse — number of bills, total ₹, how many Rx are still waiting, average bill size.',
      'Open TODAY\'S BILLS for the running list of every dispensed bill so far.',
      'Watch PENDING Rx PICKUPS — these are prescribed cards that have not yet been billed; tell the patient when they walk in.',
      'TOP MEDICINES shows what\'s being dispensed most often — use it to spot stock-reorder priorities.',
      'PAYMENT METHODS · TODAY shows cash vs UPI vs card split for end-of-day reconciliation.',
      'Use the three Quick Action cards — Start New Bill (opens medicine_bill.php), Past Bills (searchable history), Medicine Inventory (stock + pricing) — for the most-used jumps.',
      'DETAILED METRICS at the bottom is a four-tile cross-check on the headline numbers — useful if a tile looks off.',
    ],
    example: {
      title: 'A typical 16:00 check before the evening rush',
      steps: [
        'Tiles: Today\'s Bills 18 · Today\'s Revenue ₹4,250 · Pending Rx Pickups 4 · Avg Bill ₹236',
        'PENDING Rx PICKUPS lists 4 patients — receptionist tells each one to collect on their way out',
        'TOP MEDICINES: Telmisartan 40 (12 dispensed today), Amlodipine 5 (9), Aspirin 75 (8) — pharmacist confirms stock above 30 each',
        'PAYMENT METHODS: Cash ₹1,800 · UPI ₹2,150 · Card ₹300 — matches the till manual count',
      ],
    },
    tips: [
      'Keep this dashboard open in a second tab — it auto-refreshes, so you can glance for new pending pickups while you bill.',
      'The Avg Bill Value KPI is a quick health-check — a sudden dip usually means a concession was applied incorrectly.',
      'Top Medicines is a 7-day cumulative count, not just today — that\'s by design for reorder visibility.',
    ],
  },

  'dashboard.accountant.php': {
    eyebrow: 'Accountant · Revenue dashboard',
    title:   'Revenue & Outstandings',
    updated: 'Updated 2026-05-11',
    purpose:
      'Your finance command centre. Four KPI tiles (today\'s revenue, this week\'s revenue, outstanding bills, refunds today), a 30-day stacked revenue trend, today\'s payments-by-method split, the outstanding-bills queue, top revenue sources, a refund tracker (today + this week), the recent audit trail, and Quick Actions.',
    steps: [
      'Read the four KPI tiles — Today\'s Revenue, This Week\'s Revenue, Outstanding Bills, Refunds Today.',
      'Use the Revenue Trend · 30 Days (Stacked) chart to spot weekly seasonality and outlier days.',
      'Scan Today\'s Payments by Method to reconcile against the till at end-of-day.',
      'Work through the Outstanding Bills Queue — each row is a bill not yet fully paid; call the patient or follow up.',
      'Top Revenue Sources (top 5) tells you which services / doctors are driving the most ₹.',
      'Refund Tracker (Today + This Week) flags an unusual spike in refunds early.',
      'Recent Audit Trail at the bottom shows finance-relevant changes (bills voided, refunds added, prices edited).',
      'Quick Actions row offers five jumps — Process Refund · Daily Report · Revenue Report · Billing Report · Audit Log.',
    ],
    example: {
      title: 'End-of-day close',
      steps: [
        'Tiles at 19:30: Today ₹18,400 · Week ₹94,200 · Outstanding ₹2,150 (2 bills) · Refunds Today ₹85',
        'Today\'s Payments: Cash ₹6,800 · UPI ₹9,250 · Card ₹2,350 — matches the till manual count',
        'Outstanding Bills Queue: 2 rows — call both patients tomorrow morning',
        'Refund Tracker: ₹85 (Amlodipine return, audited & explained) — no anomaly',
        'Click Quick Action "Daily Report" → print PDF and file in the binder',
      ],
    },
    tips: [
      'The stacked revenue chart layers consultation + medicine + services — click a band in the legend to isolate one.',
      'Audit Trail here only shows finance-relevant rows; full system audit is on the SA dashboard or audit_log.php.',
      'Refunds Today red-flags at >5% of revenue — that\'s usually a data-entry pattern, not an actual returns spike.',
    ],
    warnings: [
      'Don\'t void bills directly from the Outstanding Bills Queue — route every void through Refunds so the audit trail is clean.',
    ],
  },

  // Generic fallback (the role-keyed entries above win when window.H360_HELP_VARS.roleKey
  // resolves to one of sa/doctor/admin/pharmacist/accountant)
  'dashboard.php': {
    eyebrow: 'All roles · Landing',
    title:   'Dashboard',
    updated: 'Updated 2026-05-11',
    purpose:
      'Your role\'s home screen — what you see right after login. Each role gets a different dashboard tuned to its daily work: SA sees governance + DB health, Doctor sees today\'s queue and patient signals, Pharmacist sees billing and stock, Accountant sees revenue and outstandings, Admin (Dinesh) sees the full cross-role picture with a doctor-switcher.',
    steps: [
      'Read the KPI tiles across the top — they are clickable and jump to the underlying report or workspace.',
      'Below the tiles, scan the activity / queue / chart cards relevant to your role.',
      'Use the actions on each card (View, Open, Refresh) to drill into details.',
    ],
    example: {
      title: 'Admin Dinesh\'s morning briefing at {{vars.clinicName}}',
      steps: [
        '08:55 — Dinesh logs in and lands on the Admin dashboard',
        'Top tiles: 14 appointments today · ₹0 revenue (clinic not open) · 2 low-stock alerts',
        'Doctor-switcher in top bar: All Doctors · {{vars.doctor1}} · {{vars.doctor2}}',
        'Click "Low stock" tile → jumps to Medicines page filtered to items below threshold',
        'Pick {{vars.doctor1}} from switcher → tiles narrow to his side of the clinic only',
      ],
    },
    tips: [
      'Tiles update on a timer — no need to refresh the page; numbers stay fresh.',
      'If a tile shows "0" all day, check the date filter (admin / SA dashboards have one) or your doctor-scope (admin only).',
      'Super Admin can preview any role\'s dashboard via ?as=doctor / ?as=admin / ?as=pharmacist / ?as=accountant in the URL.',
    ],
  },

  // ===== Cross-clinic overview dashboards =====
  'clinicdashboard.php': {
    eyebrow: 'Super Admin · Overview',
    title:   'Clinic Overview',
    updated: 'Updated 2026-05-11',
    purpose:
      'A consolidated read-only view of the clinic\'s day — appointments, revenue, patient flow, and pharmacy activity in a single screen. Mainly used by the Super Admin and Admin for at-a-glance health checks.',
    steps: [
      'Pick the date (defaults to today) and the doctor filter if you want to scope to one provider.',
      'Scan the KPI tiles — they cover registrations, completed visits, revenue, refunds, and stock alerts.',
      'Drill into any KPI by clicking it; it opens the underlying report in a new tab.',
    ],
    example: {
      title: 'End-of-day health check for {{vars.clinicName}}',
      steps: [
        'Date: today · Doctor filter: All',
        'KPIs: 32 registrations · 28 completed · ₹18,400 revenue · 1 refund (₹85)',
        'Drill into "Revenue" → opens Revenue Report scoped to today',
        'Drill into "Refund" → opens the single refund row to confirm reason matches notes',
      ],
    },
    tips: [
      'Use this as your morning briefing — open it once before the clinic opens.',
      'All numbers here come from the same source as the detailed reports — there is no separate ledger.',
    ],
  },

  'orgdashboard.php': {
    eyebrow: 'Super Admin · Overview',
    title:   'Organisation Dashboard',
    updated: 'Updated 2026-05-11',
    purpose:
      'Roll-up view across every organisation on the platform — useful when more than one clinic is configured. Today AR Clinic is the only live org, so this view mostly mirrors the clinic dashboard.',
    steps: [
      'Pick the org from the filter at the top (or leave All Orgs to compare).',
      'Scan the cross-org KPI tiles — registrations, revenue, refunds, by org.',
    ],
    example: {
      title: 'Comparing two clinics once a sister org is onboarded',
      steps: [
        'Org filter: All Orgs',
        'Tiles split per org: {{vars.clinicName}} 32 visits / ₹18,400 · KK ENT 18 visits / ₹9,200',
        'Notice {{vars.clinicName}} is doing 2× volume — drill in to see if KK ENT needs more slots or marketing',
      ],
    },
    tips: [
      'On a single-clinic deploy this view is informational — the Clinic Overview is the one to keep open during the day.',
    ],
  },

  // ===== Audit / Admin tools =====
  'audit_log.php': {
    eyebrow: 'Admin · Audit',
    title:   'Audit Log',
    updated: 'Updated 2026-05-11',
    purpose:
      'A searchable record of every meaningful action in the system — logins, patient creates/edits, Rx saves, bill saves, refunds, role changes. Read-only.',
    // FIX_B_2351: actual filters are Date Range, Module, Action (enum:
    // Create/Update/Delete/Login/Logout), and Select Doctor (only shown to a
    // receptionist with >1 assigned doctor). There is no "bill.delete"-style
    // dotted action; module + action are separate columns. Row click opens a
    // Change Details modal with the before/after JSON diff.
    steps: [
      'Pick the Date Range, optionally narrow by Module (one of the modules the system logs against, e.g. Appointments, Prescriptions, Security) and by Action (Create / Update / Delete / Login / Logout).',
      'Click Apply to load the table; Export CSV downloads the current view.',
      'Click a row to open the Change Details modal — full before/after JSON of what changed.',
    ],
    example: {
      title: 'Investigating a missing bill in yesterday\'s daily close',
      steps: [
        'Daily Billing Report shows ₹400 missing vs the till count',
        'Open Audit Log · Date Range: yesterday · Module: leave All · Action: Delete · Apply',
        'Spot a delete row in the Appointments module at 18:42 by an admin → click it',
        'The Change Details modal shows before_json with the deleted invoice (₹400) and the reason logged',
        'That explains the ₹400 — case closed',
      ],
    },
    tips: [
      'This is your first stop when a number on a report looks wrong — find the event, see who did what and when.',
    ],
    warnings: [
      'The audit log is append-only — entries cannot be edited or deleted by anyone, by design.',
    ],
  },

  // ===== Reports (read-only analytics) =====
  'allpatients.php': {
    eyebrow: 'Reports · Patients',
    title:   'All Patients',
    updated: 'Updated 2026-05-11',
    purpose:
      'The master directory of every patient ever registered at the clinic. Search, filter, and export the full list.',
    steps: [
      'Use the search box to find a patient by name, mobile, or unique ID.',
      'Use the column filters (gender, city, date range) to narrow the list.',
      'Click Export to download the current view as Excel / PDF.',
    ],
    example: {
      title: 'Pulling a recall list for {{vars.doctor2}}\'s ANC patients',
      steps: [
        'Filter: Sex = F · Last visit between 01 Jan and 30 Apr 2026 · Doctor = {{vars.doctor2}}',
        'Result: 47 patients',
        'Click Export → Excel with names + mobiles for the front-desk to make follow-up calls',
      ],
    },
    tips: [
      'This is the right place to pull a marketing or recall list — never edit patient details from here, use Registration for that.',
    ],
  },

  'appointmentreports.php': {
    eyebrow: 'Reports · Appointments',
    title:   'Appointment Report',
    updated: 'Updated 2026-05-11',
    purpose:
      'Every appointment in a chosen date range — who, when, with which doctor, status (Done / Cancelled / No-Show), and the booking type.',
    steps: [
      'Pick the From and To dates, and optionally filter by doctor or status.',
      'Click Search to load the table.',
      'Click Export to download as Excel / PDF.',
    ],
    example: {
      title: 'Last week\'s no-show audit for {{vars.doctor1}}',
      steps: [
        'From: 04 May 2026 · To: 10 May 2026 · Doctor: {{vars.doctor1}} · Status: No-Show',
        'Search → 6 rows',
        'Export to PDF · share with admin for follow-up calls',
      ],
    },
    tips: [
      'Use this to reconcile the day\'s schedule against what actually happened.',
    ],
  },

  'billing_report.php': {
    eyebrow: 'Reports · Billing',
    title:   'Billing Report',
    updated: 'Updated 2026-05-11',
    purpose:
      'Every bill raised in a date range — bill number, patient, items, payment method, total, and refund status.',
    steps: [
      'Pick the From and To dates, optionally filter by doctor or payment method.',
      'Click Search, then Export for Excel / PDF.',
    ],
    example: {
      title: 'Month-end billing snapshot for the accountant',
      steps: [
        'From: 01 May 2026 · To: 31 May 2026 · Payment method: All',
        'Search → 612 bills · ₹4,87,650 gross · 11 refunds · ₹2,340 refunded',
        'Export Excel → reconcile against the bank statement',
      ],
    },
    tips: [
      'Cross-check this against the Revenue Report — totals should match. If they don\'t, look in the audit log for edits.',
    ],
  },

  'dailyreports.php': {
    eyebrow: 'Reports · Billing',
    title:   'Daily Billing Report',
    updated: 'Updated 2026-05-11',
    purpose:
      'A one-day cash-and-card close for the clinic — every bill, every refund, totalled by payment method. Use it at end-of-day to balance the till.',
    steps: [
      'Pick the date (defaults to today).',
      'Review the totals by payment method (Cash / Card / UPI). Each row links to the underlying bill.',
      'Export to PDF if you want a printed end-of-day sheet.',
    ],
    example: {
      title: 'Closing the till at 19:30',
      steps: [
        'Date: today',
        'Totals: Cash ₹6,800 · UPI ₹9,250 · Card ₹2,350 · Refunds ₹85 (cash)',
        'Cash drawer count: ₹6,715 — matches (₹6,800 in - ₹85 refund)',
        'Print PDF → file in the daily binder',
      ],
    },
    tips: [
      'Run this every evening before locking the cash box — it is the fastest way to spot a missed bill or an unrecorded refund.',
    ],
  },

  'echo_report.php': {
    eyebrow: 'Reports · Cardiology',
    title:   '2D Echo Report',
    updated: 'Updated 2026-05-11',
    purpose:
      'All 2D Echo investigations recorded for cardiology patients — date, patient, doctor, findings summary, and any uploaded report file.',
    steps: [
      'Filter by date range and/or patient.',
      'Click a row to view the full report; use the file link to download the uploaded PDF/image if attached.',
    ],
    example: {
      title: 'Pulling Mr. Suresh Kumar\'s last echo before today\'s {{vars.doctor1}} visit',
      steps: [
        'Filter: Patient = "Suresh Kumar"',
        'Result: 3 prior echoes — 2024-08, 2025-03, 2025-11',
        'Click 2025-11 row → findings: EF 52%, mild LVH, mitral regurgitation grade 1',
        'Download the PDF → open in {{vars.doctor1}}\'s consult window',
      ],
    },
    tips: [
      'Useful for follow-up visits — pulls the patient\'s last echo without going back to Patient History.',
    ],
  },

  'noshowcancellationreport.php': {
    eyebrow: 'Reports · Operations',
    title:   'No-Show & Cancellation Rate',
    updated: 'Updated 2026-05-11',
    purpose:
      'How often booked appointments are missed or cancelled, by doctor and time slot. A high rate flags scheduling or reminder problems.',
    steps: [
      'Pick the date range and doctor filter.',
      'Scan the rate KPIs at the top, then the slot-by-slot breakdown below.',
      'Click any slot to see the actual list of no-shows / cancellations.',
    ],
    example: {
      title: 'Investigating high no-shows in {{vars.doctor1}}\'s 9 AM slots',
      steps: [
        'Range: 01 Apr → 30 Apr · Doctor: {{vars.doctor1}}',
        'KPIs: overall no-show rate 12% · 9:00–9:30 slot rate 41% (red)',
        'Click the 9:00–9:30 row → 14 of 34 booked patients no-showed',
        'Action: receptionist starts confirming early-morning bookings the day before',
      ],
    },
    tips: [
      'A persistent no-show pattern at a specific slot usually means the slot is being overbooked or the doctor is consistently late.',
    ],
  },

  'op_lab_util.php': {
    eyebrow: 'Reports · OP Analytics',
    title:   'OP Lab Test Utilisation',
    updated: 'Updated 2026-05-11',
    purpose:
      'Which lab / diagnostic tests are being ordered, in what volume, by which doctor. Helps spot over-ordering and high-revenue tests.',
    steps: [
      'Pick the date range and (optionally) the doctor.',
      'The top KPI tiles show total tests, unique tests, and the most-ordered test.',
      'The breakdown table sorts tests by volume; click to drill into individual orders.',
    ],
    example: {
      title: 'Q1 lab utilisation cross-doctor at {{vars.clinicName}}',
      steps: [
        'Range: 01 Jan → 31 Mar 2026 · Doctor: All',
        'KPIs: 412 tests · 27 unique · top: ECG (108)',
        'Drill into ECG → 92 ordered by {{vars.doctor1}}, 16 by {{vars.doctor2}}',
        'Insight: cardiology drives most ECGs (expected); revenue line worth promoting',
      ],
    },
    tips: [
      'Pair this with the Revenue report to find the highest-margin diagnostic services.',
    ],
  },

  'op_patient_outcome.php': {
    eyebrow: 'Reports · OP Analytics',
    title:   'OP Patient Outcomes & Follow-Up',
    updated: 'Updated 2026-05-11',
    purpose:
      'Patient-level outcome roll-up — improved, declined, defaulted (no follow-up), against the doctor\'s recorded plan. A coarse quality-of-care indicator.',
    steps: [
      'Pick the date range and doctor filter.',
      'Review the Total / Improved / Declined / Defaulted tiles.',
      'Click any bucket to see the patient list and drill into individual cases.',
    ],
    example: {
      title: 'Building a follow-up call list for {{vars.doctor2}}\'s ANC defaulters',
      steps: [
        'Range: last 60 days · Doctor: {{vars.doctor2}}',
        'Tiles: 84 total · 62 Improved · 8 Declined · 14 Defaulted',
        'Click "Defaulted" → 14 patients, mostly ANC visits past their next-due date',
        'Export → receptionist makes confirmation calls before next week',
      ],
    },
    tips: [
      'The "Defaulted" bucket is a follow-up call list — patients who were due back but never returned.',
    ],
  },

  'op_revenue.php': {
    eyebrow: 'Reports · OP Analytics',
    title:   'OP Revenue & Invoices',
    updated: 'Updated 2026-05-11',
    purpose:
      'OP revenue cut by service, doctor, and date — gross, discounts, tax, and net. The headline number for monthly P&L conversations.',
    steps: [
      'Pick the From / To dates and (optional) doctor filter.',
      'Scan the Gross / Discounts / Tax / Net tiles at the top.',
      'Use the table below for line-by-line drill.',
    ],
    example: {
      title: 'April P&L review for {{vars.clinicName}}',
      steps: [
        'Range: 01 Apr → 30 Apr 2026 · Doctor: All',
        'Tiles: Gross ₹4,98,400 · Discounts ₹14,200 · Tax ₹6,800 · Net ₹4,77,400',
        'Drill: {{vars.doctor1}} ₹3,12,000 · {{vars.doctor2}} ₹1,86,400',
        'Spot 22-Apr outlier (₹0 revenue) → check audit log; clinic was closed for the festival',
      ],
    },
    tips: [
      'Net here = Gross - Discounts - Tax. If the number looks off, check the Daily Billing report for that day to find the outlier bill.',
    ],
  },

  'op_rx_patterns.php': {
    eyebrow: 'Reports · OP Analytics',
    title:   'OP Diagnosis & Prescription Patterns',
    updated: 'Updated 2026-05-11',
    purpose:
      'What doctors are diagnosing and prescribing most often. Useful for clinical audit, formulary planning, and stocking decisions in the pharmacy.',
    steps: [
      'Pick the date range and doctor filter.',
      'Top KPI tiles: Total scripts, Unique drugs, Top diagnosis.',
      'Tables below break out diagnosis frequency and drug frequency — sort by volume.',
    ],
    example: {
      title: 'Pharmacy stocking review against {{vars.doctor1}}\'s March prescribing',
      steps: [
        'Range: 01 Mar → 31 Mar 2026 · Doctor: {{vars.doctor1}}',
        'KPIs: 218 scripts · 47 unique drugs · top diagnosis: HTN (102)',
        'Top 5 drugs: Telmisartan 40 · Amlodipine 5 · Aspirin 75 · Atorvastatin 10 · Metoprolol 50',
        'Pharmacy reorders top 5 in bulk → reduces stock-outs',
      ],
    },
    tips: [
      'Cross-reference the most-prescribed drugs against pharmacy stock — that\'s your reorder priority list.',
    ],
  },

  'opappointmentsreport.php': {
    eyebrow: 'Reports · Operations',
    title:   'OP Appointments Volume & Utilisation',
    updated: 'Updated 2026-05-11',
    purpose:
      'How many appointment slots existed, how many were booked, and how many were attended — per doctor, per day. The slot-utilisation report.',
    steps: [
      'Pick the date range and (optional) doctor.',
      'Read the utilisation % per day / per doctor — anything under 60% is room to grow; anything at 100% is room to add slots.',
    ],
    example: {
      title: 'Quarterly slot utilisation for both doctors at {{vars.clinicName}}',
      steps: [
        'Range: 01 Jan → 31 Mar 2026',
        '{{vars.doctor1}} avg utilisation: 84% · busiest day Mon (101%)',
        '{{vars.doctor2}} avg utilisation: 58% · slow days Wed/Thu (32%)',
        'Action: shift one of {{vars.doctor2}}\'s Wed slots to a popular morning window',
      ],
    },
    tips: [
      'Low utilisation can mean low demand OR the slots aren\'t being made visible to patients — investigate before adding capacity.',
    ],
  },

  'org_reports.php': {
    eyebrow: 'Super Admin · Reports',
    title:   'Organisation Reports',
    updated: 'Updated 2026-05-11',
    purpose:
      'Cross-organisation roll-up of revenue, patients, and activity. Only meaningful when more than one org is configured — today AR Clinic is the single live org.',
    steps: [
      'Pick the date range and optionally a specific organisation.',
      'Read the totals; export if you need to share externally.',
    ],
    example: {
      title: 'Quarterly board pack — both clinics',
      steps: [
        'Range: 01 Jan → 31 Mar 2026 · Org: All',
        '{{vars.clinicName}} 2,140 patients · ₹19.4 L revenue',
        'KK ENT Clinic 980 patients · ₹8.6 L revenue',
        'Export PDF → share with the board',
      ],
    },
    tips: [
      'On a single-clinic deploy this mirrors the regular reports. It becomes useful once a second clinic is onboarded.',
    ],
  },

  'patientreporthistory.php': {
    eyebrow: 'Reports · Patient',
    title:   'Patient Report History',
    updated: 'Updated 2026-05-11',
    purpose:
      'Per-patient timeline of every uploaded investigation / test report — lab files, imaging, echo, etc. Read-only.',
    steps: [
      'Search the patient by unique ID, mobile, or name.',
      'Each row is one uploaded report — click to download / preview.',
    ],
    example: {
      title: 'Pulling Mrs. Anjali Pillai\'s ANC investigations before today\'s visit',
      steps: [
        'Search "Anjali Pillai" → 11 uploaded files over 6 visits',
        'Most recent: 18-Apr-2026 anomaly scan PDF · 09-Apr-2026 CBC + urine routine',
        'Download both → {{vars.doctor2}} reviews them in the consult window',
      ],
    },
    tips: [
      'Useful before a follow-up consultation — the doctor can pull all past investigations without leaving this screen.',
    ],
  },

  'patientwaitingreport.php': {
    eyebrow: 'Reports · Operations',
    title:   'Patient Waiting Time',
    updated: 'Updated 2026-05-11',
    purpose:
      'Average time from patient registration to consultation, by doctor and day. A patient-experience indicator.',
    steps: [
      'Pick the date range and doctor filter.',
      'Read the average and 95th-percentile wait times.',
      'Drill into a high-waiting day to see which appointments slipped.',
    ],
    example: {
      title: 'Investigating a string of patient complaints about long waits',
      steps: [
        'Range: last 14 days · Doctor: {{vars.doctor1}}',
        'Avg wait 24 min, 95th percentile 71 min',
        'Drill 06-May (worst day) → 18 patients booked into the 10:30 slot due to a configuration mistake',
        'Action: cap slot capacity at 1 again on the Time Slots page',
      ],
    },
    tips: [
      'Waits > 30 minutes on average usually flag overbooking — pair with the Appointment Volume report to confirm.',
    ],
  },

  'periodicrevenue.php': {
    eyebrow: 'Reports · Billing',
    title:   'Periodic Revenue',
    updated: 'Updated 2026-05-11',
    purpose:
      'Revenue summary by week / month / quarter — useful for trend tracking rather than per-day analysis.',
    steps: [
      'Pick the granularity (weekly / monthly / quarterly) and the date window.',
      'Read the trend chart and the underlying table; export for accounting.',
    ],
    example: {
      title: 'Tracking quarter-on-quarter growth at {{vars.clinicName}}',
      steps: [
        'Granularity: Quarterly · Window: last 4 quarters',
        'Q2 2025 ₹14.2 L · Q3 ₹15.6 L · Q4 ₹17.9 L · Q1 2026 ₹19.4 L',
        'Trend chart: steady ~9% q/q growth — share with the partners',
      ],
    },
    tips: [
      'Pair the monthly view with the OP Revenue report for the per-day breakdown of any outlier month.',
    ],
  },

  'prescriptionreports.php': {
    eyebrow: 'Reports · Clinical',
    title:   'Prescription Report',
    updated: 'Updated 2026-05-11',
    purpose:
      'Every prescription written in a chosen date range — patient, doctor, diagnosis, medicines, and follow-up date.',
    steps: [
      'Pick the date range and (optional) doctor filter.',
      'Use Export for Excel / PDF.',
    ],
    example: {
      title: 'Monthly clinical audit on {{vars.doctor1}}\'s Rx',
      steps: [
        'Range: 01 Apr → 30 Apr · Doctor: {{vars.doctor1}}',
        'Result: 184 Rx · 47 unique drugs · 18 follow-ups due in May',
        'Export to PDF for the clinical audit meeting',
      ],
    },
    tips: [
      'Useful for clinical audit and for the pharmacy to reconcile what was prescribed vs. dispensed.',
    ],
  },

  'revenuereport.php': {
    eyebrow: 'Reports · Billing',
    title:   'Revenue by Service / Doctor / Date',
    updated: 'Updated 2026-05-11',
    purpose:
      'Revenue cut three ways — by service line, by doctor, and by date. The most detailed revenue breakdown the system offers.',
    steps: [
      'Pick the date range and the cut you want (service / doctor / date).',
      'The table below is sortable on any column.',
      'Export the slice you need.',
    ],
    example: {
      title: 'Monthly P&L meeting prep — three cuts of April',
      steps: [
        'Range: 01 Apr → 30 Apr 2026',
        'Cut by Doctor: {{vars.doctor1}} ₹3,12,000 · {{vars.doctor2}} ₹1,86,400',
        'Cut by Service: Consultation ₹2,90,000 · ECG ₹68,000 · Procedures ₹1,40,400',
        'Cut by Date: see daily trend — 18-Apr peak ₹38,400, 22-Apr nil (festival closure)',
        'Export each cut → reconcile against Daily Billing report',
      ],
    },
    tips: [
      'For a clean P&L conversation, run all three cuts for the same window and reconcile against Daily Billing.',
    ],
  },

  'testreport.php': {
    eyebrow: 'Reports · Patient',
    title:   'Uploaded Test Reports',
    updated: 'Updated 2026-05-11',
    purpose:
      'The repository of investigation files uploaded against patients — labs, imaging, echo PDFs. Browse, search, and download.',
    steps: [
      'Search by patient or date range.',
      'Click a row to preview / download the uploaded file.',
    ],
    example: {
      title: 'Pulling all anomaly-scan PDFs uploaded last week',
      steps: [
        'Range: 04 May → 10 May 2026',
        'Filter file type: PDF',
        'Result: 6 scans uploaded against {{vars.doctor2}}\'s ANC patients',
        'Download all → backup to the clinic NAS',
      ],
    },
    tips: [
      'This is read-only — to add a new uploaded file, use the prescription screen during a visit; the upload box is on the Rx form.',
    ],
  },

  'databasetruncate.php': {
    eyebrow: 'Super Admin · Danger zone',
    title:   'Administration',
    updated: 'Updated 2026-05-11',
    purpose:
      'A platform-admin tools screen with two distinct sections: (1) seed-data utilities that pre-populate IP, rooms, services, packages, schemes, consumables and similar reference tables, and (2) a Truncate Table function that wipes a chosen table. Used during initial setup and very rarely thereafter.',
    steps: [
      'For seed-data: click the relevant Add card (Add Basic IP Data, Add Rooms, Add Medical Services, Add Schemes, Add Consumables Stock, etc.) — each is idempotent on the seeded set.',
      'For truncate: pick the table from the dropdown, read the warning carefully, take a database backup, then confirm. The table is emptied (not dropped).',
      'All actions are logged to the audit trail.',
    ],
    example: {
      title: 'Wiping a sandbox-only test table during initial setup',
      steps: [
        'Take a fresh DB backup (mysqldump h360_op > pre-truncate-backup.sql)',
        'Pick the table: scratch_seed_test',
        'Read the on-screen warning, type CONFIRM if prompted',
        'Confirm → table emptied; row count drops to 0; entry written to audit log',
      ],
      note: 'NEVER do this on patients, appointments, prescriptions, bills, refunds, or audit tables — those are protected by the constitutional No-Destructive-DB rule.',
    },
    warnings: [
      'Truncate is destructive and irreversible. Never run it on patients, appointments, prescriptions, bills, refunds, or any audit table — those are protected by the No-Destructive-DB rule.',
      'Re-running a seed Add card on a populated table can create duplicates. Use only on a fresh deploy unless you have audited the target table first.',
      'If you are not sure what an option does, stop and ask the platform admin first.',
    ],
  },

};
