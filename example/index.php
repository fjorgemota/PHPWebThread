<?php
require(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."PHPWebThread.php"); // Load the file! :D
function the_header(){
	/* Just echo the code for header, simple, huh? */
	?>
	<div id="header">
		This is only the header of the website using PHPWebThreads
	</div>
	<?php
	}
	function the_content(){
		/* Just sleep and echo the code for content, simple, huh? */
	sleep(5);
	?>
	<div id="posts">
	<?php
	for($c=0;$c<2; ++$c){
	?>
	
		<div class="post">
			<h1 class="post-title">Hello World <?php echo $c; ?></h1><br />
			<div class="post-content">
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla feugiat auctor consequat. Donec nec enim mi, vel imperdiet lacus. Morbi blandit risus rhoncus eros ultrices sagittis. Vivamus quis metus vel velit euismod gravida et vitae nisi. Sed quis dolor id est consequat porta. In hac habitasse platea dictumst. Curabitur iaculis ullamcorper molestie. Pellentesque tempus molestie lectus, at malesuada lacus tincidunt sit amet. Maecenas purus velit, hendrerit eu imperdiet quis, consequat at ante. Ut faucibus magna eu diam porttitor gravida. Vestibulum pellentesque rutrum urna, non vehicula ante gravida sed. Vivamus euismod fermentum diam non dictum. Vivamus ac fermentum magna. Nulla lectus felis, consequat sit amet hendrerit ac, facilisis eu ante. Ut congue lectus ut lacus tincidunt eu egestas ligula varius.
</p><p>
Morbi ornare sem eu leo facilisis malesuada venenatis augue viverra. Cras vel dolor ante. Praesent accumsan, lacus nec auctor viverra, ipsum leo elementum lacus, posuere varius risus lectus eu ipsum. Donec sit amet turpis est. Sed ac nunc sem, at bibendum mauris. Nunc id nibh sit amet eros lacinia mattis. Curabitur ac augue dui, et tempus justo. Duis eu ornare quam.
</p>
<p>
Curabitur gravida dignissim quam, quis dictum eros cursus et. Proin tempus faucibus condimentum. Proin sed turpis nibh. Nullam sit amet mi massa. Nullam purus est, ultricies vel euismod at, imperdiet vitae nunc. Aliquam tempor elit ut purus suscipit ullamcorper. Etiam cursus, est molestie varius venenatis, mauris turpis rutrum nibh, ut sagittis quam quam sit amet ipsum. Nulla mattis, lectus ac pharetra fermentum, leo lacus tempus arcu, et cursus orci ante mattis lacus. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
</p><p>
Nam id tortor turpis. Nulla in libero et lacus sagittis molestie. Integer at urna vel dolor semper dapibus. Morbi faucibus ante vel massa volutpat gravida. Donec dictum, lorem sit amet ultrices pretium, erat magna posuere felis, non posuere augue nisl vel justo. Proin hendrerit dignissim libero eget facilisis. Suspendisse at sapien dignissim orci auctor faucibus.
</p>
<p>
Vivamus sed elit sem, sed porttitor quam. Suspendisse vestibulum ultrices metus et gravida. Aenean enim leo, dapibus et pretium sodales, pretium at lacus. Fusce lobortis tempus nunc, quis consequat nunc aliquet non. Pellentesque nec nisi dolor, eget lacinia nunc. Fusce libero nulla, ullamcorper a pellentesque sit amet, porta vel orci. Aliquam leo leo, auctor sit amet facilisis vel, viverra nec magna. Aliquam erat volutpat. Vivamus semper lacus sed eros cursus dignissim. Nam lobortis facilisis turpis vel vestibulum. Integer sit amet velit nisl, ac ultrices velit. Donec quis neque rutrum nibh aliquet blandit. Nam accumsan imperdiet lectus, a bibendum urna placerat dapibus. Nulla sit amet lorem neque, at fermentum dolor. Morbi convallis pretium dignissim. Phasellus ipsum libero, pharetra et imperdiet eget, scelerisque quis nisl.
	</p>		</div>
		</div>
	
	<?php
	}
	?>
	</div>
	<?php
	}
	function the_sidebar(){
		/* Just sleep and echo the code for sidebar, simple, huh? */
	sleep(3);
	?>
	<div id="sidebar">
	<?php
	for($c=0;$c<5; ++$c){
	?>
		<div class="widget">
			<h1 class="widget-title">Widget <?php echo $c; ?></h1><br />
			<div class="widget-content">
				This is the content of the widget <?php echo $c; ?>. =)
			</div>
		</div>
	<?php
	}
	?>
	</div>
	<?php
	}
	function the_footer(){
		/* Just echo the code to the footer, simple, huh? */
	?>
	<div id="footer">
		This is only the footer of the website running PHPWebThreads
	</div>
	<?php
	}
?>
<html>
	<head>
		<title>Teste</title>
	</head>
	<body>
		<?php
		/*
		 /* Header */
		$header = new PHPWebThread("the_header"); //Constructs a PHPWebThread to the header, passing as a argument the function with the content of the header
		$header -> addCSSFile("css/header.css"); // Load the CSS of the header
		$header -> setCache(true);// We want to cache the header, that's static, huh?
		$header -> start(); // Just start, and echo, the code to initialize the thread

		/* Content */
		$content = new PHPWebThread("the_content");  //Constructs a PHPWebThread to the content, passing as a argument the function with the content of the content
		$content -> addCSSFile("css/content.css"); // Load the CSS of the content
		$content -> start(); // Just start, and echo, the code to initialize the thread

		/* Sidebar */
		$sidebar = new PHPWebThread("the_sidebar");  //Constructs a PHPWebThread to the sidebar, passing as a argument the function with the content of the sidebar	
		$sidebar -> addCSSFile("css/sidebar.css"); // Load the CSS of the sidebar
		$sidebar -> start(); // Just start, and echo, the code to initialize the thread

		/* Footer */
		$footer = new PHPWebThread("the_footer");
		$footer -> setCache(true); // We want to cache the footer, that's static too, huh?
		$footer -> addCSSFile("css/footer.css");  // Load the CSS of the footer
		$footer -> start(); // Just start, and echo, the code to initialize the thread
		PHPWebThread::printScripts("../"); //Just echo the script to load the file PHPWebThread.js, that initializes all the threads above
		// Oh, we need to pass an argument indicating to load the PHPWebThread.js to load file from up directory
		?>
		
	</body>
</html>

