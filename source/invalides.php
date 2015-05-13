<?php
    include 'head.php';
?>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-12" id="div_logo">
                    <div id="container_logo">
                    	<a href="index.php">
                        	<img src="img/logo_v2.png" id="logo" alt="logo site">
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <?php
                    include 'menu.php';
                ?>
                <div class="col-md-8">
                    <ul class="bxslider">
                        <li><img src="img/Chantier6/pic1.jpg" /></li>
                        <li><img src="img/Chantier6/pic2.jpg" /></li>
                        <li><img src="img/Chantier6/pic3.jpg" /></li>
                        <li><img src="img/Chantier6/pic4.jpg" /></li>
                        <li><img src="img/Chantier6/pic5.jpg" /></li>
                        <li><img src="img/Chantier6/pic6.jpg" /></li>
                        <li><img src="img/Chantier6/pic7.jpg" /></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="js/bootstrap.min.js"></script>

        <!-- bxSlider Javascript file -->
        <script src="js/jquery.bxslider.js"></script>
        <script type="text/javascript">
            $(document).ready(function(){
                $('.bxslider').bxSlider();
            });
        </script>

    </body>
</html>