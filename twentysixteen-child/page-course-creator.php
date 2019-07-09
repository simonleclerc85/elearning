<?php

get_header();

// Check le status successfull ou failed
checkPost();

get_template_part( 'template-parts/content', 'page' );

?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">

	<form id="cc_create_post" action="" method="POST">
		
		<fieldset name="entry1">

			<label for="course_title">Course Title</label/>
			<input name="course_title" placeholder="Enter a title for your course" id="course_title" type="text" /><hr>

			<label for="course_descr">Course Description</label>
			<textarea name="course_descr" placeholder="Describe your course. for ex; you can explain how your course is divided" id="course_descr"></textarea><hr>

			<label for="_lp_duration">Duration</label>
			<input type="number" class="rwmb-number" name="_lp_duration" id="_lp_duration" value="5" step="1" min="0" placeholder="">
			<select name="_lp_duration_select" id="_lp_duration_select">
				<option value="hour" selected="selected">Hour(s)</option>
			</select>
			<br/><br/>

			<label for="_lp_students">Students Enrolled</label>
			<input step="1" min="0" value="0" type="number" size="30" id="_lp_students" class="rwmb-number " name="_lp_students"><br/><br/>

			<label for="_lp_retake_count">Re-Take Count (Set 0 to disable re-take)</label>
			<input step="1" min="-1" value="0" type="number" size="30" id="_lp_retake_count" class="rwmb-number" name="_lp_retake_count"><br/><br/>

			<label for="_lp_max_students">Maximum students</label>
			<input step="1" min="0" value="1000" type="number" size="30" id="_lp_max_students" class="rwmb-number " name="_lp_max_students"><hr>

			<label for="_lp_price">The price of your course</label><br>
			<input step="0.01" min="0" value="29.99" type="number" size="30" id="_lp_price" class="rwmb-number " name="_lp_price"><br><hr>

			<label for="excerpt">Excerpt</labe><br>
			<textarea rows="1" cols="40" placeholder="A brief description. This will be used to resume your course" name="excerpt" id="excerpt"></textarea><hr>

			<fieldset name="checkboxes">
			<label for="_lp_featured">Set course as featured</label>
			<input type="hidden" name="_lp_featured" value="no">
			<input type="checkbox" class="rwmb-yes-no" name="_lp_featured" id="_lp_featured" value="yes"><br/><br/>
			
			<label for="_lp_block_lesson_content">Block lessons content when the course is completed</label>
			<input type="hidden" name="_lp_block_lesson_content" value="no">
			<input type="checkbox" class="rwmb-yes-no" name="_lp_block_lesson_content" id="_lp_block_lesson_content" value="yes"><br/><br/>

			<label for="comment_status">Allow comments on your course</label>
			<input name="comment_status" type="checkbox" id="comment_status" value="open" checked="checked"><br/><br/>
			</fieldset>

		</fieldset>



		<?php

	
		wp_nonce_field('course_nonce', 'course_nonce_field'); 


		?>
		
		<input type="submit" name="course_submit" value="<?php _e('Submit course for revision', 'coursecreator'); ?>"/>

	</form>

<?php get_footer(); ?>

