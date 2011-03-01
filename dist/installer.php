<?php

class Ld_Installer_BmSlot extends Ld_Installer
{

	/* Install */

	public function postInstall($preferences = array())
	{
		if (isset($preferences['bmUsername'])) {
			$this->setConfiguration(array('bmUsername' => $preferences['bmUsername']));
		}

		$this->handleRewrite();
	}

	/* Move */

	public function postMove()
	{
		$this->handleRewrite();
	}

	/* Update */

	public function postUpdate()
	{
		$this->handleRewrite();

		Ld_Files::denyAccess($this->getAbsolutePath() . '/views', true);
	}

	/* App Management */

	public function setConfiguration($configuration)
	{
		if (isset($configuration['maxItems'])) {
			$configuration['maxItems'] = (int)$configuration['maxItems'];
			if ($configuration['maxItems'] <= 0 || $configuration['maxItems'] >= 250) {
				$configuration['maxItems'] = 50;
			}
		}
		$configuration = array_merge($this->getConfiguration(), $configuration);
		return parent::setConfiguration($configuration);
	}

	/* Install Utilities */

	public function handleRewrite()
	{
		if (defined('LD_REWRITE') && constant('LD_REWRITE')) {
			$path = $this->getSite()->getPath() . '/' . $this->getPath() . '/';
			$htaccess  = "RewriteEngine on\n";
			$htaccess .= "RewriteBase $path\n";
			$htaccess .= "RewriteCond %{REQUEST_FILENAME} !-f\n";
			$htaccess .= "RewriteCond %{REQUEST_FILENAME} !-d\n";
			$htaccess .= "RewriteRule ^(.*)$ index.php?/$1 [QSA,L]\n";
			Ld_Files::put($this->getAbsolutePath() . "/.htaccess", $htaccess);
		}
		if (defined('LD_NGINX') && constant('LD_NGINX')) {
			// Generate configuration
			$path = $this->getSite()->getPath() . '/' . $this->getPath() . '/';
			$nginxConf  = 'location {PATH} {' . "\n";
			$nginxConf .= '  if (!-e $request_filename) {' . "\n";
			$nginxConf .= '   rewrite ^{PATH}(.*)$  {PATH}index.php?uri=$1 last;' . "\n";
			$nginxConf .= '  }' . "\n";
			$nginxConf .= '}' . "\n";
			$nginxConf = str_replace('{PATH}', $path, $nginxConf);
			// Write configuration
			$nginxDir = $this->getSite()->getDirectory('dist') . '/nginx';
			Ld_Files::ensureDirExists($nginxDir);
			Ld_Files::put($nginxDir . "/" . $this->getInstance()->getId() . ".conf", $nginxConf);
		}
	}

	public function getLinks()
	{
		$links = parent::getLinks();
		$configuration = $this->getConfiguration();
		$username = $configuration['bmUsername'];
		$links[] = array(
			'id'  => 'user_timeline',
			'title' => "User timeline",
			'rel'   => 'public-feed',
			// 'href'  => $this->getInstance()->getAbsoluteUrl("/feed/$username"),
			'href'  => "http://blogmarks.net/api/user/$username/marks/?format=atom",
			'type'  => 'application/atom+xml'
		);
		$links[] = array(
			'id'  => 'friends_timeline',
			'title' => "Friends timeline",
			'rel'   => 'feed',
			// 'href'  => $this->getInstance()->getAbsoluteUrl("/feed/$username/mentions"),
			'href'  => "http://blogmarks.net/api/user/$username/friends/marks/?format=atom",
			'type'  => 'application/atom+xml'
		);
		return $links;
	}
}
