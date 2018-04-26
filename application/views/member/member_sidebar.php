<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <title>Caraway Portal</title>

         <!-- Bootstrap CSS CDN -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        
        <!-- Style Sheets Used by the Original Author for the large calendar -->
        <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo  base_url(); ?>style/reset.css" type="text/css"> <!-- CSS reset -->
        <link rel="stylesheet" href="<?php echo  base_url(); ?>style/calendarStyle.css"> <!-- Resource style -->

        <!-- Our Custom CSS -->
        <link rel="stylesheet" href="<?php echo base_url('style/family_sidebar.css'); ?>" type = "text/css">
        <link rel="stylesheet" href="<?php echo  base_url(); ?>style/familyStyle.css">
        <link rel="stylesheet" href="<?php echo  base_url(); ?>style/error_message_style.css">

        <!-- JQuery & JQueryUI Resources-->
        <link rel="stylesheet" type="text/css" media="screen" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/themes/base/jquery-ui.css"> 

    </head>
    

    <title> <?php echo $title ?>  </title>

        <?php  $this->load->helper('url'); ?>

        <div class="wrapper">
            <!-- Sidebar Holder -->
            <nav id="sidebar">

                <div class = "logo-contain">
                    <img id="logo" src="<?php echo  base_url('media/portal-logo.png'); ?>"></img>
                </div>
               
                  <ul class="list-unstyled CTAs">
                    <li><a href="logout" class="article">Log Out</a></li>
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
                                <li><a href="donation">Hour Donation</a></li>
                               
                                <li><a href="calendar">Sign Up</a></li>
                                <li><a href="mybookings">My Bookings</a></li>
                            </ul>
                        </div>
                    </div>
                </nav>
   



        <!-- jQuery CDN -->
         <script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
         <!-- Bootstrap Js CDN -->
         <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

         <script type="text/javascript">
             $(document).ready(function () {
                 $('#sidebarCollapse').on('click', function () {
                     $('#sidebar').toggleClass('active');
                     $(this).toggleClass('active');
                 });
             });
         </script>
