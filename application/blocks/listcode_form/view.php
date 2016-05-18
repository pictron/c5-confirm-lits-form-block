<?php
defined('C5_EXECUTE') or die("Access Denied.");
use Concrete\Core\Validation\CSRF\Token;
$form = Core::make('helper/form');
$val = Loader::helper('validation/form');
$token = Loader::helper('validation/token');
$crsftag = $token->generate($bid.'ask');
?>
<?php if ($section == 'edit') { ?>
<h2><?php echo h($formname); ?></h2>
<form method="post" action="<?php echo $view->action('confirm')?>">
<?php foreach($formList as $flist){ ?>
  <div class="form-group <?php echo $flist['name'] ?>">
  <?php echo $form->label($flist['name'], $flist['label']); ?>
  <?php if($flist['type'] == 'text'){
    echo $form->text($flist['name'],$input[$flist['name']]);
    echo(isset($errors[$flist['name']]) ? '<font color="#ff0000">'.$errors[$flist['name']].'</font>' : '');
  }else if($flist['type'] == 'textarea'){
    echo $form->textarea($flist['name'],$input[$flist['name']]);
    echo(isset($errors[$flist['name']]) ? '<font color="#ff0000">'.$errors[$flist['name']].'</font>' : '');
  }else if($flist['type'] == 'select'){
    echo $form->select($flist['name'],$flist['options'],$input[$flist['name']]);
    echo(isset($errors[$flist['name']]) ? '<font color="#ff0000">'.$errors[$flist['name']].'</font>' : '');
  }else if($flist['type'] == 'radio'){
    foreach($flist['options'] as $radio){
      echo '<div class="checkbox-inline"><label>';
      echo $form->radio($flist['name'],$radio,$input[$flist['name']]);
      echo ' '.$radio;
      echo '</label></div>';
    }
    echo(isset($errors[$flist['name']]) ? '<font color="#ff0000">'.$errors[$flist['name']].'</font>' : '');
  }else if($flist['type'] == 'checkbox'){
    foreach($flist['options'] as $checkbox){
      echo '<div class="checkbox-inline"><label>';
      echo $form->checkbox($flist['name'].'[]',$checkbox,$input[$flist['name']]);
      echo ' '.$checkbox;
      echo '</label></div>';
    }
    echo(isset($errors[$flist['name']]) ? '<font color="#ff0000">'.$errors[$flist['name']].'</font>' : '');
  }
  ?>
  </div>
<?php } ?>
  <?php echo $form->hidden('ccm_token',$crsftag);?>
  <?php echo $form->submit('submit','確認')?>
</form>
<?php } ?>
<?php if ($section == 'confirm'){ ?>
<h2><?php echo h($formname); ?>：確認</h2>
<table class="table">
  <?php foreach($formList as $flist){ ?>
  <tr>
    <td><?php echo h($flist['label']); ?></td>
    <td><?php
      if(is_array($input[$flist['name']])){
        echo h(implode(',',$input[$flist['name']]));
      }else{
        echo h($input[$flist['name']]);
      }
    ?></td>
  </tr>
  <?php } ?>
</table>
<form method="post" id="form_confirm" action="<?php echo $view->action('send')?>">
<div class="form-group">
  <?php foreach($formList as $flist){
      if(is_array($input[$flist['name']])){
      echo $form->hidden($flist['name'],implode(',',$input[$flist['name']])); 
      }else{
      echo $form->hidden($flist['name'],$input[$flist['name']]);
      }
    }
  ?>
    <?php echo $form->hidden('ccm_token',$crsftag);?>
    <?php echo $form->submit('submit','送信')?>
    <a href="#" onclick="document.form_back.submit()" class="btn btn-default">戻る</a>
</div>
</form>
<form method="post" id="form_back" name="form_back" action="<?php echo $view->action('back')?>">
  <?php foreach($formList as $flist){
    echo $form->hidden($flist['name']);
    }
  ?>
  <?php echo $form->hidden('ccm_token',$crsftag);?>
</form>
<?php } ?>
<?php if ($section == 'complete'){ ?>
<h2><?php echo h($formname); ?>：完了</h2>
<p>お問い合わせありがとうございました。</p>
<?php } ?>