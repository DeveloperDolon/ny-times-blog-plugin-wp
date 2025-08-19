<p>
  <label>Title</label> 
  <input class="widefat" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
</p>

<p>
  <label>How many articles would you like to display?</label> 
  <input size="4" name="<?php echo $this->get_field_name('num_articles'); ?>" type="text" value="<?php echo $num_articles; ?>" />
</p>

<p>
  <label>Display image?</label> 
  <input type="checkbox" name="<?php echo $this->get_field_name('display_image'); ?>" value="1" <?php checked( $display_image, 1 ); ?> />
</p>
