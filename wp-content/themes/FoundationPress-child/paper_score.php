<?php
function dump($data) {
    if(is_array($data)) { //If the given variable is an array, print using the print_r function.
        print "<pre>-----------------------\n";
        print_r($data);
        print "-----------------------</pre>";
    } elseif (is_object($data)) {
        print "<pre>==========================\n";
        var_dump($data);
        print "===========================</pre>";
    } else {
        print "=========&gt; ";
        var_dump($data);
        print " &lt;=========";
    }
} 

function set_paper_review_score($review_id) {
	$meta = get_post_meta($review_id);
	
	$ratings = [
		"wpcf-clarity-of-purpose-rating",
		"wpcf-composition-and-structure-rating",
		"wpcf-biases-rating",
		"wpcf-conflicts-of-interest-rating",
		"wpcf-ethics-rating",
		"wpcf-replicability-rating",
		"wpcf-methodology-rating",
		"wpcf-evidence-rating",
		"wpcf-evidence-comments",
		"wpcf-references-rating",
		"wpcf-pertinence-rating",
		"wpcf-impact-rating",
		"wpcf-novelty-rating",
	];
	
	$scores = array_map(function($rating) use ($meta) {
		return $meta[$rating][0];
	}, $ratings);

	$scores = array_filter($scores);
	$num_scores = count($scores);

	if ($num_scores > 0) {
		$average = array_sum($scores) / $num_scores;
		$average = round($average, 1);

		update_post_meta($review_id, 'wpcf-paper-review-score', $average);
	}
}

function set_paper_score($paper_id) {
	if (!empty($paper_id)) {
		$paper_reviews_query = array(
			'post_type' => 'paper-review',
			'numberposts' => -1,
			'meta_query' => array(array('key' => '_wpcf_belongs_paper_id', 'value' => $paper_id))
		);
		$reviews = get_posts($paper_reviews_query);

		$scores = array_map(function($review) {
			// FIX: This leads to N queries where N is the number of reviews on a paper.
			// Can't figure out how to batch query for review metas.
			$review_score = get_post_meta($review->ID, 'wpcf-paper-review-score', true);
			return $review_score;
		}, $reviews);

		$scores = array_filter($scores);
		$num_scores = count($scores);

		if ($num_scores > 0) {
			$average = array_sum($scores) / $num_scores;
			$average = round($average, 1);

			update_post_meta($paper_id, 'wpcf-paper-score', $average);
		}
	}
}
function set_scores($post_id, $post) {
	if ($post->post_type != "paper-review") {
		return;
	}

	// If this is a revision, get real review ID
	if ( $parent_id = wp_is_post_revision( $post_id ) ) {
		$post_id = $parent_id;
	}

	$paper_id = wpcf_pr_post_get_belongs($post_id, 'paper');

	remove_action('wp_insert_post', 'set_scores', 99, 3);

	set_paper_review_score($post_id);
	set_paper_score($paper_id);

	add_action('wp_insert_post', 'set_scores', 99, 3);
}
add_action('wp_insert_post', 'set_scores', 99, 3);
