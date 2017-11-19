<?php
namespace SpeedTheme\Core;

class ST_PostTypeFeed {
  private $postType;
  
  public function __construct($postType) {
    $this->postType = $postType;
    
    add_action('init', array($this, 'loadRSS'));
  }
  
  public function loadRSS() {
    add_feed($this->postType . '-rss', array($this, 'renderRSS'));
  }
  
  public function renderRSS() {
    header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
    echo '<?xml version="1.0" encoding="' . get_option('blog_charset') . '"?' . '>';
    ?>
    <rss version="2.0"
         xmlns:content="http://purl.org/rss/1.0/modules/content/"
         xmlns:wfw="http://wellformedweb.org/CommentAPI/"
         xmlns:dc="http://purl.org/dc/elements/1.1/"
         xmlns:atom="http://www.w3.org/2005/Atom"
         xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
         xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
         xmlns:media="http://search.yahoo.com/mrss/"
      <?php do_action('rss2_ns'); ?>>
      <channel>
        <title><?php bloginfo_rss('name'); ?> - Feed</title>
        <atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml"/>
        <link><?php bloginfo_rss('url') ?></link>
        <description><?php bloginfo_rss('description') ?></description>
        <lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
        <language><?php echo get_option('rss_language'); ?></language>
        <sy:updatePeriod><?php echo apply_filters('rss_update_period', 'hourly'); ?></sy:updatePeriod>
        <sy:updateFrequency><?php echo apply_filters('rss_update_frequency', '1'); ?></sy:updateFrequency>
        <?php do_action('rss2_head'); ?>
        <?php
        $args = array(
          'post_type'      => $this->postType,
          'posts_per_page' => get_option('posts_per_page'),
        );
        $postQuery = new \WP_Query($args);
        ?>
        <?php while ($postQuery->have_posts()) {
          $postQuery->the_post();
          $image = ST_ThemeFunctions::getFeaturedImage(get_the_ID(), 'large') ?>
          <item>
            <title><?php the_title_rss(); ?></title>
            <link><?php the_permalink_rss(); ?></link>
            <?php if ($image) { ?>
              <media:content url="<?php echo ST_ThemeFunctions::getImageURL($image[0]) ?>" medium="image"></media:content>
            <?php } ?>
            <pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
            <dc:creator><?php the_author(); ?></dc:creator>
            <guid isPermaLink="false"><?php the_guid(); ?></guid>
            <description><![CDATA[<?php the_excerpt() ?>]]></description>
            <content:encoded><![CDATA[<?php the_content() ?>]]></content:encoded>
            <?php rss_enclosure(); ?>
            <?php do_action('rss2_item'); ?>
          </item>
        <?php }; ?>
      </channel>
    </rss>
    <?php
  }
}
