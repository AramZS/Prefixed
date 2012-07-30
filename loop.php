<?php /* If there are no posts to display, such as an empty archive page */ ?>
<?php if (!have_posts()) : ?>
	<div class="notice">
		<p class="bottom"><?php _e('Sorry, no results were found.', 'reverie'); ?></p>
	</div>
	<?php get_search_form(); ?>	
<?php endif; ?>

<?php /* Start loop */ ?>
<?php while (have_posts()) : the_post(); ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="row">
			<header class="two columns">
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<?php reverie_entry_meta(); ?>
				<span class="front-comments"><?php comments_popup_link( 'Add a comment', 'One comment', '% comments', 'comments-link', ''); ?></span>
			</header>
			<div class="entry-content ten columns">
		<?php if (is_archive() || is_search()) : // Only display excerpts for archives and search ?>
			<?php the_excerpt(); ?>
		<?php else : ?>
			<?php the_content('Continue reading...'); ?>
		<?php endif; ?>
			</div>
		</div>
		<div class="row <?php $tag = get_the_tags(); if (!$tag) { } else { ?>footrow<?php } ?>">
			<div class="two columns <?php $tag = get_the_tags(); if (!$tag) { } else { ?>hash<?php } ?>">
				<?php $tag = get_the_tags(); if (!$tag) { } else { ?>#<?php } ?>
			</div>
			<footer class="ten columns">
				
					<?php $tag = get_the_tags(); if (!$tag) { } else { ?><p><?php the_tags(''); ?></p><?php } ?>
				
			</footer>
		</div>
		<div class="row divider <?php $tag = get_the_tags(); if (!$tag) { } else { ?>taggedfoot<?php } ?>"></div>
	</article>	

<?php endwhile; // End the loop ?>

<?php /* Display navigation to next/previous pages when applicable */ ?>
<?php if ($wp_query->max_num_pages > 1) : ?>
	<nav id="post-nav">
		<div class="post-previous"><?php next_posts_link( __( '&larr; Older posts', 'reverie' ) ); ?></div>
		<div class="post-next"><?php previous_posts_link( __( 'Newer posts &rarr;', 'reverie' ) ); ?></div>
	</nav>
<?php endif; ?>