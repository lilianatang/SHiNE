    <!-- header
    ================================================== -->
    <header class="s-header">

        <div class="header-logo">
            <a class="site-logo" href="index.html">
                <img src="<?php echo base_url('media/shine_logo.jpg'); ?>" alt="Homepage">
            </a>
        </div>

        <nav class="header-nav">

            <a href="#0" class="header-nav__close" title="close"><span>Close</span></a>

            <div class="header-nav__content">
                <h3>Navigation</h3>
                
                <ul class="header-nav__list">
                    <li class="current"><a class="smoothscroll"  href="#home" title="home">Home</a></li>
                    <li><a class="smoothscroll"  href="#about" title="about">About</a></li>
                    <li><a class="smoothscroll"  href="#services" title="services">Services</a></li>
                    <li><a class="smoothscroll"  href="#works" title="works">Works</a></li>
                    <li><a class="smoothscroll"  href="#clients" title="clients">Clients</a></li>
                    <li><a class="smoothscroll"  href="#contact" title="contact">Contact</a></li>
                    <li><a class="smoothscroll"  href="view_login" title="login">Log In</a></li>
                    <li><a class="smoothscroll"  href="#register" title="login">Register</a></li>

                </ul>
    
                <ul class="header-nav__social">
                    <li>
                        <a href="https://www.facebook.com/Society-for-the-Homeschool-Network-of-Edmonton-28338092310/"><i class="fa fa-facebook"></i></a>
                    </li>
                </ul>

            </div> <!-- end header-nav__content -->

        </nav>  <!-- end header-nav -->

        <a class="header-menu-toggle" href="#0">
            <span class="header-menu-text">Menu</span>
            <span class="header-menu-icon"></span>
        </a>

    </header> <!-- end s-header -->


    <!-- home
    ================================================== -->
    <section id="home" class="s-home target-section" data-parallax="scroll" data-image-src="<?php echo base_url('media/background.jpg'); ?>" data-natural-width=3000 data-natural-height=2000 data-position-y=center>

        <div class="overlay"></div>
        <div class="shadow-overlay"></div>

        <div class="home-content">

            <div class="row home-content__main">

                <h3>Welcome to SHiNE - The Society for the Homeschool Network of Edmonton </h3>

                <h1>
                    We are a secular, inclusion nonprofit <br> 
                    organization for home educated 
                    children in Edmonton to offer <br> resources and support.
                </h1>

                <!-- Log in button-->
                <div class="home-content__buttons">
                   
                        <button onclick="document.getElementById('id01').style.display='block'" class="smoothscroll btn btn--stroke" style="width:auto;">Login
                        </button>

                        <div id="id01" class="modal">
                            <form class="modal-content animate" action="/action_page.php">
                                <div class="imgcontainer">
                                    <span onclick="document.getElementById('id01').style.display='none'" class="close" title="Close Modal">&times;</span>
                                    <img src="<?php echo base_url('media/shine_logo.jpg'); ?>" alt="SHiNE logo" class="avatar">
                                </div>

                        <!-- The log in form -->
                                <div class="container">
                                    <label for="uname"><b>Username</b></label>
                                    <input type="text" placeholder="Enter Username" name="uname" required>

                                    <label for="psw"><b>Password</b></label>
                                    <input type="password" placeholder="Enter Password" name="psw" required>
                
                                    <button type="submit">Login</button>
                                    <label>

                                    <input type="checkbox" checked="checked" name="remember"> Remember me
                                    </label>
                                    </div>

                                    <div class="container" style="background-color:#f1f1f1">
                                    <button type="button" onclick="document.getElementById('id01').style.display='none'" class="cancelbtn">Cancel</button>
                                    <span class="psw"><a href="#">Forgot password?</a></span>
                                    </div>
                            </form>
                        </div>


                    <a href="#about" class="smoothscroll btn btn--stroke">
                        Learn About Us
                    </a>
                </div>

            </div>

            <div class="home-content__scroll">
                <a href="#about" class="scroll-link smoothscroll">
                    <span>Scroll Down</span>
                </a>
            </div>

            <div class="home-content__line"></div>

        </div> <!-- end home-content -->


        <ul class="home-social">
            <li>
                <a href="https://www.facebook.com/Society-for-the-Homeschool-Network-of-Edmonton-28338092310/"><i class="fa fa-facebook" aria-hidden="true"></i><span>Facebook</span></a>
            </li>
        </ul> 
        <!-- end home-social -->

    </section> <!-- end s-home -->


    <!-- about
    ================================================== -->
    <section id='about' class="s-about">

        <div class="row section-header has-bottom-sep" data-aos="fade-up">
            <div class="col-full">
                <h3 class="subhead subhead--dark">Hello There</h3>
                <h1 class="display-1 display-1--light">We Are SHiNE</h1>
            </div>
        </div> <!-- end section-header -->

        <div class="row about-desc" data-aos="fade-up">
            <div class="col-full">
                <p>
                The Society for the Homeschool Network of Edmonton is a non-profit secular inclusive organization whose mission is to foster a sense of belonging and a rich home-educating community through creative, diverse and oriented on children’s needs and abilities educational activities and support programs aligned with core Canadian values.
                </p>
            </div>
        </div> <!-- end about-desc -->

        <div class="row about-stats stats block-1-4 block-m-1-2 block-mob-full" data-aos="fade-up">
                

        <div class="about__line"></div>

    </section> <!-- end s-about -->


    <!-- services
    ================================================== -->
    <section id='services' class="s-services">

        <div class="row section-header has-bottom-sep" data-aos="fade-up">
            <div class="col-full">
                <h3 class="subhead">What We Do</h3>
                <h1 class="display-2">We enable families to develop educational methods that best suit their philosophies, beliefs, and lifestyle.</h1>
            </div>
        </div> <!-- end section-header -->

        <div class="row services-list block-1-2 block-tab-full">

            <div class="col-block service-item" data-aos="fade-up">
                <div class="service-icon">
                    <i class="icon-paint-brush"></i>
                </div>
                <div class="service-text">
                    <h3 class="h2">Network Support</h3>
                    <p>We promote the availability of a support network and to create a community of openness, acceptance, and belonging that includes all people interested in home education.
                    </p>
                </div>
            </div>

            <div class="col-block service-item" data-aos="fade-up">
                <div class="service-icon">
                    <i class="icon-group"></i>
                </div>
                <div class="service-text">
                    <h3 class="h2">Diversity Respect</h3>
                    <p>We recognize and respect the strength of our diversity in learning styles, instructional methods, philosophy, choice of resources, religious and political affiliation and family lifestyles.
                    </p>
                </div>
            </div>

            <div class="col-block service-item" data-aos="fade-up">
                <div class="service-icon">
                    <i class="icon-megaphone"></i>
                </div>  
                <div class="service-text">
                    <h3 class="h2">Opportunities</h3>
                    <p>We facilitate opportunities for a variety of friendly, educational, recreational and social activities for group members and/or their families.<br>
                    We  facilitate opportunities for parent to parent support that may include support group meetings, lectures, panels, retreats, informal gatherings, one to one contact, e-groups, newsletters, conferences, etc.
                    </p>
                </div>
            </div>

            <div class="col-block service-item" data-aos="fade-up">
                <div class="service-icon">
                    <i class="icon-earth"></i>
                </div>
                <div class="service-text">
                    <h3 class="h2">Communication</h3>
                    <p>We provide a forum for discussions, lectures and panels on social, educational and other subjects that will include a range of teaching and learning styles where questions and ideas can be exchanged in a non-threatening environment.
                    </p>
                </div>
            </div>

            <div class="col-block service-item" data-aos="fade-up">
                <div class="service-icon">
                    <i class="icon-cube"></i>
                </div>
                <div class="service-text">
                    <h3 class="h2">Resources</h3>
                    <p>We establish and maintain a reference and resource library.<br>

                     We respond to the various needs expressed by the members and to work together so that all members may meaningfully participate in and contribute to the group.<br>

                     We liaison with other homeschool support groups and school boards in order to promote cohesiveness within the homeschool community.


                    </p>
                </div>
            </div>
    
            <div class="col-block service-item" data-aos="fade-up">
                <div class="service-icon"><i class="icon-lego-block"></i></div>
                <div class="service-text">
                    <h3 class="h2">Enhancement</h3>
                    <p>We enhance the profile of homeschooling with businesses, government and the community at large and to promote a recognition of the importance and viability of home education.
                    </p>
                </div>
            </div>

        </div> <!-- end services-list -->

    </section> <!-- end s-services -->

     <!-- clients
    ================================================== -->
    <section id="clients" class="s-clients">

        <div class="row section-header" data-aos="fade-up">
            <div class="col-full">
                <h3 class="subhead">Partnerships</h3>
                <h1 class="display-2">SHiNE has been honored to
                partner up with these organizations</h1>
            </div>
        </div> <!-- end section-header -->

        <div class="row clients-outer" data-aos="fade-up">
            <div class="col-full">
                <div class="clients">
                    
                    <a href="#0" title="" class="clients__slide"><img src="images/clients/apple.png" /></a>
                    <a href="#0" title="" class="clients__slide"><img src="images/clients/atom.png" /></a>
                    <a href="#0" title="" class="clients__slide"><img src="images/clients/blackberry.png" /></a>
                    <a href="#0" title="" class="clients__slide"><img src="images/clients/dropbox.png" /></a>
                    <a href="#0" title="" class="clients__slide"><img src="images/clients/envato.png" /></a>
                    <a href="#0" title="" class="clients__slide"><img src="images/clients/firefox.png" /></a>
                    <a href="#0" title="" class="clients__slide"><img src="images/clients/joomla.png" /></a>
                    <a href="#0" title="" class="clients__slide"><img src="images/clients/magento.png" /></a>
                     
                </div> <!-- end clients -->
            </div> <!-- end col-full -->
        </div> <!-- end clients-outer -->

    </section> <!-- end s-clients -->



    <!-- contact
    ================================================== -->
    <section id="contact" class="s-contact">

        <div class="overlay"></div>
        <div class="contact__line"></div>

        <div class="row section-header" data-aos="fade-up">
            <div class="col-full">
                <h3 class="subhead">Contact Us</h3>
                <h1 class="display-2 display-2--light">Reach out for more information or just say hello</h1>
            </div>
        </div>

        <div class="row contact-content" data-aos="fade-up">
            
            <div class="contact-primary">

                <h3 class="h6">Send Us A Message</h3>

                <form name="contactForm" id="contactForm" method="post" action="" novalidate="novalidate">
                    <fieldset>
    
                    <div class="form-field">
                        <input name="contactName" type="text" id="contactName" placeholder="Your Name" value="" minlength="2" required="" aria-required="true" class="full-width">
                    </div>
                    <div class="form-field">
                        <input name="contactEmail" type="email" id="contactEmail" placeholder="Your Email" value="" required="" aria-required="true" class="full-width">
                    </div>
                    <div class="form-field">
                        <input name="contactSubject" type="text" id="contactSubject" placeholder="Subject" value="" class="full-width">
                    </div>
                    <div class="form-field">
                        <textarea name="contactMessage" id="contactMessage" placeholder="Your Message" rows="10" cols="50" required="" aria-required="true" class="full-width"></textarea>
                    </div>
                    <div class="form-field">
                        <button class="full-width btn--primary">Submit</button>
                        <div class="submit-loader">
                            <div class="text-loader">Sending...</div>
                            <div class="s-loader">
                                <div class="bounce1"></div>
                                <div class="bounce2"></div>
                                <div class="bounce3"></div>
                            </div>
                        </div>
                    </div>
    
                    </fieldset>
                </form>

                <!-- contact-warning -->
                <div class="message-warning">
                    Something went wrong. Please try again.
                </div> 
            
                <!-- contact-success -->
                <div class="message-success">
                    Your message was sent, thank you!<br>
                </div>

            </div> <!-- end contact-primary -->

            <div class="contact-secondary">
                <div class="contact-info">

                    <h3 class="h6 hide-on-fullwidth">Contact Info</h3>

                    <div class="cinfo">
                        <h5>Where to Find Us</h5>
                        <p>
                            Society for the Homeschool Network of Edmonton<br> 
                            PO Box 3173 Stn Main <br>
                            Sherwood Park, AB<br>
                            T8H 2T2<br>
                        </p>
                    </div>

                    <div class="cinfo">
                        <h5>Email Us At</h5>
                        <p>
                            shinedmonton@gmail.com
                        </p>
                    </div>


                    <ul class="contact-social">
                        <li>
                            <a href="https://www.facebook.com/Society-for-the-Homeschool-Network-of-Edmonton-28338092310/"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                        </li>
                    </ul> <!-- end contact-social -->

                </div> <!-- end contact-info -->
            </div> <!-- end contact-secondary -->

        </div> <!-- end contact-content -->

    </section> <!-- end s-contact -->


    <!-- footer
    ================================================== -->
    <footer>

        <div class="row footer-bottom">

            <div class="col-twelve">
                <div class="copyright">
                    <span>© Copyright SHiNE 2018</span> 
                    <span>Site Template by <a href="https://www.colorlib.com/">Colorlib</a></span>
                    <span>Modified and Developed by <a href="https://www.linkedin.com/in/lilianaquyentang/">Liliana Quyen Tang</a></span>	
                </div>

                <div class="go-top">
                    <a class="smoothscroll" title="Back to Top" href="#top"><i class="icon-arrow-up" aria-hidden="true"></i></a>
                </div>
            </div>

        </div> <!-- end footer-bottom -->

    </footer> <!-- end footer -->


    <!-- photoswipe background
    ================================================== -->
    <div aria-hidden="true" class="pswp" role="dialog" tabindex="-1">

        <div class="pswp__bg"></div>
        <div class="pswp__scroll-wrap">

            <div class="pswp__container">
                <div class="pswp__item"></div>
                <div class="pswp__item"></div>
                <div class="pswp__item"></div>
            </div>

            <div class="pswp__ui pswp__ui--hidden">
                <div class="pswp__top-bar">
                    <div class="pswp__counter"></div><button class="pswp__button pswp__button--close" title="Close (Esc)"></button> <button class="pswp__button pswp__button--share" title=
                    "Share"></button> <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button> <button class="pswp__button pswp__button--zoom" title=
                    "Zoom in/out"></button>
                    <div class="pswp__preloader">
                        <div class="pswp__preloader__icn">
                            <div class="pswp__preloader__cut">
                                <div class="pswp__preloader__donut"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                    <div class="pswp__share-tooltip"></div>
                </div><button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button> <button class="pswp__button pswp__button--arrow--right" title=
                "Next (arrow right)"></button>
                <div class="pswp__caption">
                    <div class="pswp__caption__center"></div>
                </div>
            </div>

        </div>

    </div> <!-- end photoSwipe background -->


    <!-- preloader
    ================================================== -->
    <div id="preloader">
        <div id="loader">
            <div class="line-scale-pulse-out">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div>


    <!-- Java Script
    ================================================== -->
    <script type="text/javascript" src="<?php echo base_url(); ?>script/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>script/plugins.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>script/main.js"></script>


    <script>
    // Get the modal
    var modal = document.getElementById('id01');

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    </script>
</body>

</html>