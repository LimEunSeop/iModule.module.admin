<?php
/**
 * 이 파일은 iModule 관리자모듈의 일부입니다. (https://www.imodule.kr)
 * 
 * 모듈정보를 가져온다.
 *
 * @file /modules/admin/process/@getModule.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 6. 21.
 */
if (defined('__IM__') == false) exit;

$module = Request('target');

/**
 * 모듈 package.json 정보를 가져온다.
 */
$package = $this->getModule()->getPackage($module);
if ($package == null) {
	$results->success = false;
	$results->message = $this->getErrorText('NOT_FOUND');
} else {
	$data = new stdClass();
	$data->icon = $package->icon;
	$data->title = $this->getModule()->getTitle($module);
	$data->version = $package->version;
	$data->author = $package->author->name.' (<a href="mailto:'.$package->author->email.'">'.$package->author->email.'</a>)';
	$data->homepage = '<a href="mailto:'.$package->homepage.'" target="_blank">'.$package->homepage.'</a>';
	$data->language = $package->language;
	$data->description = $this->getModule()->getDescription($module);
	
	$data->context = $package->context;
	$data->global = $package->global;
	$data->article = $package->article;
	$data->admin = $package->admin;
	
	$data->dependencies = array();
	foreach ($package->dependencies as $dependency=>$version) {
		if ($dependency == 'core') {
			$data->dependencies[] = array(
				'name'=>'iModule v'.$version,
				'checked'=>version_compare($version,__IM_VERSION__,'>=') == true
			);
		} else {
			$name = $this->getModule()->getTitle($dependency);
			
			$data->dependencies[] = array(
				'name'=>($name !== null ? $name : $dependency).' v'.$version,
				'checked'=>$this->getModule()->isInstalled($dependency) === true && version_compare($version,$package->version,'>=') == true
			);
		}
	}
	
	$data->isInstalled = $this->getModule()->isInstalled($module);
	$data->isLatest = $data->isInstalled == true && $this->getModule()->getInstalled($module)->hash == $this->getModule()->getHash($module);
	$data->isConfigPanel = $this->getModule()->isConfigPanel($module);
	
	$results->success = true;
	$results->data = $data;
}
?>