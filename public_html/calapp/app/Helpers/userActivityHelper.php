<?php
/**
 * Global user activity helper function.
 *
 * PHP version 7.1.3
 *
 * @category Helper
 * @package  Modules\Helper
 * @author   Vipul Patel <vipul@chetsapp.com>
 * @license
 * @link
 */

use Modules\UserActivity\Models\UserActivity;

/**
 * Create user activity descriptions
 *
 * @param string  $moduleName
 * @param string  $requestMethod
 * @param string  $description
 * @param boolean $isStatusChanged
 *
 * @return string
 */
function createActivityDescription(
    $moduleName,
    $requestMethod,
    $description,
    $isStatusChanged
) {
    switch ($requestMethod) {
    case 'POST':
        $requestMethod = 'added';
        break;
    case 'PUT':
        $requestMethod = 'updated';
        break;
    case 'DELETE':
        $requestMethod = 'deleted';
        break;
    default:
        $requestMethod = 'added';
    }

    if ($isStatusChanged) {
        return $moduleName." Status changed : " .'<b>'.$description.'</b>';
    }
    return $moduleName." informantion ".$requestMethod." : ".'<b>'.$description.'</b>';
}

/**
 * Create user activity
 *
 * @param string  $moduleName
 * @param string  $moduleFieldId
 * @param string  $requestMethod
 * @param string  $description
 * @param string  $clientIp
 * @param boolean $isStatusChanged
 * @param string  $status
 *
 * @return string
 */
function createUserActivity(
    $moduleName,
    $moduleFieldId,
    $requestMethod,
    $description,
    $clientIp,
    $isStatusChanged = false,
    $status = ''
) {
    $data = array();
    $user = Auth::user();
    $description = createActivityDescription(
        $moduleName,
        $requestMethod,
        $description,
        $isStatusChanged
    );
    $userActivities = new UserActivity;
    $data['module'] = $moduleName;
    $data['module_field_id'] = $moduleFieldId;
    $data['status'] = $status;
    $data['action'] = $requestMethod;
    $data['description'] = $description;
    $data['ip_address'] = $clientIp;
    $userActivities->fill($data);
    $user->userActivity()->save($userActivities);
}
