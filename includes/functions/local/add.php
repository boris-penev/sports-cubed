<?php
	function addClub ( )
	{
		if ( ! wh_db_post_input_check_string ( 'name' ) )
		{
			return;
		}

		$name = wh_db_post_input_string ( 'name' );

		if ( wh_not_null ( wh_db_fetch_row_custom ( getClubByName ( $name ) ) ) )
		{
			wh_define ( 'TEXT_ERROR', '<strong style="color: #FF0000">There is another club with the same name</strong>' );
			return;
		}

		$address = wh_db_post_input_string ( 'address' );

		$postcode = wh_db_post_input_string( 'postcode' );

		$latitude = wh_db_post_input_string ( 'latitude' );

		if ( wh_not_null ($latitude) && ! is_numeric ( $latitude ) )
		{
			wh_define ( 'TEXT_ERROR', '<strong style="color: #FF0000">Latitude is not numeric</strong>' );
			return;
		}

		$longtitude = wh_db_post_input_string ( 'longtitude' );

		if ( wh_not_null ($longtitude) && ! is_numeric ( $longtitude ) )
		{
			wh_define ( 'TEXT_ERROR', '<strong style="color: #FF0000">Longtitude is not numeric</strong>' );
			return;
		}

		$comment = wh_db_post_input_string ( 'comment' );

		if ( wh_not_null ( $comment ) && strlen ( $comment ) > 4000 ) {
			wh_define ( 'TEXT_ERROR', '<strong style="color: #FF0000">Comment is too long &mdash; over 4000 symbols</strong>' );
			return;
		}

		$website = wh_db_post_input_string ( 'website' );

		$email = wh_db_post_input_string ( 'email' );

		$phone = wh_db_post_input_string ( 'phone' );

		$time_open = wh_db_post_input_string ( 'time_open' );

		if ( $time_open == '00:00:00' ) {
			$time_open = null;
		}

// previous to PHP 5.1.0 you would compare with -1, instead of false
		//if (($timestamp = strtotime($time_open)) === false) {
		if ( wh_not_null ($time_open) && strtotime($time_open) === false ) {
			wh_define ( 'TEXT_ERROR', '<strong style="color: #FF0000">Opening time is not a valid time</strong>' );
			return;
		}
		//else {
			//die ("$time_open == " . date('l dS \o\f F Y h:i:s A', $timestamp));
		//}

		$time_close = wh_db_post_input_string ( 'time_close' );

		if ( $time_close == '00:00:00' ) {
			$time_close = null;
		}

		if ( wh_not_null ($time_close) && strtotime($time_close) === false ) {
			wh_define ( 'TEXT_ERROR', '<strong style="color: #FF0000">Closing time is not a valid time</strong>' );
			return;
		}

		$price_member = wh_db_post_input_string ( 'price_member' );

		if ( wh_not_null ($price_member) && ! is_numeric ( $price_member ) )
		{
			wh_define ( 'TEXT_ERROR', '<strong style="color: #FF0000">Members price is not numeric</strong>' );
			return;
		}

		$price_nonmember = wh_db_post_input_string ( 'price_nonmember' );

		if ( wh_not_null ($price_nonmember) && ! is_numeric ( $price_nonmember ) )
		{
			wh_define ( 'TEXT_ERROR', '<strong style="color: #FF0000">Non members price is not numeric</strong>' );
			return;
		}

		$everyday = wh_db_post_input_prepare ( 'everyday' );
		if ( $everyday != true )
		{
			$days = array ( );
			if ( wh_db_post_input_check ( 'monday' ) )
				$days ['monday'] = 1;
			if ( wh_db_post_input_check ( 'tuesday' ) )
				$days ['tuesday'] = 2;
			if ( wh_db_post_input_check ( 'wednesday' ) )
				$days ['wednesday'] = 3;
			if ( wh_db_post_input_check ( 'thursday' ) )
				$days ['thursday'] = 4;
			if ( wh_db_post_input_check ( 'friday' ) )
				$days ['friday'] = 5;
			if ( wh_db_post_input_check ( 'saturday' ) )
				$days ['saturday'] = 6;
			if ( wh_db_post_input_check ( 'sunday' ) )
				$days ['sunday'] = 7;
		}
		$sports = wh_db_post_input_prepare_array ( 'sports' );

		// as of PHP 5.4
		$data = [
			"name" => $name,
			"address" => $address,
			"postcode" => $postcode,
			"latitude" => $latitude,
			"longtitude" => $longtitude,
			"comment" => $comment,
			"website" => $website,
			"email" => $email,
			"phone" => $phone
		];
		if ( $address == null ) {
			unset ($data ['address']);
		}
		if ( $latitude == null ) {
			unset ($data ['latitude']);
		}
		if ( $longtitude == null ) {
			unset ($data ['longtitude']);
		}
		if ( $comment == null ) {
			unset ($data ['comment']);
		}
		if ( $website == null ) {
			unset ($data ['website']);
		}
		if ( $email == null ) {
			unset ($data ['email']);
		}
		if ( $phone == null ) {
			unset ($data ['phone']);
		}

		wh_db_perform ( 'clubs', $data );
		$club_id = wh_db_insert_id ( );

		//foreach do not check if sports is null

		if ( $everyday == true )
		{
			foreach ( $sports as $sport )
			{
				$data = [
					"club_id" => $club_id,
					"sport_id" => $sport,
					'price_member' => $price_member,
					'price_nonmember' => $price_nonmember,
					"opening_time" => $time_open,
					"closing_time" => $time_close
					//"day_id" => 8
				];
				if ( $price_member == null ) {
					unset ($data ['price_member']);
				}
				if ( $price_nonmember == null ) {
					unset ($data ['price_nonmember']);
				}
				if ( $time_open == null ) {
					unset ($data ['opening_time']);
				}
				if ( $time_close == null ) {
					unset ($data ['closing_time']);
				}
				wh_db_perform ( 'clubosport', $data );
			}
		}	//if ( #everyday...
		else
		{
			if ( ! is_array ( $days ) )
				wh_error ( 'days is not array' );
			foreach ( $sports as $sport )
			{
				foreach ( $days as $day )
				{
					$data = [
						"club_id" => $club_id,
						"sport_id" => $sport,
						"day_id" => $day,
						"price_member" => $price_member,
						"price_nonmember" => $price_nonmember,
						"opening_time" => $time_open,
						"closing_time" => $time_close
					];
					if ( $price_member == null ) {
						unset ($data ['price_member']);
					}
					if ( $price_nonmember == null ) {
						unset ($data ['price_nonmember']);
					}
					if ( $time_open == null ) {
						unset ($data ['opening_time']);
					}
					if ( $time_close == null ) {
						unset ($data ['time_close']);
					}
					wh_db_perform ( 'clubosport', $data );
				}	//foreach ( $days...
			}	//foreach ( $sports...
		}	//if ( $everyday... else...

/*
		$bash_sum = 0.0;
		$bash_max = 0.0;
		for ( $i = 0; $i < 100; ++$i )
		{
			$starttime = microtime(true);
			exec ('echo `date +%s`  > /var/www/html/database/newest.txt');
			$endtime = microtime(true);
			$t = $endtime - $starttime;
			if ( $t > $bash_max )
				$bash_max = $t;
			$bash_sum += $t;
		}

		$php_sum = 0.0;
		$php_max = 0.0;
		for ( $i = 0; $i < 100; ++$i )
		{
			$starttime = microtime(true);
			file_put_contents ( '/var/www/html/database/newest.txt', time () );
			$endtime = microtime(true);
			$t = $endtime - $starttime;
			if ( $t > $php_max )
				$php_max = $t;
			$php_sum += $t;
		}

		echo 'Average bash = ', $bash_sum / 100.0, ' <br />', PHP_EOL;
		echo 'Average php = ', $php_sum / 100.0, ' <br />', PHP_EOL;
		echo 'Max bash = ', $bash_max, ' <br />', PHP_EOL;
		echo 'Max php = ', $php_max, ' <br />', PHP_EOL;
*/

		file_put_contents ( '/var/www/html/database/newest.txt', time () . PHP_EOL );
		// will write the Unix timestamp to newest.txt

		wh_define ( 'TEXT_SUCCESS', '<strong style="color: #00FF00">Successfully added a club</strong>' );


	}

	addClub ( );
?>
