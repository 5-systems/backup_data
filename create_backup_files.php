<?php
	require_once('settings_create_backup_files.php');
	require_once('5c_files_lib.php');
	require_once('5c_email_lib.php');

	date_default_timezone_set('Etc/GMT-3');

        write_log('blank_line', $log_file);
        write_log('start create files', $log_file);

	$loc_destination_dir=$destination_dir;
	if( strlen($loc_destination_dir)>0 && substr($loc_destination_dir, -1)!=='/' ) $loc_destination_dir.='/';

	$all_operations_status=true;
	$list_databases_backuped=Array();
	while( list($key, $value)=each($list_of_bases) ) {
		$database_name=$value;
		$backup_file_name=$database_name.'_backup-'.date('Ymd').'.gz';
		$backup_full_path=$loc_destination_dir.$backup_file_name;

		$shell_command='su -c "pg_dump '.$database_name.' | gzip > '.$backup_full_path.'" postgres ';
		$operation_status=exec_shell_command($shell_command, $log_file, 'pg_dump '.$database_name);

		if( $operation_status!==0 ) {
			$all_operations_status=false;
		}
		else {
			$list_databases_backuped[]=$database_name;
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
               $subject = $loc_server_name.": создание бэкапов выполнилось успешно";
               $body = $loc_server_name.': создание бэкапов выполнилось успешно по БД:<br/>';
	       
	      while( list($key, $value)=each($list_databases_backuped) ) {
	      		$body.='<br/>'.$value;       
	      }
       }
       else {
               $subject = $loc_server_name.": создание файлов выполнилось с ошибками";
               $body = $loc_server_name.": создание файлов выполнилось с ошибками: подробности в файле ".$log_file;
       }

       send_message($SendMailFrom, $SendMailTo, $SMTPHost, $SMTPPort, $SMTPUser, $SMTPPassword, $SMTPSecure, $SMTPDebug, $subject, $body);

       write_log('finish create files', $log_file);
?>
