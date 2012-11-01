<?php 
/**
 * dao factory
 */
final class GFactory {
	/**
	 * Enter description here ...
	 * @param unknown_type $daoName
	 * @return GBaseDao instance
	 */
	static function dao($daoName) {
		$dao = ucfirst($daoName).'Dao';
		include SITE_ROOT.'/app/daos/'.$dao.PHP;
		$dao2 = new $dao();
		return $dao2;
	}
}
?>