<?php

namespace Modules\ProjectComment\Repositories;

use Modules\ProjectComment\Models\ProjectComment;
use Modules\Projects\Models\ProjectUser;
use Modules\Helper\Helpers\UploadFileHelper;
use Modules\Helper\Helpers\EmailsHelper;

use Auth;

/**
 * Class ProjectCommentRepository
 *
 * ProjectComment create, update, delete and view.
 *
 * PHP version 7.1.3
 *
 * @category  PM
 * @package   Modules\ProjectComment
 * @author    Vipul Patel <vipul@chetsapp.com>
 * @copyright 2019 Chetsapp Group
 * @license   Chetsapp Private Limited
 * @version   Release: @1.0@
 * @link      http://chetsapp.com
 * @since     Class available since Release 1.0
 */
class ProjectCommentRepository
{
    protected $uploadHelper;
    protected $emailsHelper;

    /**
     * Instantiate a new repository instance.
     *
     * @param UploadFileHelper $uploadHelper [Object]
     * @param EmailsHelper     $emailsHelper [Object]
     *
     * @return void
     */
    public function __construct(
        UploadFileHelper $uploadHelper,
        EmailsHelper $emailsHelper
    ) {
        $this->uploadHelper = $uploadHelper;
        $this->emailsHelper = $emailsHelper;
    }

    /**
     * Display a listing of the resource.
     *
     * @return object
     */
    public function findAll()
    {
        return ProjectComment::with('user')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request [Request for create project comment]
     *
     * @return Boolean
     */
    public function create($request)
    {
        $input = $request->all();
        $logginUser = Auth::user();
        $projectcomment = new ProjectComment;

        // --
        // Comment attachments
        if (isset($input['files'])) {
            $projectcomment->attachments = $this->uploadHelper
                ->uploadMultipleAttachment(
                    $input['files'],
                    'project_attachment'
                );
        }

        $projectcomment->user_id = Auth::user()->id;
        $projectcomment->fill($input);
        if ($projectcomment->save()) {
            // --
            // Send comment mail.
            $this->emailsHelper->sendProjectCommentMail(
                $logginUser,
                $projectcomment
            );
            // --
            // Add activities
            createUserActivity(
                ProjectComment::MODULE_NAME,
                $projectcomment->id,
                $request->method(),
                $projectcomment->comment,
                $request->ip()
            );
            return true;
        } else {
            return false;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request [Request for delete project comment]
     * @param int     $id      [Row id]
     *
     * @return Boolen
     */
    public function delete($request, $id)
    {
        $projectcomment = ProjectComment::findOrFail($id);
        if ($projectcomment->delete()) {
            // --
            // Remove file.
            if (!empty($projectcomment)) {
                if (!empty($projectcomment->attachments)) {
                    $this->_removeAttachments(
                        json_decode($projectcomment->attachments)
                    );
                }
                // --
                // Remove child comment.
                if ($projectcomment->parent_id == 0) {
                    $childComments = ProjectComment::where('parent_id', $id)
                        ->delete();
                }
            }
            // --
            // Add activities
            createUserActivity(
                ProjectComment::MODULE_NAME,
                $projectcomment->id,
                $request->method(),
                $projectcomment->comment,
                $request->ip()
            );
            return true;
        }
        return false;
    }

    /**
     * Remove the attachments from storage.
     *
     * @param array $attachments [Attachments array]
     *
     * @return void
     */
    private function _removeAttachments($attachments)
    {
        foreach ($attachments as $key => $value) {
            $files = public_path() .'/uploads/project_attachment/'. $value->file;
            if (file_exists($files)) {
                unlink($files);
            }
        }
    }

    /**
     * Check user permission.
     *
     * @param Int    $id [Project Comment Id]
     * @param String $action     [Action For]
     *
     * @return Boolean
     */
    public function checkPermission($id, $action)
    {
        $user = Auth::user();
        if ($user->hasRole('admin') || $user->is_super_admin) {
            return true;
        }

        $projectcomment = ProjectComment::findOrFail($id);
        $projectUser = ProjectUser::where('project_id', $projectcomment->project_id)
            ->where('user_id', Auth::user()->id)
            ->where($action, true)
            ->first();

        if ($projectUser) {
            return true;
        }
        return false;
    }
}
