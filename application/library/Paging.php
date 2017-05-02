<?php
class Paging{
	/**页码**/
	public $pageNo = 1;
	/**页大小**/
	public $pageSize = 10;
	/**共多少页**/
	public $pageCount = 0;
	/**总记录数**/
	public $totalNum = 0;
	/**偏移量,当前页起始行**/
	public $offSet = 0;
	/**每页数据**/
	public $pageData = array();

	/**是否有上一页**/
	public $PrePage = true;
	/**是否有下一页**/
	public $NextPage = true;

	public $pageNoList = array();

	
	/**
	 *
	 * @param unknown_type $count 总行数
	 * @param unknown_type $size 分页大小
	 * @param unknown_type $string
	 */
	public function __construct($count,$size,$pageNo){

		$this->totalNum = $count;//总记录数
		$this->pageSize = $size;//每页大小
		$this->pageNo = $pageNo;
		//计算总页数
		$this->pageCount = ceil($this->totalNum/$this->pageSize);
		$this->pageCount = ($this->pageCount<=0)?1:$this->pageCount;
		//检查pageNo
		$this->pageNo = $this->pageNo == 0 ? 1 : $this->pageNo;
		$this->pageNo = $this->pageNo > $this->pageCount? $this->pageCount : $this->pageNo;

		//计算偏移
		$this->offset = ( $this->pageNo - 1 ) * $this->pageSize;
		//计算是否有上一页下一页
		$this->PrePage = $this->pageNo == 1 ?false:true;

		$this->NextPage = $this->pageNo >= $this->pageCount ?false:true;

		//$this->pageData = $pageData;
		

	}
	/**
	 * 分页算法
	 * @return
	 */
	private function generatePageList(){
		$pageList = array();
		if($this->pageCount <= 5){
			for($i=0;$i<$this->pageCount;$i++){
				array_push($pageList,$i+1);
			}
		}
		else
		{
			if($this->pageNo <= 4){
				for($i=0;$i<5;$i++){
					array_push($pageList,$i+1);
				}
				array_push($pageList,-1);
				array_push($pageList,$this->pageCount);

			}else if($this->pageNo > $this->pageCount - 4){
				array_push($pageList,1);

				array_push($pageList,-1);
				for($i=5;$i>0;$i--){
					array_push($pageList,$this->pageCount - $i+1);
				}
			}else if($this->pageNo > 4 && $this->pageNo <= $this->pageCount - 4){
				array_push($pageList,1);
				array_push($pageList,-1);
				array_push($pageList,$this->pageNo -2);
				array_push($pageList,$this->pageNo -1);
				array_push($pageList,$this->pageNo);
				array_push($pageList,$this->pageNo + 1);
				array_push($pageList,$this->pageNo + 2);
				array_push($pageList,-1);
				array_push($pageList,$this->pageCount);
			}
		}
		return $pageList;
	}
	
	/***
	 * 创建分页控件
	* @param
	* @return String
	*/
	public function PageHtml(){
		$pageList = $this->generatePageList();
		$pageString ="<ul class='pagination'>";
		if(!empty($pageList))
		{
			if($this->pageCount >0)
			{
				if($this->PrePage)
				{
					$pageString = $pageString ."<li><a href='?page=1'>&laquo;</a></li>";
				}
				foreach ($pageList as $k=>$p){
					$params = array("page"=>$p);
					if($p == -1){
						$pageString = $pageString ."<li><a href='#'>...</a></li>";
						continue;
					}
					$pageUrl = '?'.http_build_query($params);
					if($this->pageNo == $p){
						$pageString = $pageString ."<li class='active'><a href='".$pageUrl."'>" . $this->pageNo . "</a></li>";
						continue;
					}
					else 
					{
						$pageString = $pageString ."<li><a href='".$pageUrl."'>" . $p . "</a></li>";
					}
					
				}
	
				if($this->NextPage){
					$pageString = $pageString ."<li><a href='?page=".$this->pageCount."'>»</a></li>";
				}
			}
		}
		$pageString = $pageString .("</ul>");
		return $pageString;
	}
}