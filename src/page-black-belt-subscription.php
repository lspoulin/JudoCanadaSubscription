<?php
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    //echo "request is a post ";
    $type = $_POST['type']; 
    switch ($type) { 
        case 'email': 
            sendEmail(); 
            break; 
        case 'validate':
            validate();
            break; 
    } 
    exit(); 
} 
 
function sendEmail(){ 
    echo "send email please setup the smtp server";
    $msg = "First line of text\nSecond line of text";
    // use wordwrap() if lines are longer than 70 characters 
    $msg = wordwrap($msg,70); 
    // send email 
    /*ini_set('SMTP','myserver'); 
    ini_set('smtp_port',25); 
    mail("lspoulin@gmail.com","My subject",$msg);
     */ 
} 

function validate(){
   $data = json_decode($_POST['data'], true);
   var_dump($data);
}
?>

<?php get_header(); ?>
  <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri().'/bin/style/w3.css';?>">
  <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri().'/bin/style/dcalendar.picker.css';?>">
  <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri().'/bin/style/navigation.css';?>">
  <script src="<?php echo get_stylesheet_directory_uri().'/bin/javascript/jquery-3.3.1.min.js';?>"></script>
  <script src="<?php echo get_stylesheet_directory_uri().'/bin/javascript/lib.js';?>"></script>
  <script src="<?php echo get_stylesheet_directory_uri().'/bin/javascript/dcalendar.picker.js';?>"></script>

    <div id="primary" class="site-content">
        <div id="content" role="main">
           <div class="w3-container">
              <h1><?php the_title(); ?></h1>
            </div>

          <div id="idDivPageIndicator" style="text-align:center;margin-top:40px;margin-bottom:40px;margin-left:40px;margin-right:40px;"></div>
                    <!-- inject:form_pages:html -->
                    <!-- contents of html partials will be injected here -->
                    <!-- endinject -->
            <?php while ( have_posts() ) : the_post(); ?>

                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                        <header class="entry-header">
                            <h1 class="entry-title"><?php the_title(); ?></h1>
                        </header>

                        <div class="entry-content">
                            <?php the_content(); ?>
                            

                        </div> .entry-content -->

                    </article><!-- #post -->

            <?php endwhile; // end of the loop. ?>

        </div><!-- #content -->
    </div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>