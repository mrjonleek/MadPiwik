<?php

# Copyright (C) 2012 Jonathan Leek
# Homepage   : www.jonathanleek.com 
# Author     : Jonathan Leek	    	
# Email      : builder@dev.jonathanleek.com 
# Version    : 1.0.0                     
# License    : http://www.gnu.org/copyleft/gpl.html GNU/GPL          

######################################################################

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin');
jimport( 'joomla.html.parameter');

class plgSystemPiwik extends JPlugin
{

	function plgSystemPiwik(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->_plugin = JPluginHelper::getPlugin( 'system', 'piwik' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

	function onAfterRender()
	{
		$mainframe = &JFactory::getApplication();
		$buffer = JResponse::getBody();

		$piwik = new stdClass();
		$proto = array( 'http://', 'https://' );
		$piwik->url = trim(str_replace( $proto, '', $this->params->get('path')));
		$piwik->siteId = preg_replace("/[^0-9,.]/", "", $this->params->get('site_id'));
		$piwik->credits = $this->params->get('credit');
		
		//Put URL validation here.
		if(substr($piwik->url, -1) != '/') {
			//$piwik->url = $piwik->url . '/';
		}

		$tracking_code = '
			<!-- Piwik Tracking Code Provided by Jonathan Leek (http://www.JonathanLeek.com) -->
			<script type="text/javascript">
				var pkBaseURL = (("https:" == document.location.protocol) ? "https://'.$piwik->url.'" : "http://'.$piwik->url.'");
				document.write(unescape("%3Cscript src=\'" + pkBaseURL + "piwik.js\' type=\'text/javascript\'%3E%3C/script%3E"));
			</script>
			<script type="text/javascript">
				try {
					var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", '.$piwik->siteId.');
					piwikTracker.trackPageView();
					piwikTracker.enableLinkTracking();
				} catch( err ) {}
			</script>
			<noscript>
				<p><img src="'.$this->params->get('path').'piwik.php?idsite='.$this->params->get('site_id').'" style="border:0" alt="" /></p>
			</noscript>
			<!-- End Piwik Tracking Code -->';

		if($piwik->credits == 1)
			$tracking_code .= '<span style="font-size: 75% !important;"><a href="http://www.JonathanLeek.com">Analytics tracking code by Jonathan Leek.</a></span>';	
				
		$buffer = str_replace ("</body>", $tracking_code."</body>", $buffer);
		JResponse::setBody($buffer);
		return true;
	}

}

?>