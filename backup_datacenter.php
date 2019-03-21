<?php
    
    date_default_timezone_set ('Etc/GMT-3');
    require_once("settings_backup_datacenter.php");
    require_once("5c_files_lib.php");
    require_once("5c_email_lib.php");

    // Main
    $all_opertions_status=true;
    
    write_log('blank_line', $log_file);
    write_log('start backup', $log_file);	

    // Read copied files
    $copied_files=select_files($destination_ftp, "FILE", 1);
    $copied_files_by_name=Array();
    while( list($key, $value)=each($copied_files) ) {
	$copied_files_by_name[$key]=$value['name'];
    }

    $list_copied_files_after_backup=Array();	
    
    // Copy files
    foreach( $list_of_bases as $base_name ) {
    
	// Copy the most recent file
	$selected_files=select_files($search_dir, $base_name, 1);
	
	$selected_tmp=Array();
	$initial_selected_files=Array();
	while( list($key, $value)=each($selected_files) ) {
	    $selected_tmp[$key]=$value['mtime'];
	    $initial_selected_files[$key]=$value;
	}

	array_multisort($selected_tmp, SORT_DESC, $selected_files);

	$file_counter=0;
	$selected_files_short=Array();
	while( (list($key, $value)=each($selected_files)) && $file_counter<1 ) {
	    $selected_files_short[$key]=$value;
	    $file_counter+=1;
	}
	
	// Do not copy, if the file is copied 
	$selected_files_short_tmp=Array();
	reset($selected_files_short);
	while( list($key, $value)=each($selected_files_short) ) {
	
	    if(  in_array($value['name'], $copied_files_by_name) ) {
		continue;
	    }
	    
	    $selected_files_short_tmp[$key]=$value;
	}

	$selected_files_short=$selected_files_short_tmp;
	
	if( count($selected_files_short)>0 ) {		
	
	    reset($selected_files_short);
	    $copy_status=action_ftp_put($selected_files_short, $destination_ftp, $log_file, 'FTP_BINARY', true);
	    if( $copy_status===false ) {
		$all_opertions_status=false;
	    }
	    else {
		$loc_destination_ftp=$destination_ftp;
		if( substr($loc_destination_ftp, -1)!=='/' ) $loc_destination_ftp.='/';

		while( list($copied_file, $file_attributes)=each($selected_files_short) ) {
		    $loc_copied_file_path=$loc_destination_ftp.$file_attributes['name'];
		    $loc_at_position=strpos($loc_copied_file_path, '@');
		    if( $loc_at_position!==false ) $loc_copied_file_path=substr($loc_copied_file_path, $loc_at_position);
		    $list_copied_files_after_backup[]=$loc_copied_file_path;
		}

	    }
	    
	    if( $copy_status===true ) {
		
		// Remove all files except 3 recent
		$selected_files=select_files($destination_ftp, $base_name, 1);
		
		$selected_tmp=Array();
		while( list($key, $value)=each($selected_files) ) {
		    $selected_tmp[$key]=$value['mtime'];
		}

		array_multisort($selected_tmp, SORT_DESC, $selected_files);

	        $loc_number_of_files_ftp=5;

	        if( isset($number_of_files_ftp_default)
	            && is_numeric($number_of_files_ftp_default) ) $loc_number_of_files_ftp=intVal($number_of_files_ftp_default);

	        if( isset($number_of_files_for_each_base_ftp)
	            && is_array($number_of_files_for_each_base_ftp)
	            && array_key_exists($base_name, $number_of_files_for_each_base_ftp)
	            && is_numeric($number_of_files_for_each_base_ftp[$base_name]) ) {

	            $loc_number_of_files_ftp=intVal($number_of_files_for_each_base_ftp[$base_name]);
	        }

		$file_counter=0;
		$selected_files_short=Array();
		while( list($key, $value)=each($selected_files) ) {

		    if( $file_counter>=$loc_number_of_files_ftp ) {
			$selected_files_short[$key]=$value;
		    }

		    $file_counter+=1;
		}

		if( count($selected_files_short)>0 ) {
		
		    $status_delete=action_delete($selected_files_short, $log_file);
		    if( $status_delete===false ) {
			$all_opertions_status=false;
		    }

		}

	    }
	    
	}
	
	if( count($initial_selected_files)>0 ) {
	
	    // Move files in other directories
	    $rename_dst=$destination_dir_others;		
	    
	    $status_rename=action_rename($initial_selected_files, $rename_dst, $log_file);

	    if( $status_rename===false ) {
		$all_opertions_status=false;
	    }

	    // Remove all files except 7 recent
	    $selected_files=select_files($rename_dst, $base_name, 1);
	    
	    $selected_tmp=Array();
	    while( list($key, $value)=each($selected_files) ) {
		$selected_tmp[$key]=$value['mtime'];
	    }

	    array_multisort($selected_tmp, SORT_DESC, $selected_files);

            $loc_number_of_files=7;

            if( isset($number_of_files_default)
                && is_numeric($number_of_files_default) ) $loc_number_of_files=intVal($number_of_files_default);

            if( isset($number_of_files_for_each_base)
                && is_array($number_of_files_for_each_base)
                && array_key_exists($base_name, $number_of_files_for_each_base)
                && is_numeric($number_of_files_for_each_base[$base_name]) ) {

                $loc_number_of_files=intVal($number_of_files_for_each_base[$base_name]);
            }

	    $file_counter=0;
	    $selected_files_short=Array();
	    while( list($key, $value)=each($selected_files) ) {

		if( $file_counter>=$loc_number_of_files ) {
		    $selected_files_short[$key]=$value;
		}

		$file_counter+=1;
	    }

	    if( count($selected_files_short)>0 ) {
	    
		$status_delete=action_delete($selected_files_short, $log_file);
		if( $status_delete===false ) {
		    $all_opertions_status=false;
		}

	    }

	
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

    if($all_opertions_status===true) {
	$subject = $loc_server_name.": копирование на FTP выполнилось успешно";
	$body = $loc_server_name.": копирование на FTP выполнилось успешно. Список файлов:<br/>";

                while( list($key, $value)=each($list_copied_files_after_backup) ) {
                    $body.='<br/>'.$value;
                }

    }
    else {
	$subject = $loc_server_name.": копирование на FTP выполнилось с ошибками";
	$body = $loc_server_name.": копирование на FTP выполнилось с ошибками: подробности в файле ".$log_file;	
    }
    
    send_message($SendMailFrom, $SendMailTo, $SMTPHost, $SMTPPort, $SMTPUser, $SMTPPassword, $SMTPSecure, $SMTPDebug, $subject, $body);
    write_log('finish backup', $log_file);

    
function select_function($select_function_type, $file_path, $file_attributes) {
    
    $function_result=false;
       
    if( substr($file_attributes['name'], 0, strlen($select_function_type))===$select_function_type ) {

	if( $file_attributes['directory']===false ) {
	    $function_result=true;
	}
    
    }
    elseif( $select_function_type==="OLD_FILES" ) {
	
	if( $file_attributes['directory']===false ) {
	
	    $cur_time=time();
	    $time_diff=4*24*60*60;
	    if( $cur_time-$file_attributes['mtime']>$time_diff ) {
		$function_result=true;	
	    }
	    
	}
	
    }
    elseif( $select_function_type==="DIRECTORY" ) {
	$function_result=($file_attributes['directory']===true);
    }
    elseif( $select_function_type==="FILE" ) {
	$function_result=($file_attributes['directory']===false);
    }	

    return($function_result);
}
    
?>
