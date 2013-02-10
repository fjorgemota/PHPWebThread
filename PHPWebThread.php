<?php
error_reporting(E_ALL);
if(!class_exists("PHPWebThread")){
	if (session_id() == "") { // It's session not started?
	    session_start(); // Just initialize a session
	}
	class PHPWebThread
	{
	    public static $instances = array();
	    private static $print_thread = false; // Just a flag to process one time the function
	    private static $keep_alive = false; // Deactivate the keep alive mode in servers that support it
	    private $run_original; // Save the method (or function) to call :B
	    private $arguments = array(); // Save the arguments
	    private $started; // It is started?
	    private $name; // The name of the thread
	    private $id; // The ID of the thread
	    private $func_name; // The function name
	    private $js_files; // The javascript files to load 
	    private $css_files; // The javascript files to load 
	    private $cached; //Is cached?
	    public function __construct()
	    {
	        /* Constructs the thread */
	        $arguments = func_get_args();
	        if (get_class($this) != "PHPWebThread") { // If it extended
	            $this->run_original = array(
	                $this,
	                "run"
	            ); // Uses the RUN method :B
	        } else if (count($arguments) > 0) {
	            $this->run_original = array_shift($arguments); // Save the content of the first argument and shift of the array of arguments
	        } else {
	            throw new Exception("Function not detected."); // Raise a exception, we not have a callable! :V
	        }
	        $this->arguments        = $arguments;
	        $this->started          = false; // Is start? Now, not..
	        $this->id               = count(PHPWebThread::$instances); // Get the auto ID
	        $this->name             = 'Thread-' . $this->id; // Set the name
	        $this->func_name        = sha1(serialize($this->run_original)); // Save the name of the function
	        $this->css_files        = array(); // Initialize the array
	        $this->js_files 		= array(); // Initialize the array
	        $this->cached           = false;
	        PHPWebThread::$instances[] = $this; // Add this instance 
	    }
	    private function convertHTMLtoJS($html, $parent = NULL, $counter = 0)
	    {
	    	/* Just convert the HTML code to JS. Simple, huh? */
	        if (is_string($html)) {
	            $type = "string";
	        } else {
	            $type = get_class($html);
	        }
			++$counter;
	        switch ($type) {
	            case "DOMDocument":
	            case "DOMElement":
	                $id = "e".$counter;
	                if ($html->nodeName != "#document") {
	                    $code = "var " . $id . "=d.createElement('" . $html->nodeName . "');";
	                } else {
	                    $code = "";
	                }
	                if (!!$html->attributes) {
						foreach ($html->attributes as $attr) {
		                	$code .= $id.".setAttribute('" . $attr->name . "', '" . $attr->value . "');";
		                }
	                }
	                if (!!$html->childNodes) {
							foreach ($html->childNodes as $child) {
		                        if ($child->nodeType == XML_TEXT_NODE) {
		                            $code .= $id . ".appendChild(d.createTextNode('" . utf8_decode(str_replace(array(
		                                "\r\n",
		                                "\r",
		                                "\n"
		                            ), '\n', $child->nodeValue)) . "'));\n";
		                        } else {
		                            $element = $this->convertHTMLtoJS($child, $html, $counter);
		                            $code .= $element["code"];
		                            if ($html->nodeName != "#document") {
		                                $code .= $id . ".appendChild(" . $element["id"] . ");";
		                            } else {
		                                $id = $element["id"];
		                            }
		                        }
		                    }
	                }
	                return array(
	                    "code" => $code,
	                    "id" => $id
	                );
	                break;
	            case "DOMDocumentType":
	                return array(
	                    "code" => "",
	                    "id" => ""
	                );
	                break;
	            default:
	            case "string":
	                $dom                      = new DOMDocument();
	                $dom->strictErrorChecking = FALSE;
	                $dom->loadHTML($html);
	                if (!!$parent) {
	                    if ($parent == "content") {
	                        $dom = $dom->getElementsByTagName("body");
	                        $dom = $dom->item(0)->firstChild;
	                    } else if ($parent[0] == "#") {
	                        $dom = $dom->getElementById(substr($parent, 1));
	                    } else {
	                        $dom = $dom->getElementsByTagName($parent);
	                        $dom = $dom->item(0);
	                    }
						$result = "var d = document;";
	                    $result .= $this->convertHTMLtoJS($dom, NULL, $counter);
	                    return $result;
	                } else { // We not have a parent
	                    $result = array();
	                    $dom    = $dom->getElementsByTagName("body");
	                    $dom    = $dom->item(0);
	                    for ($c = 0, $length = $dom->childNodes->length; $c < $length; ++$c) {
	                        $node     = $dom->childNodes->item($c);
	                        $result[] = $this->convertHTMLtoJS($node, $dom, $counter);
							++$counter;
	                    }
						if($counter > 0){
							$result[0]["code"] = "var d = document;".$result[0]["code"];
						}
	                    return $result;
	                }
	                break;
	        }
	        return NULL;
	    }
	    public function setCache($activated)
	    {
	    	/* It's to cache the request? */
	    	if($this->started){
	    		throw new Exception("Not able to change cache status with the thread started");
	    	}
	        $this->cached = $activated;
	    }
	    public function isCached()
	    {
	    	/* Return true if the thread is to be cached */ 
	        return $this->cached;
	    }
	    private function getHTML()
	    {
	    	/* Just return the HTML to call the PHP Thread */
	        $content = "";
	        $content .= "<script language='javascript' type='text/javascript' data-phpwebthread-id='" . $this->id . "'>\n//<!--\n";
			$first = true; // It's the first thread that is initializated? We assume to yes
	        foreach(PHPWebThread::$instances as $thread){ // Iterate over all the threads to verify it
	        	if($thread->isStarted()){ //It's started?
	        		$first = false; //Oh, it's not the first! :B
	        		break;
	        	}
	        }
			if($first){
				$content .= "window.php_web_threads = [];\n";
			}
	        $content .= "window.php_web_threads.push(function(){\n";
	        $content .= "var thread = new PHPWebThread('" . $this->func_name . "',".($this->cached?"true":"false").");thread.start();";//Instance and start the new thread
	        $content .= "});\n";
	        $content .= "//-->\n</script>";
	        $_SESSION["PHPWebThread_" . $this->id . "_" . $this->func_name] = $_SERVER["SCRIPT_FILENAME"]; // Save the requested filename in a session to posterior use..
	        return $content;
	    }
	    public function addJSFile($path)
	    {
	    	/* Add a Javascript file to the queue */
	        $this->js_files[] = $path;
	    }
	    public function addCSSFile($path)
	    {
	    	/* Add a CSS file to the queue */
	        $this->css_files[] = $path;
	    }
	    public function start()
	    {
	        /* Starts the thread */
	        if($this->started){
	        	throw new Exception("The thread was already started!");
	        }
	        if (!PHPWebThread::isThreadProcessing() and PHPWebThread::isActive()) {
	            echo $this->getHTML();
	        }
            else if(!PHPWebThread::isActive()){ // It's not active?
                ob_start(); // Buffer it! :D
                foreach($this->css_files as $css_file){
                    echo "<link rel='stylesheet' type='text/css' href='",$css_file,"' />\n";
                }
                call_user_func_array($this->run_original, $this->arguments); // Just call the function! 
                foreach($this->js_files as $js_file){
                    echo "<script language='javascript' type='text/javascript' src='",$js_file,"'></script>\n";
                }
                ob_end_flush(); // Just send the content!
                flush();
            }
	        $this->started = true;
	    }
		public static function isActive(){
			return !isset($_GET["phpwebthread_deactivate"]);
		} 
	    public function isStarted()
	    {
	        /* Is started? */
	        return $this->started;
	    }
	    public function getFunctionName()
	    {
	        /* Just return the name, and this not has a setter */
	        return $this->func_name;
	    }
	    public function getID()
	    {
	        /* Just a getter to return ID */
	        return $this->id;
	    }
	    public function getName()
	    {
	        /* Just a getter to return name */
	        return $this->name;
	    }
	    public function setName($name)
	    {
	        /* Just set the name of the thread */
	        return $this->name;
	    }
	    public function getJSFiles()
	    {
	        /* Just the javascript files */
	        return $this->js_files;
	    }
	    public function getCSSFiles()
	    {
	        /* Just the javascript files */
	        return $this->css_files;
	    }
	    public function handle_run()
	    {
	    	/* Executes the function and convert it to Javascript :D*/
	        ob_start(); // Just start the buffer
	        call_user_func_array($this->run_original, $this->arguments); // Just call it, with arguments!
	        $html_content = ob_get_contents(); // Get all the content from the buffer
	        ob_end_clean(); // And clean and end the buffer
	        $javascript_content = $this->convertHTMLtoJS($html_content); // Converte para javascript \o/
	        return $javascript_content; //Just returns :D
	    }
	    private static function isThreadProcessing()
	    {
	    	/* Return true if it's a processing page */
	        $path  = $_SERVER["REQUEST_URI"]; // Get the Request URI of the script
	        return preg_match('/.*phpwebthread\/\d*\/([a-z0-9]*).(php|js)$/', $path) == 1; // Verify if the page extension is .js and if PHPWebThread is a directory in the request uri
	    }
	    public static function printScripts($path = "")
	    {
	    	/* Echo the script to load the PHPWebThread script */
	    	if(!PHPWebThread::isActive()){
	    	    return;
	    	}
			?>
			<script language="javascript" type="text/javascript" src="<?php
			        echo $path;
			?>PHPWebThread.js" async="async" data-server-supported="<?php echo PHPWebThread::isSupported()?"1":"0"; ?>"></script>
			<?php
	    }
        private static function isSupported(){
            /* Just verify if it's apache */
            return strtolower(substr($_SERVER["SERVER_SOFTWARE"], 0, 6)) == "apache";
        }
		public static function setKeepAlive($active){
			/* This function can activate or deactivate keep-alive, transforming each request in a parallel request */
			PHPWebThread::$keep_alive = $active; 
		}
	    public static function process()
	    {
	    	/* Execute the thread */ 
	    	if(PHPWebThread::$print_thread or !PHPWebThread::isThreadProcessing()){ // It's printed or it's to process a thread?
	    		return; // If yes, we just show a blank page
	    	}
	    	PHPWebThread::$print_thread = true;
	        $path   = $_SERVER["REQUEST_URI"]; // Get the Request URI of the script
	        $parts  = explode("/", $path); // Split into parts
	        $length = count($parts);
	        list($name, $ext) = explode(".",array_pop($parts));// Extract the name and the extension
	        $cached = $ext == "js";
	        $id     = array_pop($parts); // Extract the ID
	        $key = "PHPWebThread_" . $id . "_" . $name;
	        if (!isset($_SESSION[$key])) { // It's seted?
	            return;//If not, we just return and show a blank page :~
	        }
	        $the_file = $_SESSION[$key];// Save it in a variable! :D
	        unset($_SESSION[$key]);// Delete from the session
	        if (!file_exists($the_file)) {
	            return;
	        }
			ob_start(); // Start the buffering, we do not need of any content echoed from the page now! :D
	        require($the_file); // Load the file
	        ob_end_clean(); // Clean the content. Simple this. 
	        if (array_pop($parts) != "phpwebthread") {
	            return;
	        } else if (!is_numeric($id) or count(PHPWebThread::$instances) < $id) {
	           return;
	        }
			$thread = PHPWebThread::$instances[$id]; // Get the Thread, or raises a error
	        if ($thread->getFunctionName() != $name) {
	            return;
	        }
	        $path = implode("/", array_slice($parts, 1, -2));
	        // Ignore the cache directory
	        $cache_dir = dirname($_SERVER["SCRIPT_FILENAME"]) . "/phpwebthread/";
	        // Just create the directory
	        if ($thread->isCached() and $cached and !is_dir($cache_dir.$id)) {
	            mkdir($cache_dir.$id, 0777, true);
			}
	        header("Content-type: text/javascript");
			if(!PHPWebThread::$keep_alive){
				header("Connection: Close");
			}
	        $result = $thread->handle_run();
	        // Execute the function and call as...Javascript \o/
	        $content = "";
	        $content .= "((function(){";
	        $content .= "var elements = [];";
	        foreach ($result as $element) {
	            $content .= $element['code']; // Paste the DOM code
	            $content .= "elements.push(". $element["id"].");";// Append the element to the array:D
	        }
	        $css_list  = "[";//Initialize a javascript array of CSS Files to load
	        $css_files = $thread->getCSSFiles(); // Get the array of CSS Files to load
	        if (!empty($css_files)) { // It's empty?
	            foreach ($css_files as $file) { // Iterate over it!
	                $css_list .= "'" . $file . "', "; // Append it to the array
	            }
	            $css_list = substr($css_list, 0, -2); // Removes the last comma from the string
	        }
	        $css_list .= "]"; // Close the javascript array of CSS files to load
	        $js_list  = "["; // Initialize the javascript array of JS Files to load
	        $js_files = $thread->getJSFiles();// Get the array of Javascript Files to load
	        if (!empty($js_files)) { // It's empty?
	            foreach ($js_files as $file) { // Iterate over it!
	                $js_list .= "'" . $file . "', "; // Append it to the array
	            }
	            $js_list = substr($js_list, 0, -2); // Removes the last comma from the string
	        }
	        $js_list .= "]"; // Close the javascript array of JS Files to load
	        $content .= "PHPWebThread.getInstance(".$id.").put(elements, ".$css_list.", ".$js_list.")"; // Call the .put method in the PHPWebThread Javascript instance! :D
	        $content .= "})());"; // And call the instruction at the final of the loading
			if($thread->isCached() and $cached){ // Verify if it's cached
				$arq = fopen($cache_dir.$id."/".$name.".js","w+"); // If yes, we create the file
				fwrite($arq, $content); // Write in it
				fclose($arq); // And close :~
			}
			echo $content; //Oh, and we echo too! :D
	    }
	}
}

?>