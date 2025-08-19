<?php
echo $before_widget;
echo $before_title . $title . $after_title;
?>

<ul class="pgnyt-articles frontend">
    <?php
    $articles_to_show = min(10, count($pgnyt_results->response->docs));
    for ($i = 0; $i < $articles_to_show; $i++):
        $article = $pgnyt_results->response->docs[$i];
        ?>
        <li class="pgnyt-articles">
            <ul class="pgnyt-articles-info">
                <?php
                // Check if multimedia exists
                if (isset($article->multimedia)):
                    // The multimedia appears to be an object with image properties
                    // Check for default image first
                    if (isset($article->multimedia->default) && isset($article->multimedia->default->url)) {
                        $imageUrl = $article->multimedia->default->url;
                    }
                    // Fallback to thumbnail if default not available
                    elseif (isset($article->multimedia->thumbnail) && isset($article->multimedia->thumbnail->url)) {
                        $imageUrl = $article->multimedia->thumbnail->url;
                    }

                    // Output the image if we found a URL
                    if (isset($imageUrl)): ?>
                        <li>
                            <img width="120px" src="<?php echo esc_url($imageUrl); ?>">
                        </li>
                    <?php endif;
                endif;
                ?>
                <li class="pgnyt-articles-name">
                    <a href="<?php echo esc_url($article->web_url); ?>" target="_blank" rel="noopener noreferrer">
                        <?php echo esc_html($article->headline->main); ?>
                    </a>
                </li>
            </ul>
        </li>
    <?php endfor; ?>
</ul>

<?php
echo $after_widget;
?>
