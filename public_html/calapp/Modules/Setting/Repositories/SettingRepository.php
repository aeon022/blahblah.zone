<?php

namespace Modules\Setting\Repositories;

use Modules\Setting\Models\Setting;
use Modules\Helper\Helpers\UploadFileHelper;
use Illuminate\Support\Facades\Artisan;
use File;

use Auth;

/**
 * Class SettingRepository
 *
 * Setting create, update and view.
 *
 * PHP version 7.1.3
 *
 * @category  Administration
 * @package   Modules\Setting
 * @author    Vipul Patel <vipul@chetsapp.com>
 * @copyright 2019 Chetsapp Group
 * @license   Chetsapp Private Limited
 * @version   Release: @1.0@
 * @link      http://chetsapp.com
 * @since     Class available since Release 1.0
 */
class SettingRepository
{
	protected $fileHelper;

	/**
	 * Instantiate a new controller instance.
	 *
	 * @param UploadFileHelper $fileHelper [Object]
	 *
	 * @return void
	 */
	public function __construct(
		UploadFileHelper $fileHelper
	) {
		$this->fileHelper = $fileHelper;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return object
	 */
	public function findAll()
	{
		return Setting::first();
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request [Request for create user setting]
	 *
	 * @return Boolean
	 */
	public function create($request)
	{
		if (!empty(Auth::user())) {
			$user = Auth::user();
			$setting = Setting::first();
			if(empty($setting)){
				$setting = new Setting();
			}
			$input = $request->all();
			if( isset($input['form_for']) && $input['form_for'] == 'theme_setting'){
				$themeSettings = $input["settings_images"];
				//--
				// Upload login backgound
				if (array_key_exists('login_background',$themeSettings)) {
					$input['login_background'] = $this->fileHelper->uploadImage(
						'login_bg',
						$themeSettings['login_background']
					);
					if( !empty($setting->login_background) && $setting->login_background != $input['login_background']){
						$this->fileHelper->deleteFile('login_bg',$setting->login_background);
					}
				}
				// --
				// Upload company logo
				if (array_key_exists('company_logo',$themeSettings)) {
					$input['company_logo'] = $this->fileHelper->uploadImage(
						'company_logo',
						$themeSettings['company_logo']
					);
					if( !empty($setting->company_logo) && $setting->company_logo != $input['company_logo']){
						$this->fileHelper->deleteFile('company_logo',$setting->company_logo);
					}
				}
				// --
				// Upload company sidebar logo
				if(array_key_exists('company_sidebar_logo',$themeSettings)){					
					$input['company_sidebar_logo'] = $this->fileHelper->uploadImage(
						'company_sidebar_logo',
						$themeSettings['company_sidebar_logo']
					);
					if( !empty($setting->company_sidebar_logo) && $setting->company_sidebar_logo != $input['company_sidebar_logo']){
						$this->fileHelper->deleteFile('company_sidebar_logo',$setting->company_sidebar_logo);
					}
				}
				// --
				// Upload sidear background images
				$tempImgArr = [];
				if (isset($themeSettings['sidebar_background_images_obj']) && !empty($themeSettings['sidebar_background_images_obj'])) {
					
					foreach ($themeSettings['sidebar_background_images_obj'] as $value) {
						$imageName = $this->fileHelper->uploadImage(
							'sidebar_background_images',
							$value["file"]
						);
						array_push($tempImgArr, $imageName);
					}
				}
				if(isset($themeSettings['sidebar_background_images']) && !empty($themeSettings['sidebar_background_images'])){
					$tempImgArr = array_merge($tempImgArr,$themeSettings['sidebar_background_images']);
				}
				// --
				// Delete sidebar background images.
				if(File::isDirectory(public_path('uploads/sidebar_background_images'))){
					$files = File::files(public_path('uploads/sidebar_background_images'));
					foreach ($files as $file) {
						if(!in_array($file->getFilename(), $tempImgArr)){
							File::delete(public_path('uploads/sidebar_background_images/'.$file->getFilename()));
						}
					}
			    }

				if(!empty($tempImgArr)){
					$input['sidebar_background_images'] = json_encode($tempImgArr);
				}else{
					$input['sidebar_background_images'] = null;
				}
			}

			$setting->fill($input)->save();

			if (!empty($setting)) {
				
				if (isset($input['form_for'])) {
					// --
					// Set system settings
					if ($input['form_for'] == 'system_setting') {
						// --
						// Set timezone
						if (isset($input['timezone'])) {
							setEnv(
								'TIMEZONE',
								$input['timezone'],
								config('app.timezone')
							);
						}

						// --
						// Set local
						if (isset($input['default_locale'])) {
							setEnv(
								'LOCALE',
								$input['default_locale'],
								config('app.locale')
							);
						}
						Artisan::call('config:cache');
					}
					// --
					// Set email settings
					if ($input['form_for'] == 'email_setting') {
						if (isset($input['smtp_protocol'])) {
							setEnv(
								'MAIL_DRIVER',
								$input['smtp_protocol'],
								config('mail.driver')
							);
						}
						if (isset($input['smtp_host'])) {
							setEnv(
								'MAIL_HOST',
								$input['smtp_host'],
								config('mail.host')
							);
						}
						if (isset($input['smtp_port'])) {
							setEnv(
								'MAIL_PORT',
								$input['smtp_port'],
								config('mail.port')
							);
						}
						if (isset($input['smtp_encryption'])) {
							setEnv(
								'MAIL_ENCRYPTION',
								$input['smtp_encryption'],
								config('mail.encryption')
							);
						}
						if (isset($input['smtp_user'])) {
							setEnv(
								'MAIL_USERNAME',
								$input['smtp_user'],
								config('mail.username')
							);
						}
						if (isset($input['smtp_password'])) {
							setEnv(
								'MAIL_PASSWORD',
								$input['smtp_password'],
								config('mail.password')
							);
						}
						if (isset($input['company_from_email'])) {
							setEnv(
								'MAIL_FROM_ADDRESS',
								$input['company_from_email'],
								config('mail.from.address')
							);
						}
						if (isset($input['mailgun_domain'])) {
							setEnv(
								'MAILGUN_DOMAIN',
								$input['mailgun_domain'],
								config('services.mailgun.domain')
							);
						}
						if (isset($input['mailgun_secret'])) {
							setEnv(
								'MAILGUN_SECRET',
								$input['mailgun_secret'],
								config('services.mailgun.secret')
							);
						}
						if (isset($input['sparkpost_secret'])) {
							setEnv(
								'SPARKPOST_SECRET',
								$input['sparkpost_secret'],
								config('services.sparkpost.secret')
							);
						}
						if (isset($input['mandrill_secret'])) {
							setEnv(
								'MANDRILL_SECRET',
								$input['mandrill_secret'],
								config('services.mandrill.secret')
							);
						}
						Artisan::call('config:cache');
					}
				}

				// --
				// Add activities
				createUserActivity(
					Setting::MODULE_NAME,
					$setting->id,
					$request->method(),
					$setting->company_name,
					$request->ip()
				);
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}
