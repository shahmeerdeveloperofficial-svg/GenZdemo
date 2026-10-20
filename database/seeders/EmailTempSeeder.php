<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Currency;
use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTempSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EmailTemplate::where('id', '!=', 0)->delete();
        $data = [
            [
                'category' => 'User Create',
                'slug' => 'user-create',
                'subject' => 'User Create',
                'body' => "
                    Hello, {{name}}

                    Thank you for creating an account with us. Please verify your email address to activate your account by clicking the link below:

                    Verification Link: {{link}}

                    Your account details:
                    Email: {{email}}
                    Temporary Password: {{password}}

                    If you did not register with us, please ignore this email.

                    Best regards.
                ",
                'variables' => '{{name}}, {{email}}, {{link}}, {{password}}',
                'target_audience' => EMAIL_TARGET_AUDIENCE_OF_USER,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ], [
                'category' => 'Password Reset',
                'slug' => 'password-reset',
                'subject' => 'Password Reset',
                'body' => "
                    Hello, {{name}}

                    We received a request to reset the password associated with your account ({{email}}). Please click the link below to reset your password:

                    Reset Password Link: {{link}}

                    If you did not make this request, please contact our support team immediately.

                    Best regards.
                ",
                'variables' => '{{name}}, {{email}}, {{link}}',
                'target_audience' => EMAIL_TARGET_AUDIENCE_OF_USER,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ], [
                'category' => 'Verify',
                'slug' => 'verify',
                'subject' => 'Verify',
                'body' => "
                     Hello, {{name}}

                    To complete the registration process, please verify your email address by clicking the link below:

                    Verification Link: {{link}}

                    If you did not sign up for this account using {{email}}, please disregard this message.

                    Best regards.
                ",
                'variables' => '{{name}}, {{email}}, {{link}}',
                'target_audience' => EMAIL_TARGET_AUDIENCE_OF_USER,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ], [
                'category' => 'Consultation Booking Success',
                'slug' => 'consultation-booking-success',
                'subject' => 'Consultation Booking Success',
                'body' => "
                    Hello, {{name}}

                    Your consultation has been successfully booked! Here are the details:

                    Email: {{email}}
                    Consultation Details: {{link}}

                    If you have any questions or need to reschedule, feel free to reach out.

                    Best regards.
                ",
                'variables' => '{{name}}, {{email}}, {{link}}',
                'target_audience' => EMAIL_TARGET_AUDIENCE_OF_CONSULTER,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ], [
                'category' => 'Consultation Booking Consultant Notification',
                'slug' => 'consultation-booking-consultant-notification',
                'subject' => 'Consultation Booking Consultant Notification',
                'body' => "
                    Hello, {{name}}

                    You have received a new consultation booking request from {{student_name}}. Please check the details below and confirm at your earliest convenience:

                    Booking Details: {{link}}

                    Thank you for providing your valuable expertise.

                    Best regards.
                ",
                'variables' => '{{name}}, {{student_name}}, {{link}}',
                'target_audience' => EMAIL_TARGET_AUDIENCE_OF_CONSULTER,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ], [
                'category' => 'Consultation Meeting Link',
                'slug' => 'consultation-meeting-link',
                'subject' => 'Consultation Meeting Link',
                'body' => "
                    Hello, {{name}}

                    Your consultation meeting has been scheduled. Use the link below to join the meeting at the designated time:

                    Meeting Link: {{link}}

                    If you have any questions, feel free to contact our support team.

                    Best regards.

                ",
                'variables' => '{{name}}, {{link}}',
                'target_audience' => EMAIL_TARGET_AUDIENCE_OF_CONSULTER,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ], [
                'category' => 'Event Booking Confirmation',
                'slug' => 'event-booking-confirmation',
                'subject' => 'Event Booking Confirmation',
                'body' => "
                    Hello, {{name}}

                    Your booking for the event has been confirmed! Here are your booking details:

                    Email: {{email}}
                    Event Details: {{link}}

                    We look forward to seeing you at the event!

                    Best regards.
                ",
                'variables' => '{{name}}, {{email}}, {{link}}',
                'target_audience' => EMAIL_TARGET_AUDIENCE_OF_STUDENT,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ], [
                'category' => 'Course Purchase Success',
                'slug' => 'course-purchase-success',
                'subject' => 'Course Purchase Success',
                'body' => "
                    Hello, {{name}}

                    Congratulations on your course purchase! You can access the course materials using the link below:

                    Course Access Link: {{link}}

                    Registered Email: {{email}}

                    If you encounter any issues, feel free to contact us.

                    Best regards.

                ",
                'variables' => '{{name}}, {{email}}, {{link}}',
                'target_audience' => EMAIL_TARGET_AUDIENCE_OF_STUDENT,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'category' => 'Invoice Payment Success',
                'slug' => 'invoice-payment-success',
                'subject' => 'Invoice Payment Success',
                'body' => "
                    Hello, {{name}}

                    Thank you for payment our invoice. Here are your details:

                    Email: {{email}}
                    Link: {{link}}

                    If you have any questions or need assistance, please contact our support team.

                    Best regards.
                ",
                'variables' => '{{name}}, {{email}}, {{link}}',
                'target_audience' => EMAIL_TARGET_AUDIENCE_OF_STUDENT,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'category' => 'Service Order Status Change',
                'slug' => 'service-order-status-change',
                'subject' => 'Service Order Status Change',
                'body' => "
                    Hello, {{name}}

                    Change your service order status. Here are your details:

                    Email: {{email}}
                    Link: {{link}}

                    If you have any questions or need assistance, please contact our support team.

                    Best regards.
                ",
                'variables' => '{{name}}, {{email}}, {{link}}',
                'target_audience' => EMAIL_TARGET_AUDIENCE_OF_STUDENT,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'category' => 'Onboard Request',
                'slug' => 'onboard-request',
                'subject' => 'Onboard Request',
                'body' => "
                    Hello, {{name}}

                    Here are your onboard request details:

                    Email: {{email}}
                    Link: {{link}}

                    If you have any questions or need assistance, please contact our support team.

                    Best regards.
                ",
                'variables' => '{{name}}, {{email}}, {{link}}',
                'target_audience' => EMAIL_TARGET_AUDIENCE_OF_STUDENT,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'category' => 'Order Task Status Change',
                'slug' => 'order-task-status-change',
                'subject' => 'Order Task Status Change',
                'body' => "
                    Hello, {{name}}

                    Here are your order task status change details:

                    Email: {{email}}
                    Link: {{link}}

                    If you have any questions or need assistance, please contact our support team.

                    Best regards.
                ",
                'variables' => '{{name}}, {{email}}, {{link}}',
                'target_audience' => EMAIL_TARGET_AUDIENCE_OF_STUDENT,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'category' => 'Consultation Booking Status Update',
                'slug' => 'consultation-booking-status-update',
                'subject' => 'Consultation Booking Status Update',
                'body' => "
                    Hello, {{name}}

                    Here are your consultation booking status update details:

                    Email: {{email}}
                    Status: {{status}}

                    If you have any questions or need assistance, please contact our support team.

                    Best regards.
                ",
                'variables' => '{{name}}, {{email}}, {{status}}',
                'target_audience' => EMAIL_TARGET_AUDIENCE_OF_STUDENT,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'category' => 'Service Purchase Success',
                'slug' => 'service-purchase-success',
                'subject' => 'Service Purchase Success',
                'body' => "
                    Hello, {{name}}

                    Here are your Service Purchase Success details:

                    Email: {{email}}
                    Link: {{link}}

                    If you have any questions or need assistance, please contact our support team.

                    Best regards.
                ",
                'variables' => '{{name}}, {{email}}, {{link}}',
                'target_audience' => EMAIL_TARGET_AUDIENCE_OF_STUDENT,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],[
                'category' => 'Onboard Done By Student',
                'slug' => 'onboard-done-by-student',
                'subject' => 'Onboard Done By Student',
                'body' => "
                    Hello, {{name}}

                    Here are your onboard done Success details:

                    Email: {{email}}
                    Link: {{link}}

                    If you have any questions or need assistance, please contact our support team.

                    Best regards.
                ",
                'variables' => '{{name}}, {{email}}, {{link}}',
                'target_audience' => EMAIL_TARGET_AUDIENCE_OF_STUDENT,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]

        ];
        EmailTemplate::where('id', '!=', 0)->delete();
        EmailTemplate::insert($data);
    }
}
