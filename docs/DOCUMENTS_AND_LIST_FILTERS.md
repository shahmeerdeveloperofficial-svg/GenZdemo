# Documents and list filters

## Upload a student document

1. Open `http://127.0.0.1:8000/student/my-study-profile` while signed in as a student. Use an Incognito window if the normal window is signed in as an administrator.
2. Select the student's **Branch** and click **Save study profile**.
3. Scroll to **Your documents**.
4. Choose a PDF, JPG or PNG under the appropriate document category (maximum 10 MB).
5. Click **Submit for review**. The document receives **Pending** status.
6. In the administrator window, open `http://127.0.0.1:8000/admin/student-documents`.

The review queue initially shows pending documents. Select **All** or **Approved** in Review status to see previously reviewed documents. A replacement upload supersedes the previous document in that category.

## Review queue filters

Select **Branch**, **Document type**, **Review status**, **Student**, or **Uploaded**, then click **Apply filters**. Filters work together. **Reset** restores the default pending queue.

Branch comes from the student's account. Administrators can also set it in Student Add/Edit. Existing accounts without a branch appear as **Not assigned** until updated; no branch is inferred from their names or email addresses.

## Other lists

- DataTables lists display relevant dropdowns for the categorical columns available on that list: status, type, country, university, program, category, payment method, student, consultant, service, and branch where supported.
- Leads: stage, source, owner, registration/inquiry branch, and created period.
- Admissions: branch, admission stage, visa status, university, academic program, counselor, and created period.
- Academic programs: status, country, university, study level, plus text search.
- Services: price type, status for administrators, and text search.
- Course certificates: student branch and certificate status.

Only relevant supported fields appear on each list. DataTables dropdown options come from the records the current account can access. Filters run before pagination, so they apply to all matching records, not just the current page. Existing permissions and assignment restrictions still apply.

Lead branch filtering includes the branch captured on a study registration or website inquiry. Document and student-related branch filters use the student's account branch.

## Installation note

The local `demo` database already has the nullable `users.branch` column. For another installation, apply `database/migrations/2026_10_20_150000_add_branch_to_users.php` with Laravel migrations before using these filters.

After updating assets, refresh the browser with **Ctrl + F5**.
