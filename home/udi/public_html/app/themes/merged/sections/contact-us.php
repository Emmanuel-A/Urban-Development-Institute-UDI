
<div id="contact-us" class="row section-contact-us">
	<div class="col-sm-6">
		<div class="section-content">
			<?php the_field('contact_us_content','option') ?>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="section-content">
			<?php the_field('contact_us_form_heading','option') ?>
		</div>
		<form name="contactForm" role="form" class="row">
			<div class="col-sm-12 form-group">
				<label class="sr-only" for="contactFormName"><?= trans('Enter Your Name', 'Nom') ?></label>
				<input type="text" class="form-control" id="contactFormName" name="name" placeholder="<?= trans('Enter Your Name', 'Nom') ?>" required>
			</div>
			<div class="col-sm-12 form-group">
				<label class="sr-only" for="contactFormEmail"><?= trans('Enter Your Email', 'Courriel') ?></label>
				<input type="email" class="form-control" id="contactFormEmail" name="email" placeholder="<?= trans('Enter Your Email', 'Courriel') ?>" required>
			</div>
			<div class="col-sm-12 form-group">
				<label class="sr-only" for="contactFormMessage"><?= trans('Enter Your Message', 'Adresse') ?></label>
				<textarea class="form-control" id="contactFormMessage" name="message" placeholder="<?= trans('Enter Your Message', 'Message') ?>" required></textarea>
			</div>
			<div class="col-sm-12">
				<button type="submit" class="btn"><?= trans('SUBMIT', 'ENVOYER') ?></button>
			</div>
		</form>
		<div class="thankyou" style="display:none">
			<?php the_field('contact_us_thank_you_message','option') ?>
		</div>
	</div>
</div>
