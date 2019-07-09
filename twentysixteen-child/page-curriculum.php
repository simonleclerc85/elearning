<?php

get_header();

// Check le status successfull ou failed
checkPost();

get_template_part( 'template-parts/content', 'page' );



?>

<button type="button" class="add_section" name="add_section" id="add_section">Add Section</button><br /><br />
<form name="add_mo" id="add_mo" method="post" action="">

    
    <div id="container1" class="container">
        <div id="dynamic1" class="dynamic">
            <input type="text" name="section[1][titreSection]" id="1" placeholder="Nom de section"/><br /><br />
            <button type="button" name="add" class="add_btn" id="add_input">Add Lesson</button><br /><br />
        </div>
    </div>
    <input type="submit" name="submit" class="submit" value="submit">

    <?php

    
    wp_nonce_field('curri_nonce', 'curri_nonce_field'); 


    ?>
    
</form>



<script>
    jQuery(document).ready(function(){
        var i=0
        jQuery('#add_input').click(function(){
            i++;
            jQuery('#dynamic1').append('<div id="row'+i+'" class="row"><input type="text" name="section[1][contenu]['+i+'][lessonTitle]" id="section[1][lessonTitle['+i+']" placeholder="Nom de leçon"/><br><input type="text" name="section[1][contenu]['+i+'][lessonContent]" id="section[1][lessonContent['+i+']" placeholder="Contenu"/><button type="button" name="remove" id="'+i+'" class="btn_remove">Remove</button></div>');
        });

        jQuery(document).on('click', '.btn_remove', function(){
            var button_id = jQuery(this).attr("id");
            jQuery('#row'+button_id+'').remove();
        });

        var s=1;
        jQuery('#add_section').click(function(){
            s++;
            jQuery('#add_mo').append('<div id="container'+s+'" class="container"><div id="dynamic'+s+'" class="dynamic"><input type="text" name="section['+s+'][titreSection]" id="'+s+'" placeholder="Nom de section"/><button type="button" name="add" class="add_btn" id="'+s+'">Add Lesson</button><button type="button" name="remove" id="'+s+'"class="section_remove">X</button></div></div>');
        });

        jQuery(document).on('click', '.section_remove', function(){
            var remove_id = jQuery(this).attr("id");
            jQuery('#container'+remove_id+'').remove();
        });

        var t=0;
        var d=1;
        jQuery(document).on('click', '.add_btn', function(){
            t++;
            d++;
            var add_more = jQuery(this).attr("id");
            jQuery('#container'+add_more+'').append('<div id="row'+d+'" class="row"><input type="text" name="section['+add_more+'][contenu]['+t+'][lessonTitle]" placeholder="Nom de leçon"/><br><input type="text" name="section['+add_more+'][contenu]['+t+'][lessonContent]" placeholder="Contenu"/><button type="button" name="remove" id="'+d+'" class="lesson_remove">Remove</button></div>');
        });

        jQuery(document).on('click', '.lesson_remove', function(){
            var remove_btn = jQuery(this).attr("id");
            jQuery('#row'+remove_btn+'').remove();
        });

    });

</script>

    


</body>


<?php get_footer(); 

