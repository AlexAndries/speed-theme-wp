<?php get_header(); ?>

<div class="not-found">
  <div class="container">
    <div class="row">
      <div class="col-12 col-gt-sm-6 text-center--xs text-center--sm">
        <div class="not-found__title h1 has-bbcode primary__color">
          <h1><?php echo \SpeedTheme\Core\BbCodes::renderBBCodes(get_field('title_not_found', 'options')) ?></h1>
        </div>
        <div class="not-found__content paragraph--small">
          <?php the_field('content_not_found','options') ?>
        </div>
      </div>
      <div class="col-12 col-gt-sm-6">
        <div class="not-found__image text-center">
          <?php  \SpeedTheme\Core\ST_ThemeFunctions::acfImage(get_field('image_not_found','options'))?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php get_footer();
