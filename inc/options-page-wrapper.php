<div class="wrap">
	<div id="icon-options-general" class="icon32"></div>
	<h1>NY Times Articles</h1>

	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<!-- main content -->
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
					<?php if (!isset($pgnyt_search) || $pgnyt_search == ''): ?>
						<div class="postbox">
							<div class="handlediv" title="Click to toggle"><br></div>
							<h2 class="hndle"><span>Let's Get Started</span></h2>
							<div class="inside">
								<form method="post" action="">
									<input type="hidden" name="pgnyt_form_submitted" value="Y">
									<table class="form-table">
										<tr valign="top">
											<td scope="row"><label for="tablecell">Search String</label></td>
											<td><input name="pgnyt_search" id="pgnyt_search" type="text" value=""
													class="regular-text" /></td>
										</tr>
										<tr valign="top">
											<td scope="row"><label for="tablecell">API Key</label></td>
											<td><input name="pgnyt_apikey" id="pgnyt_apikey" type="password" value=""
													class="regular-text" /></td>
										</tr>
									</table>
									<p>
										<input class="button-primary" type="submit" name="pgnyt_form_submit" value="Save" />
									</p>
								</form>
							</div>
						</div>
					<?php else: ?>
						<div class="postbox">
							<div class="handlediv" title="Click to toggle"><br></div>
							<h2 class="hndle"><span>Let's Get Started</span></h2>
							<div class="inside">
								<?php if (isset($pgnyt_results) && isset($pgnyt_results->response) && !empty($pgnyt_results->response->docs)): ?>
									<p>Below are the articles</p>
									<ul class="pgnyt-articles">
										<?php
										$articles_to_show = min(10, count($pgnyt_results->response->docs));
										for ($i = 0; $i < $articles_to_show; $i++):
											$article = $pgnyt_results->response->docs[$i];
											?>
											<li>
												<ul>
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
														<a href="<?php echo esc_url($article->web_url); ?>" target="_blank"
															rel="noopener noreferrer">
															<?php echo esc_html($article->headline->main); ?>
														</a>
													</li>
													<li class="pgnyt-articles-paragraph">
														<p><?php echo esc_html($article->multimedia->caption); ?></p>
													</li>
												</ul>
											</li>
										<?php endfor; ?>
									</ul>
								<?php else: ?>
									<p>No articles found. Please check your search parameters and API key.</p>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>

					<div class="postbox">
						<div class="handlediv" title="Click to toggle"><br></div>
						<h2 class="hndle"><span>JSON Feed</span></h2>
						<div class="inside">
							<?php if (isset($pgnyt_results) && isset($pgnyt_results->response)): ?>
								<pre><code><?php echo esc_html(print_r($pgnyt_results, true)); ?></code></pre>
							<?php else: ?>
								<p>No API results to display.</p>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>

			<!-- sidebar -->
			<div id="postbox-container-1" class="postbox-container">
				<div class="meta-box-sortables">
					<div class="postbox">
						<div class="handlediv" title="Click to toggle"><br></div>
						<h2 class="hndle"><span>Settings</span></h2>
						<div class="inside">
							<form method="post" action="">
								<input type="hidden" name="pgnyt_form_submitted" value="Y">
								<p>
									<input name="pgnyt_search" id="pgnyt_search" placeholder="Search" type="text"
										value="<?php echo $pgnyt_search; ?>" class="all-options" />
									<br>
									<input name="pgnyt_apikey" id="pgnyt_apikey" placeholder="Key String"
										type="password" value="<?php echo $pgnyt_apikey; ?>" class="all-options" />
								</p>
								<p>
									<input class="button-primary" type="submit" name="pgnyt_form_submit"
										value="Update" />
								</p>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		<br class="clear">
	</div>
</div>