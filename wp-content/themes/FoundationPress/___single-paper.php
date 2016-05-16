<?php
/**
 * The template for displaying all single papers and attachments
 *
 * @package FoundationPress
 * @since FoundationPress 1.0.0
 */
$authors = types_render_field("authors", array("raw"=>"true"));
$abstract = types_render_field("abstract", array("raw"=>"true"));
$keywords = types_render_field("keywords", array("raw"=>"true"));
$paperfile = types_render_field("paper-file", array("raw"=>"true"));

get_header(); ?>

<div id="single-post" role="main">

<?php do_action( 'foundationpress_before_content' ); ?>
<?php while ( have_posts() ) : the_post(); ?>
  <article <?php post_class('main-content') ?> id="post-<?php the_ID(); ?>">
    <header>
      <h1 class="entry-title"><?php the_title(); ?></h1>
    </header>
    <div class="section-divider"><hr /></div>
    <div class="row">
      <div class="columns large-6">
        <?php foundationpress_entry_meta(); ?>
      </div>
      <div class="columns large-6 text-right">
        <a href="<?php echo $paperfile;?>" target="_blank" class="button large">Download PDF</a>
      </div>
    </div>
    <div class="section-divider"><hr /></div>
    <section>
      <h4>Authors</h4>
      <?php echo wpautop($authors); ?>
    </section>
    <br>
    <section>
      <h4>Abstract</h4>
      <?php echo wpautop($abstract); ?>
    </section>
    <br>
    <section>
      <h4>Keywords</h4>
      <?php echo wpautop($keywords); ?>
    </section>

    <?php do_action( 'foundationpress_post_before_entry_content' ); ?>
    <div class="entry-content">

    <?php
      if ( has_post_thumbnail() ) :
        the_post_thumbnail();
      endif;
    ?>

    <?php the_content(); ?>
    </div>
    <footer>
      <?php wp_link_pages( array('before' => '<nav id="page-nav"><p>' . __( 'Pages:', 'foundationpress' ), 'after' => '</p></nav>' ) ); ?>
      <p><?php the_tags(); ?></p>
    </footer>
    <?php do_action( 'foundationpress_post_before_comments' ); ?>
    <?php comments_template(); ?>
    <?php do_action( 'foundationpress_post_after_comments' ); ?>
  </article>
<?php endwhile;?>

<?php do_action( 'foundationpress_after_content' ); ?>
<?php get_sidebar(); ?>
</div>
<?php get_footer();
