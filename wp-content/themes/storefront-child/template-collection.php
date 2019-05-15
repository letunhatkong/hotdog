<?php
/**
 * The template for displaying collection by ACF fields
 *
 * Template Name: Collection
 *
 * @author Samuel Kong
 */

?>

<?php get_header(); ?>


<div class="template-collection">

    <?php
    // check if the flexible content field has rows of data
    if( have_rows('section_list') ):

        // loop through the rows of data
        while ( have_rows('section_list') ) {
            the_row();
            $layout = get_row_layout();
            get_template_part("sections/".$layout);
        }
    endif;
    ?>

</div>


<?php get_footer(); ?>
