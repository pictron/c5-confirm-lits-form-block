<?php
namespace Application\Block\ListcodeForm;

use Core;
use UserInfo;
use Page;
use Database;
use Concrete\Core\Block\BlockController;

class Controller extends BlockController
{
    protected $btInterfaceWidth = 300;
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $btInterfaceHeight = 320;
    protected $btTable = 'ListcodeFormTable';
    protected $btWriteInd = 'ListcodeFormAnswerInd';
    protected $btWriteTable = 'ListcodeFormAnswerTable';
    public $formList;
    
    public function __construct($b = null){
      parent::__construct($b);
      $formList = array();
      $formItem = array();
      
      $formItem['name'] = 'name';
      $formItem['validation'] = array('required');
      $formItem['label'] = '名前';
      $formItem['type'] = 'text';
      array_push($formList,$formItem);
      
      $formItem['name'] = 'email';
      $formItem['validation'] = array('required','email');
      $formItem['label'] = 'メールアドレス';
      $formItem['type'] = 'text';
      array_push($formList,$formItem);
      
      $formItem['name'] = 'place';
      $formItem['validation'] = array('required');
      $formItem['label'] = '会場';
      $formItem['type'] = 'select';
      $formItem['options'] = array('大阪'=>'大阪','東京'=>'東京','名古屋'=>'名古屋');
      array_push($formList,$formItem);
      
      $formItem['name'] = 'gender';
      $formItem['validation'] = array('required');
      $formItem['label'] = '性別';
      $formItem['type'] = 'radio';
      $formItem['options'] = array('男性','女性');
      array_push($formList,$formItem);
      
      $formItem['name'] = 'session';
      $formItem['validation'] = array('required');
      $formItem['label'] = '受講セッション';
      $formItem['type'] = 'checkbox';
      $formItem['options'] = array('セッションＡ','セッションＢ','セッションＣ');
      array_push($formList,$formItem);

      
      $formItem['name'] = 'message';
      $formItem['validation'] = array('required');
      $formItem['label'] = 'メッセージ';
      $formItem['type'] = 'textarea';
      array_push($formList,$formItem);
      
      $this->set('formList', $formList);
      $this->formList = $formList;
      
      $this->set('formname',$this->formname);
      $this->set('bid', $this->bID);
    }

    /** 
     * Used for localization. If we want to localize the name/description we have to include this.
     */
    public function getBlockTypeDescription()
    {
        return "配列で作成したフォームを確認画面付きで送信できるブロック";
    }

    public function getBlockTypeName()
    {
        return "リストフォームブロック";
    }
    public function view(){ 
      $this->set('rtmail',$this->rtmail);
      $this->set('section', 'edit');
      $this->set('errors', array());
      $this->set('input', array());
    }
    
    public function action_confirm() {
        $input = $this->getpost($input);
        $this->set('formname',$this->formname);
        if ($this->chekval()) {
            $section = 'confirm';
            $this->set('input', $input);
        } else {
            $section = 'edit';
        }
        $this->set('section', $section);		
        return true;    	
    }
    
    public function action_send() {
      $input = $this->getpost($input);
      $formList = $this->formList;
      $mail = Core::make('helper/mail');
      if ($this->chekval()) {
        $adminUserInfo = UserInfo::getByID(USER_SUPER_ID);
        $formFormEmailAddress = $adminUserInfo->getUserEmail();
              
        $body = '';
        foreach($formList as $flist){
          $body .= $flist['label'].":\r\n".$input[$flist['name']]. "\r\n";
        }
            
        // Send the mail
        $mail->to($formFormEmailAddress, 'admin');
        $mail->from($input['email'], $input['name']);
        $mail->replyto($input['email'], $input['name']);
        $mail->addParameter('formList', $formList);
        $mail->addParameter('formname',$this->formname);
        $mail->load('list_form_submission_admin');
        $mail->setSubject(t('%sフォーム:受付確認',$this->formname));
        @$mail->sendMail();
        
        //Send Retrun mail
        if($this->rtmail){
        $mail = null;
        $mail = Core::make('helper/mail');
        $mail->to($input['email'], $input['name']);
        $mail->from($formFormEmailAddress, 'admin');
        $mail->replyto($formFormEmailAddress, 'admin');
        $mail->addParameter('formList', $formList);
        $mail->addParameter('formname',$this->formname);
        $mail->load('list_form_submission');
        $mail->setSubject(t('%sフォーム',$this->formname));
        @$mail->sendMail();
        }
        
        //data save
        $db = Database::connection();
        
        //index
        $qid = "insert into {$this->btWriteInd} (bid) values (?)";
        $vid = array($this->bID);
        $rsid = $db->Execute($qid,$vid);
        $new_id = $db->Insert_ID();
        
        $q = "insert into {$this->btWriteTable} (asID, colname, label, answer, answerLong	) values (?, ?, ?, ?, ?)";
        
        foreach($formList as $flist){
          $v = null;
          if($flist['type'] == 'text' || $flist['type'] == 'select' || $flist['type'] == 'radio' || $flist['type'] == 'checkbox'){
            $v = array($new_id,$flist['name'],$flist['label'],$input[$flist['name']],null);
          }else if($flist['type'] == 'textarea'){
            $v = array($new_id,$flist['name'],$flist['label'],null,$input[$flist['name']]);
          }
          $rs = $db->Execute($q,$v);
        }
        
        $c = Page::getCurrentPage();
        header('location: '.Core::make('helper/navigation')->getLinkToCollection($c, true).'/complete');
        exit;
      }
    }
    
    public function action_complete() {
        $this->set('section','complete');
    }
    
    public function action_back() {
        $this->set('section','edit');
    }
    
    public function chekval() {
      $isvalid = true;
    
      $data = $_POST;
      $vf = Core::make('helper/validation/form');
      $vf->setData($data);
      $vf->addRequiredToken($this->bID.'ask');
      if(!$vf->test()){
        $isvalid = false;
      }
    
      $val = Core::make('helper/validation/strings');
      
      $formList = $this->formList;
      foreach($formList as $flist){
        if(in_array('required',$flist['validation'])){
          if(is_array($this->post($flist['name']))){
            if(count($this->post($flist['name'])) == 0){
              $errors[$flist['name']] = $flist['label'].'を入力してください';
              $isvalid = false;
            }
          }else{
            if (!strlen(trim($this->post($flist['name'])))) {
              $errors[$flist['name']] = $flist['label'].'を入力してください';
              $isvalid = false;
            }
          }
        }
        if(in_array('email',$flist['validation'])){
          if(!$val->email($this->post($flist['name']))){
            $errors[$flist['name']] = 'メールアドレスの形式を確認してください';
            $isvalid = false;
          }
        }
      }
      
      $this->set('errors', $errors);
      return $isvalid;
    }
    
    public function getpost($array) {
      $text = Core::make('helper/text');
      $formList = $this->formList;
      foreach($formList as $flist){
        if(is_array($this->post($flist['name']))) {
          $formarray = array();
          foreach($this->post($flist['name']) as $val) {
            array_push($formarray,$text->sanitize($val));
          }
          $array[$flist['name']] = $formarray;
        }else{
        $array[$flist['name']] = $text->sanitize($this->post($flist['name']));
        }
      }
      return $array;
    }

}
