<?php
	// Settings
        $server_name='SERVER_NAME';

	// Directories
	$search_dir="/home/Backup/Daily";
	$destination_ftp="ftp://user:passwd@domain.ru/folder";

	$destination_dir_others="/home/Backup/Bases";
	$log_file="/home/Backup/datacenter_ftp.log";
	
	// Mail peremeters
	$SendMailFrom='post@5-systems.ru';
	$SendMailTo=Array();
	$SendMailTo[]='account@5-systems.ru';
	
        $SMTPHost = 'smtp.yandex.ru';
        $SMTPUser = 'post@mydomain.ru';
        $SMTPPassword = 'passwd';
        $SMTPPort = 465;
        $SMTPSecure='ssl';
        $SMTPDebug=0;
	
	// List of bases
	$list_of_bases=Array();
	$list_of_bases[]='database_name';

        $number_of_files_default=7;
        $number_of_files_for_each_base=array();
        $number_of_files_for_each_base['database_name']=7;

        $number_of_files_ftp_default=7;
        $number_of_files_for_each_base_ftp=array();
        $number_of_files_for_each_base_ftp['database_name']=7;

	// End settings
?>
