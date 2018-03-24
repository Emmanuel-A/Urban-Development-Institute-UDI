<?php

namespace IUOE;

class MailerOptionsPage {

	/**
	 * Create an instance.
	 */
	public function __construct()
	{
		add_action('admin_menu', array($this, 'admin_menu'), 999);
		add_action('admin_init', array($this, 'admin_init'));
	}

	/**
	 * Register the options page with the Wordpress menu.
	 */
	function admin_menu()
	{
		add_submenu_page('acf-options-analytics', 'Mailer Options', 'Mailer Options', 'manage_options', 'iuoe-mailer', array($this, 'options_page'));
	}

	/**
	 * Register settings and default fields.
	 */
	function admin_init()
	{
		register_setting('iuoe', 'iuoe');

		add_settings_section(
			'iuoe-mailer-section',
			'',
			array($this, 'section_mailer_section'),
			'iuoe'
		);
		add_settings_field(
			'Test Mode',
			'Test Mode',
			array($this, 'field_mailer_test_mode'),
			'iuoe',
			'iuoe-mailer-section'
		);
		add_settings_field(
			'From',
			'From',
			array($this, 'field_mailer_from'),
			'iuoe',
			'iuoe-mailer-section'
		);
		add_settings_field(
			'Transport',
			'Transport',
			array($this, 'field_mailer_transport'),
			'iuoe',
			'iuoe-mailer-section'
		);
		add_settings_section(
			'iuoe-smtp-section',
			'SMTP Section',
			'__return_true',
			'iuoe'
		);
		add_settings_field(
			'Hostname',
			'Hostname',
			array($this, 'field_smtp_hostname'),
			'iuoe',
			'iuoe-smtp-section'
		);
		add_settings_field(
			'Port',
			'Port',
			array($this, 'field_smtp_port'),
			'iuoe',
			'iuoe-smtp-section'
		);
		add_settings_field(
			'Username',
			'Username',
			array($this, 'field_smtp_username'),
			'iuoe',
			'iuoe-smtp-section'
		);
		add_settings_field(
			'Password',
			'Password',
			array($this, 'field_smtp_password'),
			'iuoe',
			'iuoe-smtp-section'
		);
		add_settings_field(
			'Encryption',
			'Encryption',
			array($this, 'field_smtp_encryption'),
			'iuoe',
			'iuoe-smtp-section'
		);
		add_settings_section(
			'iuoe-log-section',
			'Log Section',
			'__return_true',
			'iuoe'
		);
		add_settings_field(
			'Log file location',
			'Log file location',
			array($this, 'field_log_file'),
			'iuoe',
			'iuoe-log-section'
		);
	}

	/**
	 * Render the options page.
	 */
	function options_page()
	{
		?>
		<div class="wrap">
			<form action="options.php" method="post">
			<?php
				settings_fields('iuoe');
				do_settings_sections('iuoe');
				submit_button();
			?>
			</form>
		</div>
		<style>
		h2 {margin-top: 40px;}
		.form-table th {padding:10px 10px 10px 0;}
		.form-table td {padding:5px 10px;}
		.help-block {font-size:80%;font-style:italic;line-height:1.2;max-width:400px}
		#mailer_test_rcpt {margin-top:4px;}
		</style>
		<?php
	}

	/**
	 * Render the mailing options section.
	 */
	public function section_mailer_section()
	{
	?>
		<h1>Mailer Options</h1>
		<p>
			If sending high volumes of messages consider using a ESP like
			<a href="https://sendgrid.com/" target="_blank">Sendgrid</a>,
			<a href="https://www.mailgun.com/" target="_blank">Mailgun</a> or
			<a href="https://www.mandrill.com/" target="_blank">Mandrill</a>.
			Using an ESP to send messages with greatly increase the deliverability and performance of the
			outgoing messages. In addition most ESPs offer bounce management and some level of analytics.
		</p>
		<script type="text/javascript">
		jQuery(function($){
			var log = jQuery('h2:contains(Log Section)').hide();
			var smtp = jQuery('h2:contains(SMTP Section)').hide();
			$('#mailer_options').on('change', function(){
				var value = $(this).val();
				switch(value) {
					case 'SMTP':
						$(this).next().hide();
						log.next('.form-table').hide();
						smtp.next('.form-table').show();
						break;
					case 'LOG':
						$(this).next().hide();
						log.next('.form-table').show();
						smtp.next('.form-table').hide();
						break;
					default:
						$(this).next().show();
						log.next('.form-table').hide();
						smtp.next('.form-table').hide();
				}
			}).trigger('change');
			$('#mailer_test_mode').on('change', function(){
				if ($(this).val() === 'on') {
					$(this).next().show();
				} else {
					$(this).next().hide();
				}
			}).trigger('change');
			$(window).load(function(){
				var $from = $('#mailer_from_email');
				qTranslateConfig.qtx.addContentHookC($from.get(0), $from.closest('form').get(0));
			});
		});
		</script>
	<?php
	}

	/**
	 * Render the from field.
	 */
	public function field_mailer_from()
	{
		$mailer_from_email = iuoe_get_option('mailer_from_email', '');
		/*
		$mailer_from_name  = iuoe_get_option('mailer_from_name',  '');
		<input id="mailer_from_name"  type="text" name="iuoe[mailer_from_name]"  size="20" placeholder="Name" value="<?= esc_attr($mailer_from_name) ?>">
		*/
		?>
		<input id="mailer_from_email" type="text" name="iuoe[mailer_from_email]" size="40" placeholder="E-mail Address" value="<?= esc_attr($mailer_from_email) ?>">
		<?php
	}

	/**
	 * Render the test mode field.
	 */
	public function field_mailer_test_mode()
	{
		$mailer_test_mode = iuoe_get_option('mailer_test_mode', 'off');
		$mailer_test_rcpt = iuoe_get_option('mailer_test_rcpt', '');
		?>
		<select id="mailer_test_mode" name="iuoe[mailer_test_mode]">
			<option value="off" <?php selected($mailer_test_mode, 'off') ?>>OFF</option>
			<option value="on"  <?php selected($mailer_test_mode, 'on')  ?>>ON</option>
		</select>
		<div style="display:none">
			<textarea id="mailer_test_rcpt" name="iuoe[mailer_test_rcpt]" cols="80" rows="5"><?= esc_html($mailer_test_rcpt) ?></textarea>
			<div class="help-block">
			Comma-separated list of email addresses to be used as the recipients for
			all outgoing messages while in Test Mode.
			</div>
		</div>
		<?php
	}

	/**
	 * Render the transport field.
	 */
	public function field_mailer_transport()
	{
		$mailer_transport = iuoe_get_option('mailer_transport', 'MAIL');
		?>
		<select id="mailer_options" name="iuoe[mailer_transport]">
			<option value="MAIL" <?php selected($mailer_transport, 'MAIL') ?>>PHP mail()</option>
			<option value="SMTP" <?php selected($mailer_transport, 'SMTP') ?>>SMTP</option>
			<option value="LOG"  <?php selected($mailer_transport, 'LOG')  ?>>Log file</option>
		</select>
		<div class="help-block" style="margin-top:8px;">
		The Mail Transport sends messages by delegating to PHP's internal mail() function.
		The mail() function is not particularly predictable, or helpful. You'd be much better
		off using the SMTP Transport.
		</div>
		<?php
	}

	/**
	 * Render the SMTP hostname field.
	 */
	public function field_smtp_hostname()
	{
		$smtp_hostname = iuoe_get_option('smtp_hostname', '');
		?>
		<input id="smtp_hostname" type="text" name="iuoe[smtp_hostname]" value="<?= esc_attr($smtp_hostname) ?>">
		<?php
	}

	/**
	 * Render the SMTP hostname field.
	 */
	public function field_smtp_port()
	{
		$smtp_port = iuoe_get_option('smtp_port', '');
		?>
		<input id="smtp_port" type="number" name="iuoe[smtp_port]" value="<?= esc_attr($smtp_port) ?>">
		<?php
	}

	/**
	 * Render the SMTP username field.
	 */
	public function field_smtp_username()
	{
		$smtp_username = iuoe_get_option('smtp_username', '');
		?>
		<input id="smtp_username" type="text" name="iuoe[smtp_username]" value="<?= esc_attr($smtp_username) ?>">
		<?php
	}

	/**
	 * Render the SMTP password field.
	 */
	public function field_smtp_password()
	{
		$smtp_password = iuoe_get_option('smtp_password', '');
		?>
		<input id="smtp_password" type="password" name="iuoe[smtp_password]" value="<?= esc_attr($smtp_password) ?>">
		<?php
	}

	/**
	 * Render the SMTP encryption field.
	 */
	public function field_smtp_encryption()
	{
		$smtp_encryption = iuoe_get_option('smtp_encryption', '');
		?>
		<select id="mailer_options" name="iuoe[smtp_encryption]">
			<option value="">(none)</option>
			<option value="ssl" <?php selected($smtp_encryption, 'ssl') ?>>SSL</option>
			<option value="tls" <?php selected($smtp_encryption, 'tls') ?>>TLS</option>
		</select>
		<?php
	}

	/**
	 * Render the Log file field.
	 */
	public function field_log_file()
	{
		$log_file = iuoe_get_option('log_file');
		if (!empty($log_file)) {
			if (!file_exists($log_file)) {
				@file_put_contents($log_file,'');
			}
			$writable = is_writable($log_file);
		}
		?>
		<input id="log_file" type="text" name="iuoe[log_file]" placeholder="<?= get_stylesheet_directory().'/mail_log' ?>" size="90" value="<?= esc_attr($log_file) ?>">
		<?php if (!empty($log_file)): ?>
			<div class="help-block">
				The file path to the log file.
				<?php if ($writable): ?>
					<span style="display:inline-block;line-height:20px;color:#1CB755">
						<span class="dashicons dashicons-yes"></span>
						Log file location writable.
					</span>
				<?php else: ?>
					<span style="display:inline-block;line-height:20px;color:red">
						<span class="dashicons dashicons-no-alt"></span>
						Log file location not writable!
					</span>
				<?php endif ?>
			</div>
		<?php endif ?>
		<?php
	}

}
