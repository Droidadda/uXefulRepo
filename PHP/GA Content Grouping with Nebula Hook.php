<?php
	//Set up the content grouping in Google Analytics first using "Enable Tracking Code"
	
	add_action('nebula_ga_before_send_pageview', 'custom_ga_content_groups');
	function custom_ga_content_groups(){
		//Blog Posts
		if ( has_category('blog') ){
			echo 'ga("set", "contentGroup1", "Blog");'; //Create this group in GA first
		}

		//News Posts
		if ( has_category('news') ){
			echo 'ga("set", "contentGroup2", "News");'; //Create this group in GA first
		}
	}
