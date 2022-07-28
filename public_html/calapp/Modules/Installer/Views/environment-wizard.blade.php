@extends('theme::layouts.master')

@section('template_title')
{{ trans('installer_messages.environment.wizard.templateTitle') }}
@endsection

@section('title')
<i class="fa fa-magic fa-fw" aria-hidden="true"></i>
{!! trans('installer_messages.environment.wizard.title') !!}
@endsection

@section('container')
<div class="tabs tabs-full">
	@if($bug_error != '')
		<div class="alert alert-danger text-center">{{$bug_error}}</div>
	@endif

	<input id="tab1" type="radio" name="tabs" class="tab-input" checked />
	<label for="tab1" class="tab-label">
		<i class="fa fa-cog fa-2x fa-fw" aria-hidden="true"></i>
		<br />
		{{ trans('installer_messages.environment.wizard.tabs.environment') }}
	</label>

	<input id="tab2" type="radio" name="tabs" class="tab-input" />
	<label for="tab2" class="tab-label">
		<i class="fa fa-database fa-2x fa-fw" aria-hidden="true"></i>
		<br />
		{{ trans('installer_messages.environment.wizard.tabs.database') }}
	</label>

	<input id="tab3" type="radio" name="tabs" class="tab-input" />
	<label for="tab3" class="tab-label">
		<i class="fa fa-cogs fa-2x fa-fw" aria-hidden="true"></i>
		<br />
		{{ trans('installer_messages.environment.wizard.tabs.application') }}
	</label>
	<form method="post" action="{{ route('LaravelInstaller::environmentSaveWizard') }}" class="tabs-wrap" onsubmit="ShowLoading()">
		<div class="tab" id="tab1content">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">

			<div class="form-group {{ $errors->has('app_name') ? ' has-error ' : '' }}">
				<label for="app_name">
					{{ trans('installer_messages.environment.wizard.form.app_name_label') }}
					<span class="text-danger">&nbsp;*</span>
				</label>
				<input type="text" name="app_name" class="form-control" id="app_name" value="{{@$request['app_name']}}" placeholder="{{ trans('installer_messages.environment.wizard.form.app_name_placeholder') }}" />
				@if ($errors->has('app_name'))
				<span class="error-block">
					<i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
					{{ $errors->first('app_name') }}
				</span>
				@endif
			</div>

			<div class="form-group {{ $errors->has('site_email') ? ' has-error ' : '' }}">
				<label for="site_email">Site Email<span class="text-danger">&nbsp;*</span></label>
				<input type="text" name="site_email" class="form-control" id="site_email" placeholder="info@example.com" value="{{@$request['site_email']}}" />
				@if ($errors->has('site_email'))
				<span class="error-block">
					<i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
					{{ $errors->first('site_email') }}
				</span>
				@endif
			</div>

			<div class="form-group {{ $errors->has('support_email') ? ' has-error ' : '' }}">
				<label for="support_email">Support Email<span class="text-danger">&nbsp;*</span></label>
				<input type="text" name="support_email" class="form-control" id="support_email" placeholder="support@example.com" value="{{@$request['support_email']}}" />
				@if ($errors->has('support_email'))
				<span class="error-block">
					<i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
					{{ $errors->first('support_email') }}
				</span>
				@endif
			</div>

			<div class="form-group {{ $errors->has('timezone') ? ' has-error ' : '' }}">
				<label for="timezone">Timezone<span class="text-danger">&nbsp;*</span></label>
				<select name="timezone" id="timezone" class="select2">
					<option value="">Select Timezone</option>
					<option value="Pacific/Midway">(UTC-11:00) Midway Island</option>
					<option value="Pacific/Samoa">(UTC-11:00) Samoa</option>
					<option value="Pacific/Honolulu">(UTC-10:00) Hawaii</option>
					<option value="US/Alaska">(UTC-09:00) Alaska</option>
					<option value="America/Los_Angeles">(UTC-08:00) Pacific Time (US &amp; Canada)</option>
					<option value="America/Tijuana">(UTC-08:00) Tijuana</option>
					<option value="US/Arizona">(UTC-07:00) Arizona</option>
					<option value="America/Chihuahua">(UTC-07:00) Chihuahua</option>
					<option value="America/Chihuahua">(UTC-07:00) La Paz</option>
					<option value="America/Mazatlan">(UTC-07:00) Mazatlan</option>
					<option value="US/Mountain">(UTC-07:00) Mountain Time (US &amp; Canada)</option>
					<option value="America/Managua">(UTC-06:00) Central America</option>
					<option value="US/Central">(UTC-06:00) Central Time (US &amp; Canada)</option>
					<option value="America/Mexico_City">(UTC-06:00) Guadalajara</option>
					<option value="America/Mexico_City">(UTC-06:00) Mexico City</option>
					<option value="America/Monterrey">(UTC-06:00) Monterrey</option>
					<option value="Canada/Saskatchewan">(UTC-06:00) Saskatchewan</option>
					<option value="America/Bogota">(UTC-05:00) Bogota</option>
					<option value="US/Eastern">(UTC-05:00) Eastern Time (US &amp; Canada)</option>
					<option value="US/East-Indiana">(UTC-05:00) Indiana (East)</option>
					<option value="America/Lima">(UTC-05:00) Lima</option>
					<option value="America/Bogota">(UTC-05:00) Quito</option>
					<option value="Canada/Atlantic">(UTC-04:00) Atlantic Time (Canada)</option>
					<option value="America/Caracas">(UTC-04:30) Caracas</option>
					<option value="America/La_Paz">(UTC-04:00) La Paz</option>
					<option value="America/Santiago">(UTC-04:00) Santiago</option>
					<option value="Canada/Newfoundland">(UTC-03:30) Newfoundland</option>
					<option value="America/Sao_Paulo">(UTC-03:00) Brasilia</option>
					<option value="America/Argentina/Buenos_Aires">(UTC-03:00) Buenos Aires</option>
					<option value="America/Argentina/Buenos_Aires">(UTC-03:00) Georgetown</option>
					<option value="America/Godthab">(UTC-03:00) Greenland</option>
					<option value="America/Noronha">(UTC-02:00) Mid-Atlantic</option>
					<option value="Atlantic/Azores">(UTC-01:00) Azores</option>
					<option value="Atlantic/Cape_Verde">(UTC-01:00) Cape Verde Is.</option>
					<option value="Africa/Casablanca">(UTC+00:00) Casablanca</option>
					<option value="Europe/London">(UTC+00:00) Edinburgh</option>
					<option value="Etc/Greenwich">(UTC+00:00) Greenwich Mean Time : Dublin</option>
					<option value="Europe/Lisbon">(UTC+00:00) Lisbon</option>
					<option value="Europe/London">(UTC+00:00) London</option>
					<option value="Africa/Monrovia">(UTC+00:00) Monrovia</option>
					<option value="UTC">(UTC+00:00) UTC</option>
					<option value="Europe/Amsterdam">(UTC+01:00) Amsterdam</option>
					<option value="Europe/Belgrade">(UTC+01:00) Belgrade</option>
					<option value="Europe/Berlin">(UTC+01:00) Berlin</option>
					<option value="Europe/Berlin">(UTC+01:00) Bern</option>
					<option value="Europe/Bratislava">(UTC+01:00) Bratislava</option>
					<option value="Europe/Brussels">(UTC+01:00) Brussels</option>
					<option value="Europe/Budapest">(UTC+01:00) Budapest</option>
					<option value="Europe/Copenhagen">(UTC+01:00) Copenhagen</option>
					<option value="Europe/Ljubljana">(UTC+01:00) Ljubljana</option>
					<option value="Europe/Madrid">(UTC+01:00) Madrid</option>
					<option value="Europe/Paris">(UTC+01:00) Paris</option>
					<option value="Europe/Prague">(UTC+01:00) Prague</option>
					<option value="Europe/Rome">(UTC+01:00) Rome</option>
					<option value="Europe/Sarajevo">(UTC+01:00) Sarajevo</option>
					<option value="Europe/Skopje">(UTC+01:00) Skopje</option>
					<option value="Europe/Stockholm">(UTC+01:00) Stockholm</option>
					<option value="Europe/Vienna">(UTC+01:00) Vienna</option>
					<option value="Europe/Warsaw">(UTC+01:00) Warsaw</option>
					<option value="Africa/Lagos">(UTC+01:00) West Central Africa</option>
					<option value="Europe/Zagreb">(UTC+01:00) Zagreb</option>
					<option value="Europe/Athens">(UTC+02:00) Athens</option>
					<option value="Europe/Bucharest">(UTC+02:00) Bucharest</option>
					<option value="Africa/Cairo">(UTC+02:00) Cairo</option>
					<option value="Africa/Harare">(UTC+02:00) Harare</option>
					<option value="Europe/Helsinki">(UTC+02:00) Helsinki</option>
					<option value="Europe/Istanbul">(UTC+02:00) Istanbul</option>
					<option value="Asia/Jerusalem">(UTC+02:00) Jerusalem</option>
					<option value="Europe/Helsinki">(UTC+02:00) Kyiv</option>
					<option value="Africa/Johannesburg">(UTC+02:00) Pretoria</option>
					<option value="Europe/Riga">(UTC+02:00) Riga</option>
					<option value="Europe/Sofia">(UTC+02:00) Sofia</option>
					<option value="Europe/Tallinn">(UTC+02:00) Tallinn</option>
					<option value="Europe/Vilnius">(UTC+02:00) Vilnius</option>
					<option value="Asia/Baghdad">(UTC+03:00) Baghdad</option>
					<option value="Asia/Kuwait">(UTC+03:00) Kuwait</option>
					<option value="Europe/Minsk">(UTC+03:00) Minsk</option>
					<option value="Africa/Nairobi">(UTC+03:00) Nairobi</option>
					<option value="Asia/Riyadh">(UTC+03:00) Riyadh</option>
					<option value="Europe/Volgograd">(UTC+03:00) Volgograd</option>
					<option value="Asia/Tehran">(UTC+03:30) Tehran</option>
					<option value="Asia/Muscat">(UTC+04:00) Abu Dhabi</option>
					<option value="Asia/Baku">(UTC+04:00) Baku</option>
					<option value="Europe/Moscow">(UTC+04:00) Moscow</option>
					<option value="Asia/Muscat">(UTC+04:00) Muscat</option>
					<option value="Europe/Moscow">(UTC+04:00) St. Petersburg</option>
					<option value="Asia/Tbilisi">(UTC+04:00) Tbilisi</option>
					<option value="Asia/Yerevan">(UTC+04:00) Yerevan</option>
					<option value="Asia/Kabul">(UTC+04:30) Kabul</option>
					<option value="Asia/Karachi">(UTC+05:00) Islamabad</option>
					<option value="Asia/Karachi">(UTC+05:00) Karachi</option>
					<option value="Asia/Tashkent">(UTC+05:00) Tashkent</option>
					<option value="Asia/Calcutta">(UTC+05:30) Chennai</option>
					<option value="Asia/Kolkata">(UTC+05:30) Kolkata</option>
					<option value="Asia/Calcutta">(UTC+05:30) Mumbai</option>
					<option value="Asia/Calcutta">(UTC+05:30) New Delhi</option>
					<option value="Asia/Calcutta">(UTC+05:30) Sri Jayawardenepura</option>
					<option value="Asia/Katmandu">(UTC+05:45) Kathmandu</option>
					<option value="Asia/Almaty">(UTC+06:00) Almaty</option>
					<option value="Asia/Dhaka">(UTC+06:00) Astana</option>
					<option value="Asia/Dhaka">(UTC+06:00) Dhaka</option>
					<option value="Asia/Yekaterinburg">(UTC+06:00) Ekaterinburg</option>
					<option value="Asia/Rangoon">(UTC+06:30) Rangoon</option>
					<option value="Asia/Bangkok">(UTC+07:00) Bangkok</option>
					<option value="Asia/Bangkok">(UTC+07:00) Hanoi</option>
					<option value="Asia/Jakarta">(UTC+07:00) Jakarta</option>
					<option value="Asia/Novosibirsk">(UTC+07:00) Novosibirsk</option>
					<option value="Asia/Hong_Kong">(UTC+08:00) Beijing</option>
					<option value="Asia/Chongqing">(UTC+08:00) Chongqing</option>
					<option value="Asia/Hong_Kong">(UTC+08:00) Hong Kong</option>
					<option value="Asia/Krasnoyarsk">(UTC+08:00) Krasnoyarsk</option>
					<option value="Asia/Kuala_Lumpur">(UTC+08:00) Kuala Lumpur</option>
					<option value="Australia/Perth">(UTC+08:00) Perth</option>
					<option value="Asia/Singapore">(UTC+08:00) Singapore</option>
					<option value="Asia/Taipei">(UTC+08:00) Taipei</option>
					<option value="Asia/Ulan_Bator">(UTC+08:00) Ulaan Bataar</option>
					<option value="Asia/Urumqi">(UTC+08:00) Urumqi</option>
					<option value="Asia/Irkutsk">(UTC+09:00) Irkutsk</option>
					<option value="Asia/Tokyo">(UTC+09:00) Osaka</option>
					<option value="Asia/Tokyo">(UTC+09:00) Sapporo</option>
					<option value="Asia/Seoul">(UTC+09:00) Seoul</option>
					<option value="Asia/Tokyo">(UTC+09:00) Tokyo</option>
					<option value="Australia/Adelaide">(UTC+09:30) Adelaide</option>
					<option value="Australia/Darwin">(UTC+09:30) Darwin</option>
					<option value="Australia/Brisbane">(UTC+10:00) Brisbane</option>
					<option value="Australia/Canberra">(UTC+10:00) Canberra</option>
					<option value="Pacific/Guam">(UTC+10:00) Guam</option>
					<option value="Australia/Hobart">(UTC+10:00) Hobart</option>
					<option value="Australia/Melbourne">(UTC+10:00) Melbourne</option>
					<option value="Pacific/Port_Moresby">(UTC+10:00) Port Moresby</option>
					<option value="Australia/Sydney">(UTC+10:00) Sydney</option>
					<option value="Asia/Yakutsk">(UTC+10:00) Yakutsk</option>
					<option value="Asia/Vladivostok">(UTC+11:00) Vladivostok</option>
					<option value="Pacific/Auckland">(UTC+12:00) Auckland</option>
					<option value="Pacific/Fiji">(UTC+12:00) Fiji</option>
					<option value="Pacific/Kwajalein">(UTC+12:00) International Date Line West</option>
					<option value="Asia/Kamchatka">(UTC+12:00) Kamchatka</option>
					<option value="Asia/Magadan">(UTC+12:00) Magadan</option>
					<option value="Pacific/Fiji">(UTC+12:00) Marshall Is.</option>
					<option value="Asia/Magadan">(UTC+12:00) New Caledonia</option>
					<option value="Asia/Magadan">(UTC+12:00) Solomon Is.</option>
					<option value="Pacific/Auckland">(UTC+12:00) Wellington</option>
					<option value="Pacific/Tongatapu">(UTC+13:00) Nuku'alofa</option>
				</select>
				@if ($errors->has('timezone'))
				<span class="error-block">
					<i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
					{{ $errors->first('timezone') }}
				</span>
				@endif
			</div>

			<div class="form-group {{ $errors->has('environment') ? ' has-error ' : '' }}">
				<label for="environment">
					{{ trans('installer_messages.environment.wizard.form.app_environment_label') }}
					<span class="text-danger">&nbsp;*</span>
				</label>
				<select name="environment" id="environment" onchange='checkEnvironment(this.value);'>
					<option value="local" selected>{{ trans('installer_messages.environment.wizard.form.app_environment_label_local') }}</option>
					<option value="development">{{ trans('installer_messages.environment.wizard.form.app_environment_label_developement') }}</option>
					<option value="qa">{{ trans('installer_messages.environment.wizard.form.app_environment_label_qa') }}</option>
					<option value="production">{{ trans('installer_messages.environment.wizard.form.app_environment_label_production') }}</option>
					<option value="other">{{ trans('installer_messages.environment.wizard.form.app_environment_label_other') }}</option>
				</select>
				<div id="environment_text_input" style="display: none;">
					<input type="text" name="environment_custom" class="form-control" id="environment_custom" placeholder="{{ trans('installer_messages.environment.wizard.form.app_environment_placeholder_other') }}"/>
				</div>
				@if ($errors->has('environment'))
				<span class="error-block">
					<i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
					{{ $errors->first('environment') }}
				</span>
				@endif
			</div>

			<div class="form-group {{ $errors->has('app_log_level') ? ' has-error ' : '' }}">
				<label for="app_log_level">
					{{ trans('installer_messages.environment.wizard.form.app_log_level_label') }}
					<span class="text-danger">&nbsp;*</span>
				</label>
				<select name="app_log_level" id="app_log_level">
					<option value="debug" selected>{{ trans('installer_messages.environment.wizard.form.app_log_level_label_debug') }}</option>
					<option value="info">{{ trans('installer_messages.environment.wizard.form.app_log_level_label_info') }}</option>
					<option value="notice">{{ trans('installer_messages.environment.wizard.form.app_log_level_label_notice') }}</option>
					<option value="warning">{{ trans('installer_messages.environment.wizard.form.app_log_level_label_warning') }}</option>
					<option value="error">{{ trans('installer_messages.environment.wizard.form.app_log_level_label_error') }}</option>
					<option value="critical">{{ trans('installer_messages.environment.wizard.form.app_log_level_label_critical') }}</option>
					<option value="alert">{{ trans('installer_messages.environment.wizard.form.app_log_level_label_alert') }}</option>
					<option value="emergency">{{ trans('installer_messages.environment.wizard.form.app_log_level_label_emergency') }}</option>
				</select>
				@if ($errors->has('app_log_level'))
				<span class="error-block">
					<i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
					{{ $errors->first('app_log_level') }}
				</span>
				@endif
			</div>

			<div class="form-group radio-group {{ $errors->has('app_debug') ? ' has-error ' : '' }}">
				<label for="app_debug">
					{{ trans('installer_messages.environment.wizard.form.app_debug_label') }}
					<span class="text-danger">&nbsp;*</span>
				</label>
				<div class="radio">
					<input type="radio" name="app_debug" id="app_debug_true" value=true checked />
					<label for="app_debug_true">
						{{ trans('installer_messages.environment.wizard.form.app_debug_label_true') }}
					</label>
				</div>
				<div class="radio">
					<input type="radio" name="app_debug" id="app_debug_false" value=false />
					<label for="app_debug_false">
						{{ trans('installer_messages.environment.wizard.form.app_debug_label_false') }}
					</label>
				</div>
				@if ($errors->has('app_debug'))
				<span class="error-block">
					<i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
					{{ $errors->first('app_debug') }}
				</span>
				@endif
			</div>

			<div class="form-group {{ $errors->has('app_url') ? ' has-error ' : '' }}">
				<label for="app_url">
					{{ trans('installer_messages.environment.wizard.form.app_url_label') }}
					<span class="text-danger">&nbsp;*</span>
					<span class="text-success">This should be your API root URL.</span>
				</label>
				<input type="text" name="app_url" class="form-control" id="app_url" placeholder="{{ trans('installer_messages.environment.wizard.form.app_url_placeholder') }}" value="{{@$request['app_url']}}" />
				@if ($errors->has('app_url'))
				<span class="error-block">
					<i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
					{{ $errors->first('app_url') }}
				</span>
				@endif
				<div class="form-group has-error">
					<span class="text-light bg-dark">
						ex.https://www.example.com/
					</span>
				</div>
			</div>

			<div class="form-group {{ $errors->has('front_url') ? ' has-error ' : '' }}">
				<label for="front_url">
					{{ trans('installer_messages.environment.wizard.form.front_url_label') }}
					<span class="text-danger">&nbsp;*</span>
					<span class="text-success">This should be your site root URL.</span>
				</label>
				<input type="text" class="form-control" name="front_url" id="front_url" placeholder="{{ trans('installer_messages.environment.wizard.form.front_url_placeholder') }}" value="{{@$request['front_url']}}" />
				@if ($errors->has('front_url'))
				<span class="error-block">
					<i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
					{{ $errors->first('front_url') }}
				</span>
				@endif
			</div>

			<div class="buttons">
				<a class="button button--default" href="{{ route('LaravelInstaller::permissions') }}">
					<i class="fa fa-angle-left fa-fw" aria-hidden="true"></i>
					Back
				</a>
				<a class="button" onclick="showDatabaseSettings(); return false" href="javascript:void(0)">
					{{ trans('installer_messages.environment.wizard.form.buttons.setup_database') }}
					<i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
				</a>
			</div>
		</div>
		<div class="tab" id="tab2content">

			<div class="form-group {{ $errors->has('table_prefix') ? ' has-error ' : '' }}">
				<label for="table_prefix">Table Prefix<span class="text-danger">&nbsp;*</span></label>
				<input type="text" name="table_prefix" class="form-control" id="table_prefix" placeholder="ex. capl" value="{{@$request['table_prefix']}}" />
				@if ($errors->has('table_prefix'))
				<span class="error-block">
					<i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
					{{ $errors->first('table_prefix') }}
				</span>
				@endif
			</div>

			<div class="form-group {{ $errors->has('database_hostname') ? ' has-error ' : '' }}">
				<label for="database_hostname">
					{{ trans('installer_messages.environment.wizard.form.db_host_label') }}
					<span class="text-danger">&nbsp;*</span>
				</label>
				<input type="text" name="database_hostname" class="form-control" id="database_hostname" value="127.0.0.1" placeholder="{{ trans('installer_messages.environment.wizard.form.db_host_placeholder') }}"  />
				@if ($errors->has('database_hostname'))
				<span class="error-block">
					<i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
					{{ $errors->first('database_hostname') }}
				</span>
				@endif
			</div>

			<div class="form-group {{ $errors->has('database_port') ? ' has-error ' : '' }}">
				<label for="database_port">
					{{ trans('installer_messages.environment.wizard.form.db_port_label') }}
					<span class="text-danger">&nbsp;*</span>
				</label>
				<input type="text" name="database_port" class="form-control" id="database_port" value="3306" placeholder="{{ trans('installer_messages.environment.wizard.form.db_port_placeholder') }}" />
				@if ($errors->has('database_port'))
				<span class="error-block">
					<i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
					{{ $errors->first('database_port') }}
				</span>
				@endif
			</div>

			<div class="form-group {{ $errors->has('database_name') ? ' has-error ' : '' }}">
				<label for="database_name">
					{{ trans('installer_messages.environment.wizard.form.db_name_label') }}
					<span class="text-danger">&nbsp;*</span>
				</label>
				<input type="text" name="database_name" class="form-control" id="database_name" value="{{@$request['database_name']}}" placeholder="{{ trans('installer_messages.environment.wizard.form.db_name_placeholder') }}" />
				@if ($errors->has('database_name'))
				<span class="error-block">
					<i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
					{{ $errors->first('database_name') }}
				</span>
				@endif
			</div>

			<div class="form-group {{ $errors->has('database_username') ? ' has-error ' : '' }}">
				<label for="database_username">
					{{ trans('installer_messages.environment.wizard.form.db_username_label') }}
					<span class="text-danger">&nbsp;*</span>
				</label>
				<input type="text" name="database_username" class="form-control" id="database_username" value="{{@$request['database_username']}}" placeholder="{{ trans('installer_messages.environment.wizard.form.db_username_placeholder') }}" />
				@if ($errors->has('database_username'))
				<span class="error-block">
					<i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
					{{ $errors->first('database_username') }}
				</span>
				@endif
			</div>

			<div class="form-group {{ $errors->has('database_password') ? ' has-error ' : '' }}">
				<label for="database_password">
					{{ trans('installer_messages.environment.wizard.form.db_password_label') }}
				</label>
				<input type="password" name="database_password" class="form-control" id="database_password" value="" placeholder="{{ trans('installer_messages.environment.wizard.form.db_password_placeholder') }}" />
				@if ($errors->has('database_password'))
				<span class="error-block">
					<i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
					{{ $errors->first('database_password') }}
				</span>
				@endif
			</div>

			<div class="buttons">
				<a class="button button--default" onclick="backStep1();
						return false;">
					<i class="fa fa-angle-left fa-fw" aria-hidden="true"></i>
					Back
				</a>
				<a class="button" onclick="showApplicationSettings();
						return false" href="{{ route('LaravelInstaller::permissions') }}">
					{{ trans('installer_messages.environment.wizard.form.buttons.setup_application') }}
					<i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
				</a>
			</div>
		</div>
		<div class="tab" id="tab3content">
			<div class="form-group {{ $errors->has('mail_driver') ? ' has-error ' : '' }}">
				<label for="mail_driver">
					{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_driver_label') }}
					<sup>
						<a href="https://laravel.com/docs/5.7/mail" target="_blank" title="{{ trans('installer_messages.environment.wizard.form.app_tabs.more_info') }}">
							<i class="fa fa-info-circle fa-fw" aria-hidden="true"></i>
							<span class="sr-only">{{ trans('installer_messages.environment.wizard.form.app_tabs.more_info') }}</span>
						</a>
					</sup>
				</label>
				<input type="text" name="mail_driver" class="form-control" id="mail_driver" value="{{@$request['mail_driver']}}" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_driver_placeholder') }}" />
				@if ($errors->has('mail_driver'))
				<span class="error-block">
					<i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
					{{ $errors->first('mail_driver') }}
				</span>
				@endif
			</div>
			<div class="form-group {{ $errors->has('mail_host') ? ' has-error ' : '' }}">
				<label for="mail_host">
					{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_host_label') }}
				</label>
				<input type="text" name="mail_host" class="form-control" id="mail_host" value="{{@$request['mail_host']}}" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_host_placeholder') }}" />
				@if ($errors->has('mail_host'))
				<span class="error-block">
					<i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
					{{ $errors->first('mail_host') }}
				</span>
				@endif
			</div>
			<div class="form-group {{ $errors->has('mail_port') ? ' has-error ' : '' }}">
				<label for="mail_port">
					{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_port_label') }}
				</label>
				<input type="text" name="mail_port" class="form-control" id="mail_port" value="{{@$request['mail_port']}}" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_port_placeholder') }}" />
				@if ($errors->has('mail_port'))
				<span class="error-block">
					<i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
					{{ $errors->first('mail_port') }}
				</span>
				@endif
			</div>
			<div class="form-group {{ $errors->has('mail_username') ? ' has-error ' : '' }}">
				<label for="mail_username">
					{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_username_label') }}
				</label>
				<input type="text" name="mail_username" class="form-control" id="mail_username" value="{{@$request['mail_username']}}" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_username_placeholder') }}" />
				@if ($errors->has('mail_username'))
				<span class="error-block">
					<i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
					{{ $errors->first('mail_username') }}
				</span>
				@endif
			</div>
			<div class="form-group {{ $errors->has('mail_password') ? ' has-error ' : '' }}">
				<label for="mail_password">
					{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_password_label') }}
				</label>
				<input type="password" name="mail_password" class="form-control" id="mail_password" value="" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_password_placeholder') }}" />
				@if ($errors->has('mail_password'))
				<span class="error-block">
					<i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
					{{ $errors->first('mail_password') }}
				</span>
				@endif
			</div>
			<div class="form-group {{ $errors->has('mail_encryption') ? ' has-error ' : '' }}">
				<label for="mail_encryption">
					{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_encryption_label') }}
				</label>
				<input type="text" name="mail_encryption" class="form-control" id="mail_encryption" value="{{@$request['mail_encryption']}}" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_encryption_placeholder') }}" />
				@if ($errors->has('mail_encryption'))
				<span class="error-block">
					<i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
					{{ $errors->first('mail_encryption') }}
				</span>
				@endif
			</div>
			<div class="loader hide" id="loader">Loading...</div>
			<div class="buttons" id="envButton">
				<a class="button button--default" onclick="backStep2();
						return false;">
					<i class="fa fa-angle-left fa-fw" aria-hidden="true"></i>
					Back
				</a>
				<button class="button" type="submit">
					{{ trans('installer_messages.environment.wizard.form.buttons.install') }}
					<i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
				</button>
			</div>
		</div>
	</form>
</div>
@endsection

@section('scripts')

<!-- Scripts -->
<script type="text/javascript">
	// Check environment
	function checkEnvironment(val) {
		var element = document.getElementById('environment_text_input');
		if (val == 'other') {
			element.style.display = 'block';
		} else {
			element.style.display = 'none';
		}
	}

	// Show database settings
	function showDatabaseSettings() {
		document.getElementById('tab2').checked = true;
	}

	// Show application setting
	function showApplicationSettings() {
		document.getElementById('tab3').checked = true;
	}

	// Show loading
	function ShowLoading(e) {
		document.getElementById("loader").classList.remove("hide");
		document.getElementById("envButton").classList.add("hide");
		document.getElementById('tab1').disabled = true;
		document.getElementById('tab2').disabled = true;
		return true;
	}

	// Back to step1
	function backStep1() {
		document.getElementById('tab1').checked = true;
	}

	// Back to step2
	function backStep2() {
		document.getElementById('tab2').checked = true;
	}
</script>
@endsection
