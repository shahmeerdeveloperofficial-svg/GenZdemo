# Admin tabs: presentation guide

Updated: 20 October 2026  
Project: local Laravel admin at `http://127.0.0.1:8000`  
Database: `demo`

## Health check

- Signed in with the local Admin account and opened all **42 admin sidebar links**. Every page returned HTTP 200 after database setup.
- Admissions returned HTTP 503 before the missing schema migrations were applied. All pending migrations are now applied to the `demo` database, and Admissions opens normally.
- No tab currently returns HTTP 500, so no tab was hidden.
- This checks that each page opens. It does not certify every form submission, import, payment, email, or third-party integration.
- Demo records have now been added for the main presentation flows. Current sample counts include 16 users, 5 roles, 8 CRM leads, 3 consultation requests, 3 appointments, 3 universities, 3 academic programs, 4 admission applications, 8 courses, 20 enrollments, 1 quiz, 1 assignment, 1 live session, 9 services, 1 service order/invoice, 3 events, 3 FAQs, 2 website enquiries, 3 subjects, and 1 student profile/document.
- Transactions, payments, and withdrawals remain empty intentionally: the demo does not claim that real money moved.

## Presentation talk track

### Workspace and CRM

| Tab | Kaam | Presentation line |
|---|---|---|
| Dashboard | Quick overview of the records and work that need attention. | “Yahan se team ko poore operation ka ek nazar mein status milta hai.” |
| Lead CRM | Capture and manage prospective students, assign owners, track progress, notes and follow-ups. | “Har enquiry ka owner aur agla follow-up clear rehta hai, taake koi lead miss na ho.” |
| Consultation Requests | Review people who requested a free consultation; add requests or import them. | “Website ya team se aane wali consultation requests ek inbox mein jama hoti hain.” |
| Availability slots | Set when consultants can take appointments. | “Consultant apni availability set karta hai, phir booking usi schedule ke mutabiq hoti hai.” |
| Meeting platforms | Maintain the meeting options consultants can use. | “Team meeting ka tareeqa yahan manage karti hai, booking ke waqt sahi option select karne ke liye.” |
| Consultants | View and manage consultant accounts and profiles. | “Yahan se hum dekhte hain ke consultation kaun provide kar raha hai.” |
| Appointments | Review booked consultations and their status. | “Request se confirmed appointment tak consultation ka schedule track hota hai.” |
| Consultation orders | Review paid consultation orders. | “Consultation service ki order history aur payment status yahan follow hoti hai.” |
| Reviews | Review feedback submitted after consultations. | “Client feedback team ko service quality samajhne aur improve karne mein madad deta hai.” |
| Student Directory | Search and manage student accounts and records. | “Enquiry convert hone ke baad student ka record yahan se manage hota hai.” |
| Student Documents | Review students’ uploaded documents and their review status. | “Sensitive documents ko controlled access ke saath review aur track kiya jata hai.” |

### Study abroad setup and applications

| Tab | Kaam | Presentation line |
|---|---|---|
| Destinations | Maintain country and destination records. | “Counsellors ke paas study destinations ki ek central list hoti hai.” |
| Study Levels | Maintain study-level options used across forms and records. | “Forms mein study options consistent rakhne ke liye levels yahan manage hote hain.” |
| Universities | Maintain university profiles. | “University information ek jagah maintain hoti hai, jise counsellors student planning mein use karte hain.” |
| Academic Programs & Intakes | Manage programs linked to universities and their intake dates. | “Student application ke liye program aur intake ko university ke saath connect kiya jata hai.” |
| Scholarships | Maintain scholarship records and eligibility information. | “Team scholarship options aur unki eligibility ko student ke liye review kar sakti hai.” |
| Subject Categories | Organize subjects into categories. | “Categories se subject catalogue ko browse karna aur maintain karna asaan hota hai.” |
| Subjects | Maintain subject records used by the catalogue. | “Subjects catalogue ka bunyadi hissa hain jo student ke academic interests ko organize karta hai.” |
| Admissions | Track university applications, status changes, counselors, notes, tasks and visa progress. | “Application submit hone ke baad har stage aur zimmedar counselor ka record yahan rehta hai.” |

### Learning, services and events

| Tab | Kaam | Presentation line |
|---|---|---|
| Course Tracks | Organize courses into learning tracks/programs. | “Tracks courses ko ek learning path mein arrange karte hain.” |
| Courses | Create and manage courses and their publishing status. | “Team course ko prepare, organize aur learners ke liye publish kar sakti hai.” |
| Course Orders | Review course purchases and orders. | “Course purchase ka order record yahan se follow hota hai.” |
| Manage Services | Maintain the consultancy services offered by the business. | “Yahan company ki service catalogue aur uski details manage hoti hain.” |
| Service Orders & Tasks | Track service work, assignments, notes and task progress. | “Har service order ke kaam, owner aur progress ko ek workflow mein follow kiya jata hai.” |
| Service Invoices | Create and manage invoices for service orders. | “Service work ke billing records aur invoice status yahan maintain hote hain.” |
| Manage Events | Create and manage events. | “Team events ko publish aur unki information maintain kar sakti hai.” |
| Event Orders | Review event-related orders. | “Event ke saath aane wale orders ka record yahan milta hai.” |

### Finance, reports and team

| Tab | Kaam | Presentation line |
|---|---|---|
| Transactions | Review recorded transactions. | “Yahan recorded financial transactions ko ek jagah review kiya jata hai.” |
| Affiliates | Manage affiliate accounts and referral activity. | “Affiliate referrals ko track karke partner activity samajhna asaan hota hai.” |
| Affiliate Payouts | Review affiliate payout records and statuses. | “Commission payout ka record aur uski progress yahan visible rehti hai.” |
| Analytics & Reports | Review date-filtered operational summaries. | “Reports team ko trends dekhne mein madad deti hain; yeh audited financial statements ka badal nahi.” |
| Staff Directory | Manage staff accounts. | “Team members aur unke accounts yahan se manage hote hain.” |
| Roles & Permissions | Configure staff roles and their allowed actions. | “Har role ko sirf zaroori access de kar team ka kaam secure rakha jata hai.” |

### Website, system and account

| Tab | Kaam | Presentation line |
|---|---|---|
| Landing Page | Manage sections shown on the public landing page. | “Website ka main page content admin panel se update kiya ja sakta hai.” |
| About Us | Manage public About Us content. | “Company ki public introduction aur related content yahan maintain hota hai.” |
| Blogs & News | Manage blog posts and related content. | “Team website par publish hone wali updates aur articles yahan manage karti hai.” |
| SEO Settings | Maintain SEO metadata and related settings. | “Page metadata ko manage karke search engines ke liye site information tayyar rakhi jati hai.” |
| Website Enquiries | Review messages sent through the website contact form. | “Website se aane wale contact messages yahan team tak pohanchte hain.” |
| Configurations | Manage technical/application configuration screens. | “System ki supported configuration options admin ke liye ek jagah hain.” |
| Application Settings | Manage application-wide settings. | “Branding aur application ke general settings yahan se maintain hote hain.” |
| System Update | Open the vendor update workflow. | “Yeh maintenance ke liye hai; normal daily operations mein isay use nahi karte.” |
| Notifications | Review in-app notifications. | “System updates aur assigned work ke notifications yahan milte hain.” |
| Profile & Security | Manage the signed-in user’s profile and security options. | “Har user apni profile aur account security yahan se manage kar sakta hai.” |

## Simple demo order

Dashboard → Lead CRM → Consultation Requests → Appointments → Student Directory → Student Documents → Destinations / Universities / Programs → Admissions → Courses → Service Orders → Analytics → Roles & Permissions.

Use fictional/sample data in a presentation. Populated sample records are now available in the `demo` database; some areas still have no records. Do not claim that email, payment, external meeting, or other provider integrations are live unless separately configured and verified.

The role-oriented demo accounts created by the seeder use fictional `.example` email addresses and the shared local-only password `GenZLocal!2026`. The original `admin@gmail.com` account remains unchanged.

| Demo role | Login email |
|---|---|
| Admin | `demo.admin@genzconsultants.example` |
| Counselor | `demo.counselor@genzconsultants.example` |
| Staff advisor | `demo.advisor@genzconsultants.example` |
| Affiliate | `demo.affiliate@genzconsultants.example` |
| Instructor | `demo.instructor@genzconsultants.example` |
| Finance | `demo.finance@genzconsultants.example` |
| CMS editor | `demo.editor@genzconsultants.example` |
| Students | `demo.learner.<first-name>@genzconsultants.example` (8 fictional student accounts) |

To safely rerun the demo seeder from PowerShell while targeting this local database:

```powershell
$env:APP_ENV = 'local'
php artisan db:seed --class=Database\Seeders\GenZDemoSeeder --no-interaction
```
