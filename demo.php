<?php
/**
 * Created by PhpStorm.
 * User: zhouwenping
 * Date: 2017/8/28
 * Time: 下午4:02
 */

namespace app\extend;


class demo {
	
	public function index ()
	{
		//TODO：保存目录自己定义！！！！！
		$DataDir = config ('databak_path') ? config ('databak_path') : "./DataBak/";
		$lists = $this->MyScandir ($DataDir);
		unset($lists[ 0 ], $lists[ 1 ]);
		$version = Db::query ("select VERSION()");
		$this->assign ('data', $lists);
		$this->assign ('version', $version[ 0 ][ 'VERSION()' ]);
		
		return $this->fetch ();
	}
	
	public function databak_api ($type = "", $name = "")
	{
		if ( !isset($type) ) {
			$this->error ('参数错误');
		}
		$DataDir = config ('databak_path') ? config ('databak_path') : "./DataBak/";
		$config = [
			'host' => config ('database.hostname'),
			'port' => config ('database.hostport'),
			'userName' => config ('database.username'),
			'userPassword' => config ('database.password'),
			'dbprefix' => config ('database.prefix'),
			'charset' => 'UTF8',
			'path' => $DataDir,
			'isCompress' => 1,
			//是否开启gzip压缩
			'isDownload' => 0,
			'expect' => ['`newking_online_log`'],
			//一定要加`
		];
		$MyReback = new MysqlReback($config);
		$MyReback->setDBName (config ('database.database'));
		switch ($type) {
			case "backup":
				$result = $MyReback->backup ();
				
				return $result == TRUE ? TRUE : FALSE;
			case "reback":
				$result = $MyReback->recover (input ('post.name'));
				
				return $result;
				break;
			case "delete":
				$name = input ('post.name');
				if ( !$name || $name == "" ) {
					return;
				}
				if ( unlink ($DataDir . DS . $name) ) {
					return TRUE;
				} else {
					return;
				}
				break;
			case "baklist":
				break;
			case 'download':
				$filename = $name ? $name : input ('get.name');
				if ( !$filename || $filename == "" ) {
					return;
				}
				download ($DataDir . DS . $filename);
				break;
		}
		
		return;
		
	}
	
	/**
	 * 目录检索
	 *
	 * @param string $FilePath
	 * @param int    $Order
	 *
	 * @return array
	 */
	private function MyScandir ($FilePath = './', $Order = 0)
	{
		$FilePath = opendir ($FilePath);
		while ( FALSE !== ($filename = readdir ($FilePath)) ) {
			$FileAndFolderAyy[] = $filename;
		}
		$Order == 0 ? sort ($FileAndFolderAyy) : rsort ($FileAndFolderAyy);
		
		return $FileAndFolderAyy;
	}
}