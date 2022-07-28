<?php

namespace Modules\Helper\Helpers;

use Carbon\Carbon;
use Mail;
use Modules\EmailTemplate\Models\EmailTemplate;
use Modules\Helper\Jobs\SendEmailJob;
use Modules\User\Models\User\User;
use Modules\Incident\Models\Incident;
use Modules\Setting\Models\Setting;

/**
 * Class EmailsHelper
 *
 * The Helper is Defined for send different mail.
 *
 * PHP version 7.1.3
 *
 * @category  Helper
 * @package   Modules\Helper
 * @author    Vipul Patel <vipul@chetsapp.com>
 * @copyright 2019 Chetsapp Group
 * @license   Chetsapp Private Limited
 * @version   Release: @1.0@
 * @link      http://chetsapp.com
 * @since     Class available since Release 1.0
 */
class EmailsHelper
{
    /**
     * Send mail.
     *
     * @param String $template   [Email html]
     * @param Array  $parameters [Required params]
     * @param Array  $config     [Send mail config]
     *
     * @return void
     */
    public function sendmail($template = '', $parameters = [], $config = [])
    {
        try {
            Mail::send(
                $template,
                $parameters,
                function ($mail) use ($config) {
                    $mail->to($config['email'], $config['name'])
                        ->from($config['from'])
                        ->subject($config['subject']);
                }
            );
        } catch (\Exception $e) {
        }
    }

    /**
     * Send mail with attachments.
     *
     * @param String $template         [Email html]
     * @param Array  $parameters       [Required params]
     * @param Array  $config           [Send mail config]
     * @param Array  $attachmentsFiles [Attachment files]
     *
     * @return void
     */
    public function sendmailAttachments(
        $template = '',
        $parameters = [],
        $config = [],
        $attachmentsFiles = []
    ) {
        try {
            Mail::send(
                ['html' => $template],
                $parameters,
                function ($mail) use ($config, $attachmentsFiles) {
                    $mail->to($config['email'], $config['name'])
                        ->from($config['from'])
                        ->subject($config['subject']);

                    if (!empty($attachmentsFiles)) {
                        foreach ($attachmentsFiles as $key => $value) {
                            $mail->attach(
                                $value['file'],
                                array(
                                'as'   => $value['name'],
                                'mime' => $value['mime']
                                )
                            );
                        }
                    }
                }
            );
        } catch (\Exception $e) {
        }
    }

    /**
     * Send mails.
     *
     * @param String $to      [Mail to address]
     * @param String $name    [Mail from name]
     * @param String $subject [Mail subject]
     * @param String $body    [Mail body]
     *
     * @return void
     */
    private function _sendEmails($to, $name, $subject, $body)
    {
        try {
            \Mail::send(
                [],
                [],
                function ($message) use ($to, $name, $subject, $body) {
                    $message->setBody($body, 'text/html');
                    $message->to($to, $name);
                    $message->subject($subject);
                }
            );
        } catch (\Exception $e) {
            // pr($e->getMessage(), 1);
            return $e->getMessage();
        }
    }

    /**
     * Send mails in queue.
     *
     * @param String $to      [Mail to]
     * @param String $name    [From name]
     * @param String $subject [Mail subject]
     * @param String $body    [Mail body]
     *
     * @return void
     */
    private function _sendEmailsInQueue($to, $name, $subject, $body)
    {
        try {
            $details['to'] = $to;
            $details['name'] = $name;
            $details['subject'] = $subject;
            $details['body'] = $body;
            $job = (new SendEmailJob($details))->delay(Carbon::now()->addSeconds(5));
            dispatch($job);
        } catch (\Exception $e) {
            pr($e->getMessage(), 1);
        }
    }

    /**
     * Send user activate email.
     *
     * @param Object $user     [User object]
     * @param String $password [Password]
     *
     * @return Boolean
     */
    public function sendActivateEmail($user, $password)
    {
        $email_template = EmailTemplate::where('type', 'activate_account')->first();
        if (!empty($email_template)) {
            $name = $user->firstname. ' '. $user->lastname;
            $activate_url = str_replace("{ACTIVATE_URL}", config('app.url').'api/verify/user/' . $user->email_verification_code, $email_template->template_body);
            $activate_period = str_replace("{ACTIVATION_PERIOD}", config('core.EMAIL_ACTIVATION_EXPIRE') / 3600, $activate_url);
            $username = str_replace("{USERNAME}", $user->username, $activate_period);
            $user_email = str_replace("{EMAIL}", $user->email, $username);
            $user_password = str_replace("{PASSWORD}", $password, $user_email);
            $message = str_replace("{SITE_NAME}", config('core.COMPANY_NAME'), $user_password);

            $this->_sendEmails(
                $user->email,
                $name,
                $email_template->template_subject,
                $message
            );
        }
        return true;
    }

    /**
     * Send change email verify mail.
     *
     * @param Object $user  [User object]
     * @param Array  $input [Request data for change mail]
     *
     * @return Boolean
     */
    public function sendChangeEmailVerifyEmail($user, $input)
    {
        $email_template = EmailTemplate::where('type', 'change_email')->first();
        if (!empty($email_template)) {
            $name = $user->firstname. ' '. $user->lastname;
            $activate_url = str_replace("{NEW_EMAIL_KEY_URL}", config('app.url').'api/verify/user/' . $user->email_verification_code .'/'.encrypt($input['email']), $email_template->template_body);
            $site_url = str_replace("{SITE_URL}", config('app.front_url'), $activate_url);
            $user_email = str_replace("{NEW_EMAIL}", $input['email'], $site_url);
            $message = str_replace("{SITE_NAME}", config('core.COMPANY_NAME'), $user_email);

            $this->_sendEmails(
                $input['email'],
                $name,
                $email_template->template_subject,
                $message
            );
        }
        return true;
    }

    /**
     * Send change password mail.
     *
     * @param Object $user     [User object]
     * @param String $password [Actual password]
     *
     * @return Boolean
     */
    public function sendChangePasswordEmail($user, $password)
    {
        $email_template = EmailTemplate::where('type', 'reset_password')->first();
        if (!empty($email_template)) {
            $name = $user->firstname. ' '. $user->lastname;

            $username = str_replace("{USERNAME}", $user->username, $email_template->template_body);
            $user_email = str_replace("{EMAIL}", $user->email, $username);
            $user_password = str_replace("{NEW_PASSWORD}", $password, $user_email);
            $message = str_replace("{SITE_NAME}", config('core.COMPANY_NAME'), $user_password);

            $this->_sendEmails(
                $user->email,
                $name,
                $email_template->template_subject,
                $message
            );
        }
        return true;
    }

    /**
     * Send meeting invite people emails.
     *
     * @param Array  $users      [Users array]
     * @param Object $logginUser [Login user name]
     * @param Object $meeting    [Meeting details]
     *
     * @return Boolean
     */
    public function sendMeetingEmails($users, $logginUser, $meeting)
    {
        $users = User::whereIn('id', $users)->get();
        $email_template = EmailTemplate::where('type', 'meeting')->first();

        if (!empty($email_template) && !empty($users)) {
            foreach ($users as $key => $value) {
                $name = $value->firstname.' '.$value->lastname;
                $message = $email_template->template_body;
                $subject = $meeting->title .' '.
                date("l, F j, Y g:i a", strtotime($meeting->start_date));
                $meeting_date = date("l, F j, Y", strtotime($meeting->start_date));
                $meeting_time = date("g:i a", strtotime($meeting->start_date));
                $site_name = str_replace("{COMPANY_NAME}", config('core.COMPANY_NAME'), $message);
                $user_name = str_replace("{NAME}", $name, $site_name);
                $meeting_date_time = str_replace("{MEETING_DATE}", $meeting_date, $user_name);
                $meeting_time_date = str_replace("{MEETING_TIME}", $meeting_time, $meeting_date_time);
                $description = str_replace("{DESCRIPTION}", $meeting->description, $meeting_time_date);
                $message = str_replace("{LOCATION}", $meeting->location, $description);
                
                $this->_sendEmails(
                    $value->email,
                    $name,
                    $subject,
                    $message
                );
            }
        }
    }

    /**
     * Send announcement emails.
     *
     * @param Array  $users        [Users array]
     * @param Object $announcement [Announcement details]
     *
     * @return Boolean
     */
    public function sendAnnouncementEmails($users, $announcement)
    {
        $setting = Setting::select("is_announcement_notification")->first();
        if($setting->is_announcement_notification){
            $email_template = EmailTemplate::where('type', 'announcement')->first();
            if (!empty($email_template) && !empty($users)) {
                foreach ($users as $key => $value) {
                    $name = $value->firstname.' '.$value->lastname;
                    $message = $email_template->template_body;
                    $subject = $announcement->title .' '.
                    date("l, F j, Y g:i a", strtotime($announcement->start_date));
                    $announcement_start_date = date("l, F j, Y g:i a", strtotime($announcement->start_date));
                    $announcement_end_date = date("l, F j, Y g:i a", strtotime($announcement->end_date));
                    $site_name = str_replace("{COMPANY_NAME}", config('core.COMPANY_NAME'), $message);
                    $user_name = str_replace("{NAME}", $name, $site_name);
                    $title = str_replace("{TITLE}", $announcement->title, $user_name);
                    $announcement_start_date = str_replace("{START_DATE}", $announcement_start_date, $title);
                    $announcement_end_date = str_replace("{END_DATE}", $announcement_end_date, $announcement_start_date);
                    $message = str_replace("{DESCRIPTION}", $announcement->description, $announcement_end_date);
                    $this->_sendEmails(
                        $value->email,
                        $name,
                        $subject,
                        $message
                    );
                }
            }
        }
    }

    /**
     * Forgot user password.
     *
     * @param String $url  [Forgot password URL]
     * @param Object $user [User object]
     *
     * @return Boolean
     */
    public function sendForgotPasswordEmail($url, $user)
    {
        $email_template = EmailTemplate::where('type', 'forgot_password')->first();

        if (!empty($email_template)) {
            $message = $email_template->template_body;
            $subject = $email_template->template_subject;

            $site_url = str_replace("{SITE_URL}", config('app.url'), $message);
            $key_url = str_replace("{PASS_KEY_URL}", $url, $site_url);
            $message = str_replace("{SITE_NAME}", config('core.COMPANY_NAME'), $key_url);

            $this->_sendEmails(
                $user->email,
                $user->firstname.' '.$user->lastname,
                $email_template->template_subject,
                $message
            );
        }
        return true;
    }

    /**
     * Send user reset password email.
     *
     * @param Object $user     [User object]
     * @param String $password [Password]
     *
     * @return Boolean
     */
    public function sendResetEmail($user, $password)
    {
        $email_template = EmailTemplate::where('type', 'reset_password')->first();

        if (!empty($email_template)) {
            $message = $email_template->template_body;
            $subject = $email_template->template_subject;

            $site_name = str_replace("{SITE_NAME}", config('core.COMPANY_NAME'), $message);
            $username = str_replace("{USERNAME}", $user->username, $site_name);
            $user_email = str_replace("{EMAIL}", $user->email, $username);
            $message = str_replace("{NEW_PASSWORD}", $password, $user_email);

            $this->_sendEmails(
                $user->email,
                $user->firstname,
                $subject,
                $message
            );
        }
        return true;
    }

    /**
     * Send invite people mail.
     *
     * @param Object $user [User object]
     *
     * @return Boolean
     */
    public function sendInviteUserMail($user)
    {
        $url = config('app.front_url').'/#/users/profile/'. $user->id;
        $email_template = EmailTemplate::where('type', 'welcome_email')->first();

        if (!empty($email_template)) {
            $message = $email_template->template_body;
            $subject = $email_template->template_subject;

            $username = str_replace("{NAME}", $user->firstname .' '. $user->lastname, $message);
            $site_url = str_replace("{COMPANY_URL}", $url, $username);
            $message = str_replace("{COMPANY_NAME}", config('core.COMPANY_NAME'), $site_url);

            $this->_sendEmails($user->email, $user->firstname, $subject, $message);
        }
        return true;
    }

    /**
     * Send project assign email.
     *
     * @param Object $users      [Users object]
     * @param Int    $project_id [Project id]
     * @param String $subject    [Subject]
     * @param String $project    [Project name]
     * @param String $name       [Sender name]
     *
     * @return Boolean
     */
    public function sendProjectAssignUserEmail($users, $project_id, $subject, $project, $name)
    {
        $email_template = EmailTemplate::where('type', 'assigned_project')->first();

        if (!empty($email_template)) {
            $url = config('app.front_url').'#/projects/detail/'.$project_id;
            foreach ($users as $key => $value) {
                $message = $email_template->template_body;

                $site_name = str_replace("{SITE_NAME}", config('core.COMPANY_NAME'), $message);
                $site_url = str_replace("{PROJECT_NAME}", $project, $site_name);
                $assign_by = str_replace("{ASSIGNED_BY}", $name, $site_url);
                $message = str_replace("{PROJECT_URL}", $url, $assign_by);

                $this->_sendEmailsInQueue($value['email'], $name, $subject, $message);
            }
        }

        return true;
    }

    /**
     * Send project create client notification.
     *
     * @param String $project_name [Project name]
     * @param Int    $client_id    [Client id]
     * @param Int    $project_id   [Project id]
     *
     * @return Boolean
     */
    public function sendCreateProjectNotiClientMail($project_name, $client_id, $project_id)
    {
        $user = User::findOrFail($client_id);
        $email_template = EmailTemplate::where('type', 'notification_client')->first();

        if (!empty($user) && !empty($email_template)) {
            $name = $user->firstname.' '.$user->lastname;
            $url = config('app.front_url').'#/projects/detail/'.$project_id;
            
            $message = $email_template->template_body;
            $subject = $email_template->template_subject;

            $site_name = str_replace("{SITE_NAME}", config('core.COMPANY_NAME'), $message);
            $project_name = str_replace("{PROJECT_NAME}", $project_name, $site_name);
            $project_link = str_replace("{CLIENT_NAME}", $name, $project_name);
            $message = str_replace("{PROJECT_LINK}", $url, $project_link);

            $this->_sendEmailsInQueue($user->email, $name, $subject, $message);
        }
    }

    /**
     * Send project completed client notification.
     *
     * @param Object $project    [Project object]
     * @param Int    $project_id [Project id]
     *
     * @return Boolean
     */
    public function sendProjectCompletedNotificationClientMail($project, $project_id)
    {
        $user = User::findOrFail($project->client_id);
        $email_template = EmailTemplate::where('type', 'complete_projects')->first();

        if (!empty($user)) {
            $name = $user->firstname.' '.$user->lastname;
            $url = config('app.front_url').'#/projects/detail/'.$project_id;

            $message = $email_template->template_body;
            $subject = $email_template->template_subject;

            $site_name = str_replace("{SITE_NAME}", config('core.COMPANY_NAME'), $message);
            $project_name = str_replace("{PROJECT_NAME}", $project->project_name, $site_name);
            $project_link = str_replace("{CLIENT_NAME}", $user->firstname.' '.$user->lastname, $project_name);
            $message = str_replace("{PROJECT_LINK}", $url, $project_link);

            $this->_sendEmailsInQueue($user->email, $name, $subject, $message);
        }
    }

    /**
     * Send project user comment.
     *
     * @param Object $logginUser     [Login user object]
     * @param Object $projectcomment [Project comment object]
     *
     * @return Boolean
     */
    public function sendProjectCommentMail($logginUser, $projectcomment)
    {
        $users = $this->_getAssignProjectUsers($projectcomment->project_id);
        $email_template = EmailTemplate::where('type', 'project_comments')->first();

        if (!empty($email_template)) {
            $url = config('app.front_url').'#/projects/detail/'. $projectcomment->project_id;
            foreach ($users as $key => $value) {
                $message = $email_template->template_body;
                $subject = $email_template->template_subject;

                $posted_by = str_replace("{POSTED_BY}", $logginUser->firstname.' '.$logginUser->lastname, $message);
                $project_name = str_replace("{PROJECT_NAME}", $value->project_name, $posted_by);
                $site_url = str_replace("{COMMENT_URL}", $url, $project_name);
                $comment = str_replace("{COMMENT_MESSAGE}", $projectcomment->comment, $site_url);
                $message = str_replace("{SITE_NAME}", config('core.COMPANY_NAME'), $comment);

                $this->_sendEmailsInQueue(
                    $value->email,
                    $value->name,
                    $subject,
                    $message
                );
            }
        }
    }

    /**
     * Send project user attachment.
     *
     * @param Object $logginUser        [Login user object]
     * @param Object $projectAttachment [Project attachment object]
     *
     * @return Boolean
     */
    public function sendProjectAttachmentMail($logginUser, $projectAttachment)
    {
        $users = $this->_getAssignProjectUsers($projectAttachment->project_id);
        $email_template = EmailTemplate::where('type', 'project_attachment')->first();

        if (!empty($email_template)) {
            $url = config('app.front_url').'#/projects/detail/'. $projectAttachment->project_id;
            foreach ($users as $key => $value) {
                $message = $email_template->template_body;
                $subject = $email_template->template_subject;

                $posted_by = str_replace("{UPLOADED_BY}", $logginUser->firstname.' '.$logginUser->lastname, $message);
                $project_name = str_replace("{PROJECT_NAME}", $value->project_name, $posted_by);
                $site_url = str_replace("{PROJECT_URL}", $url, $project_name);
                $message = str_replace("{SITE_NAME}", config('core.COMPANY_NAME'), $site_url);

                $this->_sendEmailsInQueue(
                    $value->email,
                    $value->name,
                    $subject,
                    $message
                );
            }
        }
    }

    /**
     * Get project users.
     *
     * @param Int $projectId [Project id]
     *
     * @return Object
     */
    private function _getAssignProjectUsers($projectId)
    {
        $pu_table = config('core.acl.project_user_table');
        $project_table = config('core.acl.projects_table');
        $user_table = config('core.acl.users_table');
        $result = [];

        $project = \DB::table($pu_table)
            ->select(
                $project_table.'.project_name',
                $project_table.'.id',
                $user_table.'.id as userId',
                $user_table.'.firstname',
                $user_table.'.lastname',
                \DB::raw("CONCAT($user_table.firstname,' ',$user_table.lastname) as name"),
                $user_table.'.email'
            )
            ->join($project_table, $pu_table.'.project_id', '=', $project_table.'.id')
            ->join($user_table, $pu_table.'.user_id', '=', $user_table.'.id')
            ->where($project_table.'.id', $projectId)
            ->get();

        return $project;
    }

    /**
     * Send task/subtask assign email.
     *
     * @param Array  $users   [Users array]
     * @param Int    $taskId  [Task id]
     * @param String $subject [Subject]
     * @param String $task    [Task name]
     * @param String $name    [Sender name]
     *
     * @return Boolean
     */
    public function sendTaskAssignUserEmail($users, $taskId, $subject, $task, $name)
    {
        $email_template = EmailTemplate::where('type', 'task_assigned')->first();
        if (!empty($email_template)) {
            $url = config('app.front_url').'#/tasks/detail/'.$taskId;
            foreach ($users as $key => $value) {
                $message = $email_template->template_body;

                $site_name = str_replace("{SITE_NAME}", config('core.COMPANY_NAME'), $message);
                $site_url = str_replace("{TASK_NAME}", $task, $site_name);
                $assign_by = str_replace("{ASSIGNED_BY}", $name, $site_url);
                $message = str_replace("{TASK_URL}", $url, $assign_by);
                $this->_sendEmailsInQueue(
                    $value['email'],
                    $name,
                    $subject,
                    $message
                );
            }
        }
        return true;
    }

    /**
     * Send task assign user comment.
     *
     * @param Object $logginUser  [Login user object]
     * @param Object $taskComment [Task comment object]
     *
     * @return Boolean
     */
    public function sendTaskCommentMail($logginUser, $taskComment)
    {
        $url = config('app.front_url').'#/tasks/detail/'. $taskComment->task_id;
        $users = $this->_getAssignTaskUsers($taskComment->task_id);
        $email_template = EmailTemplate::where('type', 'task_comments')->first();
        if (!empty($email_template)) {
            foreach ($users as $key => $value) {
                $message = $email_template->template_body;
                $subject = $email_template->template_subject;

                $posted_by = str_replace("{POSTED_BY}", $logginUser->firstname.' '.$logginUser->lastname, $message);
                $project_name = str_replace("{TASK_NAME}", $value->task_name, $posted_by);
                $site_url = str_replace("{COMMENT_URL}", $url, $project_name);
                $comment = str_replace("{COMMENT_MESSAGE}", $taskComment->comment, $site_url);
                $message = str_replace("{SITE_NAME}", config('core.COMPANY_NAME'), $comment);
                
                $this->_sendEmailsInQueue(
                    $value->email,
                    $value->name,
                    $subject,
                    $message
                );
            }
        }
    }

    /**
     * Send task assign user attachment.
     *
     * @param Object $logginUser     [Login user object]
     * @param Object $taskAttachment [Task attachment object]
     *
     * @return Boolean
     */
    public function sendTaskAttachmentMail($logginUser, $taskAttachment)
    {
        $url = config('app.front_url').'#/tasks/detail/'. $taskAttachment->task_id;
        $users = $this->_getAssignTaskUsers($taskAttachment->task_id);
        $email_template = EmailTemplate::where('type', 'task_attachment')->first();
        
        if (!empty($email_template)) {
            foreach ($users as $key => $value) {
                $message = $email_template->template_body;
                $subject = $email_template->template_subject;

                $posted_by = str_replace("{UPLOADED_BY}", $logginUser->firstname.' '.$logginUser->lastname, $message);
                $project_name = str_replace("{TASK_NAME}", $value->task_name, $posted_by);
                $site_url = str_replace("{TASK_URL}", $url, $project_name);
                $message = str_replace("{SITE_NAME}", config('core.COMPANY_NAME'), $site_url);
                
                $this->_sendEmailsInQueue(
                    $value->email,
                    $value->name,
                    $subject,
                    $message
                );
            }
        }
    }

    /**
     * Get assign task users.
     *
     * @param Int $taskId [Task id]
     *
     * @return Object
     */
    private function _getAssignTaskUsers($taskId)
    {
        $task_user_table = config('core.acl.task_user_table');
        $task_table = config('core.acl.task_table');
        $user_table = config('core.acl.users_table');
        $result = [];

        $users = \DB::table($task_user_table)
            ->select(
                $task_table.'.name as task_name',
                $task_table.'.id',
                $user_table.'.id as userId',
                \DB::raw("CONCAT($user_table.firstname,' ',$user_table.lastname) as name"),
                $user_table.'.email'
            )
            ->join($task_table, $task_user_table.'.task_id', '=', $task_table.'.id')
            ->join($user_table, $task_user_table.'.user_id', '=', $user_table.'.id')
            ->where($task_table.'.id', $taskId)
            ->get();

        return $users;
    }

    /**
     * Send task assign email.
     *
     * @param Object $users      [Users object]
     * @param String $name       [Sender name]
     * @param String $subject    [Defect subject]
     * @param Int    $defectId   [Defect id]
     * @param String $defectName [Defect name]
     *
     * @return Boolean
     */
    public function sendDefectAssignMails($users, $name, $subject, $defectId, $defectName)
    {
        $email_template = EmailTemplate::where('type', 'defect_assigned')->first();

        if (!empty($email_template)) {
            $url = config('app.front_url').'#/defects/detail/'.$defectId;
            foreach ($users as $key => $value) {
                $message = $email_template->template_body;

                $site_name = str_replace("{SITE_NAME}", config('core.COMPANY_NAME'), $message);
                $defect_title = str_replace("{DEFECT_TITLE}", $defectName, $site_name);
                $assign_by = str_replace("{ASSIGNED_BY}", $name, $defect_title);
                $message = str_replace("{DEFECT_URL}", $url, $assign_by);

                $this->_sendEmailsInQueue(
                    $value['email'],
                    $name,
                    $subject,
                    $message
                );
            }
        }
        return true;
    }

    /**
     * Send defects change status mail.
     *
     * @param Object $logginUser [Login user object]
     * @param Object $defect     [Defect object]
     *
     * @return Boolean
     */
    public function sendDefectStatusChangeMails($logginUser, $defect)
    {
        $status_list = [
            1 => 'Assigned',
            2 => 'Closed',
            3 => 'In Progress',
            4 => 'Open',
            5 => 'Solved',
            6 => 'Re-open',
            7 => 'Deferred',
        ];
        $url = config('app.front_url').'#/defects/detail/'. $defect->id;
        $users = $this->_getAssignDefectsUsers($defect->id);
        $email_template = EmailTemplate::where('type', 'defect_updated')->first();

        if (!empty($email_template)) {
            foreach ($users as $key => $value) {
                $message = $email_template->template_body;
                $subject = $email_template->template_subject;

                $defect_name = str_replace("{DEFECT_TITLE}", $defect->defect_name, $message);
                $bug_status = str_replace("{STATUS}", $status_list[$defect->status], $defect_name);
                $assigned_by = str_replace("{MARKED_BY}", ucfirst($logginUser->firstname.' '.$logginUser->lastname), $bug_status);
                $site_url = str_replace("{DEFECT_URL}", $url, $assigned_by);
                $message = str_replace("{SITE_NAME}", config('core.COMPANY_NAME'), $site_url);

                $this->_sendEmailsInQueue(
                    $value->email,
                    $value->name,
                    $subject,
                    $message
                );
            }
        }
    }

    /**
     * Send defects assign user comment.
     *
     * @param Object $logginUser    [Login user object]
     * @param Object $defectComment [Defect comment object]
     *
     * @return Boolean
     */
    public function sendDefectCommentMail($logginUser, $defectComment)
    {
        $url = config('app.front_url').'#/defects/detail/'. $defectComment->defect_id;
        $users = $this->_getAssignDefectsUsers($defectComment->defect_id);
        $email_template = EmailTemplate::where('type', 'defect_comments')->first();

        if (!empty($email_template)) {
            foreach ($users as $key => $value) {
                $message = $email_template->template_body;
                $subject = $email_template->template_subject;

                $posted_by = str_replace("{POSTED_BY}", $logginUser->firstname.' '.$logginUser->lastname, $message);
                $defect_name = str_replace("{DEFECT_TITLE}", $value->defect_name, $posted_by);
                $site_url = str_replace("{COMMENT_URL}", $url, $defect_name);
                $comment = str_replace("{COMMENT_MESSAGE}", $defectComment->comment, $site_url);
                $message = str_replace("{SITE_NAME}", config('core.COMPANY_NAME'), $comment);

                $this->_sendEmailsInQueue(
                    $value->email,
                    $value->name,
                    $subject,
                    $message
                );
            }
        }
    }

    /**
     * Send defects assign user attachment.
     *
     * @param Object $logginUser       [Login user object]
     * @param Object $defectAttachment [Defect Attachment Object]
     *
     * @return Boolean
     */
    public function sendDefectAttachmentMail($logginUser, $defectAttachment)
    {
        $url = config('app.front_url').'#/defects/detail/'. $defectAttachment->defect_id;
        $users = $this->_getAssignDefectsUsers($defectAttachment->defect_id);
        $email_template = EmailTemplate::where('type', 'defect_attachment')->first();

        if (!empty($email_template)) {
            foreach ($users as $key => $value) {
                $message = $email_template->template_body;
                $subject = $email_template->template_subject;

                $posted_by = str_replace("{UPLOADED_BY}", $logginUser->firstname.' '.$logginUser->lastname, $message);
                $defect_name = str_replace("{DEFECT_TITLE}", $value->defect_name, $posted_by);
                $site_url = str_replace("{DEFECT_URL}", $url, $defect_name);
                $message = str_replace("{SITE_NAME}", config('core.COMPANY_NAME'), $site_url);

                $this->_sendEmailsInQueue(
                    $value->email,
                    $value->name,
                    $subject,
                    $message
                );
            }
        }
    }

    /**
     * Get assign defect users.
     *
     * @param Int $defectId [Defect Id]
     *
     * @return Object
     */
    private function _getAssignDefectsUsers($defectId)
    {
        $defect_user_table = config('core.acl.defects_user_table');
        $defects_table = config('core.acl.defects_table');
        $user_table = config('core.acl.users_table');
        $result = [];

        $defects = \DB::table($defect_user_table)
            ->select(
                $defects_table.'.id',
                $defects_table.'.defect_name',
                $user_table.'.id as userId',
                \DB::raw("CONCAT($user_table.firstname,' ',$user_table.lastname) as name"),
                $user_table.'.email'
            )
            ->join($defects_table, $defect_user_table.'.defect_id', '=', $defects_table.'.id')
            ->join($user_table, $defect_user_table.'.user_id', '=', $user_table.'.id')
            ->where($defects_table.'.id', $defectId)
            ->get();

        return $defects;
    }

    /**
     * Send incident assign email.
     *
     * @param Array  $users        [Users array]
     * @param String $name         [Login user name]
     * @param String $subject      [Email subject]
     * @param String $url          [URL]
     * @param String $incidentName [Incident name]
     *
     * @return Boolean
     */
    public function sendIncidentAssignMails($users, $name, $subject, $url, $incidentName)
    {
        $email_template = EmailTemplate::where('type', 'incident_assigned')->first();
        if (!empty($email_template)) {
            foreach ($users as $key => $value) {
                $message = $email_template->template_body;

                $site_name = str_replace("{SITE_NAME}", config('core.COMPANY_NAME'), $message);
                $defect_title = str_replace("{INCIDENT_TITLE}", $incidentName, $site_name);
                $assign_by = str_replace("{ASSIGNED_BY}", $name, $defect_title);
                $message = str_replace("{INCIDENT_URL}", $url, $assign_by);

                $this->_sendEmailsInQueue(
                    $value['email'],
                    $name,
                    $subject,
                    $message
                );
            }
        }

        return true;
    }

    /**
     * Send incidents comment mail.
     *
     * @param Object $logginUser      [Login user object]
     * @param Object $incidentComment [Incident comment]
     *
     * @return Boolean
     */
    public function sendIncidentCommentMail($logginUser, $incidentComment)
    {
        $url = config('app.front_url').'#/incidents/detail/'. $incidentComment->incident_id;
        $incidentUsers = $this->_getAssignIncidentUsers($incidentComment->incident_id);
        $email_template = EmailTemplate::where('type', 'incident_comments')->first();

        if (!empty($email_template) && !empty($incidentUsers)) {
            $incidentName = $incidentUsers->incident_name;
            foreach ($incidentUsers->users as $key => $value) {
                $message = $email_template->template_body;
                $subject = $email_template->template_subject;

                $posted_by = str_replace("{POSTED_BY}", $logginUser->firstname.' '.$logginUser->lastname, $message);
                $incident_name = str_replace("{INCIDENT_TITLE}", $incidentName, $posted_by);
                $site_url = str_replace("{COMMENT_URL}", $url, $incident_name);
                $comment = str_replace("{COMMENT_MESSAGE}", $incidentComment->comment, $site_url);
                $message = str_replace("{SITE_NAME}", config('core.COMPANY_NAME'), $comment);

                $this->_sendEmailsInQueue(
                    $value->email,
                    $value->name,
                    $subject,
                    $message
                );
            }
        }
    }

    /**
     * Send incidents assign user attachment.
     *
     * @param Object $logginUser         [Login user object]
     * @param Object $incidentAttachment [Incident Attachment]
     *
     * @return Boolean
     */
    public function sendIncidentAttachmentMail($logginUser, $incidentAttachment)
    {
        $url = config('app.front_url').'#/incidents/detail/'. $incidentAttachment->incident_id;
        $incidentUsers = $this->_getAssignIncidentUsers($incidentAttachment->incident_id);
        $email_template = EmailTemplate::where('type', 'incident_attachment')->first();

        if (!empty($email_template) && !empty($incidentUsers)) {
            $incidentName = $incidentUsers->incident_name;
            foreach ($incidentUsers->users as $key => $value) {
                $message = $email_template->template_body;
                $subject = $email_template->template_subject;

                $posted_by = str_replace("{UPLOADED_BY}", $logginUser->firstname.' '.$logginUser->lastname, $message);
                $incident_name = str_replace("{INCIDENT_TITLE}", $incidentName, $posted_by);
                $site_url = str_replace("{INCIDENT_URL}", $url, $incident_name);
                $message = str_replace("{SITE_NAME}", config('core.COMPANY_NAME'), $site_url);

                $this->_sendEmailsInQueue(
                    $value->email,
                    $value->name,
                    $subject,
                    $message
                );
            }
        }
    }

    /**
     * Get assign incident users.
     *
     * @param Int $incidentId [Row id]
     *
     * @return Object
     */
    private function _getAssignIncidentUsers($incidentId)
    {
        $incidents = Incident::with(
            ['users' => function ($query) {
                $query->select(
                    'id',
                    \DB::raw("CONCAT(firstname,' ',lastname) as name"),
                    'email'
                );
            }]
        )
        ->where('id', $incidentId)
        ->first();
        return $incidents;
    }
}
