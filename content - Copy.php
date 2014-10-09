<?php
/**
 * @package kriate
 */
?>Content

       <div class="work">
        <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >
			<?php if ( has_post_thumbnail() ) { the_post_thumbnail('', array('class' => 'media'));} ?> 
				<div class="caption">
					<div class="work_title">
						<?php the_title( '<h1>', '</h1>' ); ?>
					</div>
				</div>
         </a>
         </div>