<?php
	function addSport ( )
	{
		if ( wh_db_post_input_string ('action') != 'add' ) {
			return;
		}

		if ( ! wh_db_post_input_check_string ( 'sport' ) ) {
			return;
		}

		$sport = wh_db_post_input_string ( 'sport' );

		if ( ! wh_not_null ($sport) ) {
			return;
		}

		if ( wh_not_null ( wh_db_fetch_row_custom ( getSportByName ( $sport ) ) ) )
		{
			wh_define ( 'TEXT_ERROR', '<strong style="color: #FF0000">There is another sport with the same name</strong>' );
			return;
		}

		// as of PHP 5.4
		$data = [
			"name" => $sport,
		];

		wh_db_perform ( 'sports', $data );

		file_put_contents ( '/var/www/html/database/newest.txt', time () . PHP_EOL );
		// will write the Unix timestamp to newest.txt

		wh_define ( 'TEXT_SUCCESS', '<strong style="color: #00FF00">Successfully added a sport</strong>' );

#		header("Location: add_sport.php");
	}

	function deleteSport ( )
	{
#		header( "refresh:5;url=add_sport.php" );
#		header("Location: add_sport.php");

		if ( wh_db_post_input_string ('action') != 'delete' ) {
			return;
		}

		$sports_query = getSports ( );
		while ( $row_obj = wh_db_fetch_object_custom($sports_query) )
		{
			if ( isset ( $_POST [ $row_obj->id ] ) )
			{
				$sport = $row_obj->id;
			}
		}

		if ( ! wh_not_null ($sport) ||
			wh_not_null ( wh_db_fetch_row_custom ( getSportByName ( $sport ) ) ) )
		{
			wh_define ( 'TEXT_ERROR', '<strong style="color: #FF0000">There is not a sport with this name</strong>' );
			return;
		}

		$handle_status = wh_db_query ( "delete from sports where id = '{$sport}'" );
		if ( ! $handle_status ) {
			return;
		}
		unset ($handle_status);

		wh_define ( 'TEXT_SUCCESS', '<strong style="color: #00FF00">Successfully deleted a sport</strong>' );

#		header("Location: add_sport.php");

#		wh_define ( 'TEXT_SUCCESS', '<strong style="color: #00FF00">Successfully deleted a sport</strong>' );

#		ignore_user_abort(true);
#		set_time_limit(0);
#		echo 'Testing connection handling in PHP';
	}

	addSport ( );
	deleteSport ( );


?>
