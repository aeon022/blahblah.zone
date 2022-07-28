<?php

namespace Modules\EmailTemplate\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

use Modules\EmailTemplate\Models\EmailTemplate;
use DB;

/**
 * Class EmailTemplateDatabaseSeeder
 *
 * The Seeder is Defined for Email Template.
 *
 * PHP version 7.1.3
 *
 * @category  Administration
 * @package   Modules\EmailTemplate
 * @author    Vipul Patel <vipul@chetsapp.com>
 * @copyright 2019 Chetsapp Group
 * @license   Chetsapp Private Limited
 * @version   Release: @1.0@
 * @link      http://chetsapp.com
 * @since     Class available since Release 1.0
 */
class EmailTemplateDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // --
        // Email template
        DB::table(config('core.acl.email_template_table'))->delete();
        $data = [
            [
                'id' => 1,
                'email_group_id' => 1,
                'template_name' => 'Activate Account',
                'template_subject' => 'Activate Account',
                'template_body' =>
                    '<p>Welcome to {SITE_NAME}!</p>
                    <p>Thanks for joining {SITE_NAME}. We listed your sign in details below, make sure you keep them safe.</p><p>To verify your email address, please follow this link:<br><big><strong><a href="{ACTIVATE_URL}">Finish your registration...</a></strong></big><br>Link doesn\'t work? Copy the following link to your browser address bar:<br><a href="{ACTIVATE_URL}">{ACTIVATE_URL}</a></p><p><br>Please verify your email within {ACTIVATION_PERIOD} hours, otherwise your registration will become invalid and you will have to register again.<br>Your username: {USERNAME}<br>Your email address: {EMAIL}<br>Your password: {PASSWORD}<br><br>Have fun!<br>The {SITE_NAME} Team</p>',
                'type' => 'activate_account'
            ],
            [
                'id' => 2,
                'email_group_id' => 1,
                'template_name' => 'Change Email',
                'template_subject' => 'Change Email',
                'template_body' =>
                    '<p>New email address on {SITE_NAME}</p>
                    <p>You have changed your email address for {SITE_NAME}.<br>Follow this link to confirm your new email address:<br><big><strong><a href="{NEW_EMAIL_KEY_URL}">Confirm your new email</a></strong></big><br>Link doesn\'t work? Copy the following link to your browser address bar:<br><a href="{NEW_EMAIL_KEY_URL}">{NEW_EMAIL_KEY_URL}</a><br><br>Your email address: {NEW_EMAIL}<br><br>You received this email, because it was requested by a <a href="{SITE_URL}">{SITE_NAME}</a> user. If you have received this by mistake, please DO NOT click the confirmation link, and simply delete this email. After a short time, the request will be removed from the system.<br><br>Thank you,<br>The {SITE_NAME} Team</p>',
                'type' => 'change_email'
            ],
            [
                'id' => 3,
                'email_group_id' => 1,
                'template_name' => 'Forgot Password',
                'template_subject' => 'Forgot Password',
                'template_body' =>
                    '<p>Forgot your password, huh? No big deal.<br>To create a new password, just follow this link:<br><br><big><strong><a href="{PASS_KEY_URL}">Create a new password</a></strong></big><br>Link doesn\'t work? Copy the following link to your browser address bar:<br><a href="{PASS_KEY_URL}">{PASS_KEY_URL}</a><br>You received this email, because it was requested by a <a href="{SITE_URL}">{SITE_NAME}</a> user.</p>
                    <p>This is part of the procedure to create a new password on the system. If you DID NOT request a new password then please ignore this email and your password will remain the same.</p>
                    <p><br>Thank you,<br>The {SITE_NAME} Team</p>',
                'type' => 'forgot_password'
            ],
            [
                'id' => 4,
                'email_group_id' => 1,
                'template_name' => 'Register Email',
                'template_subject' => 'Register Email',
                'template_body' =>
                    '<p>Welcome to {SITE_NAME}</p>
                    <p>Thanks for joining {SITE_NAME}. We listed your sign in details below, make sure you keep them safe.<br>To open your {SITE_NAME} homepage, please follow this link:<br><big><strong><a href="{SITE_URL}">{SITE_NAME} Account!</a></strong></big><br>Link doesn\'t work? Copy the following link to your browser address bar:<br><a href="{SITE_URL}">{SITE_URL}</a><br>Your username: {USERNAME}<br>Your email address: {EMAIL}<br>Your password: {PASSWORD}<br>Have fun!<br>The {SITE_NAME} Team.<br>&nbsp;</p>',
                'type' => 'register_email'
            ],
            [
                'id' => 5,
                'email_group_id' => 1,
                'template_name' => 'Reset Password',
                'template_subject' => 'Reset Password',
                'template_body' =>
                    '<p>New password on {SITE_NAME}</p>
                    <p>You have changed your password.<br>Please, keep it in your records so you don\'t forget it.</p>
                    <p>Your username: {USERNAME}<br>Your email address: {EMAIL}<br>Your new password: {NEW_PASSWORD}<br><br>Thank you,<br>The {SITE_NAME} Team</p>',
                'type' => 'reset_password'
            ],
            [
                'id' => 6,
                'email_group_id' => 1,
                'template_name' => 'Welcome Email',
                'template_subject' => 'Welcome Email',
                'template_body' =>
                    '<p>Hello <strong>{NAME}</strong>,</p>
                    <p>Welcome to <strong>{COMPANY_NAME}</strong> .Thanks for joining <strong>{COMPANY_NAME}</strong>.</p>
                    <p>We just wanted to say welcome.</p>
                    <p>Please contact us if you need any help.</p>
                    <p>Click here to view your profile: <strong><a href="{COMPANY_URL}">View Profile</a></strong></p>
                    <p><br>Have fun!<br>The <strong>{COMPANY_NAME}</strong> Team.</p>',
                'type' => 'welcome_email'
            ],
            [
                'id' => 7,
                'email_group_id' => 1,
                'template_name' => 'Meeting',
                'template_subject' => 'Meeting',
                'template_body' =>
                    'Hi <strong>{NAME}</strong>,
                    <div><br></div>
                    <div>A meeting has been scheduled on <strong>{MEETING_DATE}</strong>, in the <strong>{LOCATION}</strong> at <strong>{MEETING_TIME}</strong>.</div>
                    <div><br></div>
                    <div>{DESCRIPTION}</div>
                    <div><br></div>
                    <div>Have fun!<br>The&nbsp;<strong>{COMPANY_NAME}</strong>&nbsp;Team.<br></div>
                    <div><br></div>',
                'type' => 'meeting'
            ],
            [
                'id' => 8,
                'email_group_id' => 1,
                'template_name' => 'Announcement',
                'template_subject' => 'Announcement',
                'template_body' =>
                    'Hi <strong>{NAME}</strong>,
                    <div><br></div>
                    <div><strong>{TITLE}</strong></div>
                    <div><br></div>
                    <div><strong>Start Date :</strong> {START_DATE}</div>
                    <div><strong>End Date :</strong> {END_DATE}</div>
                    <div><br></div>
                    <div>{DESCRIPTION}</div>
                    <div><br></div>
                    <div>Have fun!<br>The&nbsp;<strong>{COMPANY_NAME}</strong>&nbsp;Team.<br></div>
                    <div><br></div>',
                'type' => 'announcement'
            ],
            [
                'id' => 9,
                'email_group_id' => 2,
                'template_name' => 'Assigned Project',
                'template_subject' => 'Assigned Project',
                'template_body' => '<p>Hi There,</p><p>A<strong> {PROJECT_NAME}</strong> has been assigned by <strong>{ASSIGNED_BY} </strong>to you.You can view this project by logging in to the portal using the link below:<br><br><big><a href="{PROJECT_URL}"><strong>View Project</strong></a></big><br><br>Best Regards<br>The {SITE_NAME} Team</p><p>&nbsp;</p>',
                'type' => 'assigned_project'
            ],
            [
                'id' => 10,
                'email_group_id' => 2,
                'template_name' => 'Notification Client',
                'template_subject' => 'New Project Created',
                'template_body' => '<p>Hello, <strong>{CLIENT_NAME}</strong>,<br /><br />we have created a new project with your account.<br /><br />Project name : <strong>{PROJECT_NAME}</strong><br />You can login to see the status of your project by using this link:<br /><big><a href="{PROJECT_LINK}"><strong>View Project</strong></a></big></p><p><br />Best Regards<br />The {SITE_NAME} Team</p><p>&nbsp;</p>',
                'type' => 'notification_client'
            ],
            [
                'id' => 11,
                'email_group_id' => 2,
                'template_name' => 'Complete Projects',
                'template_subject' => 'Project Completed',
                'template_body' => '<p>Hi <strong>{CLIENT_NAME}</strong>,</p><p>Project : <strong>{PROJECT_NAME}</strong> &nbsp;has been completed.<br />You can view the project by logging into your portal Account:<br /><big><a href="{PROJECT_LINK}"><strong>View Project</strong></a></big><br /><br />Best Regards,<br />The {SITE_NAME} Team</p>',
                'type' => 'complete_projects'
            ],
            [
                'id' => 12,
                'email_group_id' => 2,
                'template_name' => 'Project Comments',
                'template_subject' => 'New Project Comment Received',
                'template_body' => '<p>Hi There,</p><p>A new comment has been posted by <strong>{POSTED_BY}</strong> to project <strong>{PROJECT_NAME}</strong>.</p><p>You can view the comment using the link below:<br /><big><a href="{COMMENT_URL}"><strong>View Project</strong></a></big><br /><br /><em>{COMMENT_MESSAGE}</em><br /><br />Best Regards,<br />The {SITE_NAME} Team</p>',
                'type' => 'project_comments'
            ],
            [
                'id' => 13,
                'email_group_id' => 2,
                'template_name' => 'Project Attachment',
                'template_subject' => 'New Project Attachment',
                'template_body' => '<p>Hi There,</p><p>A new file has been uploaded by <strong>{UPLOADED_BY}</strong> to project <strong>{PROJECT_NAME}</strong>.<br />You can view the Project using the link below:<br><br><big><a href="{PROJECT_URL}"><strong>View Project</strong></a></big><br /><br />Best Regards,<br />The {SITE_NAME} Team</p>',
                'type' => 'project_attachment'
            ],
            [
                'id' => 14,
                'email_group_id' => 3,
                'template_name' => 'Task Assigned',
                'template_subject' => 'Task Assigned',
                'template_body' => '<p>Hi there,</p><p>A new task <strong>{TASK_NAME}</strong> &nbsp;has been assigned to you by <strong>{ASSIGNED_BY}</strong>.</p><p>You can view this task by logging in to the portal using the link below.</p><p><big><strong><a href="{TASK_URL}">View Task</a></strong></big><br><br>Regards<br>The {SITE_NAME} Team</p>',
                'type' => 'task_assigned'
            ],
            [
                'id' => 15,
                'email_group_id' => 3,
                'template_name' => 'Task Comments',
                'template_subject' => 'New Task Comment Received',
                'template_body' => '<p>Hi There,</p><p>A new comment has been posted by <strong>{POSTED_BY}</strong> to <strong>{TASK_NAME}</strong>.</p><p>You can view the comment using the link below:<br /><big><strong><a href="{COMMENT_URL}">View Comment</a></strong></big><br /><br /><em>{COMMENT_MESSAGE}</em><br /><br />Best Regards,<br />The {SITE_NAME} Team</p>',
                'type' => 'task_comments'
            ],
            [
                'id' => 16,
                'email_group_id' => 3,
                'template_name' => 'Tasks Attachment',
                'template_subject' => 'New Tasks Attachment',
                'template_body' => '<p>Hi There,</p><p>A new file has been uploaded by <strong>{UPLOADED_BY} </strong>to Task <strong>{TASK_NAME}</strong>.<br>You can view the Task&nbsp;using the link below:</p><p><br><big><a href="{TASK_URL}"><strong>View Task</strong></a></big><br><br>Best Regards,<br>The {SITE_NAME} Team</p>',
                'type' => 'task_attachment'
            ],
            [
                'id' => 17,
                'email_group_id' => 3,
                'template_name' => 'Task Updated',
                'template_subject' => 'Task Updated',
                'template_body' => '<p>Hi there,</p><p><strong>{TASK_NAME}</strong> has been updated by <strong>{ASSIGNED_BY}</strong>.</p><p>You can view this Task by logging in to the portal using the link below.</p><p><br /><big><strong><a href="{TASK_URL}">View Task</a></strong></big><br /><br />Regards<br />The {SITE_NAME} Team</p>',
                'type' => 'task_updated'
            ],
            [
                'id' => 18,
                'email_group_id' => 4,
                'template_name' => 'Defect Assigned',
                'template_subject' => 'New Defect Assigned',
                'template_body' => '<p>Hi there,</p><p>A new defect &nbsp;{DEFECT_TITLE} &nbsp;has been assigned to you by {ASSIGNED_BY}.</p><p>You can view this defect by logging in to the portal using the link below.</p><p><br /><big><strong><a href="{DEFECT_URL}">View Defect</a></strong></big><br /><br />Regards<br />The {SITE_NAME} Team</p>',
                'type' => 'defect_assigned'
            ],
            [
                'id' => 19,
                'email_group_id' => 4,
                'template_name' => 'Defect Comments',
                'template_subject' => 'New Defect Comment Received',
                'template_body' => '<p>Hi there,</p><p>A new comment has been posted by {POSTED_BY} to defect {DEFECT_TITLE}.</p><p>You can view the comment using the link below.</p><p><em>{COMMENT_MESSAGE}</em></p><p><br /><big><strong><a href="{COMMENT_URL}">View Comment</a></strong></big><br><br>Regards<br />The {SITE_NAME} Team</p><p>&nbsp;</p>',
                'type' => 'defect_comments'
            ],
            [
                'id' => 20,
                'email_group_id' => 4,
                'template_name' => 'Defect Attachment',
                'template_subject' => 'New Defect Attachment',
                'template_body' => '<p>Hi there,</p><p>A new attachment&nbsp;has been uploaded by {UPLOADED_BY} to issue {DEFECT_TITLE}.</p><p>You can view the defect using the link below.</p><p><br /><big><strong><a href="{DEFECT_URL}">View Defect</a></strong></big></p><p><br />Regards<br />The {SITE_NAME} Team</p>',
                'type' => 'defect_attachment'
            ],
            [
                'id' => 21,
                'email_group_id' => 4,
                'template_name' => 'Defect Updated',
                'template_subject' => 'Defect Status Changed',
                'template_body' => '<p>Hi there,</p><p>Defect {DEFECT_TITLE} has been marked as {STATUS} by {MARKED_BY}.</p><p>You can view this defect by logging in to the portal using the link below.</p><p><big><strong><a href="{DEFECT_URL}">View Defect</a></strong></big><br />Regards<br />The {SITE_NAME} Team</p><p>&nbsp;</p>',
                'type' => 'defect_updated'
            ],
            [
                'id' => 22,
                'email_group_id' => 5,
                'template_name' => 'Incident Assigned',
                'template_subject' => 'New Incident Assigned',
                'template_body' => '<p>Hi there,</p><p>A new incident &nbsp;<big><strong>{INCIDENT_TITLE}</big></strong> &nbsp;has been assigned to you by {ASSIGNED_BY}.</p><p>You can view this incident by logging in to the portal using the link below.</p><p><br><big><strong><a href="{INCIDENT_URL}">View Incident</a></strong></big><br><br>Regards<br>The {SITE_NAME} Team</p>',
                'type' => 'incident_assigned'
            ],
            [
                'id' => 23,
                'email_group_id' => 5,
                'template_name' => 'Incident Comments',
                'template_subject' => 'New Incident Comment Received',
                'template_body' => '<p>Hi there,</p><p>A new comment has been posted by {POSTED_BY} to incident {INCIDENT_TITLE}.</p><p>You can view the comment using the link below.</p><p><em>{COMMENT_MESSAGE}</em></p><p><br><big><strong><a href="{COMMENT_URL}">View Comment</a></strong></big><br><br>Regards<br>The {SITE_NAME} Team</p><p>&nbsp;</p>',
                'type' => 'incident_comments'
            ],
            [
                'id' => 24,
                'email_group_id' => 5,
                'template_name' => 'Incident Attachment',
                'template_subject' => 'New Incident Attachment',
                'template_body' => '<p>Hi there,</p><p>A new attachment&nbsp;has been uploaded by {UPLOADED_BY} to issue {INCIDENT_TITLE}.</p><p>You can view the incident using the link below.</p><p><br><big><strong><a href="{INCIDENT_URL}">View Incident</a></strong></big></p><p><br>Regards<br>The {SITE_NAME} Team</p>',
                'type' => 'incident_attachment'
            ],
            [
                'id' => 25,
                'email_group_id' => 5,
                'template_name' => 'Incident Updated',
                'template_subject' => 'Incident Status Changed',
                'template_body' => '<p>Hi there,</p><p>Incident {INCIDENT_TITLE} has been marked as {STATUS} by {MARKED_BY}.</p><p>You can view this incident by logging in to the portal using the link below.</p><p><big><strong><a href="{INCIDENT_URL}">View Incident</a></strong></big><br>Regards<br>The {SITE_NAME} Team</p><p>&nbsp;</p>',
                'type' => 'incident_updated'
            ],
        ];
        EmailTemplate::insert($data);
    }
}
