<?php
	require_once('settings_reindex_databases.php');
	require_once('5c_files_lib.php');
	require_once('5c_email_lib.php');

	date_default_timezone_set('Etc/GMT-3');

        write_log('blank_line', $log_file);
        write_log('start reindex databases', $log_file);

	$all_operations_status=true;
        $list_databases_reindexed=Array();
	while( list($key, $value)=each($list_of_bases) ) {

		$database_name=$value;
		$shell_command='su -c "reindexdb '.$database_name.'" postgres ';
		$operation_status=exec_shell_command($shell_command, $log_file, 'pg_dump '.$database_name);

		if( $operation_status!==0 ) {
			$all_operations_status=false;
		}
		else {
                       $list_databases_reindexed[]=$database_name;
		}
	}

       // Send mail
       $subject='';
       $body='';
       $loc_server_name='LOCAL SERVER';
       if( isset($server_name)
           && strlen($server_name)>0 ) {

           $loc_server_name=$server_name;
       }

       if($all_operations_status===true) {
               $subject = $loc_server_name.": переиндексация выполнилась успешно";
               $body = $loc_server_name.": переиндексация выполнилась успешно для БД:<br/>";

              while( list($key, $value)=each($list_databases_reindexed) ) {
                        $body.='<br/>'.$value;
              }
       }
       else {
               $subject = $loc_server_name.": переиндексация выполнилась с ошибками";
               $body = $loc_server_name.": переиндексация выполнилась с ошибками: подробности в файле ".$log_file;
       }

       send_message($SendMailFrom, $SendMailTo, $SMTPHost, $SMTPPort, $SMTPUser, $SMTPPassword, $SMTPSecure, $SMTPDebug, $subject, $body);

       write_log('finish reindex databases', $log_file);
?>
