<?php

	// Settings
	$server_name='SERVER_NAME';

	// Directories
	$destination_dir="/home/Backup/Daily";
	$log_file="/home/Backup/datacenter_create_files.log";
	
	// List of bases
	$list_of_bases=Array();
	$list_of_bases[]='database_name';
    
        // Mail parameters
        $SendMailFrom='post@5-systems.ru';
        $SendMailTo=Array();
	$SendMailTo[]='account@5-systems.ru';

        $SMTPHost = 'smtp.yandex.ru';
        $SMTPUser = 'post@5-systems.ru';
        $SMTPPassword = 'passwd';
        $SMTPPort = 465;
        $SMTPSecure='ssl';
        $SMTPDebug=0;
        // End settings

?>
