<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <title>SHiNE</title>

         <!-- Bootstrap CSS CDN -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

        <!-- Style Sheets Used by the Original Author for the large calendar -->
        <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo  base_url(); ?>style/reset.css" type="text/css"> <!-- CSS reset -->
        <link rel="stylesheet" href="<?php echo  base_url(); ?>style/calendarStyle.css"> <!-- Resource style -->
      
        <!-- Custom CSS -->
        <link rel="stylesheet" href="<?php echo  base_url(); ?>style/adminStyle.css">
        <link rel="stylesheet" href="<?php echo base_url('style/family_sidebar.css'); ?>" type = "text/css">
        <link rel="stylesheet" href="<?php echo  base_url(); ?>style/error_message_style.css">
        <link rel="stylesheet" href="<?php echo  base_url(); ?>style/center.css">

        <!-- Jquery & Jquery UI -->
        <link rel="stylesheet" type="text/css" media="screen" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/themes/base/jquery-ui.css">
        
        <!-- JSGRID -->
        <link type="text/css" rel="stylesheet" href="<?php echo  base_url(); ?>script/jsgrid/jsgrid.min.css" />
        <link type="text/css" rel="stylesheet" href="<?php echo  base_url(); ?>script/jsgrid/jsgrid-theme.min.css" />

    </head>
    

    <title> <?php echo $title ?>  </title>

        <?php  $this->load->helper('url'); ?>




        <div class="wrapper">
            <!-- Sidebar Holder -->
            <nav id="sidebar">
                <div>
                    <img id="logo" src="<?php echo  base_url('media/portal-logo.png'); ?>"></img>
                </div>
                <ul class="list-unstyled components">
                    
                    <li class="active">
                        <a href="#homeSubmenu" data-toggle="collapse" aria-expanded="false">Account Creation</a>
                        <ul class="collapse list-unstyled" id="homeSubmenu">
                            <li><a href="create_admin">Admin</a></li>
                            <li><a href="create_family">Member</a></li>
                        </ul>
                    </li>

                    <li class ="active">
                        <a href="#pageSubmenu" data-toggle="collapse" aria-expanded="false">Account Removal</a>
                        <ul class="collapse list-unstyled" id="pageSubmenu">
                            <li><a href="remove_admin">Admin</a></li>
                            <li><a href="remove_family">Member</a></li>
                        </ul>
                    </li>

                    <li class="active">
                        <a href="#pageSubmenu2" data-toggle="collapse" aria-expanded="false">Account Edit</a>
                        <ul class="collapse list-unstyled" id="pageSubmenu2">
                            <li><a href="edit_admin">Admin</a></li>
                            <li><a href="edit_family">Member</a></li>
                        </ul>
                    </li>


                </ul>
                  <ul class="list-unstyled CTAs">
                    <li><a href="logout" class="article">Log Out</a></li>
                    <li><a href="export" class="article">Export Data</a></li>

                    

                </ul>
               
            </nav>

            <!--Page Content Holder -->

            <div id="content">

                <nav class="navbar navbar-default">
                    <div class="container-fluid">

                        <div class="navbar-header">
                            <button type="button" id="sidebarCollapse" class="navbar-btn">
                                <span></span>
                                <span></span>
                                <span></span>
                            </button>
                        </div> 



                        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                            <ul class="nav navbar-nav navbar-right">
                                <li><a href="family_statistics">Family Statistics</a></li>
                               
                                <li><a href="fieldtrip_creation">Fieldtrip Creation</a></li>
                                <li><a href="time_slot_management">Time Slot Management</a></li>
                            </ul>
                        </div>
                    </div>
                </nav>
   



        <!-- jQuery CDN -->
         <script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
         <!-- Bootstrap Js CDN -->
         <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

         <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo  base_url(); ?>script/jsgrid/jsgrid.min.js"></script>

        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>

         <script type="text/javascript">
             $(document).ready(function () {
                 $('#sidebarCollapse').on('click', function () {
                     $('#sidebar').toggleClass('active');
                     $(this).toggleClass('active');
                 });
             });
         </script>

