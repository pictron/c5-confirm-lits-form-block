<?php
namespace Application\Controller\SinglePage\Dashboard;
use \Concrete\Core\Page\Controller\DashboardPageController;

use Core;
use Loader,
    UserInfo,
    Page;

use Concrete\Core\File\File;
use \Concrete\Block\Form\MiniSurvey;

class Listcode extends DashboardPageController
{
    protected $encoding = 'SJIS';
    protected $rt = '\r\n';
    
    public function view(){
  		$db = Loader::db();
  		$text = Core::make('helper/text');
  		$this->set('mode', 'list');
  		
  		$q = $db->createQueryBuilder();
  		$q->select('*')->from('ListcodeFormTable,ListcodeFormAnswerInd')->where('ListcodeFormTable.bID = ListcodeFormAnswerInd.bID')->groupBy('ListcodeFormTable.bID');
  		
  		$r = $q->Execute();
  		$anserlist = array();
  		while ($row = $r->fetchRow()) {
          array_push($anserlist, $row);
      }
  		$this->set('anserlist', $anserlist);
  		
  		if($_REQUEST['qsid']){
      $questionSet = $this->get('qsid');
      $this->set('questionSet', $questionSet);
      }
  	}
  	
  	public function datadetail(){

    	$db = Loader::db();
    	$text = Core::make('helper/text');
    	$qsid = (int)$text->sanitize($_REQUEST['qsid']);
    	$q = $db->createQueryBuilder();
    	$q->select('*')->from('ListcodeFormAnswerInd')->where('bID = :bID')->setParameter('bID', $qsid);
    	$r = $q->Execute();
    	$datalist = array();
  		while ($row = $r->fetchRow()) {
          array_push($datalist, $row);
      }
  		$this->set('datalist', $datalist);
  		$this->set('mode', 'detail');
    }
    
    public function csv(){
      $this->set('mode', 'csv');
      $db = Loader::db();
      $qsid = $this->get('csvid');
      
      $textHelper = Loader::helper('text');
      
      $fileName = 'listcode'.$qsid;
      
      header("Content-Type: text/csv");
      header("Cache-control: private");
      header("Pragma: public");
      $date = date('Ymd');
      header("Content-Disposition: attachment; filename=" . $fileName . "_form_data_{$date}.csv");

      $fp = fopen('php://output', 'w');
      
      $q = $db->createQueryBuilder();
      $q->select('*')->from('ListcodeFormAnswerInd')->where('bID = :bID')->setParameter('bID', $qsid);
      $r = $q->Execute();
      $datalist = array();
  		while ($row = $r->fetchRow()) {
          array_push($datalist, $row);
      }
      //fputcsv($fp, $datalist);
      
      $asID = $datalist[0]['asID'];
      $q = $db->createQueryBuilder();
      $q->select('*')->from('ListcodeFormAnswerTable')->where('asID = :asID')->setParameter('asID', $asID);
      $r = $q->Execute();
      $csvarray = array();
      while ($row = $r->fetchRow()) {
        array_push($csvarray, $row['label']);
      }
      mb_convert_variables('sjis', 'utf-8', $csvarray);
      array_push($csvarray, t('created'));
      fputcsv($fp, $csvarray);
      
      foreach($datalist as $dlist) {
        $csvarray = array();
        $asID = $dlist['asID'];
        $query = $db->createQueryBuilder();
        $query->select('*')->from('ListcodeFormAnswerTable')->where('asID = :asID')->setParameter('asID', $asID);
        $r = $query->Execute();
        while ($row = $r->fetchRow()) {
          if($row['answer']){
            array_push($csvarray, $row['answer']);
          }else{
            array_push($csvarray, $row['answerLong']);
          }
        }
        array_push($csvarray, t('created'));
        mb_convert_variables('sjis', 'utf-8', $csvarray);
        fputcsv($fp, $csvarray);
      }
      
      fclose($fp);
      die;
      }

}